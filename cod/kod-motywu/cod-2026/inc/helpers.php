<?php
/**
 * Funkcje pomocnicze.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Czy wtyczka pól (ACF / Secure Custom Fields) jest aktywna.
 */
function cod_has_acf() {
	return function_exists( 'get_field' );
}

/**
 * Czy wartość pola jest „pusta” (null, false, '', pusta tablica).
 */
function cod_empty( $value ) {
	if ( is_string( $value ) ) {
		return '' === trim( $value );
	}
	return null === $value || false === $value || array() === $value;
}

/**
 * Pole z opcji motywu (ACF options) z wartością domyślną.
 */
function cod_option( $name, $default = '' ) {
	if ( ! cod_has_acf() ) {
		return $default;
	}
	$value = get_field( $name, 'option' );
	return cod_empty( $value ) ? $default : $value;
}

/**
 * Opcja, w której świadomie wyczyszczone pole oznacza „wyłączone” (np. identyfikatory pomiaru).
 * Wartość domyślna tylko wtedy, gdy pola nigdy nie zapisano.
 */
function cod_option_raw( $name, $default = '' ) {
	if ( ! cod_has_acf() ) {
		return $default;
	}
	$value = get_field( $name, 'option' );
	return null === $value ? $default : $value;
}

/**
 * Przełącznik (true/false) z opcji motywu. Zapisane „wyłączone” zostaje wyłączone.
 */
function cod_option_bool( $name, $default = false ) {
	if ( ! cod_has_acf() ) {
		return (bool) $default;
	}
	$value = get_field( $name, 'option' );
	if ( null === $value || '' === $value ) {
		return (bool) $default;
	}
	return (bool) $value;
}

/**
 * Pole wpisu z wartością domyślną.
 */
function cod_field( $name, $post_id = false, $default = '' ) {
	if ( ! cod_has_acf() ) {
		return $default;
	}
	$value = get_field( $name, $post_id );
	return cod_empty( $value ) ? $default : $value;
}

/**
 * Podpole (wewnątrz have_rows) z wartością domyślną.
 */
function cod_sub( $name, $default = '' ) {
	if ( ! function_exists( 'get_sub_field' ) ) {
		return $default;
	}
	$value = get_sub_field( $name );
	return cod_empty( $value ) ? $default : $value;
}

/**
 * Dzieli tekst z pola „textarea” na niepuste linie.
 */
function cod_lines( $text ) {
	if ( is_array( $text ) ) {
		return array_values( array_filter( array_map( 'trim', $text ) ) );
	}
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Dane kontaktowe: z opcji motywu, a gdy puste – z danych potwierdzonych na obecnej stronie.
 */
function cod_contact( $key = null ) {
	static $data = null;
	if ( null === $data ) {
		$phone = (string) cod_option( 'phone_number', '+48 780 756 791' );
		$digits = preg_replace( '/[^0-9+]/', '', $phone );
		if ( '' !== $digits && '+' !== $digits[0] && 9 === strlen( $digits ) ) {
			$digits = '+48' . $digits;
		}
		$display = trim( preg_replace( '/^\+48\s*/', '', $phone ) );
		$email   = sanitize_email( (string) cod_option( 'email_address', 'kontakt@cod.biz.pl' ) );
		$data    = array(
			'phone'         => $phone,
			'phone_display' => '' !== $display ? $display : $phone,
			'phone_href'    => 'tel:' . $digits,
			'email'         => $email,
			'address'       => wp_strip_all_tags( (string) cod_option( 'company_address', 'ul. Żeromskiego 51, 26-600 Radom' ) ),
			'company'       => wp_strip_all_tags( (string) cod_option( 'company_name', 'Centrum Odkrywania Doskonałości' ) ),
			'nip'           => wp_strip_all_tags( (string) cod_option( 'nip', '6332043422' ) ),
			'regon'         => wp_strip_all_tags( (string) cod_option( 'regon', '387089224' ) ),
			'hours'         => wp_strip_all_tags( (string) cod_option( 'open_hours', 'pon.–pt. 9:00–20:00' ) ),
			'maps_url'      => (string) cod_option( 'cod_maps_url', 'https://maps.app.goo.gl/1v2PFsN68EbBr6Nc7' ),
		);
	}
	if ( null === $key ) {
		return $data;
	}
	return isset( $data[ $key ] ) ? $data[ $key ] : '';
}

/**
 * Ikona z wbudowanego sprite’a SVG.
 */
function cod_icon( $name, $class = '' ) {
	return sprintf(
		'<svg class="ico %1$s" aria-hidden="true" focusable="false"><use href="#i-%2$s"/></svg>',
		esc_attr( $class ),
		esc_attr( $name )
	);
}

/**
 * Adres pliku z katalogu motywu.
 */
function cod_asset( $path ) {
	return COD_URI . '/assets/' . ltrim( $path, '/' );
}

/**
 * Obraz z pola ACF (tablica, ID lub adres URL) albo z katalogu motywu.
 *
 * @param mixed  $image    Wartość pola obrazu.
 * @param string $size     Rozmiar WordPressa.
 * @param array  $attr     Atrybuty (class, alt, loading, sizes…).
 * @param string $fallback Ścieżka w assets/img, gdy brak obrazu.
 */
function cod_img( $image, $size = 'large', $attr = array(), $fallback = '' ) {
	$id = 0;
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		$id = (int) $image['ID'];
	} elseif ( is_array( $image ) && ! empty( $image['id'] ) ) {
		$id = (int) $image['id'];
	} elseif ( is_numeric( $image ) ) {
		$id = (int) $image;
	}
	if ( $id ) {
		$attr = wp_parse_args( $attr, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
		if ( isset( $attr['alt'] ) && '' === $attr['alt'] ) {
			unset( $attr['alt'] );
		}
		$html = wp_get_attachment_image( $id, $size, false, $attr );
		if ( $html ) {
			return $html;
		}
	}
	$url = '';
	$alt = isset( $attr['alt'] ) ? $attr['alt'] : '';
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		$url = $image['url'];
		$alt = '' !== $alt ? $alt : ( isset( $image['alt'] ) ? $image['alt'] : '' );
	} elseif ( is_string( $image ) && '' !== $image && ! is_numeric( $image ) ) {
		$url = $image;
	} elseif ( '' !== $fallback ) {
		$url = cod_asset( 'img/' . $fallback );
	}
	if ( '' === $url ) {
		return '';
	}
	$attr = wp_parse_args( $attr, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
	$out  = '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"';
	foreach ( $attr as $k => $v ) {
		if ( 'alt' === $k || null === $v || false === $v ) {
			continue;
		}
		$out .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return $out . '>';
}

/**
 * Logo z opcji motywu (header_logo / footer_logo), a gdy puste – oryginalne logo z motywu.
 */
function cod_logo( $context = 'header' ) {
	$field = 'footer' === $context ? 'footer_logo' : 'header_logo';
	$image = cod_option( $field, '' );
	if ( cod_empty( $image ) && 'footer' === $context ) {
		$image = cod_option( 'header_logo', '' );
	}
	$attrs = array(
		'alt'     => get_bloginfo( 'name' ),
		'class'   => 'site-logo__img site-logo__img--default',
		'loading' => 'header' === $context ? 'eager' : 'lazy',
		'width'   => 240,
		'height'  => 120,
		'sizes'   => '160px',
	);
	$html = cod_img( $image, 'medium', $attrs, 'cod-logo.png' );

	// Wersja na ciemne tło (nagłówek nad ciemnym pierwszym ekranem, tryb ciemny).
	$inverse          = cod_option( 'cod_logo_inverse', '' );
	$attrs['class']   = 'site-logo__img site-logo__img--inverse';
	$attrs['alt']     = '';
	$attrs['loading'] = 'lazy';
	$attrs['aria-hidden'] = 'true';
	return $html . cod_img( $inverse, 'medium', $attrs, 'cod-logo-light.png' );
}

/**
 * Czy adres prowadzi do telefonu lub e-maila – do pomiaru i ikon.
 */
function cod_link_kind( $url ) {
	if ( 0 === strpos( $url, 'tel:' ) ) {
		return 'tel';
	}
	if ( 0 === strpos( $url, 'mailto:' ) ) {
		return 'mail';
	}
	return 'url';
}

/**
 * Przycisk z pola „Link” ACF.
 *
 * @param array|string $link    Tablica ACF (url, title, target) albo adres.
 * @param string       $variant primary|ghost.
 * @param array        $args    label, location, class, icon, data (tablica atrybutów data-*).
 */
function cod_button( $link, $variant = 'primary', $args = array() ) {
	if ( empty( $link ) ) {
		return '';
	}
	$url    = is_array( $link ) ? ( isset( $link['url'] ) ? $link['url'] : '' ) : (string) $link;
	$title  = is_array( $link ) && ! empty( $link['title'] ) ? $link['title'] : '';
	$target = is_array( $link ) && ! empty( $link['target'] ) ? $link['target'] : '';
	$args   = wp_parse_args(
		$args,
		array(
			'label'    => $title,
			'location' => '',
			'class'    => '',
			'icon'     => null,
			'data'     => array(),
		)
	);
	if ( '' === $url ) {
		return '';
	}
	$kind = cod_link_kind( $url );
	$icon = $args['icon'];
	if ( null === $icon ) {
		$icon = 'tel' === $kind ? 'phone' : ( 'mail' === $kind ? 'mail' : '' );
	}
	$label = '' !== $args['label'] ? $args['label'] : $url;
	$attrs = ' href="' . esc_url( $url ) . '"';
	if ( '_blank' === $target ) {
		$attrs .= ' target="_blank" rel="noopener"';
	}
	if ( '' !== $args['location'] ) {
		$attrs .= ' data-track-location="' . esc_attr( $args['location'] ) . '"';
	}
	foreach ( (array) $args['data'] as $k => $v ) {
		$attrs .= ' data-' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	$classes = trim( 'btn btn--' . $variant . ' ' . $args['class'] );
	$html    = '<a class="' . esc_attr( $classes ) . '"' . $attrs . '>';
	if ( $icon ) {
		$html .= cod_icon( $icon );
	}
	$html .= '<span>' . esc_html( $label ) . '</span>';
	if ( '_blank' === $target ) {
		$html .= '<span class="sr-only"> (otworzy się w nowej karcie)</span>';
	}
	return $html . '</a>';
}

/**
 * Przycisk telefonu.
 */
function cod_phone_button( $location, $variant = 'primary', $label = null, $class = '' ) {
	if ( null === $label ) {
		/* translators: %s: numer telefonu. */
		$label = sprintf( __( 'Zadzwoń: %s', 'cod2026' ), cod_contact( 'phone_display' ) );
	}
	return cod_button(
		cod_contact( 'phone_href' ),
		$variant,
		array(
			'label'    => $label,
			'location' => $location,
			'class'    => $class,
			'icon'     => 'phone',
		)
	);
}

/**
 * Przycisk do formularza kontaktowego (kotwica #kontakt na stronie głównej lub strona kontaktu).
 */
function cod_contact_url() {
	if ( is_front_page() ) {
		return '#kontakt';
	}
	$page = get_page_by_path( 'kontakt' );
	return $page ? get_permalink( $page ) . '#kontakt' : home_url( '/#kontakt' );
}

/**
 * Status godzin kontaktu (uzupełniany przez JS według czasu w Polsce).
 */
function cod_status( $class = '' ) {
	return '<p class="status ' . esc_attr( $class ) . '" data-status><i aria-hidden="true"></i><span>' . esc_html( cod_contact( 'hours' ) ) . '</span></p>';
}

/**
 * Klasa powierzchni sekcji na podstawie koloru tła ustawionego w starych sekcjach.
 * Stare kolory są mapowane na paletę nowego motywu (nie wstawiamy ich wprost).
 */
function cod_surface( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return 'surface-paper';
	}
	$r   = hexdec( substr( $hex, 0, 2 ) ) / 255;
	$g   = hexdec( substr( $hex, 2, 2 ) ) / 255;
	$b   = hexdec( substr( $hex, 4, 2 ) ) / 255;
	$max = max( $r, $g, $b );
	$min = min( $r, $g, $b );
	$l   = ( $max + $min ) / 2;
	if ( $l < 0.35 ) {
		return 'surface-dark on-dark';
	}
	if ( $max - $min < 0.035 ) {
		return 'surface-paper';
	}
	if ( $b >= $r && $b >= $g * 0.98 ) {
		return $l > 0.9 ? 'surface-mist' : 'surface-aqua on-aqua';
	}
	if ( $r >= $b ) {
		return 'surface-sand';
	}
	return 'surface-mist';
}

/**
 * Bezpieczne wypisanie treści WYSIWYG z pól ACF.
 */
function cod_prose( $html ) {
	return wp_kses_post( (string) $html );
}

/**
 * Krótki, czysty tekst do kafelka: bez znaczników HTML i encji, ucięty na granicy słowa.
 *
 * @param string $text Tekst (może zawierać HTML).
 * @param int    $max  Maksymalna liczba znaków.
 */
function cod_snippet( $text, $max = 120 ) {
	$text = wp_strip_all_tags( strip_shortcodes( (string) $text ), true );
	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	// Znaczniki wpisane jako tekst (np. „&lt;strong&gt;” ze starej treści).
	$text = wp_strip_all_tags( $text, true );
	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
	if ( function_exists( 'mb_strlen' ) && mb_strlen( $text ) > $max ) {
		$cut  = mb_substr( $text, 0, $max );
		$pos  = mb_strrpos( $cut, ' ' );
		$text = rtrim( $pos ? mb_substr( $cut, 0, $pos ) : $cut, " ,.;:–-" ) . '…';
	}
	return $text;
}

/**
 * Cena do wyświetlenia: bez zbędnych groszy „,00”.
 *
 * @param string $price Cena wpisana w panelu.
 */
function cod_price_clean( $price ) {
	$price = trim( html_entity_decode( wp_strip_all_tags( (string) $price ), ENT_QUOTES, 'UTF-8' ) );
	return trim( preg_replace( '/(\d)[,.]00(?!\d)/u', '$1', $price ) );
}

/**
 * Czy bieżąca strona to strona sklepu / koszyka / zamówienia / konta.
 */
function cod_is_shop_context() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
}

/**
 * Czy bieżąca strona to kurs, lekcja, zagadnienie lub test LearnDash.
 */
function cod_is_learning_context() {
	$types = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-assignment', 'sfwd-certificates', 'groups' );
	return is_singular( $types ) || is_post_type_archive( $types );
}

/**
 * Okruszki: Breadcrumb NavXT, Rank Math albo prosty zapas.
 */
function cod_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	if ( function_exists( 'bcn_display' ) ) {
		echo '<nav class="crumbs" aria-label="' . esc_attr__( 'Ścieżka', 'cod2026' ) . '">';
		bcn_display();
		echo '</nav>';
		return;
	}
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		echo '<div class="crumbs">';
		rank_math_the_breadcrumbs();
		echo '</div>';
		return;
	}
	if ( class_exists( 'WooCommerce' ) && is_shop() ) {
		$title = get_the_title( wc_get_page_id( 'shop' ) );
	} elseif ( is_singular() ) {
		$title = get_the_title( get_queried_object_id() );
	} elseif ( is_home() ) {
		$title = get_the_title( (int) get_option( 'page_for_posts' ) );
	} elseif ( is_post_type_archive( 'offer' ) ) {
		$title = __( 'Oferta', 'cod2026' );
	} elseif ( is_search() ) {
		$title = __( 'Wyszukiwanie', 'cod2026' );
	} else {
		$title = get_the_archive_title();
	}
	echo '<nav class="crumbs" aria-label="' . esc_attr__( 'Ścieżka', 'cod2026' ) . '"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Strona główna', 'cod2026' ) . '</a> <span aria-hidden="true">/</span> <span aria-current="page">' . esc_html( wp_strip_all_tags( $title ) ) . '</span></nav>';
}

/**
 * Szablon częściowy z przekazaniem danych (zgodny z WP < 5.5 przez set_query_var).
 */
function cod_part( $slug, $args = array() ) {
	get_template_part( $slug, null, $args );
}

/**
 * Unikalny identyfikator elementu na stronie.
 *
 * @param string $prefix Prefiks.
 */
function cod_uid( $prefix ) {
	static $n = 0;
	++$n;
	return $prefix . '-' . $n;
}
