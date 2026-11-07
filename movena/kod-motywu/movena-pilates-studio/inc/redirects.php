<?php
/**
 * Przekierowania 301 ze starych adresów (statyczna strona .html) na nowe + robots.txt z mapą strony.
 * Dodatkowe przekierowania można dopisać w panelu: Movena → Przekierowania 301.
 */
if (!defined('ABSPATH')) {
    exit;
}

/** Domyślna lista: stara ścieżka => nowa ścieżka. Slugi stron prawnych biorą się z mvn_legal_url(). */
function mvn_redirect_map(): array
{
    $map = [
        '/index.html'                => '/',
        '/index.htm'                 => '/',
        '/pierwszy-trening.html'     => '/pierwszy-trening/',
        '/regulamin.html'            => '/regulamin/',
        '/polityka-prywatnosci.html' => '/polityka-prywatnosci/',
        '/polityka-cookies.html'     => '/polityka-cookies/',
        '/cookies.html'              => '/polityka-cookies/',
        '/sitemap.xml'               => '/wp-sitemap.xml',
    ];
    $rows = function_exists('get_field') ? get_field('redirects', 'option') : [];
    if (is_array($rows)) {
        foreach ($rows as $r) {
            $from = isset($r['from']) ? trim((string) $r['from']) : '';
            $to   = isset($r['to']) ? trim((string) $r['to']) : '';
            if ($from !== '' && $to !== '') {
                $map['/' . ltrim($from, '/')] = $to;
            }
        }
    }
    return $map;
}

add_action('template_redirect', static function () {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    $uri  = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '';
    $path = (string) wp_parse_url($uri, PHP_URL_PATH);
    $qs   = (string) wp_parse_url($uri, PHP_URL_QUERY);
    $base = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    if ($base !== '/' && strpos($path, $base) === 0) {
        $path = '/' . ltrim(substr($path, strlen($base)), '/');
    }
    $key = '/' . trim(strtolower($path), '/');
    if ($key === '/') {
        return;
    }
    foreach (mvn_redirect_map() as $from => $to) {
        if (strtolower(rtrim($from, '/')) !== $key && strtolower($from) !== strtolower($path)) {
            continue;
        }
        $target = preg_match('#^https?://#i', $to) ? $to : home_url($to);
        if ($qs !== '') { // zachowaj UTM, gclid, fbclid
            $target .= (strpos($target, '?') === false ? '?' : '&') . $qs;
        }
        wp_redirect(esc_url_raw($target), 301, 'Movena');
        exit;
    }
}, 1);

/** Linia z mapą strony w robots.txt (WordPress ma własną: /wp-sitemap.xml). */
add_filter('robots_txt', static function ($output, $public) {
    if ($public && stripos($output, 'sitemap:') === false) {
        $output .= "\nSitemap: " . home_url('/wp-sitemap.xml') . "\n";
    }
    return $output;
}, 10, 2);
