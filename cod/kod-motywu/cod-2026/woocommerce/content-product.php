<?php
/**
 * Kafel produktu na liście (nadpisanie szablonu WooCommerce content-product.php, wersja 9.4.0).
 * Wygląd jak na stronie głównej; cena i promocje pobierane z WooCommerce.
 *
 * @package cod2026
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<?php
	if ( $product->is_on_sale() ) {
		woocommerce_show_product_loop_sale_flash();
	}
	cod_product_card( $product, 'sklep' );
	?>
</li>
