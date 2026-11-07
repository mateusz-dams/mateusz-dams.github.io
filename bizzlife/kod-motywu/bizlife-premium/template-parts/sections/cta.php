<?php
/**
 * Sekcja: złoty blok CTA (używany także na podstronach).
 *
 * @package Bizlife
 *
 * @var array $args title, text – nadpisanie treści.
 */

defined( 'ABSPATH' ) || exit;

$bzl_title = ! empty( $args['title'] ) ? $args['title'] : bzl_mod( 'cta_title' );
$bzl_text  = ! empty( $args['text'] ) ? $args['text'] : bzl_mod( 'cta_text' );
$bzl_tone  = ! empty( $args['tone'] ) ? $args['tone'] : 'tone-dark';
if ( ! $bzl_title ) {
	return;
}
?>
<section class="section section--cta <?php echo esc_attr( $bzl_tone ); ?>" aria-labelledby="cta-title">
	<div class="container">
		<div class="cta-band" data-reveal>
			<svg class="cta-band__pattern" viewBox="0 0 400 400" aria-hidden="true" focusable="false">
				<defs>
					<pattern id="bzl-grid" width="40" height="40" patternUnits="userSpaceOnUse">
						<path d="M40 0H0v40" fill="none" stroke="currentColor" stroke-width="1"/>
					</pattern>
				</defs>
				<rect width="400" height="400" fill="url(#bzl-grid)"/>
				<path d="M60 340 340 60" stroke="currentColor" stroke-width="1.5" fill="none"/>
				<circle cx="340" cy="60" r="5" fill="currentColor"/>
			</svg>
			<div class="cta-band__content">
				<h2 class="cta-band__title" id="cta-title"><?php echo bzl_rich_title( $bzl_title ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php if ( $bzl_text ) : ?>
					<p class="cta-band__text"><?php echo esc_html( $bzl_text ); ?></p>
				<?php endif; ?>
				<div class="btn-row">
					<?php
					echo bzl_button( bzl_mod( 'cta_btn1_label' ), bzl_mod( 'cta_btn1_url' ), 'dark', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput
					$bzl_btn2 = bzl_mod( 'cta_btn2_url' );
					if ( '{tel}' === $bzl_btn2 && bzl_mod( 'phone' ) ) {
						/* translators: %s: numer telefonu. */
						echo bzl_button( sprintf( '%1$s: %2$s', bzl_mod( 'cta_btn2_label' ), bzl_mod( 'phone' ) ), $bzl_btn2, 'outline-dark', 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput
					} else {
						echo bzl_button( bzl_mod( 'cta_btn2_label' ), $bzl_btn2, 'outline-dark' ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
