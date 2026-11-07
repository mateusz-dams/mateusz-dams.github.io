<?php
/**
 * Konfiguracja motywu.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Wsparcie funkcji WordPress.
 */
function bzl_setup() {
	load_theme_textdomain( 'bizlife', BZL_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 360,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_editor_style( array( 'assets/css/fonts.css', 'assets/css/editor.css' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Menu główne', 'bizlife' ),
			'footer'  => __( 'Stopka – menu', 'bizlife' ),
			'legal'   => __( 'Stopka – linki prawne', 'bizlife' ),
		)
	);

	add_image_size( 'bzl-card', 720, 480, true );
	add_image_size( 'bzl-portrait', 900, 1125, true );
	add_image_size( 'bzl-hero', 1200, 1400, true );
	add_image_size( 'bzl-wide', 1600, 900, true );
}
add_action( 'after_setup_theme', 'bzl_setup' );

/**
 * Szerokość treści.
 */
function bzl_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'bzl_content_width', 760 );
}
add_action( 'after_setup_theme', 'bzl_content_width', 0 );

/**
 * Nazwy rozmiarów w bibliotece mediów.
 *
 * @param array $sizes Rozmiary.
 * @return array
 */
function bzl_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'bzl-card'     => __( 'Karta (3:2)', 'bizlife' ),
			'bzl-portrait' => __( 'Portret (4:5)', 'bizlife' ),
			'bzl-wide'     => __( 'Szeroki (16:9)', 'bizlife' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'bzl_image_size_names' );

/**
 * Klasy body.
 *
 * @param array $classes Klasy.
 * @return array
 */
function bzl_body_classes( $classes ) {
	if ( bzl_mod( 'serif_headings' ) ) {
		$classes[] = 'has-serif-headings';
	}
	if ( ! bzl_mod( 'animations' ) ) {
		$classes[] = 'no-animations';
	}
	if ( is_front_page() || is_singular( array( 'bzl_service', 'post' ) ) || is_404() ) {
		$classes[] = 'has-overlay-header';
	}
	return $classes;
}
add_filter( 'body_class', 'bzl_body_classes' );

/**
 * Długość zajawki.
 *
 * @return int
 */
function bzl_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'bzl_excerpt_length' );

/**
 * Zakończenie zajawki.
 *
 * @return string
 */
function bzl_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'bzl_excerpt_more' );

/**
 * Pingback w nagłówku dla wpisów.
 */
function bzl_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'bzl_pingback_header' );

/**
 * Flush reguł przy aktywacji motywu.
 */
function bzl_activate() {
	bzl_register_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'bzl_activate' );

/**
 * Krótszy tytuł archiwum (bez „Archiwum:”).
 *
 * @param string $title Tytuł.
 * @return string
 */
function bzl_archive_title( $title ) {
	if ( is_category() || is_tag() ) {
		return single_term_title( '', false );
	}
	if ( is_post_type_archive( 'bzl_service' ) ) {
		return post_type_archive_title( '', false );
	}
	if ( is_author() ) {
		return get_the_author();
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'bzl_archive_title' );
