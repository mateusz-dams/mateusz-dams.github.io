<?php
/**
 * Pasek końcowy „Zacznijmy od rozmowy”.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="cta-band on-aqua" aria-labelledby="h-cta">
	<div class="peaks" aria-hidden="true"><span class="p1"></span><span class="p2"></span><span class="p3"></span><span class="flag2"></span></div>
	<div class="wrap">
		<?php echo cod_status( 'status--band' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h2 id="h-cta" class="reveal" data-split-scroll><?php echo esc_html( cod_home( 'cod_cta_title' ) ); ?></h2>
		<p class="band-lede reveal"><?php echo esc_html( cod_home( 'cod_cta_lead' ) ); ?></p>
		<div class="btn-row reveal">
			<?php echo cod_phone_button( 'pasek-koncowy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<a class="btn btn--ghost" href="<?php echo esc_url( cod_contact_url() ); ?>" data-track="cod_form_open" data-track-location="pasek-koncowy"><span><?php esc_html_e( 'Napisz wiadomość', 'cod2026' ); ?></span></a>
		</div>
	</div>
</section>
