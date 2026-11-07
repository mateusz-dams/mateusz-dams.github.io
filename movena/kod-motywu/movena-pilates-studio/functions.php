<?php
/**
 * Movena Pilates Studio — motyw WordPress.
 * Ustawienia globalne: menu „Movena”. Treść: Strony → Strona główna → pole „Sekcje” (każda sekcja to blok z kotwicą; menu powstaje z kotwic).
 * Pola udostępnia wtyczka Secure Custom Fields (SCF).
 */
if (!defined('ABSPATH')) {
    exit;
}

define('MVN_VERSION', '1.3.1');
define('MVN_SEED_VERSION', 1);
define('MVN_REFORMER_SLUG', 'reformer-10');
define('MVN_DIR', get_template_directory());
define('MVN_URI', get_template_directory_uri());

require_once MVN_DIR . '/inc/helpers.php';
require_once MVN_DIR . '/inc/fields.php';
require_once MVN_DIR . '/inc/seed.php';
require_once MVN_DIR . '/inc/migrate.php';
require_once MVN_DIR . '/inc/form.php';
require_once MVN_DIR . '/inc/tracking.php';
require_once MVN_DIR . '/inc/seo.php';
require_once MVN_DIR . '/inc/admin.php';
require_once MVN_DIR . '/inc/redirects.php';

add_action('after_setup_theme', static function () {
    load_theme_textdomain('movena-pilates-studio', MVN_DIR . '/languages');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_post_type_support('page', 'excerpt'); // opis strony do meta description
    remove_action('wp_head', 'wp_generator');
});

/* ------------------------------------------------------------ style i skrypty */

add_action('wp_enqueue_scripts', static function () {
    wp_enqueue_style('mvn-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,650&family=Work+Sans:wght@400;500;600&display=swap', [], null);
    $css = MVN_DIR . '/assets/css/main.css';
    wp_enqueue_style('mvn-main', MVN_URI . '/assets/css/main.css', ['mvn-fonts'], file_exists($css) ? (string) filemtime($css) : MVN_VERSION);

    // menu na telefonie działa na każdej stronie, więc main.js ładuje się wszędzie
    $js = MVN_DIR . '/assets/js/main.js';
    wp_enqueue_script('mvn-main', MVN_URI . '/assets/js/main.js', [], file_exists($js) ? (string) filemtime($js) : MVN_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
        wp_localize_script('mvn-main', 'MVN', [
            'ajax'   => admin_url('admin-ajax.php'),
            'action' => 'mvn_contact',
            'email'  => (string) mvo('email'),
            'phone'  => (string) mvo('phone_display'),
            'bookForms' => mvn_booking_on() ? mvn_booking_forms() : null,
        ]);
    if (is_page_template('page-grafik.php')) {
        $gjs = MVN_DIR . '/assets/js/grafik.js';
        wp_enqueue_script('mvn-grafik', MVN_URI . '/assets/js/grafik.js', [], file_exists($gjs) ? (string) filemtime($gjs) : MVN_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
    }
    if (mvn_is_site_page()) {
        // Strona główna ma własny układ; style blokowe WordPressa są zbędne
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles');
        wp_dequeue_style('classic-theme-styles');
    }
}, 20);

add_filter('wp_resource_hints', static function ($urls, $relation) {
    if ($relation === 'preconnect') {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous'];
    }
    return $urls;
}, 10, 2);

// Mniej śmieci w <head>
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

/* ------------------------------------------------------------ spacje twarde w całym wyniku */

add_action('template_redirect', static function () {
    if (is_admin() || wp_doing_ajax() || is_feed() || is_robots() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    ob_start('mvn_nb');
}, 0);
