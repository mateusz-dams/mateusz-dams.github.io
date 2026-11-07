<?php
/**
 * Zespół: kafelki z dużym zdjęciem; po kliknięciu panel z boku z dłuższym opisem.
 *
 * @package cod2026
 *
 * @var array $args people – lista z cod_team() lub z sekcji „zespół” (name, role, scope, bio, photo).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_img_for = function ( $cod_p, $size, $w, $h, $sizes ) {
	$photo = $cod_p['photo'];
	$file  = is_string( $photo ) && preg_match( '/\.(webp|png|jpe?g)$/', $photo ) && false === strpos( $photo, '/' ) ? $photo : '';
	return cod_img(
		$file ? '' : $photo,
		$size,
		array(
			/* translators: %s: imię i nazwisko. */
			'alt'    => sprintf( __( 'Portret: %s', 'cod2026' ), $cod_p['name'] ),
			'width'  => $w,
			'height' => $h,
			'sizes'  => $sizes,
		),
		$file
	);
};
?>
<ul class="team reveal-stagger">
	<?php foreach ( $args['people'] as $cod_p ) : ?>
		<?php
		$cod_key   = cod_uid( 'osoba' );
		$cod_scope = isset( $cod_p['scope'] ) ? cod_snippet( $cod_p['scope'], 110 ) : '';
		$cod_bio   = isset( $cod_p['bio'] ) ? (string) $cod_p['bio'] : '';
		?>
		<li class="person" data-svc="<?php echo esc_attr( $cod_key ); ?>" data-kicker="<?php esc_attr_e( 'Zespół', 'cod2026' ); ?>" data-view-event="cod_team_view" data-service-name="<?php echo esc_attr( $cod_p['name'] ); ?>">
			<div class="person__photo">
				<?php echo $cod_img_for( $cod_p, 'medium_large', 720, 900, '(max-width: 640px) 80vw, (max-width: 1060px) 45vw, 380px' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="person__info">
				<h3><?php echo esc_html( $cod_p['name'] ); ?></h3>
				<?php if ( $cod_p['role'] ) : ?>
					<p class="role"><?php echo esc_html( $cod_p['role'] ); ?></p>
				<?php endif; ?>
				<?php if ( $cod_scope ) : ?>
					<p class="scope"><?php echo esc_html( $cod_scope ); ?></p>
				<?php endif; ?>
				<button class="more" type="button" data-svc-open="<?php echo esc_attr( $cod_key ); ?>">
					<?php esc_html_e( 'Poznaj', 'cod2026' ); ?><span class="sr-only"> <?php echo esc_html( $cod_p['name'] ); ?></span>
					<span class="arr"><?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</button>
			</div>
			<template class="svc-full">
				<figure class="d-media d-media--portrait"><?php echo $cod_img_for( $cod_p, 'large', 900, 1125, '640px' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
				<h3><?php echo esc_html( $cod_p['name'] ); ?></h3>
				<?php if ( $cod_p['role'] ) : ?>
					<p class="d-role"><?php echo esc_html( $cod_p['role'] ); ?></p>
				<?php endif; ?>
				<div class="d-text prose">
					<?php
					if ( '' !== trim( wp_strip_all_tags( $cod_bio ) ) ) {
						echo cod_prose( wpautop( $cod_bio ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} elseif ( ! empty( $cod_p['scope'] ) ) {
						echo '<p>' . esc_html( wp_strip_all_tags( $cod_p['scope'] ) ) . '</p>';
					}
					?>
				</div>
				<div class="d-cta">
					<div class="btn-row">
						<?php echo cod_phone_button( 'zespol', 'primary', __( 'Umów konsultację', 'cod2026' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a class="btn btn--ghost" href="<?php echo esc_url( cod_contact_url() ); ?>" data-track="cod_form_open" data-track-location="zespol"><span><?php esc_html_e( 'Napisz wiadomość', 'cod2026' ); ?></span></a>
					</div>
				</div>
			</template>
		</li>
	<?php endforeach; ?>
</ul>
