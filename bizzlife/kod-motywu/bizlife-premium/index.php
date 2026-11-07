<?php
/**
 * Szablon zapasowy.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/components/post-archive',
	null,
	array(
		'title' => is_archive() ? wp_strip_all_tags( get_the_archive_title() ) : __( 'Aktualności', 'bizlife' ),
		'lead'  => '',
	)
);

get_footer();
