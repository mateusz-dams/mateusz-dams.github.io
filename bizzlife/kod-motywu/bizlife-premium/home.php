<?php
/**
 * Archiwum aktualności (strona wpisów).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

$bzl_blog_id = (int) get_option( 'page_for_posts' );
get_template_part(
	'template-parts/components/post-archive',
	null,
	array(
		'title' => $bzl_blog_id ? get_the_title( $bzl_blog_id ) : bzl_mod( 'news_title' ),
		'lead'  => ( $bzl_blog_id && has_excerpt( $bzl_blog_id ) ) ? get_the_excerpt( $bzl_blog_id ) : bzl_mod( 'news_lead' ),
	)
);

get_footer();
