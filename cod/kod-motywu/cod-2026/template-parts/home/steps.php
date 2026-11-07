<?php
/**
 * Strona główna – jak wygląda pierwszy kontakt.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_front = (int) get_option( 'page_on_front' );
$cod_steps = cod_field( 'cod_steps', $cod_front ? $cod_front : false, array() );
if ( ! is_array( $cod_steps ) || ! $cod_steps ) {
	$cod_steps = cod_default_steps();
}
?>
<section class="section steps-sec on-dark" id="jak-zaczac" aria-labelledby="h-steps">
	<div class="aurora" aria-hidden="true"><span class="a1"></span><span class="a2"></span></div>
	<div class="wrap">
		<div class="sec-head reveal">
			<div><span class="num-tag"><?php echo esc_html( cod_home( 'cod_steps_kicker' ) ); ?></span><h2 id="h-steps"><?php echo esc_html( cod_home( 'cod_steps_title' ) ); ?></h2></div>
			<p class="lede"><?php echo esc_html( cod_home( 'cod_steps_lead' ) ); ?></p>
		</div>
		<div class="steps-grid">
			<div class="tl-wrap" id="tl">
				<span class="tl-line" aria-hidden="true"><i></i></span>
				<ol class="tl">
					<?php foreach ( $cod_steps as $cod_i => $cod_s ) : ?>
						<li>
							<span class="n" aria-hidden="true"><?php echo (int) $cod_i + 1; ?></span>
							<h3><?php echo esc_html( $cod_s['title'] ); ?></h3>
							<p><?php echo esc_html( $cod_s['text'] ); ?></p>
							<?php if ( ! empty( $cod_s['note'] ) ) : ?>
								<span class="note"><?php echo esc_html( $cod_s['note'] ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
			<aside class="prep-card reveal" aria-labelledby="h-prep">
				<h3 id="h-prep"><?php echo esc_html( cod_home( 'cod_prep_title' ) ); ?></h3>
				<ul>
					<?php foreach ( cod_lines( cod_home( 'cod_prep_items' ) ) as $cod_p ) : ?>
						<li><?php echo esc_html( $cod_p ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php echo cod_status(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="btn-row">
					<?php echo cod_phone_button( 'jak-zaczac' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<a class="btn btn--ghost" href="#kontakt" data-track="cod_form_open" data-track-location="jak-zaczac"><span><?php esc_html_e( 'Przejdź do formularza', 'cod2026' ); ?></span></a>
				</div>
			</aside>
		</div>
	</div>
</section>
