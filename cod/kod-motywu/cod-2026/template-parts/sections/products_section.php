<?php
/**
 * Sekcja: produkty WooCommerce (ceny pobierane na żywo ze sklepu).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}
$cod_posts = get_sub_field( 'products' );
$cod_ids   = array();
foreach ( (array) $cod_posts as $cod_p ) {
	$cod_ids[] = is_object( $cod_p ) ? $cod_p->ID : (int) $cod_p;
}
$cod_products = $cod_ids
	? wc_get_products( array( 'include' => $cod_ids, 'status' => 'publish', 'orderby' => 'post__in', 'limit' => count( $cod_ids ) ) )
	: wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 6 ) );
if ( ! $cod_products ) {
	return;
}
?>
<section<?php echo cod_sec_attrs( 'products', 'fx-products' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wrap">
		<?php cod_sec_head( 'products' ); ?>
		<ul class="product-grid reveal-stagger">
			<?php foreach ( $cod_products as $cod_product ) : ?>
				<li><?php cod_product_card( $cod_product, cod_sec_loc( 'products' ) ); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php cod_sec_buttons( 'products_section_buttons', 'products_section_button', cod_sec_loc( 'products' ), 'fx-buttons fx-buttons--center reveal' ); ?>
	</div>
</section>
