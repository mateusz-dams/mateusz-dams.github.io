<?php
/**
 * Okruszki (breadcrumbs). Korzysta z Yoast SEO / Rank Math, jeśli są aktywne.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Buduje listę okruszków.
 *
 * @return array Lista [label, url].
 */
function bzl_breadcrumb_items() {
	$items = array( array( __( 'Strona główna', 'bizlife' ), home_url( '/' ) ) );

	$blog_id = (int) get_option( 'page_for_posts' );
	$blog    = $blog_id ? array( get_the_title( $blog_id ), get_permalink( $blog_id ) ) : null;

	if ( is_singular( 'bzl_service' ) ) {
		$items[] = array( __( 'Usługi', 'bizlife' ), get_post_type_archive_link( 'bzl_service' ) );
		$items[] = array( get_the_title(), '' );
	} elseif ( is_post_type_archive( 'bzl_service' ) ) {
		$items[] = array( __( 'Usługi', 'bizlife' ), '' );
	} elseif ( is_singular( 'post' ) ) {
		if ( $blog ) {
			$items[] = $blog;
		}
		$cats = get_the_category();
		if ( $cats ) {
			$items[] = array( $cats[0]->name, get_category_link( $cats[0] ) );
		}
		$items[] = array( get_the_title(), '' );
	} elseif ( is_page() ) {
		$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
		foreach ( $ancestors as $a ) {
			$items[] = array( get_the_title( $a ), get_permalink( $a ) );
		}
		$items[] = array( get_the_title(), '' );
	} elseif ( is_home() ) {
		$items[] = array( $blog ? $blog[0] : __( 'Aktualności', 'bizlife' ), '' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		if ( $blog && ( is_category() || is_tag() ) ) {
			$items[] = $blog;
		}
		$items[] = array( single_term_title( '', false ), '' );
	} elseif ( is_search() ) {
		/* translators: %s: fraza. */
		$items[] = array( sprintf( __( 'Wyniki wyszukiwania: %s', 'bizlife' ), get_search_query() ), '' );
	} elseif ( is_404() ) {
		$items[] = array( __( 'Nie znaleziono strony', 'bizlife' ), '' );
	} elseif ( is_archive() ) {
		if ( $blog ) {
			$items[] = $blog;
		}
		$items[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
	}

	return apply_filters( 'bzl_breadcrumb_items', $items );
}

/**
 * Wyświetla okruszki.
 */
function bzl_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	if ( function_exists( 'rank_math_the_breadcrumbs' ) && class_exists( '\RankMath\Helper' ) && \RankMath\Helper::is_breadcrumbs_enabled() ) {
		echo '<nav class="breadcrumbs breadcrumbs--plugin" aria-label="' . esc_attr__( 'Okruszki', 'bizlife' ) . '">';
		rank_math_the_breadcrumbs();
		echo '</nav>';
		return;
	}
	if ( function_exists( 'yoast_breadcrumb' ) && class_exists( 'WPSEO_Options' ) && WPSEO_Options::get( 'breadcrumbs-enable' ) ) {
		yoast_breadcrumb( '<nav class="breadcrumbs breadcrumbs--plugin" aria-label="' . esc_attr__( 'Okruszki', 'bizlife' ) . '">', '</nav>' );
		return;
	}

	$items = bzl_breadcrumb_items();
	$count = count( $items );
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Okruszki', 'bizlife' ) . '"><ol>';
	foreach ( $items as $i => $item ) {
		$is_last = ( $i === $count - 1 );
		echo '<li>';
		if ( $item[1] && ! $is_last ) {
			echo '<a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a>';
		} else {
			echo '<span aria-current="page">' . esc_html( $item[0] ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';

	// Dane strukturalne okruszków – tylko gdy nie robi tego wtyczka SEO.
	if ( ! bzl_seo_plugin_active() ) {
		$list = array();
		foreach ( $items as $i => $item ) {
			$entry = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $item[0] ),
			);
			$url   = $item[1] ? $item[1] : ( $i === $count - 1 ? bzl_current_url() : '' );
			if ( $url ) {
				$entry['item'] = $url;
			}
			$list[] = $entry;
		}
		bzl_print_jsonld(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $list,
			)
		);
	}
}

/**
 * Bieżący adres URL (kanoniczny dla wpisów).
 *
 * @return string
 */
function bzl_current_url() {
	if ( is_singular() ) {
		return (string) get_permalink();
	}
	global $wp;
	return home_url( add_query_arg( array(), $wp->request ? trailingslashit( $wp->request ) : '' ) );
}
