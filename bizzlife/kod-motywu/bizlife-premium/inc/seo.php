<?php
/**
 * SEO: meta description, Open Graph (gdy brak wtyczki SEO) i dane strukturalne.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Wypisuje blok JSON-LD.
 *
 * @param array $data Dane.
 */
function bzl_print_jsonld( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) . "</script>\n";
}

/**
 * Opis strony do meta description / OG.
 *
 * @return string
 */
function bzl_meta_description() {
	$desc = '';
	if ( is_front_page() ) {
		$desc = (string) bzl_mod( 'hero_lead' );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$desc = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	} elseif ( is_post_type_archive( 'bzl_service' ) ) {
		$desc = (string) bzl_mod( 'services_lead' );
	} elseif ( is_category() || is_tag() ) {
		$desc = wp_strip_all_tags( term_description() );
	} elseif ( is_home() ) {
		$desc = (string) bzl_mod( 'news_lead' );
	}
	if ( ! $desc ) {
		$desc = get_bloginfo( 'description' );
	}
	$desc = trim( preg_replace( '/\s+/', ' ', $desc ) );
	return mb_strlen( $desc ) > 160 ? rtrim( mb_substr( $desc, 0, 157 ) ) . '…' : $desc;
}

/**
 * Meta i Open Graph – wyłącznie gdy nie działa wtyczka SEO.
 */
function bzl_meta_tags() {
	if ( bzl_seo_plugin_active() || is_404() || is_search() ) {
		return;
	}
	$desc  = bzl_meta_description();
	$title = wp_get_document_title();
	$url   = bzl_current_url();
	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'bzl-wide' );
	}
	if ( ! $image && bzl_mod( 'hero_image' ) ) {
		$image = wp_get_attachment_image_url( (int) bzl_mod( 'hero_image' ), 'bzl-wide' );
	}

	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:locale" content="pl_PL">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	if ( is_singular( 'post' ) ) {
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'bzl_meta_tags', 5 );

/**
 * Dane strukturalne firmy (AccountingService) – strona główna i kontakt.
 */
function bzl_business_schema() {
	if ( ! bzl_mod( 'schema_enable' ) ) {
		return;
	}
	if ( ! is_front_page() && ! is_page_template( 'page-templates/template-contact.php' ) ) {
		return;
	}
	$a    = bzl_address();
	$data = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'AccountingService',
		'@id'        => home_url( '/#organization' ),
		'name'       => (string) bzl_mod( 'company_name' ),
		'url'        => home_url( '/' ),
		'telephone'  => preg_replace( '/[^0-9+]/', '', (string) bzl_mod( 'phone' ) ),
		'email'      => (string) bzl_mod( 'email' ),
		'address'    => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $a['street'],
			'postalCode'      => $a['postal'],
			'addressLocality' => $a['city'],
			'addressCountry'  => 'PL',
		),
		'areaServed' => $a['city'],
	);
	if ( bzl_mod( 'brand_name' ) ) {
		$data['alternateName'] = (string) bzl_mod( 'brand_name' );
	}
	if ( bzl_mod( 'geo_lat' ) && bzl_mod( 'geo_lng' ) ) {
		$data['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) bzl_mod( 'geo_lat' ),
			'longitude' => (float) bzl_mod( 'geo_lng' ),
		);
	}
	if ( bzl_mod( 'opening_hours_schema' ) ) {
		$data['openingHours'] = (string) bzl_mod( 'opening_hours_schema' );
	}
	if ( bzl_mod( 'nip' ) ) {
		$data['taxID'] = (string) bzl_mod( 'nip' );
	}
	$ids = array();
	if ( bzl_mod( 'krs' ) ) {
		$ids[] = array(
			'@type'      => 'PropertyValue',
			'propertyID' => 'KRS',
			'value'      => (string) bzl_mod( 'krs' ),
		);
	}
	if ( bzl_mod( 'regon' ) ) {
		$ids[] = array(
			'@type'      => 'PropertyValue',
			'propertyID' => 'REGON',
			'value'      => (string) bzl_mod( 'regon' ),
		);
	}
	if ( $ids ) {
		$data['identifier'] = $ids;
	}
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$logo    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : (string) bzl_mod( 'logo_url' );
	if ( $logo ) {
		$data['logo'] = $logo;
	}
	if ( bzl_mod( 'hero_image' ) ) {
		$data['image'] = wp_get_attachment_image_url( (int) bzl_mod( 'hero_image' ), 'large' );
	}
	$same = array();
	foreach ( bzl_socials() as $key => $s ) {
		if ( 'whatsapp' !== $key ) {
			$same[] = $s['url'];
		}
	}
	if ( $same ) {
		$data['sameAs'] = $same;
	}
	$services = bzl_services_query( 20 );
	if ( $services->have_posts() ) {
		$offers = array();
		foreach ( $services->posts as $s ) {
			$offers[] = array(
				'@type'       => 'Offer',
				'itemOffered' => array(
					'@type' => 'Service',
					'name'  => get_the_title( $s ),
					'url'   => get_permalink( $s ),
				),
			);
		}
		$data['hasOfferCatalog'] = array(
			'@type'           => 'OfferCatalog',
			'name'            => __( 'Usługi', 'bizlife' ),
			'itemListElement' => $offers,
		);
	}
	bzl_print_jsonld( apply_filters( 'bzl_business_schema', $data ) );
}
add_action( 'wp_head', 'bzl_business_schema', 20 );

/**
 * Dane strukturalne FAQPage (strona FAQ).
 *
 * @param WP_Post[] $faqs Pytania.
 */
function bzl_faq_schema( $faqs ) {
	if ( ! $faqs || ! bzl_mod( 'schema_enable' ) ) {
		return;
	}
	$entities = array();
	foreach ( $faqs as $f ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( get_the_title( $f ) ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( apply_filters( 'the_content', $f->post_content ) ),
			),
		);
	}
	bzl_print_jsonld(
		array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		)
	);
}

/**
 * Dane strukturalne artykułu (gdy brak wtyczki SEO).
 */
function bzl_article_schema() {
	if ( bzl_seo_plugin_active() || ! is_singular( 'post' ) ) {
		return;
	}
	$data = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => wp_strip_all_tags( get_the_title() ),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'mainEntityOfPage' => get_permalink(),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', (int) get_post_field( 'post_author' ) ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => (string) bzl_mod( 'company_name' ),
		),
	);
	if ( has_post_thumbnail() ) {
		$data['image'] = get_the_post_thumbnail_url( null, 'bzl-wide' );
	}
	bzl_print_jsonld( $data );
}
add_action( 'wp_head', 'bzl_article_schema', 20 );
