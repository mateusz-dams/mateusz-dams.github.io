<?php
/**
 * Bizzlife Premium – funkcje motywu.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

define( 'BZL_VERSION', '1.2.0' );
define( 'BZL_DIR', get_template_directory() );
define( 'BZL_URI', get_template_directory_uri() );

$bzl_includes = array(
	'defaults',
	'helpers',
	'icons',
	'setup',
	'enqueue',
	'post-types',
	'meta-boxes',
	'customizer',
	'walker',
	'breadcrumbs',
	'seo',
	'contact-form',
	'leads',
	'editor',
	'performance',
	'svg',
	'importer',
	'starter',
);

foreach ( $bzl_includes as $bzl_file ) {
	require_once BZL_DIR . '/inc/' . $bzl_file . '.php';
}
unset( $bzl_includes, $bzl_file );
