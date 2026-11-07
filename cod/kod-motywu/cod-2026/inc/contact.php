<?php
/**
 * Formularz kontaktowy motywu.
 *
 * Wysyła e-mail funkcją wp_mail(), więc korzysta z wtyczki SMTP skonfigurowanej na stronie.
 * Ochrona: nonce, ukryte pole (honeypot), minimalny czas wypełnienia, limit wysyłek z jednego adresu IP.
 * Dane nie są zapisywane w bazie.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_cod_contact', 'cod_contact_submit' );
add_action( 'wp_ajax_nopriv_cod_contact', 'cod_contact_submit' );
add_action( 'admin_post_cod_contact', 'cod_contact_submit' );
add_action( 'admin_post_nopriv_cod_contact', 'cod_contact_submit' );

/**
 * Odpowiedź – JSON dla JS albo przekierowanie dla wersji bez JS.
 *
 * @param bool   $ok      Sukces.
 * @param string $message Komunikat.
 * @param array  $errors  Błędy pól.
 */
function cod_contact_respond( $ok, $message = '', $errors = array() ) {
	if ( wp_doing_ajax() ) {
		if ( $ok ) {
			wp_send_json_success( array( 'message' => $message ) );
		}
		wp_send_json_error( array( 'message' => $message, 'errors' => $errors ), 400 );
	}
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( 'kontakt', $back );
	$back = preg_replace( '/#.*$/', '', $back );
	wp_safe_redirect( add_query_arg( 'kontakt', $ok ? 'wyslano' : 'blad', $back ) . '#kontakt' );
	exit;
}

/**
 * Obsługa wysyłki.
 */
function cod_contact_submit() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- weryfikacja poniżej.
	if ( ! isset( $_POST['cod_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cod_nonce'] ) ), 'cod_contact' ) ) {
		cod_contact_respond( false, __( 'Sesja formularza wygasła. Odśwież stronę i spróbuj ponownie.', 'cod2026' ) );
	}
	// Honeypot i zbyt szybkie wysłanie – udajemy sukces, nic nie wysyłamy.
	$hp = isset( $_POST['www'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['www'] ) ) ) : '';
	$ts = isset( $_POST['ts'] ) ? (int) $_POST['ts'] : 0;
	if ( '' !== $hp || ( $ts && time() - $ts < 3 ) ) {
		cod_contact_respond( true, '' );
	}

	$ip     = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$ip_key = 'cod_cf_' . md5( $ip );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 6 ) {
		cod_contact_respond( false, __( 'Wysłano już kilka wiadomości. Spróbuj ponownie za godzinę albo zadzwoń.', 'cod2026' ) );
	}

	$f = array(
		'dotyczy'   => isset( $_POST['dotyczy'] ) ? sanitize_text_field( wp_unslash( $_POST['dotyczy'] ) ) : '',
		'usluga'    => isset( $_POST['usluga'] ) ? sanitize_text_field( wp_unslash( $_POST['usluga'] ) ) : '',
		'imie'      => isset( $_POST['imie'] ) ? sanitize_text_field( wp_unslash( $_POST['imie'] ) ) : '',
		'telefon'   => isset( $_POST['telefon'] ) ? sanitize_text_field( wp_unslash( $_POST['telefon'] ) ) : '',
		'email'     => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
		'kiedy'     => isset( $_POST['kiedy'] ) ? sanitize_text_field( wp_unslash( $_POST['kiedy'] ) ) : '',
		'wiadomosc' => isset( $_POST['wiadomosc'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wiadomosc'] ) ) : '',
		'zgoda'     => ! empty( $_POST['zgoda'] ),
		'strona'    => isset( $_POST['strona'] ) ? esc_url_raw( wp_unslash( $_POST['strona'] ) ) : '',
	);
	// phpcs:enable

	$errors = array();
	if ( '' === $f['imie'] ) {
		$errors['imie'] = __( 'Wpisz imię.', 'cod2026' );
	}
	$digits = preg_replace( '/\D/', '', $f['telefon'] );
	if ( '' === $f['telefon'] && '' === $f['email'] ) {
		$errors['telefon'] = __( 'Podaj telefon lub e-mail.', 'cod2026' );
	} elseif ( '' !== $f['telefon'] && strlen( $digits ) < 9 ) {
		$errors['telefon'] = __( 'Numer telefonu powinien mieć co najmniej 9 cyfr.', 'cod2026' );
	}
	if ( isset( $_POST['email'] ) && '' !== trim( (string) wp_unslash( $_POST['email'] ) ) && ! is_email( $f['email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$errors['email'] = __( 'Sprawdź adres e-mail.', 'cod2026' );
	}
	if ( ! $f['zgoda'] ) {
		$errors['zgoda'] = __( 'Zaznacz zgodę.', 'cod2026' );
	}
	if ( $errors ) {
		cod_contact_respond( false, __( 'Popraw zaznaczone pola.', 'cod2026' ), $errors );
	}

	$to = sanitize_email( (string) cod_option( 'cod_form_to', '' ) );
	if ( ! $to ) {
		$to = cod_contact( 'email' ) ? cod_contact( 'email' ) : get_option( 'admin_email' );
	}
	$topic   = '' !== $f['usluga'] ? $f['usluga'] : ( '' !== $f['dotyczy'] ? $f['dotyczy'] : __( 'zapytanie', 'cod2026' ) );
	/* translators: %s: temat. */
	$subject = sprintf( __( 'Zapytanie ze strony – %s', 'cod2026' ), $topic );
	$lines   = array(
		__( 'Kogo dotyczy sprawa', 'cod2026' ) . ': ' . ( $f['dotyczy'] ? $f['dotyczy'] : '—' ),
		__( 'Usługa', 'cod2026' ) . ': ' . ( $f['usluga'] ? $f['usluga'] : __( 'nie wybrano', 'cod2026' ) ),
		__( 'Imię', 'cod2026' ) . ': ' . $f['imie'],
		__( 'Telefon', 'cod2026' ) . ': ' . ( $f['telefon'] ? $f['telefon'] : '—' ),
		__( 'E-mail', 'cod2026' ) . ': ' . ( $f['email'] ? $f['email'] : '—' ),
		__( 'Preferowany czas kontaktu', 'cod2026' ) . ': ' . ( $f['kiedy'] ? $f['kiedy'] : __( 'bez znaczenia', 'cod2026' ) ),
		'',
		$f['wiadomosc'] ? $f['wiadomosc'] : __( '(bez wiadomości)', 'cod2026' ),
		'',
		'—',
		__( 'Wysłano ze strony', 'cod2026' ) . ': ' . ( $f['strona'] ? $f['strona'] : home_url( '/' ) ),
		__( 'Zgoda na przetwarzanie danych w celu odpowiedzi: tak', 'cod2026' ),
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( $f['email'] ) {
		$headers[] = 'Reply-To: ' . str_replace( array( "\r", "\n", '<', '>' ), '', $f['imie'] ) . ' <' . $f['email'] . '>';
	}

	$sent = wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
	set_transient( $ip_key, $count + 1, HOUR_IN_SECONDS );

	if ( ! $sent ) {
		cod_contact_respond( false, '' );
	}
	cod_contact_respond( true, __( 'Dziękujemy, wiadomość dotarła. Odezwiemy się telefonicznie lub e-mailem.', 'cod2026' ) );
}

/**
 * Formularz na stronie: Contact Form 7 (jeśli wybrano w opcjach) albo formularz motywu.
 *
 * @param string $shortcode Wymuszony shortcode (np. z sekcji „Kontakt”).
 */
function cod_contact_form( $shortcode = '' ) {
	$mode = (string) cod_option( 'cod_form_mode', 'theme' );
	if ( '' === $shortcode && 'cf7' === $mode ) {
		$shortcode = (string) cod_option( 'cod_form_shortcode', '' );
	}
	if ( '' !== trim( $shortcode ) ) {
		echo '<div class="form form--cf7 on-light">' . do_shortcode( $shortcode ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	get_template_part( 'template-parts/contact-form' );
}
