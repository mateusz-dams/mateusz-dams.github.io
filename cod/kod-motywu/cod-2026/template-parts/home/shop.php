<?php
/**
 * Strona główna – produkty WooCommerce i kursy LearnDash.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$cod_front = (int) get_option( 'page_on_front' );
$cod_ids   = cod_field( 'cod_shop_products', $cod_front ? $cod_front : false, array() );
$cod_args  = array(
	'status'     => 'publish',
	'visibility' => 'catalog',
	'limit'      => 6,
	'orderby'    => 'menu_order',
	'order'      => 'ASC',
);
if ( is_array( $cod_ids ) && $cod_ids ) {
	$cod_args['include'] = array_map( 'intval', $cod_ids );
	$cod_args['orderby'] = 'post__in';
}
$cod_products = wc_get_products( $cod_args );
$cod_courses  = post_type_exists( 'sfwd-courses' ) ? get_post_type_archive_link( 'sfwd-courses' ) : '';
if ( ! $cod_products && ! $cod_courses ) {
	return;
}
?>
<section class="section" id="sklep" aria-labelledby="h-sklep">
	<div class="wrap">
		<div class="sec-head reveal">
			<div><span class="num-tag"><?php echo esc_html( cod_home( 'cod_shop_kicker' ) ); ?></span><h2 id="h-sklep"><?php echo esc_html( cod_home( 'cod_shop_title' ) ); ?></h2></div>
			<div>
				<p class="lede lede-gap"><?php echo esc_html( cod_home( 'cod_shop_lead' ) ); ?></p>
				<p><a class="link-arrow" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" data-track="cod_shop_click" data-track-location="strona-glowna"><?php esc_html_e( 'Wszystkie produkty', 'cod2026' ); ?> <?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></p>
			</div>
		</div>
		<?php if ( $cod_products ) : ?>
			<ul class="product-grid product-grid--home reveal-stagger" aria-label="<?php esc_attr_e( 'Produkty w sklepie', 'cod2026' ); ?>">
				<?php foreach ( $cod_products as $cod_product ) : ?>
					<li><?php cod_product_card( $cod_product, 'strona-glowna' ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $cod_courses ) : ?>
			<div class="courses reveal on-dark">
				<div>
					<h3><?php echo esc_html( cod_home( 'cod_courses_title' ) ); ?></h3>
					<p><?php echo esc_html( cod_home( 'cod_courses_text' ) ); ?></p>
				</div>
				<div class="btn-row">
					<?php echo cod_button( $cod_courses, 'primary', array( 'label' => __( 'Zobacz kursy', 'cod2026' ), 'location' => 'kursy', 'data' => array( 'track' => 'cod_courses_click' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo cod_button( wc_get_page_permalink( 'myaccount' ), 'ghost', array( 'label' => is_user_logged_in() ? __( 'Moje konto', 'cod2026' ) : __( 'Zaloguj się', 'cod2026' ), 'location' => 'kursy', 'data' => array( 'track' => 'cod_account_click' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
