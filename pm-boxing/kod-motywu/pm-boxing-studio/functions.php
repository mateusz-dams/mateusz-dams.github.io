<?php
/**
 * PM Boxing Studio — motyw WordPress.
 * Ustawienia globalne: menu „PM Boxing”. Treść: Strony → Strona główna → pole „Sekcje” (każda sekcja to blok z kotwicą; menu powstaje z kotwic).
 * Pola udostępnia wtyczka Secure Custom Fields (SCF).
 */
if (!defined('ABSPATH')) {
    exit;
}

define('PMBS_VERSION', '2.1.0');
define('PMBS_SEED_VERSION', 4);
define('PMBS_DIR', get_template_directory());
define('PMBS_URI', get_template_directory_uri());

require_once PMBS_DIR . '/inc/helpers.php';
require_once PMBS_DIR . '/inc/fields.php';
require_once PMBS_DIR . '/inc/seed.php';
require_once PMBS_DIR . '/inc/form.php';
require_once PMBS_DIR . '/inc/tracking.php';
require_once PMBS_DIR . '/inc/seo.php';
require_once PMBS_DIR . '/inc/admin.php';

add_action('after_setup_theme', static function () {
    load_theme_textdomain('pm-boxing-studio', PMBS_DIR . '/languages');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_post_type_support('page', 'excerpt'); // opis strony do meta description
    remove_action('wp_head', 'wp_generator');
});

/* ------------------------------------------------------------ style i skrypty */

add_action('wp_enqueue_scripts', static function () {
    wp_enqueue_style('pmbs-fonts', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap', [], null);
    $css = PMBS_DIR . '/assets/css/main.css';
    wp_enqueue_style('pmbs-main', PMBS_URI . '/assets/css/main.css', ['pmbs-fonts'], file_exists($css) ? (string) filemtime($css) : PMBS_VERSION);

    if (pmbs_is_site_page()) {
        $js = PMBS_DIR . '/assets/js/main.js';
        wp_enqueue_script('pmbs-main', PMBS_URI . '/assets/js/main.js', [], file_exists($js) ? (string) filemtime($js) : PMBS_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
        wp_localize_script('pmbs-main', 'PMBS', [
            'ajax'   => admin_url('admin-ajax.php'),
            'action' => 'pmbs_contact',
            'email'  => (string) pmo('email'),
            'phone'  => (string) pmo('phone_display'),
        ]);
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
    ob_start('pmbs_nb');
}, 0);
