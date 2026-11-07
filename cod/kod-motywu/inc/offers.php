<?php
/**
 * Usługi (typ wpisu „Oferta”): dane do kart, zakładek, kreatora i panelu „Szczegóły”.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalizacja tekstu do porównań (małe litery, bez polskich znaków).
 *
 * @param string $text Tekst.
 */
function cod_norm( $text ) {
	$text = function_exists( 'mb_strtolower' ) ? mb_strtolower( (string) $text, 'UTF-8' ) : strtolower( (string) $text );
	return remove_accents( $text );
}

/**
 * Grupa odbiorców usługi: z pola, z kategorii albo z treści projektu.
 *
 * @param int $post_id ID usługi.
 */
function cod_offer_audience( $post_id ) {
	$aud = (string) cod_field( 'cod_audience', $post_id, '' );
	if ( '' !== $aud ) {
		return $aud;
	}
	$terms = get_the_terms( $post_id, 'offer_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$name = cod_norm( $term->name . ' ' . $term->slug );
			foreach ( cod_audiences() as $key => $a ) {
				foreach ( $a['match'] as $needle ) {
					if ( false !== strpos( $name, remove_accents( $needle ) ) ) {
						return $key;
					}
				}
			}
		}
	}
	$key = (string) cod_field( 'cod_key', $post_id, '' );
	if ( '' !== $key ) {
		$data = include COD_DIR . '/inc/data/services.php';
		if ( isset( $data[ $key ] ) ) {
			return $data[ $key ]['audience'];
		}
	}
	return 'dorosli';
}

/**
 * Dane jednej usługi do wyświetlenia.
 *
 * @param WP_Post|int $post Usługa.
 */
function cod_offer( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}
	$id      = $post->ID;
	$price   = cod_price_clean( cod_field( 'offer_price', $id, '' ) );
	$short   = (string) cod_field( 'cod_short', $id, '' );
	$details = array();
	if ( cod_has_acf() && have_rows( 'cod_details', $id ) ) {
		while ( have_rows( 'cod_details', $id ) ) {
			the_row();
			$h = (string) get_sub_field( 'heading' );
			$x = (string) get_sub_field( 'text' );
			if ( '' !== trim( $h ) || '' !== trim( wp_strip_all_tags( $x ) ) ) {
				$details[] = array( 'heading' => $h, 'text' => $x );
			}
		}
	}
	if ( '' === $short ) {
		$short = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 24, '…' );
	}
	$title = get_the_title( $id );
	return array(
		'id'         => $id,
		'key'        => 'svc-' . $id,
		'title'      => $title,
		'short_name' => (string) cod_field( 'cod_short_name', $id, $title ),
		'label'      => (string) cod_field( 'cod_label', $id, '' ),
		'short'      => cod_snippet( $short, 400 ),
		'card_text'  => cod_snippet( $short, 115 ),
		'content'    => $post->post_content,
		'price'      => '' !== $price ? $price : __( 'Wycena indywidualna', 'cod2026' ),
		'price_text' => '' === $price || ! preg_match( '/\d/', $price ),
		'featured'   => (bool) cod_field( 'cod_featured', $id, false ),
		'bullets'    => cod_lines( cod_field( 'cod_bullets', $id, '' ) ),
		'cta'        => (string) cod_field( 'cod_cta', $id, 'phone' ),
		'details'    => $details,
		'wizard_q'   => (string) cod_field( 'cod_wizard_q', $id, '' ),
		'wizard_a'   => (string) cod_field( 'cod_wizard_a', $id, '' ),
		'audience'   => cod_offer_audience( $id ),
		'url'        => get_permalink( $id ),
		'thumb'      => get_post_thumbnail_id( $id ),
	);
}

/**
 * Wszystkie opublikowane usługi, pogrupowane według odbiorców, w kolejności z panelu.
 *
 * @return array audience => lista usług.
 */
function cod_offers_grouped() {
	static $groups = null;
	if ( null !== $groups ) {
		return $groups;
	}
	$groups = array();
	foreach ( array_keys( cod_audiences() ) as $aud ) {
		$groups[ $aud ] = array();
	}
	$posts = get_posts(
		array(
			'post_type'        => 'offer',
			'post_status'      => 'publish',
			'numberposts'      => 60,
			'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'suppress_filters' => false,
		)
	);
	foreach ( $posts as $p ) {
		$o = cod_offer( $p );
		if ( ! isset( $groups[ $o['audience'] ] ) ) {
			$groups[ $o['audience'] ] = array();
		}
		$groups[ $o['audience'] ][] = $o;
	}
	// Wyróżniona usługa na początek grupy.
	foreach ( $groups as $aud => $list ) {
		usort(
			$list,
			function ( $a, $b ) {
				return (int) $b['featured'] - (int) $a['featured'];
			}
		);
		$groups[ $aud ] = $list;
	}
	return $groups;
}

/**
 * Dane kreatora „Od czego zacząć?” w formacie JSON dla skryptu.
 */
function cod_wizard_data() {
	$out = array();
	foreach ( cod_offers_grouped() as $aud => $list ) {
		$opts = array();
		foreach ( $list as $o ) {
			if ( '' === trim( $o['wizard_q'] ) ) {
				continue;
			}
			$opts[] = array(
				'q'       => $o['wizard_q'],
				'a'       => '' !== trim( $o['wizard_a'] ) ? $o['wizard_a'] : $o['short'],
				'svc'     => $o['key'],
				'title'   => $o['title'],
				'price'   => $o['price'],
				'cta'     => $o['cta'],
				'url'     => $o['url'],
			);
		}
		// Gdy w panelu nie uzupełniono kreatora – używamy tytułów usług.
		if ( ! $opts ) {
			foreach ( $list as $o ) {
				$opts[] = array(
					'q'     => $o['title'],
					'a'     => $o['short'],
					'svc'   => $o['key'],
					'title' => $o['title'],
					'price' => $o['price'],
					'cta'   => $o['cta'],
					'url'   => $o['url'],
				);
			}
		}
		$out[ $aud ] = $opts;
	}
	return $out;
}

/**
 * Wartość „Kogo dotyczy” w formularzu dla grupy odbiorców.
 *
 * @param string $aud Grupa.
 */
function cod_topic_for( $aud ) {
	$a = cod_audiences();
	return isset( $a[ $aud ] ) ? $a[ $aud ]['topic'] : '';
}

/**
 * Układ siatki kart (4 kolumny): ile kolumn zajmuje każda karta, żeby rzędy były zawsze pełne.
 *
 * @param int  $n    Liczba kart.
 * @param bool $lead Czy pierwsza karta jest wyróżniona (2×2).
 * @return array Lista: 'lead' | 'tall' | 1 | 2 | 4.
 */
function cod_bento_spans( $n, $lead ) {
	$spans = array();
	$rows  = function ( $m ) {
		$out = array_fill( 0, max( 0, $m - ( $m % 4 ) ), 1 );
		$r   = $m % 4;
		if ( 1 === $r ) {
			$out[] = 4;
		} elseif ( 2 === $r ) {
			$out[] = 2;
			$out[] = 2;
		} elseif ( 3 === $r ) {
			$out[] = 2;
			$out[] = 1;
			$out[] = 1;
		}
		return $out;
	};
	if ( $lead && $n > 0 ) {
		$spans[] = 'lead';
		$rest    = $n - 1;
		if ( 1 === $rest ) {
			$spans[] = 'tall';
		} elseif ( 2 === $rest ) {
			$spans[] = 2;
			$spans[] = 2;
		} elseif ( $rest >= 3 ) {
			$spans = array_merge( $spans, array( 2, 1, 1 ), $rows( $rest - 3 ) );
		}
		return $spans;
	}
	return $rows( $n );
}

/**
 * Zespół: z Opcje motywu → COD 2026 → Zespół, a gdy puste – osoby z obecnej strony.
 *
 * @return array Lista: name, role, scope, photo (tablica ACF lub nazwa pliku w assets/img).
 */
function cod_team() {
	$rows = cod_option( 'cod_team', array() );
	$out  = array();
	if ( is_array( $rows ) ) {
		foreach ( $rows as $r ) {
			if ( empty( $r['name'] ) ) {
				continue;
			}
			$out[] = array(
				'name'  => $r['name'],
				'role'  => isset( $r['role'] ) ? $r['role'] : '',
				'scope' => isset( $r['scope'] ) ? $r['scope'] : '',
				'bio'   => isset( $r['bio'] ) ? $r['bio'] : '',
				'photo' => isset( $r['photo'] ) ? $r['photo'] : '',
			);
		}
	}
	if ( $out ) {
		return $out;
	}
	// Bez listy w ustawieniach: oryginalne zdjęcia i opisy z dotychczasowej sekcji „Zespół” (pełna jakość z biblioteki mediów).
	if ( function_exists( 'cod_importer_team_rows' ) ) {
		$old = cod_importer_team_rows();
		if ( $old ) {
			return $old;
		}
	}
	return cod_default_team();
}

/**
 * Wszystkie opinie do slidera na stronie głównej.
 * Kolejność: opinie wpisane na stronie głównej (COD 2026); gdy pusto – opinie z sekcji „Opinie” na wszystkich stronach
 * oraz opinia z projektu. Bez duplikatów. Wynik w pamięci podręcznej (odświeża się po zapisaniu strony).
 *
 * @return array Lista: quote, name, role, photo.
 */
function cod_all_reviews() {
	$front = (int) get_option( 'page_on_front' );
	$own   = cod_field( 'cod_reviews', $front ? $front : false, array() );
	if ( is_array( $own ) && $own ) {
		return $own;
	}
	$cached = get_transient( 'cod_all_reviews' );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$out  = array();
	$seen = array();
	$add  = function ( $r ) use ( &$out, &$seen ) {
		$key = md5( mb_strtolower( preg_replace( '/\W+/u', '', wp_strip_all_tags( (string) $r['quote'] ) ) ) );
		if ( '' === trim( wp_strip_all_tags( (string) $r['quote'] ) ) || isset( $seen[ $key ] ) ) {
			return;
		}
		$seen[ $key ] = true;
		$out[]        = $r;
	};
	if ( cod_has_acf() ) {
		$pages = get_posts(
			array(
				'post_type'   => array( 'page', 'post', 'offer' ),
				'post_status' => 'publish',
				'numberposts' => 200,
				'fields'      => 'ids',
				'meta_query'  => array( array( 'key' => 'sections', 'compare' => 'EXISTS' ) ),
			)
		);
		foreach ( $pages as $pid ) {
			$sections = get_field( 'sections', $pid );
			if ( ! is_array( $sections ) ) {
				continue;
			}
			foreach ( $sections as $sec ) {
				if ( empty( $sec['acf_fc_layout'] ) || 'reviews_section' !== $sec['acf_fc_layout'] || empty( $sec['reviews'] ) ) {
					continue;
				}
				foreach ( $sec['reviews'] as $r ) {
					$add(
						array(
							'quote' => isset( $r['reviews_desc'] ) ? (string) $r['reviews_desc'] : '',
							'name'  => isset( $r['reviews_title'] ) ? wp_strip_all_tags( (string) $r['reviews_title'] ) : '',
							'role'  => isset( $r['reviews_subtitle'] ) ? wp_strip_all_tags( (string) $r['reviews_subtitle'] ) : '',
							'photo' => isset( $r['reviews_img'] ) ? $r['reviews_img'] : '',
						)
					);
				}
			}
		}
	}
	foreach ( cod_default_reviews() as $r ) {
		$add( $r );
	}
	set_transient( 'cod_all_reviews', $out, 12 * HOUR_IN_SECONDS );
	return $out;
}

add_action( 'save_post', 'cod_flush_reviews_cache' );
add_action( 'acf/save_post', 'cod_flush_reviews_cache' );
/**
 * Odświeżenie listy opinii po zapisaniu treści.
 */
function cod_flush_reviews_cache() {
	delete_transient( 'cod_all_reviews' );
}
