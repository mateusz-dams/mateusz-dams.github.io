<?php
/**
 * Sekcja: FAQ (strona główna).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_faqs = bzl_faqs(
	array(
		'limit' => max( 1, (int) bzl_mod( 'faq_count' ) ),
		'home'  => true,
	)
);
if ( ! $bzl_faqs ) {
	return;
}
$bzl_phone = (string) bzl_mod( 'phone' );
?>
<section class="section faq <?php echo esc_attr( bzl_tone( 'faq', 'darker' ) ); ?>" id="faq" aria-labelledby="faq-title">
	<div class="container split split--faq">
		<div class="split__aside">
			<?php
			bzl_section_header(
				array(
					'eyebrow' => bzl_mod( 'faq_eyebrow' ),
					'title'   => bzl_mod( 'faq_title' ),
					'lead'    => bzl_mod( 'faq_lead' ),
					'id'      => 'faq-title',
					'ghost'   => 'FAQ',
				)
			);
			?>
			<div class="faq__actions" data-reveal>
				<?php if ( bzl_link_has_page( '{faq}' ) ) : ?>
					<?php echo bzl_button( bzl_mod( 'faq_cta_label' ), '{faq}', 'ghost', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php endif; ?>
				<?php if ( $bzl_phone ) : ?>
					<a class="contact-chip" href="<?php echo esc_url( bzl_tel_href( $bzl_phone ), array( 'tel' ) ); ?>">
						<span class="contact-chip__icon"><?php echo bzl_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span><span class="contact-chip__label"><?php esc_html_e( 'Wolisz zapytać?', 'bizlife' ); ?></span><span class="contact-chip__value"><?php echo esc_html( $bzl_phone ); ?></span></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
		get_template_part(
			'template-parts/components/accordion',
			null,
			array(
				'items' => $bzl_faqs,
				'id'    => 'home-faq',
			)
		);
		?>
	</div>
</section>
