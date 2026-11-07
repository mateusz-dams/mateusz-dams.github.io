<?php
/**
 * Menu: walker z przyciskami podmenu oraz menu zapasowe.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Walker dodający dostępny przycisk rozwijania podmenu.
 */
class BZL_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Licznik podmenu.
	 *
	 * @var int
	 */
	private $sub_id = 0;

	/**
	 * Otwarcie podmenu.
	 *
	 * @param string   $output Wyjście.
	 * @param int      $depth  Głębokość.
	 * @param stdClass $args   Argumenty.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="sub-menu" id="' . esc_attr( 'bzl-sub-' . $this->sub_id ) . '">';
	}

	/**
	 * Element menu.
	 *
	 * @param string   $output Wyjście.
	 * @param WP_Post  $item   Element.
	 * @param int      $depth  Głębokość.
	 * @param stdClass $args   Argumenty.
	 * @param int      $id     ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		parent::start_el( $output, $item, $depth, $args, $id );
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		if ( in_array( 'menu-item-has-children', $classes, true ) ) {
			++$this->sub_id;
			$output .= sprintf(
				'<button class="submenu-toggle" type="button" aria-expanded="false" aria-controls="%1$s"><span class="screen-reader-text">%2$s</span>%3$s</button>',
				esc_attr( 'bzl-sub-' . $this->sub_id ),
				/* translators: %s: nazwa pozycji menu. */
				esc_html( sprintf( __( 'Pokaż podmenu: %s', 'bizlife' ), wp_strip_all_tags( $item->title ) ) ),
				bzl_icon( 'chevron-down' ) // phpcs:ignore WordPress.Security.EscapeOutput
			);
		}
	}
}

/**
 * Menu zapasowe (gdy w Wygląd → Menu nie przypisano menu głównego).
 */
function bzl_fallback_menu() {
	$items    = array();
	$services = bzl_services_query( 12 );
	$sub      = '';
	if ( $services->have_posts() ) {
		$sub = '<ul class="sub-menu" id="bzl-sub-fallback">';
		foreach ( $services->posts as $s ) {
			$sub .= '<li class="menu-item"><a href="' . esc_url( get_permalink( $s ) ) . '">' . esc_html( get_the_title( $s ) ) . '</a></li>';
		}
		$sub .= '</ul>';
	}

	$archive = get_post_type_archive_link( 'bzl_service' );
	$current = is_post_type_archive( 'bzl_service' ) || is_singular( 'bzl_service' );
	$items[] = '<li class="menu-item' . ( $sub ? ' menu-item-has-children' : '' ) . ( $current ? ' current-menu-ancestor' : '' ) . '"><a href="' . esc_url( $archive ) . '">' . esc_html__( 'Usługi', 'bizlife' ) . '</a>'
		. ( $sub ? '<button class="submenu-toggle" type="button" aria-expanded="false" aria-controls="bzl-sub-fallback"><span class="screen-reader-text">' . esc_html__( 'Pokaż podmenu: Usługi', 'bizlife' ) . '</span>' . bzl_icon( 'chevron-down' ) . '</button>' . $sub : '' )
		. '</li>';

	$links = array(
		array( __( 'O nas', 'bizlife' ), bzl_url( '{o-nas}' ) ),
		array( __( 'Aktualności', 'bizlife' ), bzl_url( '{aktualnosci}' ) ),
		array( __( 'FAQ', 'bizlife' ), bzl_url( '{faq}' ) ),
		array( __( 'Kontakt', 'bizlife' ), bzl_url( '{kontakt}' ) ),
	);
	foreach ( $links as $l ) {
		$items[] = '<li class="menu-item"><a href="' . esc_url( $l[1] ) . '">' . esc_html( $l[0] ) . '</a></li>';
	}
	echo '<ul class="menu nav-menu">' . implode( '', $items ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
