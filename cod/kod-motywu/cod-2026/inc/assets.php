<?php
/**
 * Style, skrypty i fonty.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wersja pliku na podstawie daty modyfikacji (odświeża cache po aktualizacji).
 *
 * @param string $rel Ścieżka względna w assets/.
 */
function cod_ver( $rel ) {
	$file = COD_DIR . '/assets/' . $rel;
	return file_exists( $file ) ? COD_VERSION . '.' . filemtime( $file ) : COD_VERSION;
}

add_action( 'wp_enqueue_scripts', 'cod_assets', 20 );
/**
 * Rejestracja zasobów.
 */
function cod_assets() {
	wp_enqueue_style( 'cod-main', cod_asset( 'css/main.css' ), array(), cod_ver( 'css/main.css' ) );

	if ( cod_is_shop_context() || ( class_exists( 'WooCommerce' ) && is_front_page() ) ) {
		wp_enqueue_style( 'cod-shop', cod_asset( 'css/shop.css' ), array( 'cod-main' ), cod_ver( 'css/shop.css' ) );
	}

	$learning = cod_is_learning_context();
	if ( ! $learning && is_singular() ) {
		$post = get_post();
		if ( $post && preg_match( '/\[(ld_|learndash|course_|student|visitor|course_content|ld_profile)/', $post->post_content ) ) {
			$learning = true;
		}
	}
	if ( $learning || ( class_exists( 'WooCommerce' ) && is_account_page() ) ) {
		wp_enqueue_style( 'cod-learn', cod_asset( 'css/learn.css' ), array( 'cod-main' ), cod_ver( 'css/learn.css' ) );
	}

	wp_enqueue_script(
		'cod-main',
		cod_asset( 'js/main.js' ),
		array(),
		cod_ver( 'js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	$days = cod_option( 'cod_hours_days', array( '1', '2', '3', '4', '5' ) );
	wp_localize_script(
		'cod-main',
		'COD',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'phone'     => cod_contact( 'phone_display' ),
			'email'     => cod_contact( 'email' ),
			'hours'     => array(
				'days' => array_map( 'intval', (array) $days ),
				'from' => (string) cod_option( 'cod_hours_from', '9:00' ),
				'to'   => (string) cod_option( 'cod_hours_to', '20:00' ),
				'text' => cod_contact( 'hours' ),
			),
			'mapEmbed'  => 'https://www.google.com/maps?q=' . rawurlencode( cod_contact( 'company' ) . ', ' . cod_contact( 'address' ) ) . '&output=embed',
			'i18n'      => array(
				/* translators: %s: godziny kontaktu. */
				'open'       => __( 'Teraz w godzinach kontaktu (%s)', 'cod2026' ),
				/* translators: %s: godziny kontaktu. */
				'closed'     => __( 'Teraz poza godzinami kontaktu (%s) – zostaw wiadomość', 'cod2026' ),
				'sending'    => __( 'Wysyłanie…', 'cod2026' ),
				'send'       => __( 'Wyślij wiadomość', 'cod2026' ),
				'sent'       => __( 'Dziękujemy, wiadomość dotarła. Odezwiemy się telefonicznie lub e-mailem.', 'cod2026' ),
				/* translators: %s: numer telefonu. */
				'error'      => sprintf( __( 'Nie udało się wysłać wiadomości. Spróbuj ponownie za chwilę albo zadzwoń: %s.', 'cod2026' ), cod_contact( 'phone_display' ) ),
				'errName'    => __( 'Wpisz imię, żebyśmy wiedzieli, jak się do Ciebie zwracać.', 'cod2026' ),
				'errContact' => __( 'Podaj telefon lub e-mail.', 'cod2026' ),
				'errPhone'   => __( 'Numer telefonu powinien mieć co najmniej 9 cyfr.', 'cod2026' ),
				'errEmail'   => __( 'Sprawdź adres e-mail – brakuje w nim znaku @ lub domeny.', 'cod2026' ),
				'errConsent' => __( 'Zaznacz zgodę, abyśmy mogli odpowiedzieć na wiadomość.', 'cod2026' ),
				'menuOpen'   => __( 'Otwórz menu', 'cod2026' ),
				'menuClose'  => __( 'Zamknij menu', 'cod2026' ),
				'mapTitle'   => __( 'Mapa dojazdu', 'cod2026' ),
			),
		)
	);

	// Bloki WooCommerce ładują style na każdej stronie – zostawiamy je tylko tam, gdzie są potrzebne.
	if ( class_exists( 'WooCommerce' ) && ! cod_is_shop_context() && ! is_front_page() ) {
		$post = is_singular() ? get_post() : null;
		if ( ! $post || ! has_blocks( $post ) || false === strpos( $post->post_content, 'woocommerce/' ) ) {
			wp_dequeue_style( 'wc-blocks-style' );
		}
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_head', 'cod_preload_fonts', 2 );
/**
 * Wczesne wczytanie fontów (z serwera strony – bez Google Fonts).
 */
function cod_preload_fonts() {
	foreach ( array( 'manrope-var.woff', 'fraunces-var.woff' ) as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff" crossorigin>' . "\n", esc_url( cod_asset( 'fonts/' . $font ) ) );
	}
}

add_filter( 'woocommerce_enqueue_styles', 'cod_woo_styles' );
/**
 * Wyłączamy ogólny arkusz WooCommerce (zastępuje go assets/css/shop.css). Układ i wersja mobilna zostają.
 *
 * @param array $styles Style.
 */
function cod_woo_styles( $styles ) {
	unset( $styles['woocommerce-general'] );
	return $styles;
}
