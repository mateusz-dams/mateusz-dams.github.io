<?php
/**
 * Funkcje pomocnicze motywu.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pobiera ustawienie motywu z wartością domyślną.
 *
 * @param string $key Klucz.
 * @return mixed
 */
function bzl_mod( $key ) {
	$defaults = bzl_defaults();
	return get_theme_mod( $key, $defaults[ $key ] ?? '' );
}

/**
 * Zwraca niepuste sloty grupy (np. karty „Dlaczego my”).
 *
 * @param string $group Nazwa grupy.
 * @return array
 */
function bzl_slots( $group ) {
	$groups = bzl_slot_groups();
	if ( empty( $groups[ $group ] ) ) {
		return array();
	}
	$items = array();
	for ( $i = 1; $i <= $groups[ $group ]['count']; $i++ ) {
		$item = array();
		foreach ( $groups[ $group ]['fields'] as $field ) {
			$item[ $field ] = bzl_mod( "{$group}_{$i}_{$field}" );
		}
		$primary = isset( $item['title'] ) ? $item['title'] : ( $item['text'] ?? '' );
		if ( '' !== trim( (string) $primary ) ) {
			$items[] = $item;
		}
	}
	return $items;
}

/**
 * Znajduje stronę po szablonie lub slugu (z cache na żądanie).
 *
 * @param string $template Plik szablonu, np. page-templates/template-contact.php.
 * @param string $slug     Zapasowy slug.
 * @return int ID strony lub 0.
 */
function bzl_find_page( $template, $slug = '' ) {
	static $cache = array();
	$key          = $template . '|' . $slug;
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}
	$id    = 0;
	$pages = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'numberposts'            => 1,
			'fields'                 => 'ids',
			'meta_key'               => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'             => $template, // phpcs:ignore WordPress.DB.SlowDBQuery
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);
	if ( $pages ) {
		$id = (int) $pages[0];
	} elseif ( $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page && 'publish' === $page->post_status ) {
			$id = (int) $page->ID;
		}
	}
	$cache[ $key ] = $id;
	return $id;
}

/**
 * Zamienia tokeny linków na adresy URL.
 *
 * Obsługiwane tokeny: {kontakt}, {uslugi}, {o-nas}, {faq}, {aktualnosci}, {tel}, {email}, {usluga:slug}.
 *
 * @param string $value Adres lub token.
 * @return string
 */
function bzl_url( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	$on_front = is_front_page();

	switch ( $value ) {
		case '{kontakt}':
			if ( $on_front ) {
				return '#kontakt';
			}
			$id = bzl_find_page( 'page-templates/template-contact.php', 'kontakt' );
			return $id ? get_permalink( $id ) : home_url( '/#kontakt' );
		case '{uslugi}':
			if ( $on_front ) {
				return '#uslugi';
			}
			return (string) get_post_type_archive_link( 'bzl_service' );
		case '{o-nas}':
			$id = bzl_find_page( 'page-templates/template-about.php', 'o-nas' );
			return $id ? get_permalink( $id ) : home_url( '/#o-nas' );
		case '{faq}':
			$id = bzl_find_page( 'page-templates/template-faq.php', 'faq' );
			return $id ? get_permalink( $id ) : home_url( '/#faq' );
		case '{aktualnosci}':
			$id = (int) get_option( 'page_for_posts' );
			return $id ? get_permalink( $id ) : home_url( '/' );
		case '{tel}':
			return bzl_tel_href( bzl_mod( 'phone' ) );
		case '{email}':
			return 'mailto:' . sanitize_email( (string) bzl_mod( 'email' ) );
	}

	if ( preg_match( '/^\{usluga:([a-z0-9\-]+)\}$/', $value, $m ) ) {
		$service = get_page_by_path( $m[1], OBJECT, 'bzl_service' );
		if ( $service && 'publish' === $service->post_status ) {
			return get_permalink( $service );
		}
		return (string) get_post_type_archive_link( 'bzl_service' );
	}

	return $value;
}

/**
 * Link tel: z numeru telefonu.
 *
 * @param string $phone Numer.
 * @return string
 */
function bzl_tel_href( $phone ) {
	$clean = preg_replace( '/[^0-9+]/', '', (string) $phone );
	return $clean ? 'tel:' . $clean : '';
}

/**
 * Tytuł z wyróżnieniem: tekst w *gwiazdkach* staje się <em>.
 *
 * @param string $text Tekst.
 * @return string Bezpieczny HTML.
 */
function bzl_rich_title( $text ) {
	$safe = esc_html( (string) $text );
	return preg_replace( '/\*([^*]+)\*/u', '<em>$1</em>', $safe );
}

/**
 * Tekst wieloakapitowy (pusta linia = nowy akapit) jako bezpieczny HTML.
 *
 * @param string $text Tekst.
 * @return string
 */
function bzl_paragraphs( $text ) {
	$text = trim( (string) $text );
	if ( '' === $text ) {
		return '';
	}
	$parts = preg_split( '/\n\s*\n/', $text );
	$out   = '';
	foreach ( $parts as $p ) {
		$out .= '<p>' . nl2br( esc_html( trim( $p ) ) ) . '</p>';
	}
	return $out;
}

/**
 * Czy aktywna jest wtyczka SEO zarządzająca meta i schema.
 *
 * @return bool
 */
function bzl_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || class_exists( 'The_SEO_Framework\Load' );
}

/**
 * Adres firmy jako tablica.
 *
 * @return array
 */
function bzl_address() {
	return array(
		'street' => (string) bzl_mod( 'street' ),
		'postal' => (string) bzl_mod( 'postal_code' ),
		'city'   => (string) bzl_mod( 'city' ),
	);
}

/**
 * Link do wyznaczenia trasy w Google Maps.
 *
 * @return string
 */
function bzl_directions_url() {
	$a     = bzl_address();
	$query = trim( bzl_mod( 'company_name' ) . ', ' . $a['street'] . ', ' . $a['postal'] . ' ' . $a['city'] );
	return 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $query );
}

/**
 * Wyświetla logo: custom logo, URL z ustawień lub nazwa marki.
 *
 * @param string $context header|footer.
 */
function bzl_logo( $context = 'header' ) {
	$name   = (string) bzl_mod( 'brand_name' );
	$height = max( 24, min( 120, (int) bzl_mod( 'logo_height' ) ) );
	$src    = '';
	$w      = 0;
	$h      = 0;

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$img = wp_get_attachment_image_src( $logo_id, 'full' );
		if ( $img ) {
			$src = $img[0];
			$w   = (int) $img[1];
			$h   = (int) $img[2];
		}
	}
	if ( ! $src && bzl_mod( 'logo_url' ) ) {
		$src = (string) bzl_mod( 'logo_url' );
	}

	$tag   = ( 'header' === $context && is_front_page() ) ? 'p' : 'div';
	$class = 'brand brand--' . $context;

	echo '<' . $tag . ' class="' . esc_attr( $class ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<a class="brand__link" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
	if ( $src ) {
		// Szerokość wyliczona z proporcji (gdy znana), aby uniknąć przesunięć układu (CLS).
		$width_attr = ( $w > 1 && $h > 1 ) ? ' width="' . (int) round( $height * $w / $h ) . '"' : '';
		printf(
			// Gdy plik logo się nie wczyta, pokazujemy nazwę marki zamiast ikony uszkodzonego obrazka.
			'<img class="brand__img" src="%1$s" alt="%2$s" height="%3$d"%4$s decoding="async" style="height:%3$dpx" onerror="this.hidden=true;this.nextElementSibling.hidden=false">'
			. '<span class="brand__text" hidden>%5$s</span>',
			esc_url( $src ),
			esc_attr( $name ),
			(int) $height,
			$width_attr, // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( $name )
		);
	} else {
		echo '<span class="brand__text">' . esc_html( $name ) . '</span>';
		if ( bzl_mod( 'brand_descriptor' ) ) {
			echo '<span class="brand__sub">' . esc_html( bzl_mod( 'brand_descriptor' ) ) . '</span>';
		}
	}
	echo '</a></' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Lista profili społecznościowych.
 *
 * @return array
 */
function bzl_socials() {
	$map = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'whatsapp'  => 'WhatsApp',
		'google'    => __( 'Profil firmy w Google', 'bizlife' ),
		'linkedin'  => 'LinkedIn',
	);
	$out = array();
	foreach ( $map as $key => $label ) {
		$url = (string) bzl_mod( 'social_' . $key );
		if ( $url ) {
			$out[ $key ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}
	return $out;
}

/**
 * Wyświetla ikony social media.
 *
 * @param string $class Dodatkowa klasa.
 */
function bzl_social_links( $class = '' ) {
	$socials = bzl_socials();
	if ( ! $socials ) {
		return;
	}
	echo '<ul class="socials ' . esc_attr( $class ) . '">';
	foreach ( $socials as $key => $s ) {
		printf(
			'<li><a class="socials__link" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s<span class="screen-reader-text">%3$s %4$s</span></a></li>',
			esc_url( $s['url'] ),
			bzl_icon( $key ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( $s['label'] ),
			esc_html__( '(otwiera się w nowej karcie)', 'bizlife' )
		);
	}
	echo '</ul>';
}

/**
 * Przycisk/link w stylu motywu.
 *
 * @param string $label   Etykieta.
 * @param string $url     Adres lub token.
 * @param string $variant primary|secondary|ghost|dark|link.
 * @param string $icon    Ikona po etykiecie.
 * @return string
 */
function bzl_button( $label, $url, $variant = 'primary', $icon = '' ) {
	$href = bzl_url( $url );
	if ( '' === trim( (string) $label ) || '' === $href ) {
		return '';
	}
	$icon_html = $icon ? bzl_icon( $icon ) : '';
	if ( 'phone' === $icon && 0 === strpos( $href, 'tel:' ) ) {
		$icon_html = bzl_icon( 'phone' );
		return sprintf( '<a class="btn btn--%1$s btn--icon-start" href="%2$s">%3$s<span>%4$s</span></a>', esc_attr( $variant ), esc_url( $href, array( 'tel', 'http', 'https' ) ), $icon_html, esc_html( $label ) );
	}
	return sprintf(
		'<a class="btn btn--%1$s" href="%2$s"><span>%3$s</span>%4$s</a>',
		esc_attr( $variant ),
		esc_url( $href, array( 'http', 'https', 'tel', 'mailto' ) ),
		esc_html( $label ),
		$icon_html
	);
}

/**
 * Nagłówek sekcji (eyebrow + tytuł + lead).
 *
 * @param array $args eyebrow, title, lead, id, level, align, ghost.
 */
function bzl_section_header( $args ) {
	$args  = wp_parse_args(
		$args,
		array(
			'eyebrow' => '',
			'title'   => '',
			'lead'    => '',
			'id'      => '',
			'level'   => 2,
			'align'   => 'start',
			'ghost'   => '',
		)
	);
	$level = max( 1, min( 6, (int) $args['level'] ) );
	if ( '' === $args['title'] ) {
		return;
	}
	echo '<header class="section-head section-head--' . esc_attr( $args['align'] ) . '" data-reveal>';
	if ( $args['ghost'] && bzl_mod( 'ghost_words' ) ) {
		echo '<span class="ghost-word" aria-hidden="true">' . esc_html( $args['ghost'] ) . '</span>';
	}
	if ( $args['eyebrow'] ) {
		echo '<p class="eyebrow">' . esc_html( $args['eyebrow'] ) . '</p>';
	}
	printf(
		'<h%1$d class="section-title"%2$s>%3$s</h%1$d>',
		(int) $level,
		$args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : '',
		bzl_rich_title( $args['title'] ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
	if ( $args['lead'] ) {
		echo '<p class="section-lead">' . esc_html( $args['lead'] ) . '</p>';
	}
	echo '</header>';
}

/**
 * Klasa tonu sekcji.
 *
 * @param string $section Klucz sekcji.
 * @param string $fallback Domyślny ton.
 * @return string
 */
function bzl_tone( $section, $fallback = 'dark' ) {
	$tone = (string) bzl_mod( $section . '_tone' );
	if ( ! in_array( $tone, array( 'dark', 'darker', 'light' ), true ) ) {
		$tone = $fallback;
	}
	return 'tone-' . $tone;
}

/**
 * Szacowany czas czytania (minuty).
 *
 * @param int|null $post_id ID wpisu.
 * @return int
 */
function bzl_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() );
	$words   = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( (string) $content ) ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Obraz z ustawień (ID załącznika) z alternatywą.
 *
 * @param int    $id    ID załącznika.
 * @param string $size  Rozmiar.
 * @param array  $attr  Atrybuty.
 * @return string
 */
function bzl_image( $id, $size = 'large', $attr = array() ) {
	$id = (int) $id;
	if ( ! $id || ! wp_attachment_is_image( $id ) ) {
		return '';
	}
	return wp_get_attachment_image( $id, $size, false, $attr );
}

/**
 * Elegancki zastępnik obrazu (gdy zdjęcie nie zostało jeszcze dodane).
 *
 * @param string $label Etykieta dla administratora.
 * @return string
 */
function bzl_image_placeholder( $label = '' ) {
	$hint = '';
	if ( $label && current_user_can( 'edit_theme_options' ) ) {
		$hint = '<span class="media-placeholder__hint">' . esc_html( $label ) . '</span>';
	}
	return '<div class="media-placeholder" aria-hidden="true">' . bzl_icon( 'monogram' ) . $hint . '</div>';
}

/**
 * Zapytanie o usługi.
 *
 * @param int   $limit   Limit (-1 = wszystkie).
 * @param array $exclude ID do pominięcia.
 * @return WP_Query
 */
function bzl_services_query( $limit = -1, $exclude = array() ) {
	return new WP_Query(
		array(
			'post_type'      => 'bzl_service',
			'posts_per_page' => $limit,
			'post__not_in'   => array_map( 'intval', $exclude ),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Krótki opis usługi (zajawka lub skrócona treść).
 *
 * @param int $post_id ID.
 * @param int $words   Liczba słów.
 * @return string
 */
function bzl_service_excerpt( $post_id, $words = 22 ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	$text = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	return wp_trim_words( $text, $words, '…' );
}

/**
 * Lista linii z pola tekstowego (jedna pozycja na linię).
 *
 * @param string $text Tekst.
 * @return array
 */
function bzl_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
}

/**
 * Pobiera pytania FAQ.
 *
 * @param array $args limit, home (bool – wyróżnione na stronie głównej), topic (ID tematu).
 * @return WP_Post[]
 */
function bzl_faqs( $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'limit' => -1,
			'home'  => false,
			'topic' => 0,
		)
	);
	$query = array(
		'post_type'      => 'bzl_faq',
		'posts_per_page' => (int) $args['limit'],
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'ASC',
		),
		'no_found_rows'  => true,
	);
	if ( $args['topic'] ) {
		$query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'taxonomy' => 'bzl_faq_topic',
				'terms'    => (int) $args['topic'],
			),
		);
	}
	if ( $args['home'] ) {
		$flagged = get_posts(
			array_merge(
				$query,
				array(
					'meta_key'   => 'bzl_on_home', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value' => '1', // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			)
		);
		if ( $flagged ) {
			return $flagged;
		}
	}
	return get_posts( $query );
}

/**
 * Czy token linku prowadzi do istniejącej podstrony (a nie tylko do kotwicy na stronie głównej).
 *
 * Używane przy przyciskach „Poznaj nas”, „Wszystkie pytania”, „Wszystkie artykuły” – bez podstrony
 * przycisk wskazywałby na sekcję, w której sam się znajduje, więc go ukrywamy.
 *
 * @param string $token Token lub adres.
 * @return bool
 */
function bzl_link_has_page( $token ) {
	switch ( $token ) {
		case '{o-nas}':
			return (bool) bzl_find_page( 'page-templates/template-about.php', 'o-nas' );
		case '{faq}':
			return (bool) bzl_find_page( 'page-templates/template-faq.php', 'faq' );
		case '{aktualnosci}':
			return (bool) get_option( 'page_for_posts' );
	}
	$url = bzl_url( $token );
	return '' !== $url && 0 !== strpos( $url, '#' );
}
