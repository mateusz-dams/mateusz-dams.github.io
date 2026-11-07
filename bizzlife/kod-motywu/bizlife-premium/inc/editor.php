<?php
/**
 * Edytor bloków: style bloków i wzorce dla podstron.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Style bloków i wzorce.
 */
function bzl_register_block_extras() {
	register_block_style(
		'core/list',
		array(
			'name'  => 'checks',
			'label' => __( 'Lista ze znacznikami', 'bizlife' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'panel',
			'label' => __( 'Panel (karta)', 'bizlife' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'lead',
			'label' => __( 'Lead (większy tekst)', 'bizlife' ),
		)
	);

	register_block_pattern_category( 'bizlife', array( 'label' => __( 'Bizzlife', 'bizlife' ) ) );

	register_block_pattern(
		'bizlife/service-scope',
		array(
			'title'      => __( 'Usługa: opis + lista zakresu', 'bizlife' ),
			'categories' => array( 'bizlife' ),
			'content'    => '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'Na czym polega usługa', 'bizlife' ) . '</h2><!-- /wp:heading -->'
				. '<!-- wp:paragraph {"className":"is-style-lead"} --><p class="is-style-lead">' . esc_html__( 'Wpisz krótkie wprowadzenie do usługi.', 'bizlife' ) . '</p><!-- /wp:paragraph -->'
				. '<!-- wp:list {"className":"is-style-checks"} --><ul class="wp-block-list is-style-checks"><!-- wp:list-item --><li>' . esc_html__( 'Pierwszy element zakresu', 'bizlife' ) . '</li><!-- /wp:list-item --><!-- wp:list-item --><li>' . esc_html__( 'Drugi element zakresu', 'bizlife' ) . '</li><!-- /wp:list-item --></ul><!-- /wp:list -->',
		)
	);

	register_block_pattern(
		'bizlife/info-panel',
		array(
			'title'      => __( 'Panel informacyjny', 'bizlife' ),
			'categories' => array( 'bizlife' ),
			'content'    => '<!-- wp:group {"className":"is-style-panel","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-panel">'
				. '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html__( 'Warto wiedzieć', 'bizlife' ) . '</h3><!-- /wp:heading -->'
				. '<!-- wp:paragraph --><p>' . esc_html__( 'Krótka, ważna informacja dla klienta.', 'bizlife' ) . '</p><!-- /wp:paragraph -->'
				. '</div><!-- /wp:group -->',
		)
	);
}
add_action( 'init', 'bzl_register_block_extras' );

/**
 * Domyślny szablon bloków dla nowych usług.
 *
 * @param array  $args      Argumenty typu treści.
 * @param string $post_type Typ.
 * @return array
 */
function bzl_service_block_template( $args, $post_type ) {
	if ( 'bzl_service' === $post_type ) {
		$args['template'] = array(
			array(
				'core/heading',
				array(
					'level'   => 2,
					'content' => __( 'Na czym polega usługa', 'bizlife' ),
				),
			),
			array(
				'core/paragraph',
				array(
					'className'   => 'is-style-lead',
					'placeholder' => __( 'Krótkie wprowadzenie do usługi…', 'bizlife' ),
				),
			),
			array( 'core/list', array( 'className' => 'is-style-checks' ) ),
		);
	}
	return $args;
}
add_filter( 'register_post_type_args', 'bzl_service_block_template', 10, 2 );
