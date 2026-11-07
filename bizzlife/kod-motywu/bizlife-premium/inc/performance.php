<?php
/**
 * Wydajność: WebP dla miniatur, usunięcie skryptu emoji.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generowanie miniatur JPEG/PNG w formacie WebP (gdy serwer to obsługuje).
 *
 * @param array $formats Mapowanie formatów.
 * @return array
 */
function bzl_webp_output_format( $formats ) {
	if ( ! bzl_mod( 'webp_subsizes' ) ) {
		return $formats;
	}
	if ( ! wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		return $formats;
	}
	$formats['image/jpeg'] = 'image/webp';
	$formats['image/png']  = 'image/webp';
	return $formats;
}
add_filter( 'image_editor_output_format', 'bzl_webp_output_format' );

/**
 * Wyłączenie skryptów emoji (zbędny JS i CSS).
 */
function bzl_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'bzl_disable_emojis' );
