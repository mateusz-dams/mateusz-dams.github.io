<?php
/**
 * Style i skrypty.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Wersja pliku (data modyfikacji) dla cache-bustingu.
 *
 * @param string $rel Ścieżka względna.
 * @return string
 */
function bzl_asset_version( $rel ) {
	$path = BZL_DIR . '/' . $rel;
	return file_exists( $path ) ? BZL_VERSION . '.' . filemtime( $path ) : BZL_VERSION;
}

/**
 * Rejestracja zasobów frontu.
 */
function bzl_enqueue_assets() {
	wp_enqueue_style( 'bzl-fonts', BZL_URI . '/assets/css/fonts.css', array(), bzl_asset_version( 'assets/css/fonts.css' ) );
	wp_enqueue_style( 'bzl-main', BZL_URI . '/assets/css/main.css', array( 'bzl-fonts' ), bzl_asset_version( 'assets/css/main.css' ) );

	$accent = sanitize_hex_color( (string) bzl_mod( 'accent_color' ) );
	if ( $accent && strtolower( $accent ) !== '#b9945a' ) {
		wp_add_inline_style( 'bzl-main', ':root{--c-gold:' . $accent . ';}' );
	}

	wp_enqueue_script(
		'bzl-main',
		BZL_URI . '/assets/js/main.js',
		array(),
		bzl_asset_version( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'bzl-main',
		'bzlData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'i18n'    => array(
				'required'  => __( 'To pole jest wymagane.', 'bizlife' ),
				'email'     => __( 'Wpisz poprawny adres e-mail, np. jan@firma.pl.', 'bizlife' ),
				'phone'     => __( 'Wpisz poprawny numer telefonu (min. 9 cyfr).', 'bizlife' ),
				'contact'   => __( 'Podaj numer telefonu lub adres e-mail.', 'bizlife' ),
				'consent'   => __( 'Zaznacz zgodę, abyśmy mogli odpowiedzieć na wiadomość.', 'bizlife' ),
				'sending'   => __( 'Wysyłanie…', 'bizlife' ),
				'error'     => __( 'Nie udało się wysłać wiadomości. Spróbuj ponownie lub zadzwoń do nas.', 'bizlife' ),
				'fixErrors' => __( 'Popraw zaznaczone pola formularza.', 'bizlife' ),
				'copied'    => __( 'Link skopiowany', 'bizlife' ),
				'menuOpen'  => __( 'Otwórz menu', 'bizlife' ),
				'menuClose' => __( 'Zamknij menu', 'bizlife' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bzl_enqueue_assets' );

/**
 * Preload głównego fontu (poprawia LCP i ogranicza przeskok tekstu).
 */
function bzl_preload_fonts() {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff" crossorigin>' . "\n",
		esc_url( BZL_URI . '/assets/fonts/manrope-var.woff' )
	);
	if ( bzl_mod( 'serif_headings' ) ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff" crossorigin>' . "\n",
			esc_url( BZL_URI . '/assets/fonts/ebgaramond-var.woff' )
		);
	}
}
add_action( 'wp_head', 'bzl_preload_fonts', 1 );

/**
 * Klasa „js” najwcześniej jak to możliwe (brak migania animacji).
 */
function bzl_js_class() {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'bzl_js_class', 0 );

/**
 * Kolor motywu przeglądarki (pasek adresu na mobile).
 */
function bzl_theme_color() {
	echo '<meta name="theme-color" content="#101010">' . "\n";
}
add_action( 'wp_head', 'bzl_theme_color', 2 );
