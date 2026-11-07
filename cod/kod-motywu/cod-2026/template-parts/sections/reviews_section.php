<?php
/**
 * Sekcja: opinie.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_rows    = get_sub_field( 'reviews' );
$cod_reviews = array();
if ( is_array( $cod_rows ) ) {
	foreach ( $cod_rows as $cod_r ) {
		$cod_reviews[] = array(
			'quote' => isset( $cod_r['reviews_desc'] ) ? $cod_r['reviews_desc'] : '',
			'name'  => isset( $cod_r['reviews_title'] ) ? wp_strip_all_tags( $cod_r['reviews_title'] ) : '',
			'role'  => isset( $cod_r['reviews_subtitle'] ) ? wp_strip_all_tags( $cod_r['reviews_subtitle'] ) : '',
			'photo' => isset( $cod_r['reviews_img'] ) ? $cod_r['reviews_img'] : '',
		);
	}
}
$cod_bg = (string) get_sub_field( 'reviews_section_background_color' );
get_template_part(
	'template-parts/cards/reviews',
	null,
	array(
		'reviews'  => $cod_reviews,
		'title'    => wp_strip_all_tags( (string) get_sub_field( 'reviews_section_title' ) ),
		'subtitle' => wp_strip_all_tags( (string) get_sub_field( 'reviews_section_subtitle' ) ),
		'id'       => sanitize_title( (string) get_sub_field( 'reviews_section_id' ) ) ? sanitize_title( (string) get_sub_field( 'reviews_section_id' ) ) : cod_uid( 'opinie' ),
		'surface'  => $cod_bg ? cod_surface( $cod_bg ) : 'surface-sand',
	)
);
