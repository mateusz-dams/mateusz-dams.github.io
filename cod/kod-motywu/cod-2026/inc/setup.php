<?php
/**
 * Obsługa motywu, menu i typ wpisu „Oferta” (zgodny z dotychczasowym motywem).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'cod_setup' );
/**
 * Funkcje motywu.
 */
function cod_setup() {
	load_theme_textdomain( 'cod2026', COD_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_columns' => 3,
				'min_columns'     => 1,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	add_image_size( 'cod-card', 720, 720, true );
	add_image_size( 'cod-wide', 1280, 800, true );

	// Te same lokalizacje menu co w poprzednim motywie – przypisane menu zostają na miejscu.
	register_nav_menus(
		array(
			'header_menu'     => __( 'Menu główne', 'cod2026' ),
			'footer_menu'     => __( 'Menu w stopce', 'cod2026' ),
			'offer_menu'      => __( 'Oferta', 'cod2026' ),
			'categories_menu' => __( 'Obszary działania', 'cod2026' ),
			'shop_menu'       => __( 'Sklep', 'cod2026' ),
			'links_menu'      => __( 'Linki w stopce', 'cod2026' ),
			'blog_menu'       => __( 'Kategorie na blogu', 'cod2026' ),
			'category_menu'   => __( 'Kategorie w ofercie', 'cod2026' ),
			'products_menu'   => __( 'Kategorie w sklepie', 'cod2026' ),
		)
	);
}

add_action( 'init', 'cod_register_offer', 0 );
/**
 * Typ wpisu „Oferta” i taksonomia „Kategorie” – identyczne adresy jak dotąd (/oferta/, /kategoria-oferty/).
 */
function cod_register_offer() {
	register_post_type(
		'offer',
		array(
			'labels'        => array(
				'name'               => __( 'Oferta', 'cod2026' ),
				'singular_name'      => __( 'Usługa', 'cod2026' ),
				'add_new_item'       => __( 'Dodaj usługę', 'cod2026' ),
				'edit_item'          => __( 'Edytuj usługę', 'cod2026' ),
				'all_items'          => __( 'Wszystkie usługi', 'cod2026' ),
				'search_items'       => __( 'Szukaj usług', 'cod2026' ),
				'not_found'          => __( 'Brak usług', 'cod2026' ),
				'menu_name'          => __( 'Oferta', 'cod2026' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'oferta' ),
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'menu_icon'     => 'dashicons-list-view',
			'menu_position' => 21,
		)
	);
	register_taxonomy(
		'offer_cat',
		'offer',
		array(
			'label'             => __( 'Kategorie', 'cod2026' ),
			'rewrite'           => array( 'slug' => 'kategoria-oferty' ),
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
		)
	);
}

add_action( 'login_enqueue_scripts', 'cod_login_logo' );
/**
 * Logo na stronie logowania.
 */
function cod_login_logo() {
	$logo = cod_option( 'header_logo', '' );
	$url  = is_array( $logo ) && ! empty( $logo['url'] ) ? $logo['url'] : cod_asset( 'img/cod-logo.png' );
	printf(
		'<style>#login h1 a,.login h1 a{background-image:url(%s);width:192px;height:96px;background-size:192px 96px;background-repeat:no-repeat;margin-bottom:32px}</style>',
		esc_url( $url )
	);
}

add_filter( 'login_headerurl', 'cod_login_url' );
/**
 * Logo logowania prowadzi na stronę główną.
 */
function cod_login_url() {
	return home_url( '/' );
}

add_filter( 'excerpt_length', 'cod_excerpt_length' );
/**
 * Krótsze zajawki.
 */
function cod_excerpt_length() {
	return 26;
}

add_filter( 'excerpt_more', 'cod_excerpt_more' );
/**
 * Bez „[…]”.
 */
function cod_excerpt_more() {
	return '…';
}

add_filter( 'nav_menu_css_class', 'cod_menu_current', 10, 2 );
/**
 * Podświetlenie pozycji menu dla sekcji (sklep, kursy, oferta, blog) bez sztywnych ID pozycji.
 *
 * @param array   $classes Klasy.
 * @param WP_Post $item    Pozycja menu.
 */
function cod_menu_current( $classes, $item ) {
	$url = untrailingslashit( (string) $item->url );
	if ( '' === $url ) {
		return $classes;
	}
	// Kotwice nie są „bieżące”.
	if ( 0 === strpos( $url, '#' ) || false !== strpos( $url, '/#' ) ) {
		return array_diff( $classes, array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_parent', 'current_page_ancestor' ) );
	}
	$match = function ( $target ) use ( $url ) {
		return $target && untrailingslashit( $target ) === $url;
	};
	$current = false;
	if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product() || is_product_category() || is_product_tag() ) ) {
		$current = $match( get_permalink( wc_get_page_id( 'shop' ) ) );
	} elseif ( is_singular( 'offer' ) || is_post_type_archive( 'offer' ) || is_tax( 'offer_cat' ) ) {
		$current = $match( get_post_type_archive_link( 'offer' ) );
	} elseif ( cod_is_learning_context() ) {
		$current = $match( get_post_type_archive_link( 'sfwd-courses' ) );
	} elseif ( is_singular( 'post' ) || is_category() || is_tag() ) {
		$blog    = (int) get_option( 'page_for_posts' );
		$current = $blog && $match( get_permalink( $blog ) );
	}
	if ( $current ) {
		$classes[] = 'current-menu-item';
	}
	return $classes;
}

add_filter( 'nav_menu_link_attributes', 'cod_menu_link_attrs', 10, 2 );
/**
 * aria-current dla bieżącej pozycji.
 *
 * @param array   $atts Atrybuty.
 * @param WP_Post $item Pozycja menu.
 */
function cod_menu_link_attrs( $atts, $item ) {
	if ( in_array( 'current-menu-item', (array) $item->classes, true ) ) {
		$atts['aria-current'] = 'page';
	}
	return $atts;
}

/**
 * Domyślne pozycje menu, gdy w lokalizacji „Menu główne” nie przypisano menu.
 */
function cod_default_menu_items() {
	$items = array(
		array( 'label' => __( 'Dla dzieci i rodziców', 'cod2026' ), 'url' => home_url( '/#dzieci' ) ),
		array( 'label' => __( 'Dla dorosłych', 'cod2026' ), 'url' => home_url( '/#dorosli' ) ),
		array( 'label' => __( 'Dla firm', 'cod2026' ), 'url' => home_url( '/#firmy' ) ),
		array( 'label' => __( 'Zespół', 'cod2026' ), 'url' => home_url( '/#zespol' ) ),
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$items[] = array( 'label' => __( 'Sklep', 'cod2026' ), 'url' => get_permalink( wc_get_page_id( 'shop' ) ) );
	}
	if ( post_type_exists( 'sfwd-courses' ) ) {
		$items[] = array( 'label' => __( 'Szkolenia', 'cod2026' ), 'url' => get_post_type_archive_link( 'sfwd-courses' ) );
	}
	$items[] = array( 'label' => __( 'Kontakt', 'cod2026' ), 'url' => home_url( '/#kontakt' ) );
	return $items;
}

/**
 * Wypisuje menu główne (z panelu albo domyślne).
 *
 * @param string $class Klasa listy.
 */
function cod_primary_menu( $class ) {
	if ( has_nav_menu( 'header_menu' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'header_menu',
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( cod_default_menu_items() as $item ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
	}
	echo '</ul>';
}

// Porządki w <head>, jak w poprzednim motywie.
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

add_action( 'wp_head', 'cod_fallback_icon', 99 );
/**
 * Ikona strony z motywu, gdy w Wygląd → Dostosuj nie ustawiono ikony witryny.
 */
function cod_fallback_icon() {
	if ( has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" href="%s" sizes="512x512">' . "\n", esc_url( cod_asset( 'img/cod-favicon.png' ) ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( cod_asset( 'img/cod-favicon.png' ) ) );
}

add_filter( 'body_class', 'cod_body_class' );
/**
 * Dodatkowe klasy body.
 *
 * @param array $classes Klasy.
 */
function cod_body_class( $classes ) {
	if ( cod_is_shop_context() ) {
		$classes[] = 'is-shop';
	}
	if ( cod_is_learning_context() ) {
		$classes[] = 'is-learning';
	}
	return $classes;
}

add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
