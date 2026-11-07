<?php
/**
 * Sklep i kategorie produktów (nadpisanie szablonu WooCommerce archive-product.php, wersja 8.6.0).
 *
 * @package cod2026
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$cod_title = woocommerce_page_title( false );
$cod_lead  = '';
if ( is_product_taxonomy() ) {
	$cod_lead = wp_strip_all_tags( term_description() );
} elseif ( is_shop() ) {
	$cod_shop = wc_get_page_id( 'shop' );
	$cod_lead = $cod_shop > 0 && has_excerpt( $cod_shop ) ? get_the_excerpt( $cod_shop ) : __( 'Workbooki, e-booki i systemy do pracy we własnym tempie.', 'cod2026' );
}
get_template_part( 'template-parts/page-hero', null, array( 'title' => $cod_title, 'lead' => $cod_lead ) );

do_action( 'woocommerce_before_main_content' );

if ( woocommerce_product_loop() ) {
	echo '<div class="shop-bar">';
	do_action( 'woocommerce_before_shop_loop' );
	echo '</div>';

	woocommerce_product_loop_start();
	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();
			do_action( 'woocommerce_shop_loop' );
			wc_get_template_part( 'content', 'product' );
		}
	}
	woocommerce_product_loop_end();

	do_action( 'woocommerce_after_shop_loop' );
} else {
	do_action( 'woocommerce_no_products_found' );
}

do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
