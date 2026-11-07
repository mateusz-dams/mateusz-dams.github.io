<?php
/**
 * Funkcje pomocnicze: odczyt pól SCF z wartościami domyślnymi, obrazy, formatowanie.
 */
if (!defined('ABSPATH')) {
    exit;
}

const PMBS_NBSP = "\u{00A0}";

/** Domyślne treści (z inc/defaults.php). */
function pmbs_defaults(): array
{
    static $d = null;
    if ($d === null) {
        $d = require PMBS_DIR . '/inc/defaults.php';
    }
    return $d;
}

/** Czy pola SCF/ACF są dostępne i motyw zapisał już swoje domyślne treści do bazy. */
function pmbs_ready(): bool
{
    return function_exists('get_field') && (int) get_option('pmbs_seeded', 0) >= PMBS_SEED_VERSION;
}

function pmbs_front_id(): int
{
    return (int) get_option('page_on_front');
}

/** Normalizacja wartości z SCF do typu zgodnego z wartością domyślną. */
function pmbs_normalize($v, $def)
{
    if (is_array($def)) {
        return is_array($v) ? $v : [];
    }
    if (is_bool($v)) { // przełącznik (true/false) wyłączony = 0, a nie wartość domyślna
        return (int) $v;
    }
    if ($v === false || $v === null) {
        return is_string($def) ? '' : $def;
    }
    return $v;
}

/** Ustawienie globalne (strona „PM Boxing Studio” w panelu). */
function pmo(string $name)
{
    static $cache = [];
    if (array_key_exists($name, $cache)) {
        return $cache[$name];
    }
    $def = pmbs_defaults()['site'][$name] ?? null;
    $val = $def;
    if (pmbs_ready()) {
        $v = get_field($name, 'option');
        $val = ($v === null) ? $def : pmbs_normalize($v, $def);
    }
    return $cache[$name] = $val;
}

/* ------------------------------------------------------------ sekcje strony głównej */

/**
 * Sekcje strony głównej (pole elastyczne „Sekcje”) z ujednoliconymi kotwicami.
 * Bez SCF lub przed pierwszym zapisem zwraca sekcje domyślne z inc/defaults.php.
 */
function pmbs_is_landing(): bool
{
    return function_exists('is_page_template') && is_page_template('page-landing.php');
}

/** ID strony, z której pochodzą sekcje: landing (gdy otwarty) albo strona główna. */
/** Strona z pełnym układem motywu (strona główna albo landing). */
function pmbs_is_site_page(): bool
{
    return is_front_page() || pmbs_is_landing();
}

function pmbs_content_id(): int
{
    return pmbs_is_landing() ? (int) get_queried_object_id() : pmbs_front_id();
}

function pmbs_sections(?int $forId = null): array
{
    static $cache = [];
    $id = $forId ?? pmbs_content_id();
    if (isset($cache[$id])) {
        return $cache[$id];
    }
    $defs = pmbs_defaults();
    $rows = pmbs_is_landing() && $forId === null ? ($defs['landing'] ?? []) : ($defs['sections'] ?? []);
    if (pmbs_ready() && $id > 0) {
        $v = get_field('sekcje', $id);
        $rows = is_array($v) ? $v : [];
    }
    $out = [];
    $used = [];
    foreach ($rows as $i => $r) {
        if (!is_array($r) || empty($r['acf_fc_layout'])) {
            continue;
        }
        $layout = (string) $r['acf_fc_layout'];
        $a = sanitize_title((string) ($r['anchor'] ?? ''));
        if ($a === '') {
            $a = $layout . '-' . ($i + 1);
        }
        $show = !array_key_exists('show', $r) || !empty($r['show']);
        $base = $a;
        for ($n = 2; $show && isset($used[$a]); $n++) {
            $a = $base . '-' . $n;
        }
        if ($show) {
            $used[$a] = true; // wyłączone sekcje nie zajmują kotwic
        }
        $r['anchor'] = $a;
        $r['_show'] = $show;
        $out[] = $r;
    }
    return $cache[$id] = $out;
}

/** Pierwsza sekcja danego układu (nawet wyłączona) lub pusta tablica. */
function pmbs_first(string $layout): array
{
    foreach (pmbs_sections() as $s) {
        if ($s['acf_fc_layout'] === $layout) {
            return $s;
        }
    }
    return [];
}

/** Wartość pola sekcji. */
function pms(array $s, string $name, $def = '')
{
    $v = $s[$name] ?? $def;
    return ($v === null || $v === false) ? $def : $v;
}

/** Tablica (np. repeater) z pola sekcji. */
function pmsa(array $s, string $name): array
{
    $v = $s[$name] ?? [];
    return is_array($v) ? $v : [];
}

/** Atrybuty id i class sekcji. */
function pmbs_sec_attrs(array $s, string $class = 'section clip', string $idFallback = ''): string
{
    if (($s['tlo'] ?? '') === 'alt') {
        $class = str_replace('section ', 'section alt ', $class);
    }
    $id = (string) ($s['anchor'] ?? $idFallback);
    return ($id !== '' ? ' id="' . esc_attr($id) . '"' : '') . ' class="' . esc_attr($class) . '"';
}

/** Duży napis w tle sekcji (pole „ghost”; gdy brak pola, użyj domyślnego). */
function pmbs_ghost(array $s, string $default = '', string $class = 'ghost'): string
{
    $g = array_key_exists('ghost', $s) ? (string) $s['ghost'] : $default;
    return $g !== '' ? '<span class="' . esc_attr($class) . '" aria-hidden="true">' . esc_html($g) . '</span>' . "\n  " : '';
}

/** Adres z pola „cel”: kotwica (z # lub bez), albo pełny adres. */
function pmbs_href($cel, string $default = ''): string
{
    $c = trim((string) $cel);
    if ($c === '') {
        $c = $default;
    }
    if ($c === '') {
        return '#';
    }
    if (preg_match('~^(https?:|tel:|mailto:|/)~i', $c)) {
        return $c;
    }
    return '#' . ltrim($c, '#');
}

/** Kotwica formularza kontaktowego (pierwsza sekcja „Kontakt”). */
function pmbs_contact_href(): string
{
    $s = pmbs_first('kontakt');
    return '#' . ($s['anchor'] ?? 'kontakt');
}

/** Kotwica pierwszej sekcji (górna część strony). */
function pmbs_top_href(): string
{
    $all = pmbs_sections();
    return $all ? '#' . $all[0]['anchor'] : '#';
}

/** Nazwa układu → etykieta menu domyślna. */
function pmbs_menu_items(): array
{
    $names = function_exists('pmbs_layout_names') ? pmbs_layout_names() : [];
    $out = [];
    foreach (pmbs_sections() as $s) {
        if (!$s['_show'] || empty($s['in_menu'])) {
            continue;
        }
        $label = trim((string) ($s['menu_label'] ?? ''));
        if ($label === '') {
            $label = $names[$s['acf_fc_layout']] ?? $s['anchor'];
        }
        $out[] = ['label' => $label, 'href' => '#' . $s['anchor']];
    }
    return $out;
}

/** Cena treningu wdrożeniowego (z sekcji Cennik). */
function pmbs_onboarding()
{
    $v = pms(pmbs_first('cennik'), 'onboarding', 0);
    if (!$v) {
        $v = pms(pmbs_first('lp_oferta'), 'price', 0);
    }
    if (!$v && pmbs_is_landing()) {
        foreach (pmbs_sections(pmbs_front_id()) as $s) {
            if ($s['acf_fc_layout'] === 'cennik') {
                return pms($s, 'onboarding', 0);
            }
        }
    }
    return $v;
}

/** Grupy zajęć (strona opcji). */
function pmbs_groups(): array
{
    return array_values(array_filter((array) pmo('grupy'), static function ($g) {
        return is_array($g) && !empty($g['name']);
    }));
}

/* ------------------------------------------------------------ tekst */

/** Dzieli tekst na niepuste linie. */
function pmbs_lines($s): array
{
    $out = [];
    if (is_array($s)) { // repeater z jednym polem tekstowym
        foreach ($s as $r) {
            $t = trim((string) (is_array($r) ? ($r['text'] ?? $r['label'] ?? reset($r)) : $r));
            if ($t !== '') {
                $out[] = $t;
            }
        }
        return $out;
    }
    foreach (preg_split('/\r\n|\r|\n/', (string) $s) as $l) {
        $l = trim($l);
        if ($l !== '') {
            $out[] = $l;
        }
    }
    return $out;
}

/** Dzieli tekst na akapity (pusta linia = nowy akapit). */
function pmbs_paras($s): array
{
    $out = [];
    foreach (preg_split('/(?:\r\n|\r|\n){2,}/', trim((string) $s)) as $p) {
        $p = trim($p);
        if ($p !== '') {
            $out[] = $p;
        }
    }
    return $out;
}

/**
 * Tekst z prostym formatowaniem: **pogrubienie**, [tekst](#kotwica lub https://adres), {cena} = cena treningu wdrożeniowego.
 * Wszystko poza tym jest zabezpieczone (esc_html).
 */
function pmbs_rich($s): string
{
    $s = str_replace('{cena}', pmbs_zl(pmbs_onboarding()), (string) $s);
    $h = esc_html($s);
    $h = preg_replace('/\*\*(.+?)\*\*/su', '<strong>$1</strong>', $h);
    $h = preg_replace_callback(
        '/\[([^\]]+)\]\(((?:#|https?:\/\/|tel:|mailto:|\/)[^\s)]*)\)/u',
        static function ($m) {
            return '<a class="link-gold" href="' . esc_url(html_entity_decode($m[2])) . '">' . $m[1] . '</a>';
        },
        $h
    );
    return $h;
}

function pmbs_zl($n): string
{
    $n = (float) $n;
    $txt = (floor($n) == $n)
        ? number_format($n, 0, '.', ',')
        : number_format($n, 2, '.', ',');
    $txt = str_replace([',', '.'], [PMBS_NBSP, ','], $txt);
    return $txt . PMBS_NBSP . 'zł';
}

function pmbs_opinion_label(int $n): string
{
    $w = ($n === 1) ? 'opinia' : ((($n % 10) >= 2 && ($n % 10) <= 4 && !(($n % 100) >= 12 && ($n % 100) <= 14)) ? 'opinie' : 'opinii');
    return $n . ' ' . $w . ' w Google';
}

/** Ikona SVG po nazwie (patrz inc/icons.php). */
function pmbs_icon(string $name): string
{
    static $icons = null;
    if ($icons === null) {
        $icons = require PMBS_DIR . '/inc/icons.php';
    }
    return $icons[$name] ?? '';
}

/* ------------------------------------------------------------ adresy */

function pmbs_theme_img(string $file): string
{
    return PMBS_URI . '/assets/img/' . ltrim($file, '/');
}

/** Link do kotwicy: na stronie głównej sama kotwica, na podstronach z adresem strony głównej. */
function pmbs_anchor(string $href): string
{
    if ($href !== '' && $href[0] === '#' && !pmbs_is_site_page()) {
        return home_url('/') . $href;
    }
    if ($href !== '' && $href[0] === '#' && pmbs_is_landing()) {
        // landing ma mniej sekcji niż strona główna: brakujące kotwice prowadzą na stronę główną
        foreach (pmbs_sections() as $sec) {
            if (!empty($sec['_show']) && '#' . $sec['anchor'] === $href) {
                return $href;
            }
        }
        return home_url('/') . $href;
    }
    return $href;
}

function pmbs_wa_url(): string
{
    return 'https://wa.me/' . preg_replace('/\D+/', '', (string) pmo('whatsapp_number'))
        . '?text=' . rawurlencode((string) pmo('whatsapp_message'));
}

function pmbs_logo_url(): string
{
    $id = pmo('logo');
    if ($id && is_numeric($id) && ($u = wp_get_attachment_image_url((int) $id, 'full'))) {
        return $u;
    }
    return pmbs_theme_img('pm-logo-biale.svg');
}

function pmbs_legal_url(string $slug): string
{
    $p = get_page_by_path($slug);
    return $p ? get_permalink($p) : home_url('/' . $slug . '/');
}

/* ------------------------------------------------------------ obrazy */

/**
 * Obraz z pola SCF (ID załącznika); gdy pole puste, używa zdjęcia dołączonego do motywu.
 *
 * @param mixed  $id       ID załącznika z SCF (lub pusto)
 * @param string $fallback plik w assets/img motywu
 */
function pmbs_img($id, string $fallback, string $alt, int $w, int $h, array $attr = [], string $size = 'full'): string
{
    $id = is_array($id) ? (int) ($id['ID'] ?? 0) : (int) $id;
    if ($id > 0 && wp_attachment_is_image($id)) {
        $a = array_merge(['alt' => $alt !== '' ? $alt : get_post_meta($id, '_wp_attachment_image_alt', true)], $attr);
        $html = wp_get_attachment_image($id, $size, false, $a);
        if ($html) {
            return $html;
        }
    }
    $attrs = '';
    foreach (array_merge(['loading' => 'lazy', 'decoding' => 'async'], $attr) as $k => $v) {
        $attrs .= ' ' . esc_attr($k) . '="' . esc_attr((string) $v) . '"';
    }
    return '<img src="' . esc_url(pmbs_theme_img($fallback)) . '" alt="' . esc_attr($alt) . '" width="' . $w . '" height="' . $h . '"' . $attrs . '>';
}

/* ------------------------------------------------------------ przyciski */

function pmbs_cta(string $label, string $href, ?string $form = '', string $kind = 'primary', string $extra = ''): string
{
    $d = ($form !== null && $form !== '') ? ' data-form="' . esc_attr($form) . '"' : ($form === '' ? ' data-form=""' : '');
    return '<a class="btn btn-' . esc_attr($kind) . '" href="' . esc_url($href) . '"' . $d . ($extra ? ' ' . $extra : '') . '>' . esc_html($label) . '</a>';
}

/** Lista „ptaszków” (feats). */
function pmbs_feats(array $items): string
{
    $o = '<ul class="feats">';
    foreach ($items as $x) {
        $o .= '<li>' . pmbs_icon('check') . '<span>' . esc_html($x) . '</span></li>';
    }
    return $o . '</ul>';
}

function pmbs_hours_br(): string
{
    $out = [];
    foreach ((array) pmo('hours') as $r) {
        $out[] = esc_html(($r['days'] ?? '') . ': ' . ($r['hours'] ?? ''));
    }
    return implode('<br>', $out);
}

function pmbs_form_labels(): array
{
    $out = [];
    foreach ((array) pmo('form_options') as $r) {
        if (!empty($r['label'])) {
            $out[] = (string) $r['label'];
        }
    }
    return $out;
}

/** Pierwsza forma treningu z listy (domyślna dla głównych przycisków). */
function pmbs_form_first(): string
{
    $l = pmbs_form_labels();
    return $l ? $l[0] : '';
}

/** Domyślna forma treningu w formularzu: z sekcji Kontakt (landing), inaczej pierwsza z listy. */
function pmbs_form_default(): string
{
    if (pmbs_is_landing()) {
        $v = trim((string) pms(pmbs_first('kontakt'), 'form_default', ''));
        if ($v === '') {
            $v = trim((string) pms(pmbs_first('lp_oferta'), 'form', ''));
        }
        if ($v === '') {
            $v = pmbs_form_by_crm('personal', '');
        }
        if ($v !== '') {
            return $v;
        }
    }
    return pmbs_form_first();
}

/** Nazwa formy treningu po wartości CRM (np. „personal”). */
function pmbs_form_by_crm(string $crm, string $fallback = ''): string
{
    foreach ((array) pmo('form_options') as $r) {
        if (($r['crm'] ?? '') === $crm && !empty($r['label'])) {
            return (string) $r['label'];
        }
    }
    return $fallback;
}

/* ------------------------------------------------------------ spolszczenie spacji */

/** Nie zostawia jednoliterowych spójników na końcu wiersza (spacja twarda po „w”, „z”, „i”, „o”, „u”, „a”). */
function pmbs_nb(string $html): string
{
    $parts = preg_split('~(<script.*?</script>|<style.*?</style>|<[^>]+>)~is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($parts)) {
        return $html;
    }
    foreach ($parts as $i => $p) {
        if ($p === '' || $p[0] === '<') {
            continue;
        }
        $p = preg_replace('~(?<!\S)([wziouaWZIOUA]) (?=\S)~u', '$1' . PMBS_NBSP, $p);
        $p = str_replace(['ul. ', ' —', '&nbsp;&nbsp;'], ['ul.' . PMBS_NBSP, PMBS_NBSP . '—', '&nbsp;'], $p);
        $parts[$i] = $p;
    }
    return implode('', $parts);
}

/* ------------------------------------------------------------ cennik */

/** Karta cennika. $amountHtml i $subHtml są już zabezpieczone. */
function pmbs_price_card(string $label, string $amountHtml, string $subHtml, array $feats, string $btn, string $form, bool $hl = false, string $ribbon = ''): string
{
    $r = $ribbon !== '' ? '<span class="ribbon"><span>' . esc_html($ribbon) . '</span></span>' : '';
    return '<article class="price' . ($hl ? ' hl' : '') . '">' . $r
        . '<p class="plabel">' . esc_html($label) . '</p>'
        . '<p class="pamount">' . $amountHtml . '</p>'
        . '<p class="psub">' . $subHtml . '</p>'
        . pmbs_feats($feats)
        . '<a class="btn ' . ($hl ? 'btn-dark' : 'btn-primary') . ' full" href="' . esc_url(pmbs_contact_href()) . '" data-form="' . esc_attr($form) . '">' . esc_html($btn) . '</a>'
        . '</article>';
}
