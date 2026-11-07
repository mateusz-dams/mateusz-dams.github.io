<?php
/**
 * Strona główna – pytania.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_front = (int) get_option( 'page_on_front' );
$cod_faq   = cod_field( 'cod_faq', $cod_front ? $cod_front : false, array() );
if ( ! is_array( $cod_faq ) || ! $cod_faq ) {
	$cod_faq = cod_default_faq();
}
?>
<section class="section" id="pytania" aria-labelledby="h-faq">
	<div class="wrap faq-grid">
		<div class="sticky reveal">
			<span class="num-tag"><?php echo esc_html( cod_home( 'cod_faq_kicker' ) ); ?></span>
			<h2 id="h-faq" class="mb-h2"><?php echo esc_html( cod_home( 'cod_faq_title' ) ); ?></h2>
			<p class="lede"><?php echo esc_html( cod_home( 'cod_faq_lead' ) ); ?></p>
			<?php echo cod_phone_button( 'faq', 'primary', null, 'cta-gap' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php get_template_part( 'template-parts/cards/accordion', null, array( 'items' => $cod_faq ) ); ?>
	</div>
</section>
