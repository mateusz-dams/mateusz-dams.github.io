<?php
/**
 * Archiwa wpisów: kategorie, tagi, daty, autorzy.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/components/post-archive',
	null,
	array(
		'title' => wp_strip_all_tags( get_the_archive_title() ),
		'lead'  => wp_strip_all_tags( get_the_archive_description() ),
	)
);

get_footer();
