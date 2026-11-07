<?php
/**
 * Formularz kontaktowy motywu.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_state   = isset( $_GET['kontakt'] ) ? sanitize_key( wp_unslash( $_GET['kontakt'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$cod_privacy = get_privacy_policy_url();
$cod_groups  = cod_offers_grouped();
?>
<form class="form on-light" id="form-kontakt" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate aria-labelledby="h-form">
	<h3 id="h-form"><?php esc_html_e( 'Napisz, z czym przychodzisz', 'cod2026' ); ?></h3>
	<p class="fine intro"><?php esc_html_e( 'Odezwiemy się telefonicznie lub e-mailem. Pola oznaczone gwiazdką są wymagane.', 'cod2026' ); ?></p>

	<input type="hidden" name="action" value="cod_contact">
	<input type="hidden" name="ts" value="<?php echo esc_attr( time() ); ?>">
	<input type="hidden" name="strona" value="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>">
	<?php wp_nonce_field( 'cod_contact', 'cod_nonce', false ); ?>

	<fieldset>
		<legend><?php esc_html_e( 'Kogo dotyczy sprawa?', 'cod2026' ); ?></legend>
		<div class="chips">
			<?php foreach ( cod_audiences() as $cod_key => $cod_aud ) : ?>
				<label class="chip"><input type="radio" name="dotyczy" value="<?php echo esc_attr( $cod_aud['topic'] ); ?>"><span><?php echo esc_html( $cod_aud['topic'] ); ?></span></label>
			<?php endforeach; ?>
			<label class="chip"><input type="radio" name="dotyczy" value="Jeszcze nie wiem"><span><?php esc_html_e( 'Jeszcze nie wiem', 'cod2026' ); ?></span></label>
		</div>
	</fieldset>

	<?php if ( array_filter( $cod_groups ) ) : ?>
	<div class="field">
		<label for="f-usluga"><?php esc_html_e( 'Usługa (jeśli już wiesz)', 'cod2026' ); ?></label>
		<select id="f-usluga" name="usluga">
			<option value=""><?php esc_html_e( 'Nie wiem – proszę o podpowiedź', 'cod2026' ); ?></option>
			<?php foreach ( $cod_groups as $cod_aud => $cod_list ) : ?>
				<?php
				if ( ! $cod_list ) {
					continue;
				}
				$cod_a = cod_audiences();
				?>
				<optgroup label="<?php echo esc_attr( isset( $cod_a[ $cod_aud ] ) ? $cod_a[ $cod_aud ]['tab'] : $cod_aud ); ?>">
					<?php foreach ( $cod_list as $cod_o ) : ?>
						<option><?php echo esc_html( $cod_o['title'] ); ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>
		</select>
	</div>
	<?php endif; ?>

	<div class="field">
		<label for="f-imie"><?php esc_html_e( 'Imię', 'cod2026' ); ?> *</label>
		<input id="f-imie" name="imie" type="text" autocomplete="given-name" required aria-describedby="e-imie">
		<span class="err" id="e-imie"></span>
	</div>
	<div class="two">
		<div class="field">
			<label for="f-tel"><?php esc_html_e( 'Telefon', 'cod2026' ); ?></label>
			<input id="f-tel" name="telefon" type="tel" autocomplete="tel" inputmode="tel" aria-describedby="h-dane e-tel">
			<span class="err" id="e-tel"></span>
		</div>
		<div class="field">
			<label for="f-email"><?php esc_html_e( 'E-mail', 'cod2026' ); ?></label>
			<input id="f-email" name="email" type="email" autocomplete="email" aria-describedby="h-dane e-email">
			<span class="err" id="e-email"></span>
		</div>
	</div>
	<p class="fine hint-contact" id="h-dane"><?php esc_html_e( 'Podaj telefon lub e-mail – wystarczy jedno.', 'cod2026' ); ?></p>
	<div class="field">
		<label for="f-kiedy"><?php esc_html_e( 'Kiedy najlepiej się z Tobą skontaktować?', 'cod2026' ); ?></label>
		<select id="f-kiedy" name="kiedy">
			<option value=""><?php esc_html_e( 'Bez znaczenia', 'cod2026' ); ?></option>
			<option><?php esc_html_e( 'Rano (9:00–12:00)', 'cod2026' ); ?></option>
			<option><?php esc_html_e( 'W ciągu dnia (12:00–16:00)', 'cod2026' ); ?></option>
			<option><?php esc_html_e( 'Po południu (16:00–20:00)', 'cod2026' ); ?></option>
		</select>
	</div>
	<div class="field">
		<label for="f-wiad"><?php esc_html_e( 'Wiadomość', 'cod2026' ); ?></label>
		<span class="hint" id="h-wiad"><?php esc_html_e( 'Wystarczą 2–3 zdania. Nie musisz opisywać szczegółów zdrowotnych – porozmawiamy o nich na spotkaniu.', 'cod2026' ); ?></span>
		<textarea id="f-wiad" name="wiadomosc" aria-describedby="h-wiad"></textarea>
	</div>
	<div class="hp" aria-hidden="true"><label for="f-www"><?php esc_html_e( 'Nie wypełniaj tego pola', 'cod2026' ); ?></label><input id="f-www" name="www" type="text" tabindex="-1" autocomplete="off"></div>
	<div class="consent">
		<input id="f-zgoda" name="zgoda" type="checkbox" value="1" required aria-describedby="e-zgoda">
		<label for="f-zgoda">
			<?php
			echo esc_html( sprintf( /* translators: %s: nazwa firmy. */ __( 'Wyrażam zgodę na przetwarzanie moich danych przez %s w celu odpowiedzi na to zapytanie.', 'cod2026' ), cod_contact( 'company' ) ) );
			if ( $cod_privacy ) {
				echo ' ';
				printf( wp_kses( __( 'Zapoznałam/-em się z <a href="%s">polityką prywatności</a>.', 'cod2026' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $cod_privacy ) );
			}
			?>
			*
		</label>
	</div>
	<span class="err" id="e-zgoda"></span>
	<p class="fine rodo">
		<?php
		echo esc_html( sprintf( /* translators: 1: nazwa firmy, 2: adres. */ __( 'Administratorem danych jest %1$s, %2$s. Dane wykorzystamy wyłącznie do odpowiedzi na Twoje zapytanie. Masz prawo do dostępu do nich, ich poprawienia i żądania usunięcia.', 'cod2026' ), cod_contact( 'company' ), cod_contact( 'address' ) ) );
		?>
	</p>
	<button class="btn btn--primary" type="submit" id="f-submit"><span><?php esc_html_e( 'Wyślij wiadomość', 'cod2026' ); ?></span></button>
	<div class="form-status<?php echo 'blad' === $cod_state ? ' is-error' : ''; ?>" id="f-status" role="status" aria-live="polite"><?php
	if ( 'wyslano' === $cod_state ) {
		esc_html_e( 'Dziękujemy, wiadomość dotarła. Odezwiemy się telefonicznie lub e-mailem.', 'cod2026' );
	} elseif ( 'blad' === $cod_state ) {
		/* translators: %s: numer telefonu. */
		echo esc_html( sprintf( __( 'Nie udało się wysłać wiadomości. Sprawdź pola i spróbuj ponownie albo zadzwoń: %s.', 'cod2026' ), cod_contact( 'phone_display' ) ) );
	}
	?></div>
</form>
