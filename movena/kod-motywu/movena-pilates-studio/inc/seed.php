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
    mvn_ensure_pages();
    flush_rewrite_rules(false);
});

/** Zapis domyślnych treści do pól SCF (gdy wtyczka jest aktywna), raz. */
add_action('admin_init', static function () {
    if ((int) get_option('mvn_seeded') >= MVN_SEED_VERSION || !current_user_can('manage_options')) {
        return;
    }
    if (!function_exists('update_field') || !function_exists('acf_get_field')) {
        return;
    }
    mvn_seed(false);
}, 30);

/** Tworzy brakujące strony; zwraca ID strony głównej. */
function mvn_ensure_pages(): int
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

    $legal = require MVN_DIR . '/inc/legal-defaults.php';
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
    mvn_ensure_landing();
    mvn_ensure_reformer();
    mvn_ensure_grafik();
    return $front;
}

/** Strona docelowa pod reklamy (/pierwszy-trening/) z szablonem „Landing”; zwraca jej ID. */
function mvn_ensure_landing(): int
{
    $page = get_page_by_path('pierwszy-trening');
    if ($page) {
        $id = (int) $page->ID;
    } else {
        $id = (int) wp_insert_post([
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Pierwszy trening pilatesu w Radomiu', 'post_name' => 'pierwszy-trening',
            'post_content' => '',
            'post_excerpt' => 'Pierwszy trening pilatesu w Radomiu za 69 zł. Małe grupy, reformery, pełna opieka instruktorki. Zapisz się na termin online.',
        ]);
    }
    if ($id > 0 && get_post_meta($id, '_wp_page_template', true) !== 'page-landing.php') {
        update_post_meta($id, '_wp_page_template', 'page-landing.php');
    }
    return $id;
}

/** Strona docelowa programu Reformer 10 (/reformer-10/) z szablonem „Landing”; zwraca jej ID. */
function mvn_ensure_reformer(): int
{
    $page = get_page_by_path(MVN_REFORMER_SLUG);
    if ($page) {
        $id = (int) $page->ID;
    } else {
        $id = (int) wp_insert_post([
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Program Reformer 10 — pilates na reformerach w Radomiu', 'post_name' => MVN_REFORMER_SLUG,
            'post_content' => '',
            'post_excerpt' => 'Program Reformer 10: 10 treningów pilatesu na reformerach w 5 tygodni, zamknięte grupy po 4 osoby, 600 zł. Movena Pilates Studio, Radom.',
        ]);
    }
    if ($id > 0 && get_post_meta($id, '_wp_page_template', true) !== 'page-landing.php') {
        update_post_meta($id, '_wp_page_template', 'page-landing.php');
    }
    return $id;
}

/** Sekcje landingu programu (tylko gdy jego pole „Sekcje” jest puste) + wpis „Program Reformer 10” na liście form w formularzu. */
function mvn_seed_reformer(bool $force = false): void
{
    $id = mvn_ensure_reformer();
    if ($id > 0 && ($force || empty(get_field('field_mvn_sekcje', $id)))) {
        mvn_seed_sections($id, mvn_defaults(), 'landing_reformer');
    }
    $opts = get_field('field_mvn_form_options', 'option');
    if (is_array($opts)) {
        foreach ($opts as $o) {
            if (($o['label'] ?? '') === 'Program Reformer 10') {
                return;
            }
        }
        array_splice($opts, min(2, count($opts)), 0, [['label' => 'Program Reformer 10', 'crm' => 'other']]);
        update_field('field_mvn_form_options', $opts, 'option');
    }
}

/** Domyślne sekcje landingu (tylko gdy jego pole „Sekcje” jest puste). */
function mvn_seed_landing(bool $force = false): void
{
    $id = mvn_ensure_landing();
    if ($id > 0 && ($force || empty(get_field('field_mvn_sekcje', $id)))) {
        mvn_seed_sections($id, mvn_defaults(), 'landing');
    }
}

/** Importuje zdjęcie z motywu do biblioteki mediów (raz); zwraca ID załącznika lub 0. */
function mvn_import_image(string $rel): int
{
    $map = get_option('mvn_imported', []);
    if (!is_array($map)) {
        $map = [];
    }
    if (!empty($map[$rel]) && get_post((int) $map[$rel])) {
        return (int) $map[$rel];
    }
    $src = MVN_DIR . '/assets/img/' . $rel;
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
    update_option('mvn_imported', $map, false);
    return (int) $id;
}

/** Zapisuje domyślne treści w polach. $force = nadpisz istniejące wartości. */
function mvn_seed(bool $force): void
{
    if (!$force && get_option('mvn_seeded')) {
        return;
    }
    $front = mvn_ensure_pages();
    $d = mvn_defaults();

    // Ustawienia globalne
    foreach ($d['site'] as $name => $val) {
        if ($name === 'og_image') {
            $val = mvn_import_image((string) $val) ?: '';
        }
        update_field('field_mvn_' . $name, $val, 'option');
        if ($name === 'og_image' && $val) {
            update_post_meta((int) $val, '_wp_attachment_image_alt', (string) ($d['site']['og_image_alt'] ?? ''));
        }
    }

    if ($front > 0) {
        mvn_seed_sections($front, $d);
    }
    mvn_seed_landing($force);
    mvn_seed_reformer($force);
    update_option('mvn_seeded', MVN_SEED_VERSION);
}

/** Domyślne sekcje strony głównej → pole elastyczne „Sekcje”. */
function mvn_seed_sections(int $pageId, array $d, string $key = 'sections'): void
{
    $imageFields = ['image', 'photo', 'photo1', 'photo2'];
    $rows = [];
    foreach ((array) ($d[$key] ?? []) as $row) {
        foreach ($imageFields as $f) {
            if (isset($row[$f]) && is_string($row[$f]) && $row[$f] !== '') {
                $row[$f] = mvn_import_image($row[$f]) ?: '';
            }
        }
        if (!empty($row['gallery']) && is_array($row['gallery'])) {
            foreach ($row['gallery'] as $i => $it) {
                if (!empty($it['image']) && is_string($it['image'])) {
                    $row['gallery'][$i]['image'] = mvn_import_image($it['image']) ?: '';
                }
            }
        }
        $rows[] = $row;
    }
    update_field('field_mvn_sekcje', $rows, $pageId);
}

/** Przywraca treść stron prawnych (polityka, cookies, regulamin) z motywu; nadpisuje obecną treść tych stron. */
function mvn_restore_legal(): int
{
    $n = 0;
    foreach (require MVN_DIR . '/inc/legal-defaults.php' as $slug => $d) {
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


/** Podstrona z grafikiem i zapisami (/grafik/, szablon „Grafik zajęć i zapisy”); zwraca jej ID. */
function mvn_ensure_grafik(): int
{
    $page = get_page_by_path('grafik');
    if ($page) {
        $id = (int) $page->ID;
    } else {
        $id = (int) wp_insert_post([
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Grafik zajęć i zapisy', 'post_name' => 'grafik', 'post_content' => '',
            'post_excerpt' => 'Grafik zajęć pilatesu na reformerach w Radomiu z wolnymi miejscami. Masz karnet? Zapisz się online w kilka sekund.',
        ]);
    }
    if ($id > 0 && get_post_meta($id, '_wp_page_template', true) !== 'page-grafik.php') {
        update_post_meta($id, '_wp_page_template', 'page-grafik.php');
    }
    return $id;
}

/** Aktualizacja motywu: strona /grafik/ powstaje raz, także na działającej instalacji. */
add_action('init', static function () {
    if (get_option('mvn_grafik_page') === '1' || !function_exists('wp_insert_post')) {
        return;
    }
    mvn_ensure_grafik();
    update_option('mvn_grafik_page', '1', false);
}, 50);
