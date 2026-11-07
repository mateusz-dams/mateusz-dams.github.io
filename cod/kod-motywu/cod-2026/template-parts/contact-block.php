<?php
/**
 * Sekcja kontaktu (strona główna i sekcja „Kontakt” na stronach).
 *
 * @package cod2026
 *
 * @var array $args kicker, title, lead (HTML), shortcode, form_title, reverse, id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$a = wp_parse_args(
	$args,
	array(
		'kicker'    => cod_home( 'cod_contact_kicker' ),
		'title'     => cod_home( 'cod_contact_title' ),
		'lead'      => '<p>' . esc_html( cod_home( 'cod_contact_lead' ) ) . '</p>',
		'shortcode' => '',
		'reverse'   => false,
		'id'        => 'kontakt',
		'h'         => 'h2',
	)
);
$booking = (string) cod_option( 'cod_booking_url', '' );
?>
<section class="section contact on-dark<?php echo $a['reverse'] ? ' contact--reverse' : ''; ?>" id="<?php echo esc_attr( $a['id'] ); ?>" aria-labelledby="h-<?php echo esc_attr( $a['id'] ); ?>">
	<div class="aurora" aria-hidden="true"><span class="a1"></span><span class="a3"></span></div>
	<div class="wrap contact-grid">
		<div class="reveal contact-info">
			<?php if ( $a['kicker'] ) : ?>
				<span class="num-tag"><?php echo esc_html( $a['kicker'] ); ?></span>
			<?php endif; ?>
			<<?php echo esc_html( $a['h'] ); ?> id="h-<?php echo esc_attr( $a['id'] ); ?>" class="contact__title"><?php echo esc_html( $a['title'] ); ?></<?php echo esc_html( $a['h'] ); ?>>
			<?php if ( $a['lead'] ) : ?>
				<div class="lede prose prose--dark"><?php echo cod_prose( $a['lead'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
			<?php echo cod_status( 'status--contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<a class="big-phone" href="<?php echo esc_attr( cod_contact( 'phone_href' ) ); ?>" data-track-location="kontakt"><?php echo esc_html( cod_contact( 'phone_display' ) ); ?></a>
			<?php if ( $booking ) : ?>
				<p><?php echo cod_button( $booking, 'primary', array( 'label' => __( 'Zarezerwuj termin online', 'cod2026' ), 'icon' => 'calendar', 'location' => 'kontakt', 'data' => array( 'track' => 'cod_booking_click' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<ul class="cinfo">
				<li><span class="ci"><?php echo cod_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span><a href="mailto:<?php echo esc_attr( cod_contact( 'email' ) ); ?>" data-track-location="kontakt"><?php echo esc_html( cod_contact( 'email' ) ); ?></a></span></li>
				<li><span class="ci"><?php echo cod_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span><?php echo esc_html( cod_contact( 'company' ) ); ?><br><?php echo esc_html( cod_contact( 'address' ) ); ?><br><a href="<?php echo esc_url( cod_contact( 'maps_url' ) ); ?>" target="_blank" rel="noopener" data-track="cod_directions_click" data-track-location="kontakt"><?php esc_html_e( 'Wyznacz trasę', 'cod2026' ); ?><span class="sr-only"> <?php esc_html_e( '(otworzy się w nowej karcie)', 'cod2026' ); ?></span></a></span></li>
			</ul>
			<div class="map" data-map>
				<div>
					<button class="btn btn--ghost" type="button" data-map-load><span><?php esc_html_e( 'Pokaż mapę', 'cod2026' ); ?></span></button>
					<p><?php esc_html_e( 'Mapa ładuje się z serwerów Google dopiero po kliknięciu.', 'cod2026' ); ?></p>
				</div>
			</div>
		</div>
		<div class="reveal contact-form">
			<?php cod_contact_form( $a['shortcode'] ); ?>
		</div>
	</div>
</section>
