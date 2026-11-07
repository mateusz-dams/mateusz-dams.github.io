<?php
/**
 * Strona nie znaleziona.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="section notfound">
	<div class="wrap fx-narrow">
		<p class="num-tag"><?php esc_html_e( 'Błąd 404', 'cod2026' ); ?></p>
		<h1 class="page-hero__title"><?php esc_html_e( 'Tej strony tu nie ma', 'cod2026' ); ?></h1>
		<p class="lede"><?php esc_html_e( 'Adres mógł się zmienić albo strona została usunięta. Zacznij od strony głównej albo zadzwoń – pomożemy znaleźć to, czego szukasz.', 'cod2026' ); ?></p>
		<div class="btn-row cta-gap">
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'Strona główna', 'cod2026' ); ?></span></a>
			<?php echo cod_phone_button( '404', 'ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="cta-gap"><?php get_search_form(); ?></div>
	</div>
</section>
<?php
get_footer();
