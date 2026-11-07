<?php
/**
 * Sekcje stron (pole elastyczne ACF „sections” z poprzedniego motywu) w nowym wyglądzie.
 *
 * Obsługiwane układy (nazwy bez zmian): hero_section, full_width_section, fifty_fifty_section,
 * categories_section, offer_section, our_team_section, products_section, faq_section,
 * call_to_action_section, reviews_section, blog_section, contact_section, map_section.
 * Każdy układ ma szablon w template-parts/sections/{układ}.php.
 *
 * Kolory tła ustawione w starych sekcjach są mapowane na paletę nowego motywu (cod_surface()).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Czy strona ma sekcje.
 *
 * @param int|false $post_id ID.
 */
function cod_has_sections( $post_id = false ) {
	return cod_has_acf() && have_rows( 'sections', $post_id );
}

/**
 * Nazwa pierwszego układu na stronie (np. do pominięcia banera, gdy strona zaczyna się od „hero”).
 *
 * @param int|false $post_id ID.
 */
function cod_first_layout( $post_id = false ) {
	if ( ! cod_has_acf() ) {
		return '';
	}
	$rows = get_field( 'sections', $post_id );
	if ( is_array( $rows ) && ! empty( $rows[0]['acf_fc_layout'] ) ) {
		return $rows[0]['acf_fc_layout'];
	}
	return '';
}

/**
 * Nazwa ostatniego układu na stronie (np. żeby nie dublować paska końcowego).
 *
 * @param int|false $post_id ID.
 */
function cod_last_layout( $post_id = false ) {
	if ( ! cod_has_acf() ) {
		return '';
	}
	$rows = get_field( 'sections', $post_id );
	if ( is_array( $rows ) && $rows ) {
		$last = end( $rows );
		return isset( $last['acf_fc_layout'] ) ? $last['acf_fc_layout'] : '';
	}
	return '';
}

/**
 * Renderuje wszystkie sekcje strony.
 *
 * @param int|false $post_id ID.
 */
function cod_render_sections( $post_id = false ) {
	if ( ! cod_has_sections( $post_id ) ) {
		return;
	}
	$GLOBALS['cod_section_index'] = 0;
	while ( have_rows( 'sections', $post_id ) ) {
		the_row();
		$layout = get_row_layout();
		$file   = COD_DIR . '/template-parts/sections/' . sanitize_file_name( $layout ) . '.php';
		if ( file_exists( $file ) ) {
			get_template_part( 'template-parts/sections/' . $layout );
		}
		++$GLOBALS['cod_section_index'];
	}
}

/**
 * Atrybuty sekcji z pól {prefiks}_section_id / _padding_top / _padding_bottom / _background_color.
 *
 * @param string $p     Prefiks układu (np. full_width).
 * @param string $class Klasy bazowe.
 * @param string $default_surface Powierzchnia, gdy nie ustawiono koloru.
 * @return string Atrybuty HTML.
 */
function cod_sec_attrs( $p, $class, $default_surface = 'surface-paper' ) {
	$id      = sanitize_title( (string) get_sub_field( $p . '_section_id' ) );
	$bg      = (string) get_sub_field( $p . '_section_background_color' );
	$surface = '' !== $bg ? cod_surface( $bg ) : $default_surface;
	$classes = array( 'fx', $class, $surface );
	$top     = get_sub_field( $p . '_section_padding_top' );
	$bottom  = get_sub_field( $p . '_section_padding_bottom' );
	// Stare przełączniki odstępów: brak obu = sekcja „przyklejona”, w nowym motywie z mniejszym odstępem.
	if ( false === (bool) $top && null !== $top ) {
		$classes[] = 'fx--flush-top';
	}
	if ( false === (bool) $bottom && null !== $bottom ) {
		$classes[] = 'fx--flush-bottom';
	}
	$out = ' class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	if ( '' !== $id ) {
		$out .= ' id="' . esc_attr( $id ) . '"';
	}
	return $out;
}

/**
 * Nagłówek sekcji: podtytuł (mały, nad tytułem) i tytuł.
 *
 * @param string $p     Prefiks.
 * @param string $align center|left.
 * @param string $tag   Znacznik tytułu.
 */
function cod_sec_head( $p, $align = 'center', $tag = 'h2' ) {
	$title = (string) get_sub_field( $p . '_section_title' );
	$sub   = (string) get_sub_field( $p . '_section_subtitle' );
	if ( '' === trim( wp_strip_all_tags( $title ) ) && '' === trim( wp_strip_all_tags( $sub ) ) ) {
		return;
	}
	echo '<header class="fx-head fx-head--' . esc_attr( $align ) . ' reveal">';
	if ( '' !== trim( $sub ) ) {
		echo '<p class="num-tag">' . wp_kses( $sub, cod_inline_tags() ) . '</p>';
	}
	if ( '' !== trim( $title ) ) {
		printf( '<%1$s class="fx-title">%2$s</%1$s>', esc_html( $tag ), wp_kses( $title, cod_inline_tags() ) );
	}
	echo '</header>';
}

/**
 * Dozwolone znaczniki w tytułach (pogrubienie, łamanie linii, span).
 */
function cod_inline_tags() {
	return array(
		'br'     => array(),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'span'   => array( 'class' => array() ),
	);
}

/**
 * Przyciski sekcji z powtarzacza {prefiks}_section_buttons (lub dowolnego – parametry).
 *
 * @param string $rows     Nazwa powtarzacza.
 * @param string $field    Nazwa pola linku.
 * @param string $location Miejsce do pomiaru.
 * @param string $class    Klasa kontenera.
 */
function cod_sec_buttons( $rows, $field, $location, $class = '' ) {
	if ( ! have_rows( $rows ) ) {
		return;
	}
	$html = '';
	while ( have_rows( $rows ) ) {
		the_row();
		$link = get_sub_field( $field );
		if ( ! $link ) {
			continue;
		}
		$primary = (bool) get_sub_field( $field . '_primary' );
		$html   .= cod_button( $link, $primary ? 'primary' : 'ghost', array( 'location' => $location ) );
	}
	if ( '' !== $html ) {
		echo '<div class="btn-row ' . esc_attr( $class ) . '">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Identyfikator sekcji do pomiaru kliknięć.
 *
 * @param string $p Prefiks.
 */
function cod_sec_loc( $p ) {
	$id = sanitize_title( (string) get_sub_field( $p . '_section_id' ) );
	return ( $id ? $id : $p ) . '-' . ( isset( $GLOBALS['cod_section_index'] ) ? (int) $GLOBALS['cod_section_index'] : 0 );
}
