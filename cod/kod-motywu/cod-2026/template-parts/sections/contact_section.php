<?php
/**
 * Sekcja: kontakt (dane kontaktowe + formularz Contact Form 7 z pola sekcji albo formularz motywu).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_id = sanitize_title( (string) get_sub_field( 'contact_section_id' ) );
get_template_part(
	'template-parts/contact-block',
	null,
	array(
		'kicker'    => '',
		'title'     => wp_strip_all_tags( (string) get_sub_field( 'contact_section_title' ) ) ? wp_strip_all_tags( (string) get_sub_field( 'contact_section_title' ) ) : __( 'Kontakt', 'cod2026' ),
		'lead'      => (string) get_sub_field( 'contact_section_desc' ),
		'shortcode' => (string) get_sub_field( 'contact_section_form' ),
		'reverse'   => (bool) get_sub_field( 'contact_section_reverse' ),
		'id'        => $cod_id ? $cod_id : 'kontakt',
		'h'         => 0 === (int) $GLOBALS['cod_section_index'] ? 'h1' : 'h2',
	)
);
