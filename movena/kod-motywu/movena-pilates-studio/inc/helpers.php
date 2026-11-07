<?php
/**
 * Funkcje pomocnicze: odczyt pól SCF z wartościami domyślnymi, obrazy, formatowanie.
 */
if (!defined('ABSPATH')) {
    exit;
}

const MVN_NBSP = "\u{00A0}";

/** Domyślne treści (z inc/defaults.php). */
function mvn_defaults(): array
{
    static $d = null;
    if ($d === null) {
        $d = require MVN_DIR . '/inc/defaults.php';
    }
    return $d;
}

/** Czy pola SCF/ACF są dostępne i motyw zapisał już swoje domyślne treści do bazy. */
function mvn_ready(): bool
{
    return function_exists('get_field') && (int) get_option('mvn_seeded', 0) >= MVN_SEED_VERSION;
}

function mvn_front_id(): int
{
    return (int) get_option('page_on_front');
}

/** Normalizacja wartości z SCF do typu zgodnego z wartością domyślną. */
function mvn_normalize($v, $def)
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

/** Ustawienie globalne (strona „Movena Pilates Studio” w panelu). */
function mvo(string $name)
{
    static $cache = [];
    if (array_key_exists($name, $cache)) {
        return $cache[$name];
    }
    $def = mvn_defaults()['site'][$name] ?? null;
    $val = $def;
    if (mvn_ready()) {
        $v = get_field($name, 'option');
        $val = ($v === null) ? $def : mvn_normalize($v, $def);
    }
    return $cache[$name] = $val;
}

/* ------------------------------------------------------------ sekcje strony głównej */

/**
 * Sekcje strony głównej (pole elastyczne „Sekcje”) z ujednoliconymi kotwicami.
 * Bez SCF lub przed pierwszym zapisem zwraca sekcje domyślne z inc/defaults.php.
 */
function mvn_is_landing(): bool
{
    return function_exists('is_page_template') && is_page_template('page-landing.php');
}

/** Landing programu Reformer 10 (/reformer-10/): własne teksty przycisków i inne domyślne sekcje. */
function mvn_is_reformer(): bool
{
    return mvn_is_landing() && is_page(MVN_REFORMER_SLUG);
}

/** Adres rozmowy z klientką (Messenger / Instagram DM) z ustawień; domyślnie wiadomość na Instagramie studia. */
function mvn_msg_url(): string
{
    $u = trim((string) mvo('message_url'));
    return $u !== '' ? $u : 'https://ig.me/m/movenapilates';
}

/** ID strony, z której pochodzą sekcje: landing (gdy otwarty) albo strona główna. */
/** Strona z pełnym układem motywu (strona główna albo landing). */
function mvn_is_site_page(): bool
{
    return is_front_page() || mvn_is_landing();
}

function mvn_content_id(): int
{
    return mvn_is_landing() ? (int) get_queried_object_id() : mvn_front_id();
}

function mvn_sections(?int $forId = null): array
{
    static $cache = [];
    $id = $forId ?? mvn_content_id();
    if (isset($cache[$id])) {
        return $cache[$id];
    }
    $defs = mvn_defaults();
    $rows = mvn_is_landing() && $forId === null ? ($defs[mvn_is_reformer() ? 'landing_reformer' : 'landing'] ?? []) : ($defs['sections'] ?? []);
    if (mvn_ready() && $id > 0) {
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
function mvn_first(string $layout): array
{
    foreach (mvn_sections() as $s) {
        if ($s['acf_fc_layout'] === $layout) {
            return $s;
        }
    }
    return [];
}

/** Wartość pola sekcji. */
function mvs(array $s, string $name, $def = '')
{
    $v = $s[$name] ?? $def;
    $v = ($v === null || $v === false) ? $def : $v;
    if (is_string($v) && $v !== '' && mvn_fitssey_mode()) {
        $m = mvn_mode_texts();
        return $m[$v] ?? $v;
    }
    return $v;
}

/** Tablica (np. repeater) z pola sekcji. */
function mvsa(array $s, string $name): array
{
    $v = $s[$name] ?? [];
    $v = is_array($v) ? $v : [];
    if ($v && mvn_fitssey_mode()) {
        $m = mvn_mode_texts();
        array_walk_recursive($v, static function (&$x) use ($m) {
            if (is_string($x) && isset($m[$x])) {
                $x = $m[$x];
            }
        });
    }
    return $v;
}

/** Atrybuty id i class sekcji (tło: jasne albo beżowy panel). */
function mvn_sec_attrs(array $s, string $class = 'section', string $idFallback = ''): string
{
    if (($s['tlo'] ?? '') === 'alt') {
        $class .= ' section--panel';
    }
    $id = (string) ($s['anchor'] ?? $idFallback);
    return ($id !== '' ? ' id="' . esc_attr($id) . '"' : '') . ' class="' . esc_attr($class) . '"';
}

/** Adres główny CRM (z adresu przyjmowania leadów): https://crm.movenapilates.pl */
function mvn_crm_base(): string
{
    $p = wp_parse_url((string) mvo('crm_url'));
    if (empty($p['host']) || empty($p['scheme'])) {
        return 'https://crm.movenapilates.pl';
    }
    return $p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
}

/** Adres API zapisów w CRM (kalendarz na stronie). */
function mvn_booking_api(): string
{
    return mvn_crm_base() . '/api/rezerwacja';
}

/** Czy pokazywać kalendarz zapisów (włączony w panelu i CRM podłączony). */
/** Tryb zapisów: „fitssey” (przejściowo — zapisy i zakupy w Fitssey) albo „crm” (własny kalendarz i sklep w CRM). */
function mvn_mode(): string
{
    return (string) mvo('booking_mode') === 'crm' ? 'crm' : 'fitssey';
}

/** Czy podstrona /grafik/ pokazuje własny grafik z CRM (a nie przycisk do Fitssey). */
function mvn_grafik_on(): bool
{
    $m = (string) mvo('grafik_mode');
    if ($m === 'crm') {
        return trim((string) mvo('crm_url')) !== '';
    }
    if ($m === 'fitssey') {
        return false;
    }
    return mvn_booking_on();
}

function mvn_fitssey_mode(): bool
{
    return mvn_mode() === 'fitssey';
}

/** Teksty zapisane dla CRM → odpowiedniki na czas, gdy zapisy działają w Fitssey (podmieniane przy odczycie pola sekcji). */
function mvn_mode_texts(): array
{
    static $m = null;
    return $m ??= [
        'Wybierz termin w kalendarzu' => 'Wybierz termin w grafiku',
        'SMS i e-mail z terminem oraz adresem studia. Termin możesz zmienić najpóźniej 12 godzin wcześniej.' => 'Dostaniesz potwierdzenie z terminem i adresem studia.',
        'Karnet aktywuje się od pierwszych zajęć i jest ważny 30 dni. Płatność online, zapis w kalendarzu.' => 'Karnet aktywuje się od pierwszych zajęć i jest ważny 30 dni. Płatność online, zapis w grafiku.',
        'Po zakupie zaloguj się do panelu klientki i wybierz termin w grafiku. Pierwsze zajęcia rezerwujesz od razu w kalendarzu na stronie.' => 'Po zakupie zaloguj się do panelu klientki w Fitssey i wybierz termin w grafiku.',
        'Pierwsze zajęcia zarezerwujesz w kalendarzu na stronie na 14 dni naprzód, a z karnetem zapisujesz się w panelu klientki na 3 tygodnie naprzód.' => 'Rezerwacji można dokonywać do 14 dni naprzód.',
        'Pierwsze zajęcia rezerwujesz najpóźniej 3 godziny przed startem. Z karnetem możesz zapisać się w panelu klientki, o ile są wolne miejsca.' => 'Zapis jest możliwy do 1 minuty przed rozpoczęciem zajęć, o ile są wolne miejsca.',
        'Tak — rezerwację można odwołać najpóźniej 12 godzin przed rozpoczęciem zajęć.' => 'Tak — rezerwację można odwołać najpóźniej 12 godzin przed rozpoczęciem zajęć.',
        'Wybierz termin w kalendarzu, opłać zajęcia online i przyjdź na reformer.' => 'Wybierz termin w grafiku, opłać zajęcia online i przyjdź na reformer.',
        'Wybierz termin w kalendarzu i opłać pierwsze zajęcia online.' => 'Wybierz termin w grafiku i opłać pierwsze zajęcia online.',
        'Wybierz wolny termin w kalendarzu na tej stronie.' => 'Przejdziesz do systemu zapisów i płatności studia.',
        'Płatność kartą lub BLIK-iem, bez zakładania konta.' => 'Płatność online kartą lub BLIK-iem.',
        'Pierwsze zajęcia rezerwujesz i opłacasz w kalendarzu na tej stronie. Karnet kupisz online w cenniku, a termin wybierzesz po zalogowaniu do panelu klientki.' => 'Pierwsze zajęcia rezerwujesz i opłacasz online w systemie zapisów studia. Karnet kupisz w cenniku, a termin wybierzesz po zalogowaniu.',
        'Termin wybierasz w kalendarzu' => 'Termin wybierasz w grafiku',
    ];
}

function mvn_booking_on(): bool
{
    return mvn_mode() === 'crm' && (bool) mvo('booking_on') && (bool) mvo('crm_enabled') && trim((string) mvo('crm_url')) !== '';
}

/** Adresy zapisów i zakupów: klucz po „fitssey:” (nazwa historyczna zapisanych ustawień) → adres. Puste ustawienia uzupełniamy z CRM i strony. */
function mvn_fitssey_url(string $key): string
{
    $map = ['schedule' => 'fitssey_schedule', 'pricing' => 'fitssey_pricing', 'login' => 'fitssey_login', 'vouchers' => 'fitssey_vouchers'];
    $u = trim((string) mvo($map[$key] ?? 'fitssey_schedule'));
    if (mvn_fitssey_mode()) {
        $def = ['schedule' => 'https://app.fitssey.com/movenapilates/frontoffice', 'pricing' => 'https://app.fitssey.com/movenapilates/frontoffice/pricing/classes', 'login' => 'https://app.fitssey.com/movenapilates/frontoffice/login'];
        if ($u === '') {
            $u = $key === 'vouchers' ? trim((string) mvo('fitssey_pricing')) : '';
            $u = $u !== '' ? $u : ($def[$key] ?? $def['pricing']);
        }
        return $u;
    }
    if ($u !== '' && stripos($u, 'fitssey.com') === false) {
        return $u;
    }
    $base = mvn_crm_base();
    switch ($key) {
        case 'login':
            return $base . '/logowanie';
        case 'vouchers':
            return $base . '/voucher';
        case 'pricing':
            return mvn_anchor('#' . (mvn_first('cennik')['anchor'] ?? 'pricing'));
        default:
            return mvn_booking_on() ? mvn_anchor('#' . (mvn_first('rezerwacja')['anchor'] ?? 'rezerwacja')) : mvn_anchor('#' . (mvn_first('cennik')['anchor'] ?? 'pricing'));
    }
}

/** Czy cel linku prowadzi do zapisów/zakupów (wtedy link dostaje śledzenie click_fitssey i parametry reklamowe). */
function mvn_is_fitssey($cel): bool
{
    return strpos(trim((string) $cel), 'fitssey:') === 0;
}

/** Adres z pola „cel”: zapisy/zakupy („fitssey:schedule” — nazwa historyczna), telefon („tel”), kotwica (z # lub bez) albo pełny adres. */
function mvn_href($cel, string $default = ''): string
{
    $c = trim((string) $cel);
    if ($c === '') {
        $c = $default;
    }
    if ($c === '') {
        return '#';
    }
    if (mvn_is_fitssey($c)) {
        return mvn_fitssey_url(substr($c, 8));
    }
    if ($c === 'tel') {
        return 'tel:' . (string) mvo('phone_tel');
    }
    if ($c === 'msg') {
        return mvn_msg_url();
    }
    if (preg_match('~^(https?:|tel:|mailto:|/)~i', $c)) {
        return $c;
    }
    return '#' . ltrim($c, '#');
}

/**
 * Atrybuty linku do śledzenia: zakup w CRM → click_fitssey (rodzaj oferty, nazwa, wartość), reszta → bez atrybutów.
 * $o: offer (group|duo|private|voucher|first), name, value, sessions.
 */
function mvn_track_attrs($cel, array $o = []): string
{
    if (!mvn_is_fitssey($cel)) {
        $u = trim((string) $cel) === 'msg' ? mvn_msg_url() : trim((string) $cel);
        if (preg_match('~^https?://(ig\.me|m\.me|www\.messenger\.com)/~i', $u)) {
            return ' target="_blank" rel="noopener" data-track="click_message"';
        }
        return '';
    }
    $href = mvn_href($cel);
    if ($href === '' || $href[0] === '#' || (!mvn_fitssey_mode() && strpos($href, '#') !== false && strpos($href, mvn_crm_base()) !== 0)) {
        return ' data-track="select_pass"';   // zapisy i cennik na tej samej stronie — bez nowej karty
    }
    $a = ' target="_blank" rel="noopener" data-track="click_fitssey"';
    foreach (['offer' => 'data-offer-type', 'name' => 'data-pass-name', 'value' => 'data-value', 'sessions' => 'data-sessions'] as $k => $attr) {
        if (isset($o[$k]) && $o[$k] !== '' && $o[$k] !== 0) {
            $a .= ' ' . $attr . '="' . esc_attr((string) $o[$k]) . '"';
        }
    }
    return $a;
}

/** Kompletny przycisk-link (klasa, adres, śledzenie). Tekst przechodzi przez znaczniki {cena}. */
function mvn_btn(string $label, $cel, string $class = 'btn btn-primary', array $o = [], string $extra = ''): string
{
    $label = trim($label);
    if ($label === '') {
        return '';
    }
    $href = mvn_anchor(mvn_href($cel, '#kontakt'));
    $extraAttr = $extra !== '' ? ' ' . $extra : '';
    return '<a class="' . esc_attr($class) . '" href="' . esc_url($href) . '"' . mvn_track_attrs($cel, $o) . $extraAttr . '>' . esc_html(mvn_tokens($label)) . '</a>';
}

/** Kotwica formularza kontaktowego (pierwsza sekcja „Kontakt”). */
function mvn_contact_href(): string
{
    $s = mvn_first('kontakt');
    return '#' . ($s['anchor'] ?? 'kontakt');
}

/** Kotwica pierwszej sekcji (górna część strony). */
function mvn_top_href(): string
{
    $all = mvn_sections();
    return $all ? '#' . $all[0]['anchor'] : '#';
}

/** Nazwa układu → etykieta menu domyślna. */
function mvn_menu_items(): array
{
    $names = function_exists('mvn_layout_names') ? mvn_layout_names() : [];
    $out = [];
    // menu główne jest takie samo na każdej stronie: pozycje pochodzą ze strony głównej
    $front = mvn_front_id();
    $rows = $front > 0 ? mvn_sections($front) : mvn_sections();
    $base = is_front_page() ? '' : home_url('/');
    foreach ($rows as $s) {
        if (!$s['_show'] || empty($s['in_menu'])) {
            continue;
        }
        $label = trim((string) ($s['menu_label'] ?? ''));
        if ($label === '') {
            $label = $names[$s['acf_fc_layout']] ?? $s['anchor'];
        }
        $out[] = ['label' => $label, 'href' => $base . '#' . $s['anchor']];
    }
    // stała pozycja: podstrona z grafikiem i zapisami (przed „Kontakt”, jeśli jest w menu)
    $graf = ['label' => 'Grafik', 'href' => home_url('/grafik/')];
    $pos = count($out);
    foreach ($out as $k => $it) {
        if (substr($it['href'], -8) === '#kontakt') {
            $pos = $k;
            break;
        }
    }
    array_splice($out, $pos, 0, [$graf]);
    return $out;
}

/** Cena pierwszego treningu (promocyjna) i cena zwykła „przed” — z ustawień → Cennik. */
function mvn_first_price()
{
    return (float) mvo('first_price');
}

function mvn_first_was()
{
    return (float) mvo('first_was');
}

/** Znaczniki w tekstach: {cena}, {cena_przed}, {firma}. */
function mvn_tokens(string $s): string
{
    if (strpos($s, '{') === false) {
        return $s;
    }
    $firm = trim((string) mvo('company_name')) !== '' ? (string) mvo('company_name') : (string) mvo('brand');
    return str_replace(
        ['{cena}', '{cena_przed}', '{firma}'],
        [mvn_first_price() > 0 ? mvn_zl(mvn_first_price()) : '', mvn_first_was() > 0 ? mvn_zl(mvn_first_was()) : '', $firm],
        $s
    );
}

/* ------------------------------------------------------------ tekst */

/** Dzieli tekst na niepuste linie. */
function mvn_lines($s): array
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
function mvn_paras($s): array
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
function mvn_rich($s): string
{
    $s = mvn_tokens((string) $s);
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

function mvn_zl($n): string
{
    $n = (float) $n;
    $txt = (floor($n) == $n)
        ? number_format($n, 0, '.', ',')
        : number_format($n, 2, '.', ',');
    $txt = str_replace([',', '.'], [MVN_NBSP, ','], $txt);
    return $txt . MVN_NBSP . 'zł';
}

function mvn_opinion_label(int $n): string
{
    $w = ($n === 1) ? 'opinia' : ((($n % 10) >= 2 && ($n % 10) <= 4 && !(($n % 100) >= 12 && ($n % 100) <= 14)) ? 'opinie' : 'opinii');
    return $n . ' ' . $w . ' w Google';
}

/** Ikona SVG po nazwie (patrz inc/icons.php). */
function mvn_icon(string $name): string
{
    static $icons = null;
    if ($icons === null) {
        $icons = require MVN_DIR . '/inc/icons.php';
    }
    return $icons[$name] ?? '';
}

/* ------------------------------------------------------------ adresy */

function mvn_theme_img(string $file): string
{
    return MVN_URI . '/assets/img/' . ltrim($file, '/');
}

/** Link do kotwicy: na stronie głównej sama kotwica, na podstronach z adresem strony głównej. */
function mvn_anchor(string $href): string
{
    if ($href !== '' && $href[0] === '#' && !mvn_is_site_page()) {
        return home_url('/') . $href;
    }
    if ($href !== '' && $href[0] === '#' && mvn_is_landing()) {
        // landing ma mniej sekcji niż strona główna: brakujące kotwice prowadzą na stronę główną
        foreach (mvn_sections() as $sec) {
            if (!empty($sec['_show']) && '#' . $sec['anchor'] === $href) {
                return $href;
            }
        }
        return home_url('/') . $href;
    }
    return $href;
}

function mvn_wa_url(): string
{
    $n = preg_replace('/\D+/', '', (string) mvo('whatsapp_number'));
    return $n === '' ? '' : 'https://wa.me/' . $n . '?text=' . rawurlencode((string) mvo('whatsapp_message'));
}

/** Logo ciemne (do jasnego nagłówka) lub jasne (do ciemnej stopki). */
function mvn_logo_url(bool $light = false): string
{
    $id = mvo($light ? 'logo_light' : 'logo');
    if ($id && is_numeric($id) && ($u = wp_get_attachment_image_url((int) $id, 'full'))) {
        return $u;
    }
    return mvn_theme_img($light ? 'logo-full-light.png' : 'logo-full-dark.svg');
}

function mvn_legal_url(string $slug): string
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
function mvn_img($id, string $fallback, string $alt, int $w, int $h, array $attr = [], string $size = 'full'): string
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
    return '<img src="' . esc_url(mvn_theme_img($fallback)) . '" alt="' . esc_attr($alt) . '" width="' . $w . '" height="' . $h . '"' . $attrs . '>';
}

/* ------------------------------------------------------------ przyciski */

/** Lista „ptaszków” (feats). */
function mvn_feats(array $items): string
{
    $o = '<ul class="feats">';
    foreach ($items as $x) {
        $o .= '<li>' . mvn_icon('check') . '<span>' . esc_html($x) . '</span></li>';
    }
    return $o . '</ul>';
}

function mvn_hours_br(): string
{
    $out = [];
    foreach ((array) mvo('hours') as $r) {
        $out[] = esc_html(($r['days'] ?? '') . ': ' . ($r['hours'] ?? ''));
    }
    return implode('<br>', $out);
}

function mvn_form_labels(): array
{
    $out = [];
    foreach ((array) mvo('form_options') as $r) {
        if (!empty($r['label'])) {
            $out[] = (string) $r['label'];
        }
    }
    return $out;
}

/** Pierwsza forma treningu z listy (domyślna dla głównych przycisków). */
function mvn_form_first(): string
{
    $l = mvn_form_labels();
    return $l ? $l[0] : '';
}

/** Domyślna forma treningu w formularzu: z sekcji Kontakt (pole „Formularz: domyślna forma treningu”), inaczej pusto. */
function mvn_form_default(): string
{
    $v = trim((string) mvs(mvn_first('kontakt'), 'form_default', ''));
    return in_array($v, mvn_form_labels(), true) ? $v : '';
}

/** Nazwa formy treningu po wartości CRM (np. „personal”). */
function mvn_form_by_crm(string $crm, string $fallback = ''): string
{
    foreach ((array) mvo('form_options') as $r) {
        if (($r['crm'] ?? '') === $crm && !empty($r['label'])) {
            return (string) $r['label'];
        }
    }
    return $fallback;
}

/* ------------------------------------------------------------ spolszczenie spacji */

/** Nie zostawia jednoliterowych spójników na końcu wiersza (spacja twarda po „w”, „z”, „i”, „o”, „u”, „a”). */
function mvn_nb(string $html): string
{
    $parts = preg_split('~(<script.*?</script>|<style.*?</style>|<[^>]+>)~is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($parts)) {
        return $html;
    }
    foreach ($parts as $i => $p) {
        if ($p === '' || $p[0] === '<') {
            continue;
        }
        $p = preg_replace('~(?<!\S)([wziouaWZIOUA]) (?=\S)~u', '$1' . MVN_NBSP, $p);
        $p = str_replace(['ul. ', ' —', '&nbsp;&nbsp;'], ['ul.' . MVN_NBSP, MVN_NBSP . '—', '&nbsp;'], $p);
        $parts[$i] = $p;
    }
    return implode('', $parts);
}


/* ------------------------------------------------------------ obrazy i nagłówki sekcji (Movena) */

/**
 * Obraz z pola SCF (ID załącznika) albo, gdy pole jest jeszcze wartością domyślną (nazwa pliku), obraz dołączony do motywu.
 * Zwraca pusty tekst, gdy nie ma obrazu.
 */
function mvn_pic($val, string $alt, int $w, int $h, array $attr = [], string $size = 'large'): string
{
    $id = is_array($val) ? (int) ($val['ID'] ?? 0) : (is_numeric($val) ? (int) $val : 0);
    if ($id > 0 && wp_attachment_is_image($id)) {
        $a = array_merge(['alt' => $alt !== '' ? $alt : (string) get_post_meta($id, '_wp_attachment_image_alt', true)], $attr);
        $html = wp_get_attachment_image($id, $size, false, $a);
        if ($html) {
            return $html;
        }
    }
    if (is_string($val) && $val !== '' && !is_numeric($val) && is_file(MVN_DIR . '/assets/img/' . $val)) {
        $attrs = '';
        foreach (array_merge(['loading' => 'lazy', 'decoding' => 'async'], $attr) as $k => $v) {
            $attrs .= ' ' . esc_attr($k) . '="' . esc_attr((string) $v) . '"';
        }
        return '<img src="' . esc_url(mvn_theme_img($val)) . '" alt="' . esc_attr($alt) . '" width="' . $w . '" height="' . $h . '"' . $attrs . '>';
    }
    return '';
}

/** Adres pełnego obrazu (do linku w galerii): załącznik albo plik z motywu. */
function mvn_pic_url($val): string
{
    $id = is_array($val) ? (int) ($val['ID'] ?? 0) : (is_numeric($val) ? (int) $val : 0);
    if ($id > 0 && ($u = wp_get_attachment_image_url($id, 'full'))) {
        return $u;
    }
    return is_string($val) && $val !== '' && !is_numeric($val) && is_file(MVN_DIR . '/assets/img/' . $val) ? mvn_theme_img($val) : '';
}

/** Nagłówek sekcji: mały napis, tytuł (H2) i wstęp. Pola: eyebrow, title, lead (albo intro). */
function mvn_head(array $s, bool $center = false, string $leadKey = 'lead'): string
{
    $eyebrow = trim((string) mvs($s, 'eyebrow'));
    $title = trim((string) mvs($s, 'title'));
    $lead = trim((string) mvs($s, $leadKey, mvs($s, 'intro')));
    if ($eyebrow === '' && $title === '' && $lead === '') {
        return '';
    }
    $h = '<div class="section-head' . ($center ? ' section-head--center' : '') . '">';
    if ($eyebrow !== '') {
        $h .= '<p class="eyebrow">' . mvn_rich($eyebrow) . '</p>';
    }
    if ($title !== '') {
        $h .= '<h2>' . mvn_rich($title) . '</h2>';
    }
    if ($lead !== '') {
        $h .= '<p class="lead">' . mvn_rich($lead) . '</p>';
    }
    return $h . '</div>';
}

/** Lista wyróżników z ikoną (hero, pasek zaufania). */
function mvn_icon_list(array $rows, string $class): string
{
    $o = '';
    foreach ($rows as $row) {
        $t = trim((string) ($row['text'] ?? ''));
        if ($t === '') {
            continue;
        }
        $o .= '<li>' . mvn_icon((string) ($row['icon'] ?? 'check')) . ' ' . esc_html(mvn_tokens($t)) . '</li>';
    }
    return $o === '' ? '' : '<ul class="' . esc_attr($class) . '">' . $o . '</ul>';
}

/** Pakiety cennika (jedna zakładka) → [ [pakiet…], … ] z ustawień. */
function mvn_price_groups(): array
{
    return [
        'group' => array_values(array_filter((array) mvo('cennik_grupa'), static fn($r) => is_array($r) && !empty($r['name']))),
    ];
}

/** Zajęcia z ustawień (tylko z nazwą). */
function mvn_classes(): array
{
    return array_values(array_filter((array) mvo('zajecia'), static fn($r) => is_array($r) && !empty($r['name'])));
}

/** Plakietka z ceną: „{cena} zamiast {cena_przed}” → cena + przekreślona cena „zamiast …” w osobnym znaczniku. */
function mvn_price_tag(string $text): string
{
    $parts = preg_split('/\s*(zamiast)\s*/u', $text, 2, PREG_SPLIT_DELIM_CAPTURE);
    if (is_array($parts) && count($parts) === 3) {
        return esc_html(mvn_tokens($parts[0])) . ' <span class="was-price">' . esc_html($parts[1] . ' ' . mvn_tokens($parts[2])) . '</span>';
    }
    return esc_html(mvn_tokens($text));
}


/** Formy treningu, dla których przyciski „Umów…” otwierają kalendarz (pierwsze zajęcia). */
function mvn_booking_forms(): array
{
    $out = [''];
    foreach ((array) mvo('form_options') as $o) {
        if (is_array($o) && (string) ($o['crm'] ?? '') === 'first' && !empty($o['label'])) {
            $out[] = (string) $o['label'];
        }
    }
    return array_values(array_unique($out));
}

/** Zgoda w kalendarzu: regulamin (zasady zmiany terminu) + kontakt. */
function mvn_booking_consent(): string
{
    return 'Akceptuję <a href="' . esc_url(mvn_legal_url('regulamin')) . '" target="_blank" rel="noopener">regulamin</a> (termin można zmienić najpóźniej 12 godzin przed zajęciami) i zgadzam się na kontakt w sprawie zajęć. '
        . 'Dane przetwarzamy zgodnie z <a href="' . esc_url(mvn_legal_url('polityka-prywatnosci')) . '" target="_blank" rel="noopener">polityką prywatności</a>.';
}


/* ------------------------------------------------------------ pakiety: jedna karta w cenniku i voucherach */

/** Grafika pakietu (assets/img/pakiety/{slug}.jpg), zawsze w jednym stylu. */
function mvn_pack_img(string $slug, string $alt): string
{
    if ($slug === '' || !is_file(MVN_DIR . '/assets/img/pakiety/' . $slug . '.jpg')) {
        return '<!-- grafika pakietu: brak pliku „' . esc_html($slug) . '.jpg” w ' . esc_html(basename(MVN_DIR)) . '/assets/img/pakiety -->';
    }
    return '<img class="pack-img" src="' . esc_url(get_template_directory_uri() . '/assets/img/pakiety/' . $slug . '.jpg?v=' . MVN_VERSION) . '" alt="' . esc_attr($alt) . '" width="1000" height="562" loading="lazy" decoding="async">';
}

/** Nazwa grafiki dla pakietu: z pola „img” albo po nazwie i liczbie wejść (te same pliki co w CRM). */
function mvn_pack_slug(array $v, bool $voucher = false): string
{
    $img = trim((string) ($v['img'] ?? ''));
    if ($img !== '' && is_file(MVN_DIR . '/assets/img/pakiety/' . $img . '.jpg')) {
        return $img;   // pole „img” wskazujące na nieistniejący plik (np. ze starej wersji motywu) jest ignorowane
    }
    $n = (int) ($v['sessions'] ?? 0);
    $name = mb_strtolower((string) ($v['name'] ?? ''));
    if (strpos($name, 'poznaj') !== false || strpos($name, 'pierwsz') !== false) {
        return 'pierwszy';
    }
    if ($n <= 0) {
        return '';
    }
    return ($voucher ? 'voucher-grupa-' : 'grupa-') . $n;
}

/** Adres zakupu pakietu w CRM po liczbie wejść (sklep znajdzie aktywny pakiet o tej liczbie). */
function mvn_shop_url(int $sessions): string
{
    return mvn_crm_base() . '/zakup/' . max(1, $sessions);
}

/** Karta pakietu. $v: name, price, sessions, per, per_note, validity, badge, restriction, url, recommended. */
function mvn_pack_card(array $v, string $btn, string $best = '', string $kind = 'group'): string
{
    $rec = !empty($v['recommended']);
    $badge = trim((string) ($v['badge'] ?? ''));
    $per = (float) ($v['per'] ?? 0);
    $perNote = trim((string) ($v['per_note'] ?? ''));
    $n = (int) ($v['sessions'] ?? 0);
    $url = trim((string) ($v['url'] ?? ''));
    $href = mvn_fitssey_mode() ? mvn_fitssey_url('pricing') : ($url !== '' ? mvn_anchor(mvn_href($url, '#')) : mvn_shop_url($n));
    $external = $href !== '' && $href[0] !== '#' && (mvn_fitssey_mode() || strpos($href, mvn_crm_base()) === 0);
    $tag = $rec && $best !== '' ? $best : $badge;
    $name = (string) $v['name'];
    $h = '<div class="price-card pack-card' . ($rec ? ' is-recommended' : '') . ($badge !== '' && !$rec ? ' is-highlighted' : '') . '">';
    $h .= $tag !== '' ? '<span class="price-card-tag">' . esc_html($tag) . '</span>' : '';
    $h .= mvn_pack_img(mvn_pack_slug($v), $name);
    $h .= '<h3>' . esc_html($name) . '</h3>';
    $h .= '<div class="price-amount">' . esc_html(mvn_zl($v['price'] ?? 0)) . '</div>';
    if ($per > 0 && $n > 1) {
        $h .= '<div class="price-per-session">' . esc_html(mvn_zl($per)) . ' / trening</div>';
    } else {
        $h .= '<div class="price-per-session">' . esc_html($perNote !== '' ? $perNote : ($n === 1 ? 'za jedno wejście' : '')) . '&nbsp;</div>';
    }
    $h .= '<div class="price-meta">';
    $h .= '<div><strong>' . $n . '</strong> ' . ($n === 1 ? 'wejście' : (($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 12 || $n % 100 > 14)) ? 'wejścia' : 'wejść')) . '</div>';
    $h .= '<div>' . (!empty($v['validity']) && $n !== 1 ? 'Ważność: ' . esc_html((string) $v['validity']) : (mvn_fitssey_mode() ? 'Termin wybierasz w grafiku' : 'Termin wybierasz w kalendarzu')) . '</div>';
    $h .= '</div>';
    $h .= '<a class="btn btn-primary btn-block" href="' . esc_url($href) . '"' . ($external ? ' target="_blank" rel="noopener" data-track="click_fitssey" data-offer-type="' . esc_attr($kind) . '" data-pass-name="' . esc_attr($name) . '" data-value="' . esc_attr((string) ($v['price'] ?? '')) . '" data-sessions="' . $n . '"' : ' data-track="select_pass"') . '>' . esc_html($btn) . '</a>';
    $h .= '<div class="price-restriction">' . (!empty($v['restriction']) ? esc_html((string) $v['restriction']) : '&nbsp;') . '</div>';
    return $h . '</div>';
}
