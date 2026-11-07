<?php
/**
 * Własne typy treści: Usługi i FAQ.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rejestracja typów treści i taksonomii.
 */
function bzl_register_post_types() {
	register_post_type(
		'bzl_service',
		array(
			'labels'             => array(
				'name'               => __( 'Usługi', 'bizlife' ),
				'singular_name'      => __( 'Usługa', 'bizlife' ),
				'add_new'            => __( 'Dodaj usługę', 'bizlife' ),
				'add_new_item'       => __( 'Dodaj nową usługę', 'bizlife' ),
				'edit_item'          => __( 'Edytuj usługę', 'bizlife' ),
				'new_item'           => __( 'Nowa usługa', 'bizlife' ),
				'view_item'          => __( 'Zobacz usługę', 'bizlife' ),
				'search_items'       => __( 'Szukaj usług', 'bizlife' ),
				'not_found'          => __( 'Brak usług', 'bizlife' ),
				'not_found_in_trash' => __( 'Brak usług w koszu', 'bizlife' ),
				'all_items'          => __( 'Wszystkie usługi', 'bizlife' ),
				'menu_name'          => __( 'Usługi', 'bizlife' ),
				'archives'           => __( 'Usługi', 'bizlife' ),
			),
			'description'        => __( 'Usługi biura rachunkowego.', 'bizlife' ),
			'public'             => true,
			'show_in_rest'       => true,
			'menu_position'      => 21,
			'menu_icon'          => 'dashicons-portfolio',
			'has_archive'        => 'uslugi',
			'rewrite'            => array(
				'slug'       => 'uslugi',
				'with_front' => false,
			),
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions', 'custom-fields' ),
			'publicly_queryable' => true,
		)
	);

	register_post_type(
		'bzl_faq',
		array(
			'labels'              => array(
				'name'          => __( 'FAQ', 'bizlife' ),
				'singular_name' => __( 'Pytanie FAQ', 'bizlife' ),
				'add_new'       => __( 'Dodaj pytanie', 'bizlife' ),
				'add_new_item'  => __( 'Dodaj nowe pytanie', 'bizlife' ),
				'edit_item'     => __( 'Edytuj pytanie', 'bizlife' ),
				'all_items'     => __( 'Wszystkie pytania', 'bizlife' ),
				'menu_name'     => __( 'FAQ', 'bizlife' ),
				'not_found'     => __( 'Brak pytań', 'bizlife' ),
			),
			'description'         => __( 'Pytanie = tytuł, odpowiedź = treść. Kolejność ustawisz w polu „Kolejność”.', 'bizlife' ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_position'       => 22,
			'menu_icon'           => 'dashicons-editor-help',
			'supports'            => array( 'title', 'editor', 'page-attributes', 'revisions', 'custom-fields' ),
			'rewrite'             => false,
		)
	);

	register_taxonomy(
		'bzl_faq_topic',
		array( 'bzl_faq' ),
		array(
			'labels'            => array(
				'name'          => __( 'Tematy FAQ', 'bizlife' ),
				'singular_name' => __( 'Temat FAQ', 'bizlife' ),
				'add_new_item'  => __( 'Dodaj temat', 'bizlife' ),
				'edit_item'     => __( 'Edytuj temat', 'bizlife' ),
				'menu_name'     => __( 'Tematy', 'bizlife' ),
			),
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);

	$service_meta = array(
		'bzl_icon'      => 'string',
		'bzl_scope'     => 'string',
		'bzl_audience'  => 'string',
		'bzl_faq_topic' => 'integer',
	);
	foreach ( $service_meta as $key => $type ) {
		register_post_meta(
			'bzl_service',
			$key,
			array(
				'type'              => $type,
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'integer' === $type ? 'absint' : 'sanitize_textarea_field',
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_post_meta(
		'bzl_faq',
		'bzl_on_home',
		array(
			'type'          => 'boolean',
			'single'        => true,
			'show_in_rest'  => true,
			'default'       => false,
			'auth_callback' => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'bzl_register_post_types' );

/**
 * Archiwum usług: wszystkie, w kolejności z panelu.
 *
 * @param WP_Query $q Zapytanie.
 */
function bzl_service_archive_query( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_post_type_archive( 'bzl_service' ) ) {
		$q->set( 'posts_per_page', 50 );
		$q->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			)
		);
	}
}
add_action( 'pre_get_posts', 'bzl_service_archive_query' );

/**
 * Przekierowanie 301 ze starych adresów usług (np. /ksiegowosc/ → /uslugi/ksiegowosc/).
 *
 * Działa tylko wtedy, gdy stary adres zwraca 404 (np. stara strona została przeniesiona do szkiców).
 */
function bzl_legacy_service_redirect() {
	if ( ! is_404() ) {
		return;
	}
	$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( $home && 0 === strpos( $path, $home ) ) {
		$path = trim( substr( $path, strlen( $home ) ), '/' );
	}
	if ( ! $path || false !== strpos( $path, '/' ) || ! preg_match( '/^[a-z0-9\-]+$/', $path ) ) {
		return;
	}
	$service = get_page_by_path( $path, OBJECT, 'bzl_service' );
	if ( $service && 'publish' === $service->post_status ) {
		wp_safe_redirect( get_permalink( $service ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'bzl_legacy_service_redirect', 1 );
