<?php
/**
 * Strona główna – sekcje w kolejności ustawionej w Customizerze.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

$bzl_order    = array_filter( array_map( 'trim', explode( ',', (string) bzl_mod( 'home_order' ) ) ) );
$bzl_sections = array_keys( bzl_home_sections() );

foreach ( $bzl_order as $bzl_section ) {
	if ( ! in_array( $bzl_section, $bzl_sections, true ) ) {
		continue;
	}
	if ( 'content' !== $bzl_section && ! bzl_mod( $bzl_section . '_show' ) ) {
		continue;
	}
	get_template_part( 'template-parts/sections/' . $bzl_section );
}

get_footer();
