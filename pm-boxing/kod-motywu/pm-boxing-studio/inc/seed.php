<?php
/**
 * Pierwsze uruchomienie: tworzy stronę główną i strony prawne oraz zapisuje domyślne treści w polach SCF,
 * żeby po aktywacji motywu od razu było co edytować.
 */
if (!defined('ABSPATH')) {
    exit;
}

/** Po aktywacji motywu: strony (nie wymaga SCF). */
add_action('after_switch_theme', static function () {
    pmbs_ensure_pages();
    flush_rewrite_rules(false);
});

/** Zapis domyślnych treści do pól SCF (gdy wtyczka jest aktywna), raz. */
add_action('admin_init', static function () {
    if ((int) get_option('pmbs_seeded') >= PMBS_SEED_VERSION || !current_user_can('manage_options')) {
        return;
    }
    if (!function_exists('update_field') || !function_exists('acf_get_field')) {
        return;
    }
    $had = (int) get_option('pmbs_seeded');
    if ($had >= 1) {
        if ($had < 2) {
            pmbs_migrate_v1(); // aktualizacja z wersji 1.x: tylko nowa struktura pól
        }
        pmbs_migrate_v3(); // obraz przy udostępnianiu trafia do Biblioteki mediów i do pola
        pmbs_migrate_v4(); // strona landingowa pod reklamy
        return;
    }
    pmbs_seed(false);
}, 30);

/** Tworzy brakujące strony; zwraca ID strony głównej. */
function pmbs_ensure_pages(): int
{
    $front = (int) get_option('page_on_front');
    if (!$front || get_option('show_on_front') !== 'page' || !get_post($front)) {
        $existing = get_page_by_path('strona-glowna');
        $front = $existing ? (int) $existing->ID : (int) wp_insert_post([
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Strona główna', 'post_name' => 'strona-glowna', 'post_content' => '',
        ]);
        if ($front > 0) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $front);
        }
    }

    $legal = require PMBS_DIR . '/inc/legal-defaults.php';
    foreach ($legal as $slug => $d) {
        $page = get_page_by_path($slug);
        if ($page) {
            // Szkic lub pusta strona (np. domyślna „Polityka prywatności” utworzona przez WordPress): wypełnij treścią motywu
            if ($page->post_status === 'draft' || trim((string) $page->post_content) === '') {
                wp_update_post(['ID' => $page->ID, 'post_status' => 'publish', 'post_title' => $d['title'], 'post_content' => $d['content'], 'post_excerpt' => $d['desc']]);
            }
            continue;
        }
        wp_insert_post([
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $d['title'], 'post_name' => $slug,
            'post_content' => $d['content'], 'post_excerpt' => $d['desc'],
        ]);
    }
    pmbs_ensure_landing();
    return $front;
}

/** Strona docelowa pod reklamy (/trening-wdrozeniowy/) z szablonem „Landing”; zwraca jej ID. */
function pmbs_ensure_landing(): int
{
    $page = get_page_by_path('trening-wdrozeniowy');
    if ($page) {
        $id = (int) $page->ID;
    } else {
        $id = (int) wp_insert_post([
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Trening wdrożeniowy w Radomiu', 'post_name' => 'trening-wdrozeniowy',
            'post_content' => '',
            'post_excerpt' => 'Trening wdrożeniowy boksu w Radomiu: godzina 1 na 1 z certyfikowanym trenerem. Dla początkujących, bez kondycji i sprzętu. Umów pierwszy trening.',
        ]);
    }
    if ($id > 0 && get_post_meta($id, '_wp_page_template', true) !== 'page-landing.php') {
        update_post_meta($id, '_wp_page_template', 'page-landing.php');
    }
    return $id;
}

/** Wersja 4: strona landingowa z domyślnymi sekcjami (tylko gdy jej pole „Sekcje” jest puste). */
function pmbs_migrate_v4(): void
{
    $id = pmbs_ensure_landing();
    if ($id > 0) {
        $cur = get_field('field_pmbs_sekcje', $id);
        if (empty($cur)) {
            pmbs_seed_sections($id, pmbs_defaults(), 'landing');
        }
    }
    update_option('pmbs_seeded', PMBS_SEED_VERSION);
}

/** Importuje zdjęcie z motywu do biblioteki mediów (raz); zwraca ID załącznika lub 0. */
function pmbs_import_image(string $rel): int
{
    $map = get_option('pmbs_imported', []);
    if (!is_array($map)) {
        $map = [];
    }
    if (!empty($map[$rel]) && get_post((int) $map[$rel])) {
        return (int) $map[$rel];
    }
    $src = PMBS_DIR . '/assets/img/' . $rel;
    if (!is_file($src)) {
        return 0;
    }
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = wp_tempnam(basename($src));
    if (!$tmp || !@copy($src, $tmp)) {
        return 0;
    }
    $id = media_handle_sideload(
        ['name' => basename($rel), 'tmp_name' => $tmp],
        0,
        null,
        ['post_title' => ucfirst(str_replace(['-', '_'], ' ', pathinfo($rel, PATHINFO_FILENAME)))]
    );
    if (is_wp_error($id)) {
        @unlink($tmp);
        return 0;
    }
    $map[$rel] = (int) $id;
    update_option('pmbs_imported', $map, false);
    return (int) $id;
}

/** Aktualizacja z 1.x: dopisuje sekcje, grupy zajęć i nowe formaty list; nie rusza reszty ustawień. */
function pmbs_migrate_v1(): void
{
    $front = pmbs_ensure_pages();
    $d = pmbs_defaults();
    foreach (['grupy', 'call_times', 'foot_offer'] as $name) {
        update_field('field_pmbs_' . $name, $d['site'][$name], 'option');
    }
    if ($front > 0) {
        pmbs_seed_sections($front, $d);
    }
    update_option('pmbs_seeded', PMBS_SEED_VERSION);
}

/** Wersja 3: obraz przy udostępnianiu (og-image.jpg z motywu) do Biblioteki mediów i do pola, jeśli puste. */
function pmbs_migrate_v3(): void
{
    $cur = get_field('field_pmbs_og_image', 'option');
    if (empty($cur)) {
        $id = pmbs_import_image('og-image.jpg');
        if ($id) {
            update_field('field_pmbs_og_image', $id, 'option');
            update_post_meta($id, '_wp_attachment_image_alt', (string) (pmbs_defaults()['site']['og_image_alt'] ?? ''));
        }
    }
    update_option('pmbs_seeded', PMBS_SEED_VERSION);
}

/** Zapisuje domyślne treści w polach. $force = nadpisz istniejące wartości. */
function pmbs_seed(bool $force): void
{
    if (!$force && get_option('pmbs_seeded')) {
        return;
    }
    $front = pmbs_ensure_pages();
    $d = pmbs_defaults();

    // Ustawienia globalne
    foreach ($d['site'] as $name => $val) {
        if ($name === 'og_image') {
            $val = pmbs_import_image((string) $val) ?: '';
        }
        update_field('field_pmbs_' . $name, $val, 'option');
    }

    if ($front > 0) {
        pmbs_seed_sections($front, $d);
    }
    pmbs_migrate_v4();
    update_option('pmbs_seeded', PMBS_SEED_VERSION);
}

/** Domyślne sekcje strony głównej → pole elastyczne „Sekcje”. */
function pmbs_seed_sections(int $front, array $d, string $key = 'sections'): void
{
    $imageFields = ['image', 'photo', 'photo1', 'photo2'];
    $rows = [];
    foreach ((array) ($d[$key] ?? []) as $row) {
        foreach ($imageFields as $f) {
            if (isset($row[$f]) && is_string($row[$f]) && $row[$f] !== '') {
                $row[$f] = pmbs_import_image($row[$f]) ?: '';
            }
        }
        if ($row['acf_fc_layout'] === 'partnerzy') {
            foreach ($row['items'] as $i => $it) {
                $row['items'][$i]['logo'] = pmbs_import_image((string) $it['logo']) ?: '';
            }
        }
        $rows[] = $row;
    }
    update_field('field_pmbs_sekcje', $rows, $front);
}

/** Przywraca treść stron prawnych (polityka, cookies, regulamin) z motywu; nadpisuje obecną treść tych stron. */
function pmbs_restore_legal(): int
{
    $n = 0;
    foreach (require PMBS_DIR . '/inc/legal-defaults.php' as $slug => $d) {
        $page = get_page_by_path($slug);
        $arr = ['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $d['title'], 'post_name' => $slug, 'post_content' => $d['content'], 'post_excerpt' => $d['desc']];
        if ($page) {
            $arr['ID'] = $page->ID;
            wp_update_post($arr);
        } else {
            wp_insert_post($arr);
        }
        $n++;
    }
    return $n;
}
