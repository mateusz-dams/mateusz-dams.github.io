<?php
/**
 * Formularz kontaktowy: render, walidacja, antyspam (honeypot, pułapka czasowa, nonce, limit wysyłek), wysyłka.
 *
 * Działa bez JavaScriptu (admin-post.php + przekierowanie) oraz z JavaScriptem (fetch + JSON).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Podpisany znacznik czasu (pułapka na boty wysyłające formularz natychmiast).
 *
 * @return string
 */
function bzl_form_timestamp() {
	$t = time();
	return $t . '.' . substr( hash_hmac( 'sha256', (string) $t, wp_salt( 'nonce' ) ), 0, 16 );
}

/**
 * Weryfikacja znacznika czasu.
 *
 * @param string $value Wartość.
 * @return bool
 */
function bzl_form_timestamp_valid( $value ) {
	$parts = explode( '.', (string) $value );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	$expected = substr( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), 0, 16 );
	if ( ! hash_equals( $expected, $parts[1] ) ) {
		return false;
	}
	$age = time() - (int) $parts[0];
	return $age >= 3 && $age <= 2 * DAY_IN_SECONDS;
}

/**
 * Lista usług do pola wyboru.
 *
 * @return array
 */
function bzl_form_service_options() {
	$options = array();
	$q       = bzl_services_query( 30 );
	foreach ( $q->posts as $p ) {
		$options[] = get_the_title( $p );
	}
	$options[] = __( 'Inna sprawa / jeszcze nie wiem', 'bizlife' );
	return $options;
}

/**
 * Renderuje formularz kontaktowy.
 *
 * @param array $args preselect – nazwa usługi zaznaczonej domyślnie.
 */
function bzl_contact_form( $args = array() ) {
	$shortcode = (string) bzl_mod( 'form_shortcode' );
	if ( $shortcode ) {
		echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput
		return;
	}

	$args     = wp_parse_args( $args, array( 'preselect' => '' ) );
	$status   = isset( $_GET['bzl_form'] ) ? sanitize_key( wp_unslash( $_GET['bzl_form'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$token    = isset( $_GET['bzl_t'] ) ? sanitize_key( wp_unslash( $_GET['bzl_t'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$stored   = $token ? get_transient( 'bzl_form_' . $token ) : false;
	$errors   = is_array( $stored ) ? (array) $stored['errors'] : array();
	$old      = is_array( $stored ) ? (array) $stored['values'] : array();
	$value    = static function ( $k ) use ( $old ) {
		return isset( $old[ $k ] ) ? (string) $old[ $k ] : '';
	};
	$privacy  = get_privacy_policy_url();
	$selected = $value( 'service' ) ? $value( 'service' ) : $args['preselect'];
	$err_attr = static function ( $field ) use ( $errors ) {
		return isset( $errors[ $field ] ) ? ' aria-invalid="true"' : '';
	};
	$err_msg  = static function ( $field ) use ( $errors ) {
		$msg = isset( $errors[ $field ] ) ? $errors[ $field ] : '';
		return '<p class="field__error" id="bzl-' . esc_attr( $field ) . '-error"' . ( $msg ? '' : ' hidden' ) . '>' . esc_html( $msg ) . '</p>';
	};
	$describe = static function ( $field, $extra = '' ) {
		return ' aria-describedby="' . esc_attr( trim( 'bzl-' . $field . '-error ' . $extra ) ) . '"';
	};
	?>
	<form class="contact-form" id="kontakt-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate data-contact-form>
		<input type="hidden" name="action" value="bzl_contact">
		<input type="hidden" name="bzl_nonce" value="<?php echo esc_attr( wp_create_nonce( 'bzl_contact' ) ); ?>">
		<input type="hidden" name="bzl_ts" value="<?php echo esc_attr( bzl_form_timestamp() ); ?>">
		<input type="hidden" name="bzl_ref" value="<?php echo esc_url( bzl_current_url() ); ?>">

		<div class="form-status" role="status" aria-live="polite" data-form-status>
			<?php if ( 'sent' === $status ) : ?>
				<p class="notice notice--success"><?php echo esc_html( bzl_mod( 'form_success' ) ); ?></p>
			<?php elseif ( 'error' === $status ) : ?>
				<p class="notice notice--error"><?php echo esc_html( $errors['_form'] ?? __( 'Popraw zaznaczone pola formularza.', 'bizlife' ) ); ?></p>
			<?php endif; ?>
		</div>

		<div class="form-grid">
			<div class="field">
				<label for="bzl-name"><?php esc_html_e( 'Imię', 'bizlife' ); ?> <span class="req" aria-hidden="true">*</span></label>
				<input type="text" id="bzl-name" name="bzl_name" autocomplete="given-name" maxlength="100" required value="<?php echo esc_attr( $value( 'name' ) ); ?>"<?php echo $err_attr( 'name' ) . $describe( 'name' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php echo $err_msg( 'name' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<div class="field">
				<label for="bzl-phone"><?php esc_html_e( 'Numer telefonu', 'bizlife' ); ?></label>
				<input type="tel" id="bzl-phone" name="bzl_phone" autocomplete="tel" inputmode="tel" maxlength="30" value="<?php echo esc_attr( $value( 'phone' ) ); ?>"<?php echo $err_attr( 'phone' ) . $describe( 'phone', 'bzl-contact-hint' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php echo $err_msg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<div class="field">
				<label for="bzl-email"><?php esc_html_e( 'E-mail', 'bizlife' ); ?></label>
				<input type="email" id="bzl-email" name="bzl_email" autocomplete="email" maxlength="120" value="<?php echo esc_attr( $value( 'email' ) ); ?>"<?php echo $err_attr( 'email' ) . $describe( 'email', 'bzl-contact-hint' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php echo $err_msg( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<div class="field">
				<label for="bzl-service"><?php esc_html_e( 'Rodzaj usługi', 'bizlife' ); ?></label>
				<div class="select-wrap">
					<select id="bzl-service" name="bzl_service">
						<option value=""><?php esc_html_e( 'Wybierz (opcjonalnie)', 'bizlife' ); ?></option>
						<?php foreach ( bzl_form_service_options() as $opt ) : ?>
							<option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $selected, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php echo bzl_icon( 'chevron-down' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
			<p class="field-hint field--full" id="bzl-contact-hint"><?php esc_html_e( 'Podaj telefon lub e-mail — wystarczy jedno z nich.', 'bizlife' ); ?></p>
			<div class="field field--full">
				<label for="bzl-message"><?php esc_html_e( 'Wiadomość', 'bizlife' ); ?> <span class="optional"><?php esc_html_e( '(opcjonalnie)', 'bizlife' ); ?></span></label>
				<textarea id="bzl-message" name="bzl_message" rows="5" maxlength="3000"<?php echo $err_attr( 'message' ) . $describe( 'message' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_textarea( $value( 'message' ) ); ?></textarea>
				<?php echo $err_msg( 'message' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>

			<div class="field field--hp" aria-hidden="true">
				<label for="bzl-website"><?php esc_html_e( 'Zostaw to pole puste', 'bizlife' ); ?></label>
				<input type="text" id="bzl-website" name="bzl_website" tabindex="-1" autocomplete="off" value="">
			</div>

			<div class="field field--full field--check">
				<input type="checkbox" id="bzl-consent" name="bzl_consent" value="1" required<?php echo $err_attr( 'consent' ) . $describe( 'consent' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<label for="bzl-consent">
					<?php echo esc_html( bzl_mod( 'form_consent' ) ); ?>
					<?php if ( $privacy ) : ?>
						<?php
						printf(
							/* translators: %s: link do polityki prywatności. */
							esc_html__( 'Zapoznałem/-am się z %s.', 'bizlife' ),
							'<a href="' . esc_url( $privacy ) . '">' . esc_html__( 'Polityką prywatności', 'bizlife' ) . '</a>'
						);
						?>
					<?php endif; ?>
					<span class="req" aria-hidden="true">*</span>
				</label>
				<?php echo $err_msg( 'consent' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>

		<button class="btn btn--primary btn--block" type="submit" data-submit>
			<span data-label><?php echo esc_html( bzl_mod( 'form_submit_label' ) ); ?></span>
			<?php echo bzl_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>

		<?php if ( bzl_mod( 'form_gdpr_note' ) ) : ?>
			<p class="form-note"><?php echo esc_html( bzl_mod( 'form_gdpr_note' ) ); ?></p>
		<?php endif; ?>
	</form>
	<?php
}

/**
 * Świeży nonce i znacznik czasu (dla stron z pamięci podręcznej).
 */
function bzl_form_token() {
	nocache_headers();
	wp_send_json_success(
		array(
			'nonce' => wp_create_nonce( 'bzl_contact' ),
		)
	);
}
add_action( 'wp_ajax_bzl_form_token', 'bzl_form_token' );
add_action( 'wp_ajax_nopriv_bzl_form_token', 'bzl_form_token' );

/**
 * Adres IP klienta (zahashowany na potrzeby limitu).
 *
 * @return string
 */
function bzl_client_hash() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	return substr( hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) ), 0, 20 );
}

/**
 * Walidacja danych formularza.
 *
 * @param array $raw Dane wejściowe.
 * @return array [values, errors]
 */
function bzl_validate_contact( $raw ) {
	$values    = array(
		'name'    => sanitize_text_field( wp_unslash( $raw['bzl_name'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $raw['bzl_phone'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $raw['bzl_email'] ?? '' ) ),
		'service' => sanitize_text_field( wp_unslash( $raw['bzl_service'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $raw['bzl_message'] ?? '' ) ),
		'consent' => ! empty( $raw['bzl_consent'] ),
	);
	$raw_email = trim( (string) wp_unslash( $raw['bzl_email'] ?? '' ) );
	$errors    = array();

	if ( '' === $values['name'] ) {
		$errors['name'] = __( 'Wpisz swoje imię.', 'bizlife' );
	} elseif ( mb_strlen( $values['name'] ) > 100 ) {
		$errors['name'] = __( 'Imię jest zbyt długie.', 'bizlife' );
	}

	$digits = preg_replace( '/\D/', '', $values['phone'] );
	if ( '' !== $values['phone'] && ( strlen( $digits ) < 9 || strlen( $digits ) > 15 || ! preg_match( '/^[0-9+()\s\-]+$/', $values['phone'] ) ) ) {
		$errors['phone'] = __( 'Wpisz poprawny numer telefonu (min. 9 cyfr).', 'bizlife' );
	}
	if ( '' !== $raw_email && ! is_email( $values['email'] ) ) {
		$errors['email'] = __( 'Wpisz poprawny adres e-mail, np. jan@firma.pl.', 'bizlife' );
	}
	if ( '' === $values['phone'] && '' === $raw_email ) {
		$errors['phone'] = __( 'Podaj numer telefonu lub adres e-mail.', 'bizlife' );
	}
	if ( mb_strlen( $values['message'] ) > 3000 ) {
		$errors['message'] = __( 'Wiadomość może mieć maksymalnie 3000 znaków.', 'bizlife' );
	}
	if ( $values['service'] && ! in_array( $values['service'], bzl_form_service_options(), true ) ) {
		$values['service'] = '';
	}
	if ( ! $values['consent'] ) {
		$errors['consent'] = __( 'Zaznacz zgodę, abyśmy mogli odpowiedzieć na wiadomość.', 'bizlife' );
	}

	return array( $values, $errors );
}

/**
 * Obsługa wysyłki.
 */
function bzl_handle_contact() {
	$ajax = wp_doing_ajax() || ( isset( $_SERVER['HTTP_ACCEPT'] ) && false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ), 'application/json' ) );
	$ref  = isset( $_POST['bzl_ref'] ) ? esc_url_raw( wp_unslash( $_POST['bzl_ref'] ) ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification
	$ref  = wp_validate_redirect( $ref, home_url( '/' ) );

	$respond = static function ( $ok, $message, $errors = array(), $values = array() ) use ( $ajax, $ref ) {
		if ( $ajax ) {
			if ( $ok ) {
				wp_send_json_success( array( 'message' => $message ) );
			}
			wp_send_json_error(
				array(
					'message' => $message,
					'errors'  => $errors,
				),
				$errors ? 422 : 400
			);
		}
		$args = array( 'bzl_form' => $ok ? 'sent' : 'error' );
		if ( ! $ok ) {
			$token = strtolower( wp_generate_password( 12, false ) );
			set_transient(
				'bzl_form_' . $token,
				array(
					'errors' => array_merge( $errors, array( '_form' => $message ) ),
					'values' => $values,
				),
				10 * MINUTE_IN_SECONDS
			);
			$args['bzl_t'] = $token;
		}
		wp_safe_redirect( add_query_arg( $args, remove_query_arg( array( 'bzl_form', 'bzl_t' ), $ref ) ) . '#kontakt-form' );
		exit;
	};

	$generic = __( 'Nie udało się wysłać wiadomości. Odśwież stronę i spróbuj ponownie lub zadzwoń do nas.', 'bizlife' );

	// Nonce.
	if ( ! isset( $_POST['bzl_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bzl_nonce'] ) ), 'bzl_contact' ) ) {
		$respond( false, $generic );
	}

	// Honeypot i pułapka czasowa – boty dostają „sukces”, aby nie uczyły się obejścia.
	$hp = isset( $_POST['bzl_website'] ) ? trim( (string) wp_unslash( $_POST['bzl_website'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$ts = isset( $_POST['bzl_ts'] ) ? sanitize_text_field( wp_unslash( $_POST['bzl_ts'] ) ) : '';
	if ( '' !== $hp || ! bzl_form_timestamp_valid( $ts ) ) {
		$respond( true, (string) bzl_mod( 'form_success' ) );
	}

	// Limit: 5 wiadomości na 15 minut z jednego adresu.
	$rl_key = 'bzl_rl_' . bzl_client_hash();
	$count  = (int) get_transient( $rl_key );
	if ( $count >= 5 ) {
		$respond( false, __( 'Wysłano zbyt wiele wiadomości. Spróbuj ponownie za kilkanaście minut lub zadzwoń do nas.', 'bizlife' ) );
	}

	list( $values, $errors ) = bzl_validate_contact( $_POST );
	if ( $errors ) {
		$respond( false, __( 'Popraw zaznaczone pola formularza.', 'bizlife' ), $errors, $values );
	}

	set_transient( $rl_key, $count + 1, 15 * MINUTE_IN_SECONDS );

	$to = sanitize_email( (string) bzl_mod( 'form_recipient' ) );
	if ( ! is_email( $to ) ) {
		$to = sanitize_email( (string) bzl_mod( 'email' ) );
	}
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$name = str_replace( array( "\r", "\n" ), ' ', $values['name'] );
	/* translators: %s: imię. */
	$subject = sprintf( __( 'Nowe zapytanie ze strony – %s', 'bizlife' ), $name );
	$lines   = array(
		__( 'Imię', 'bizlife' ) . ': ' . $name,
		__( 'Telefon', 'bizlife' ) . ': ' . ( $values['phone'] ? $values['phone'] : '—' ),
		__( 'E-mail', 'bizlife' ) . ': ' . ( $values['email'] ? $values['email'] : '—' ),
		__( 'Usługa', 'bizlife' ) . ': ' . ( $values['service'] ? $values['service'] : '—' ),
		'',
		__( 'Wiadomość', 'bizlife' ) . ':',
		$values['message'] ? $values['message'] : '—',
		'',
		'—',
		__( 'Strona', 'bizlife' ) . ': ' . $ref,
		__( 'Zgoda na przetwarzanie danych', 'bizlife' ) . ': ' . __( 'tak', 'bizlife' ) . ' (' . wp_date( 'Y-m-d H:i' ) . ')',
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( is_email( $values['email'] ) ) {
		$headers[] = 'Reply-To: ' . $name . ' <' . $values['email'] . '>';
	}

	$sent   = wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
	$stored = false;
	if ( bzl_mod( 'form_store_leads' ) ) {
		$stored = bzl_store_lead( $values, $ref, $sent );
	}

	do_action( 'bzl_contact_submitted', $values, $sent );

	if ( ! $sent && ! $stored ) {
		$respond( false, $generic, array(), $values );
	}
	$respond( true, (string) bzl_mod( 'form_success' ) );
}
add_action( 'admin_post_bzl_contact', 'bzl_handle_contact' );
add_action( 'admin_post_nopriv_bzl_contact', 'bzl_handle_contact' );
add_action( 'wp_ajax_bzl_contact', 'bzl_handle_contact' );
add_action( 'wp_ajax_nopriv_bzl_contact', 'bzl_handle_contact' );
