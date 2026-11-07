<?php
/**
 * Zgłoszenia z formularza zapisywane w kokpicie (kopia zapasowa na wypadek problemów z pocztą).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rejestracja typu „Zgłoszenia” (niepubliczny).
 */
function bzl_register_leads() {
	register_post_type(
		'bzl_lead',
		array(
			'labels'              => array(
				'name'          => __( 'Zgłoszenia', 'bizlife' ),
				'singular_name' => __( 'Zgłoszenie', 'bizlife' ),
				'menu_name'     => __( 'Zgłoszenia', 'bizlife' ),
				'all_items'     => __( 'Wszystkie zgłoszenia', 'bizlife' ),
				'edit_item'     => __( 'Zgłoszenie', 'bizlife' ),
				'not_found'     => __( 'Brak zgłoszeń', 'bizlife' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_position'       => 23,
			'menu_icon'           => 'dashicons-email-alt',
			'supports'            => array( 'title' ),
			'capability_type'     => 'page',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
			'rewrite'             => false,
		)
	);
}
add_action( 'init', 'bzl_register_leads' );

/**
 * Zapis zgłoszenia.
 *
 * @param array  $values Dane.
 * @param string $ref    Strona źródłowa.
 * @param bool   $sent   Czy e-mail został wysłany.
 * @return bool
 */
function bzl_store_lead( $values, $ref, $sent ) {
	$id = wp_insert_post(
		array(
			'post_type'   => 'bzl_lead',
			'post_status' => 'private',
			'post_title'  => wp_strip_all_tags( $values['name'] . ( $values['service'] ? ' – ' . $values['service'] : '' ) ),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return false;
	}
	foreach ( array( 'phone', 'email', 'service', 'message' ) as $k ) {
		update_post_meta( $id, '_bzl_' . $k, $values[ $k ] );
	}
	update_post_meta( $id, '_bzl_ref', esc_url_raw( $ref ) );
	update_post_meta( $id, '_bzl_mail_sent', $sent ? 1 : 0 );
	return true;
}

/**
 * Podgląd zgłoszenia w kokpicie.
 */
function bzl_lead_meta_box() {
	add_meta_box(
		'bzl_lead_view',
		__( 'Treść zgłoszenia', 'bizlife' ),
		static function ( $post ) {
			$rows = array(
				__( 'Telefon', 'bizlife' )        => get_post_meta( $post->ID, '_bzl_phone', true ),
				__( 'E-mail', 'bizlife' )         => get_post_meta( $post->ID, '_bzl_email', true ),
				__( 'Usługa', 'bizlife' )         => get_post_meta( $post->ID, '_bzl_service', true ),
				__( 'Wiadomość', 'bizlife' )      => get_post_meta( $post->ID, '_bzl_message', true ),
				__( 'Strona', 'bizlife' )         => get_post_meta( $post->ID, '_bzl_ref', true ),
				__( 'E-mail wysłany', 'bizlife' ) => get_post_meta( $post->ID, '_bzl_mail_sent', true ) ? __( 'tak', 'bizlife' ) : __( 'NIE – sprawdź konfigurację poczty (np. wtyczka SMTP)', 'bizlife' ),
				__( 'Data', 'bizlife' )           => get_the_date( 'Y-m-d H:i', $post ),
			);
			echo '<table class="widefat striped"><tbody>';
			foreach ( $rows as $label => $val ) {
				echo '<tr><th style="width:160px">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( (string) $val ) ) . '</td></tr>';
			}
			echo '</tbody></table>';
		},
		'bzl_lead',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_bzl_lead', 'bzl_lead_meta_box' );

/**
 * Kolumny listy zgłoszeń.
 *
 * @param array $cols Kolumny.
 * @return array
 */
function bzl_lead_columns( $cols ) {
	return array(
		'cb'        => $cols['cb'] ?? '',
		'title'     => __( 'Osoba / usługa', 'bizlife' ),
		'bzl_phone' => __( 'Telefon', 'bizlife' ),
		'bzl_email' => __( 'E-mail', 'bizlife' ),
		'bzl_mail'  => __( 'E-mail wysłany', 'bizlife' ),
		'date'      => __( 'Data', 'bizlife' ),
	);
}
add_filter( 'manage_bzl_lead_posts_columns', 'bzl_lead_columns' );

/**
 * Wartości kolumn.
 *
 * @param string $col Kolumna.
 * @param int    $id  ID.
 */
function bzl_lead_column_value( $col, $id ) {
	if ( 'bzl_phone' === $col ) {
		echo esc_html( (string) get_post_meta( $id, '_bzl_phone', true ) );
	} elseif ( 'bzl_email' === $col ) {
		echo esc_html( (string) get_post_meta( $id, '_bzl_email', true ) );
	} elseif ( 'bzl_mail' === $col ) {
		echo get_post_meta( $id, '_bzl_mail_sent', true ) ? '✓' : '<strong style="color:#b32d2e">' . esc_html__( 'nie', 'bizlife' ) . '</strong>';
	}
}
add_action( 'manage_bzl_lead_posts_custom_column', 'bzl_lead_column_value', 10, 2 );

/**
 * Harmonogram czyszczenia starych zgłoszeń (RODO – minimalizacja danych).
 */
function bzl_schedule_lead_cleanup() {
	if ( ! wp_next_scheduled( 'bzl_lead_cleanup' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'bzl_lead_cleanup' );
	}
}
add_action( 'init', 'bzl_schedule_lead_cleanup' );

/**
 * Usuwa zgłoszenia starsze niż ustawiona liczba dni.
 */
function bzl_lead_cleanup() {
	$days = (int) bzl_mod( 'form_retention_days' );
	if ( $days <= 0 ) {
		return;
	}
	$old = get_posts(
		array(
			'post_type'   => 'bzl_lead',
			'post_status' => 'any',
			'numberposts' => 200,
			'fields'      => 'ids',
			'date_query'  => array( array( 'before' => $days . ' days ago' ) ),
		)
	);
	foreach ( $old as $id ) {
		wp_delete_post( $id, true );
	}
}
add_action( 'bzl_lead_cleanup', 'bzl_lead_cleanup' );

/**
 * Wyłączenie harmonogramu przy zmianie motywu.
 */
function bzl_unschedule_lead_cleanup() {
	wp_clear_scheduled_hook( 'bzl_lead_cleanup' );
}
add_action( 'switch_theme', 'bzl_unschedule_lead_cleanup' );
