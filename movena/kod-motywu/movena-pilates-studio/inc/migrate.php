<?php
/**
 * Migracja treści do wersji 1.1.0 (zapisy online w CRM, jeden szablon pakietów):
 * usuwa duety, zajęcia indywidualne i odwołania do Fitssey z TREŚCI ZAPISANYCH w panelu,
 * zachowując wszystko, co zostało zmienione ręcznie. Uruchamia się sama, raz, po aktualizacji motywu.
 */
if (!defined('ABSPATH')) {
    exit;
}

const MVN_MIGRATE_TO = '1.1.7';

add_action('init', static function () {
    if (get_option('mvn_migrated') === MVN_MIGRATE_TO || !function_exists('get_field') || !function_exists('update_field') || !function_exists('acf_get_field')) {
        return;
    }
    if ((int) get_option('mvn_seeded', 0) < MVN_SEED_VERSION) {
        return; // świeża instalacja: treści z domyślnych ustawień są już nowe
    }
    try {
        $cur = (string) get_option('mvn_migrated', '0');
        if (version_compare($cur, '1.1.0', '<')) {
            mvn_migrate_110();
        }
        if (version_compare($cur, '1.1.2', '<')) {
            mvn_migrate_111();
        }
        mvn_migrate_117();
    } catch (Throwable $e) {
        error_log('[Movena] migracja ' . MVN_MIGRATE_TO . ': ' . $e->getMessage());
        return;
    }
    update_option('mvn_migrated', MVN_MIGRATE_TO, false);
}, 40);

/** Mapa tekstów: stary domyślny → nowy. */
function mvn_migrate_map(): array
{
    static $m = null;
    return $m ??= (array) require MVN_DIR . '/inc/migrate-map.php';
}

/** Podmienia stare domyślne teksty w całej strukturze (tylko gdy tekst nie był zmieniany ręcznie). */
function mvn_migrate_strings($v)
{
    if (is_array($v)) {
        foreach ($v as $k => $x) {
            $v[$k] = mvn_migrate_strings($x);
        }
        return $v;
    }
    if (is_string($v)) {
        $m = mvn_migrate_map();
        if (isset($m[$v])) {
            return $m[$v];
        }
        // komunikaty z „Fitssey” w ręcznie poprawionych tekstach nie są ruszane
    }
    return $v;
}

/** Nowe wiersze sekcji (pobrane z domyślnych treści) dla układu o danej nazwie. */
function mvn_migrate_default_row(string $group, string $layout): array
{
    foreach ((array) (mvn_defaults()[$group] ?? []) as $r) {
        if (($r['acf_fc_layout'] ?? '') === $layout) {
            return $r;
        }
    }
    return [];
}

/** Przekształca wiersze sekcji jednej strony. $group: sections | landing | landing_reformer. */
function mvn_migrate_rows(array $rows, string $group): array
{
    $out = [];
    $hasBook = false;
    foreach ($rows as $r) {
        if (!is_array($r)) {
            continue;
        }
        $l = (string) ($r['acf_fc_layout'] ?? '');
        if ($l === 'formaty') {
            continue; // karty „grupa / duet / indywidualnie”
        }
        if ($l === 'rezerwacja') {
            $hasBook = true;
        }
        if ($l === 'cennik') {
            foreach (['tab_group', 'tab_duo', 'tab_private', 'note_duo', 'note_private'] as $k) {
                unset($r[$k]);
            }
        }
        if ($l === 'vouchery' && !empty($r['items']) && is_array($r['items'])) {
            $r['items'] = array_values(array_filter($r['items'], static fn($i) => stripos((string) ($i['kind'] ?? ''), 'indywid') === false));
        }
        if ($l === 'kroki' && !empty($r['steps']) && stripos(wp_json_encode($r['steps'], JSON_UNESCAPED_UNICODE), 'Fitssey') !== false) {
            $def = mvn_migrate_default_row('sections', 'kroki');
            if (!empty($def['steps'])) {
                $r['steps'] = $def['steps'];
            }
        }
        if ($l === 'faq' && !empty($r['items']) && is_array($r['items'])) {
            $r['items'] = array_values(array_filter($r['items'], static fn($i) => !preg_match('/\bduet|\bduo\b/iu', (string) ($i['q'] ?? '') . ' ' . (string) ($i['a'] ?? ''))));
        }
        $out[] = mvn_migrate_strings($r);
    }
    if (!$hasBook) {
        $def = mvn_migrate_default_row($group, 'rezerwacja');
        if ($def) {
            $pos = count($out);
            foreach ($out as $i => $r) {
                if (in_array($r['acf_fc_layout'] ?? '', $group === 'sections' ? ['zajecia'] : ['lp_oferta'], true)) {
                    $pos = $i + 1;
                    break;
                }
            }
            if ($group !== 'landing_reformer') {
                array_splice($out, $pos, 0, [$def]);
            }
        }
    }
    return $out;
}

function mvn_migrate_110(): void
{
    $d = mvn_defaults();
    $opt = static fn(string $n) => get_field($n, 'option');
    $set = static function (string $n, $v) {
        update_field('field_mvn_' . $n, $v, 'option');
    };

    // 1) Pakiety: nowa lista (z zachowaniem cen zmienionych ręcznie), bez duetów i indywidualnych
    $cur = (array) $opt('cennik_grupa');
    $byName = [];
    foreach ($cur as $r) {
        if (is_array($r) && !empty($r['name'])) {
            $byName[(string) $r['name']] = $r;
        }
    }
    $new = [];
    foreach ((array) $d['site']['cennik_grupa'] as $r) {
        $old = $byName[$r['name']] ?? ($r['name'] === 'Poznaj REFORMER' ? ($byName['Pierwszy trening'] ?? null) : null);
        if ($old && isset($old['price']) && (float) $old['price'] > 0) {
            $r['price'] = (float) $old['price'];
            if ((int) $r['sessions'] > 1) {
                $r['per'] = round((float) $r['price'] / (int) $r['sessions'], 2);
            }
        }
        $new[] = $r;
    }
    $set('cennik_grupa', $new);
    $set('cennik_duo', []);
    $set('cennik_indyw', []);

    // 2) Zajęcia: bez Private Session
    $z = array_values(array_filter((array) $opt('zajecia'), static fn($r) => is_array($r) && stripos((string) ($r['name'] ?? ''), 'private') === false));
    $set('zajecia', $z);

    // 3) Lista form w formularzu
    $set('form_options', $d['site']['form_options']);

    // 4) Adresy zapisów: stare adresy Fitssey znikają (puste = kalendarz na stronie i sklep w CRM)
    foreach (['fitssey_schedule', 'fitssey_pricing', 'fitssey_login', 'fitssey_vouchers'] as $k) {
        if (stripos((string) $opt($k), 'fitssey.com') !== false) {
            $set($k, '');
        }
    }

    // 5) Teksty ogólne
    $map = mvn_migrate_map();
    foreach (['header_cta' => 'Zarezerwuj zajęcia', 'mobile_cta' => 'Sprawdź grafik'] as $k => $oldv) {
        if ((string) $opt($k) === $oldv) {
            $set($k, $d['site'][$k]);
        }
    }
    $seo = (string) $opt('seo_description');
    if (stripos($seo, 'w duecie') !== false) {
        $set('seo_description', $d['site']['seo_description']);
    }
    $set('booking_on', 1);

    // 5b) Strony prawne zawierające stare odwołania do Fitssey dostają aktualny tekst z motywu
    foreach ((array) require MVN_DIR . '/inc/legal-defaults.php' as $slug => $dd) {
        $page = get_page_by_path($slug);
        if ($page && stripos((string) $page->post_content, 'Fitssey') !== false) {
            wp_update_post(['ID' => $page->ID, 'post_content' => $dd['content']]);
        }
    }

    // 6) Sekcje: strona główna, strona „Pierwszy trening” i landing programu
    $pages = [
        [mvn_front_id(), 'sections'],
        [(int) (get_page_by_path('pierwszy-trening')->ID ?? 0), 'landing'],
        [(int) (get_page_by_path(MVN_REFORMER_SLUG)->ID ?? 0), 'landing_reformer'],
    ];
    foreach ($pages as [$id, $group]) {
        if ($id <= 0) {
            continue;
        }
        $rows = get_field('field_mvn_sekcje', $id);
        if (!is_array($rows) || !$rows) {
            continue;
        }
        update_field('field_mvn_sekcje', mvn_migrate_rows($rows, $group), $id);
    }
}

/**
 * 1.1.1 — porządek z ofertą zajęć: w studiu (i w Fitssey/CRM) jest jeden rodzaj zajęć — „Pilates na reformerach”.
 * Wymyślone nazwy (Healthy Spine, Military Body…) i sekcja „Oferta zajęć” znikają, a teksty „na pierwszy trening wybierz…” są poprawione.
 */
function mvn_migrate_111_rows(array $rows): array
{
    $out = [];
    foreach ($rows as $r) {
        if (!is_array($r)) {
            continue;
        }
        $l = (string) ($r['acf_fc_layout'] ?? '');
        if ($l === 'zajecia') {
            continue;
        }
        if ($l === 'pierwszy') {
            if (stripos(wp_json_encode($r['box_items'] ?? [], JSON_UNESCAPED_UNICODE), 'Healthy Spine') !== false) {
                $r['box_title'] = 'Pierwsze zajęcia Poznaj REFORMER:';
                $r['box_items'] = [['text' => '55 minut w grupie do 4 osób'], ['text' => 'Bez wcześniejszego doświadczenia'], ['text' => 'Termin wybierasz w kalendarzu']];
            }
            if (in_array((string) ($r['box_cel'] ?? ''), ['#classes', '#zajecia'], true)) {
                $r['box_cel'] = '#rezerwacja';
            }
        }
        if ($l === 'faq' && !empty($r['items']) && is_array($r['items'])) {
            foreach ($r['items'] as $k => $i) {
                if ((string) ($i['a'] ?? '') === 'Rezerwacji można dokonywać do 14 dni naprzód.') {
                    $r['items'][$k]['a'] = 'Pierwsze zajęcia zarezerwujesz w kalendarzu na stronie na 14 dni naprzód, a z karnetem zapisujesz się w panelu klientki na 3 tygodnie naprzód.';
                }
                if ((string) ($i['a'] ?? '') === 'Zapis jest możliwy do 1 minuty przed rozpoczęciem zajęć, o ile są wolne miejsca.') {
                    $r['items'][$k]['a'] = 'Pierwsze zajęcia rezerwujesz najpóźniej 3 godziny przed startem. Z karnetem możesz zapisać się w panelu klientki, o ile są wolne miejsca.';
                }
                if (stripos((string) ($i['a'] ?? ''), 'Healthy Spine') !== false) {
                    $r['items'][$k] = ['q' => 'Jak wyglądają pierwsze zajęcia?', 'a' => 'Poznaj REFORMER to 55-minutowe zajęcia w grupie do 4 osób. Instruktorka pokaże Ci sprzęt i poprowadzi trening od podstaw — nie potrzebujesz wcześniejszego doświadczenia.'];
                }
            }
        }
        $out[] = $r;
    }
    return $out;
}

function mvn_migrate_111(): void
{
    $d = mvn_defaults();
    $cg = (array) get_field('cennik_grupa', 'option');
    foreach ($cg as $k => $r) {
        if (is_array($r) && (string) ($r['restriction'] ?? '') === 'Pierwsze zajęcia — oferta jednorazowa, tylko dla nowych klientek') {
            $cg[$k]['restriction'] = 'Tylko dla nowych klientek';
        }
    }
    update_field('field_mvn_cennik_grupa', $cg, 'option');
    update_field('field_mvn_zajecia', $d['site']['zajecia'], 'option');
    foreach ([mvn_front_id(), (int) (get_page_by_path('pierwszy-trening')->ID ?? 0), (int) (get_page_by_path(MVN_REFORMER_SLUG)->ID ?? 0)] as $id) {
        if ($id <= 0) {
            continue;
        }
        $rows = get_field('field_mvn_sekcje', $id);
        if (is_array($rows) && $rows) {
            update_field('field_mvn_sekcje', mvn_migrate_111_rows($rows), $id);
        }
    }
}

/** 1.1.7 — voucher na pojedyncze wejście (100 zł) na początku listy voucherów. */
function mvn_migrate_117(): void
{
    foreach ([mvn_front_id(), (int) (get_page_by_path('pierwszy-trening')->ID ?? 0), (int) (get_page_by_path(MVN_REFORMER_SLUG)->ID ?? 0)] as $id) {
        if ($id <= 0) {
            continue;
        }
        $rows = get_field('field_mvn_sekcje', $id);
        if (!is_array($rows) || !$rows) {
            continue;
        }
        foreach ($rows as $k => $r) {
            if (is_array($r) && ($r['acf_fc_layout'] ?? '') === 'vouchery' && is_array($r['items'] ?? null)) {
                $has = false;
                foreach ($r['items'] as $i) {
                    if ((int) ($i['sessions'] ?? 0) === 1) {
                        $has = true;
                    }
                }
                if (!$has) {
                    array_unshift($rows[$k]['items'], ['kind' => 'Grupowy', 'sessions' => 1, 'price' => 100]);
                }
            }
        }
        update_field('field_mvn_sekcje', $rows, $id);
    }
}
