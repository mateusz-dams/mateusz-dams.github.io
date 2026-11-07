<?php
/**
 * Integracja z WooCommerce.
 *
 * Szablony sklepu są w katalogu woocommerce/ motywu (lista produktów, karta produktu, kafel produktu).
 * Koszyk, zamówienie i „Moje konto” zostają standardowymi stronami WooCommerce – motyw je tylko stylizuje,
 * więc Przelewy24, LearnDash – WooCommerce Integration i inne wtyczki działają bez zmian.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

// Bez paska bocznego i domyślnych opakowań – motyw ma własne.
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// Ceny bez zbędnych groszy: „147 zł” zamiast „147,00 zł” (gdy są grosze, np. 147,50 zł – zostają).
add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

add_filter( 'woocommerce_add_to_cart_fragments', 'cod_cart_fragment' );
/**
 * Liczba produktów w koszyku odświeżana po dodaniu do koszyka (AJAX).
 *
 * @param array $fragments Fragmenty.
 */
function cod_cart_fragment( $fragments ) {
	$qty = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	/* translators: %d: liczba produktów. */
	$label = sprintf( _n( '%d produkt w koszyku', '%d produkty w koszyku', $qty, 'cod2026' ), $qty );
	$fragments['.cart-count'] = '<span class="cart-count' . ( $qty ? '' : ' is-empty' ) . '" aria-label="' . esc_attr( $label ) . '">' . (int) $qty . '</span>';
	return $fragments;
}

add_filter( 'loop_shop_columns', 'cod_shop_columns' );
/**
 * Trzy kolumny w sklepie.
 */
function cod_shop_columns() {
	return 3;
}

add_filter( 'loop_shop_per_page', 'cod_shop_per_page' );
/**
 * Produktów na stronę.
 */
function cod_shop_per_page() {
	return 12;
}

add_filter( 'woocommerce_output_related_products_args', 'cod_related_args' );
/**
 * Trzy powiązane produkty.
 *
 * @param array $args Argumenty.
 */
function cod_related_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}

add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
add_filter( 'woocommerce_product_additional_information_heading', '__return_empty_string' );

add_filter( 'woocommerce_sale_flash', 'cod_sale_flash', 10, 3 );
/**
 * Etykieta promocji.
 *
 * @param string     $html    HTML.
 * @param WP_Post    $post    Wpis.
 * @param WC_Product $product Produkt.
 */
function cod_sale_flash( $html, $post, $product ) {
	unset( $post );
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
		$pct = (int) round( ( 1 - $sale / $regular ) * 100 );
		/* translators: %d: rabat w procentach. */
		return '<span class="onsale">' . esc_html( sprintf( __( '−%d%%', 'cod2026' ), $pct ) ) . '</span>';
	}
	return '<span class="onsale">' . esc_html__( 'Promocja', 'cod2026' ) . '</span>';
}

/**
 * Kafel produktu (strona główna, sekcje „Produkty” na stronach).
 *
 * @param WC_Product|int $product  Produkt.
 * @param string         $location Miejsce do pomiaru.
 */
function cod_product_card( $product, $location = '' ) {
	$product = wc_get_product( $product );
	if ( ! $product ) {
		return;
	}
	$id    = $product->get_id();
	$url   = $product->get_permalink();
	$name  = $product->get_name();
	$key   = 'produkt-' . $id . '-' . cod_uid( 'p' );
	$terms = get_the_terms( $id, 'product_cat' );
	$cat   = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
	$short = $product->get_short_description();
	$long  = $product->get_description();
	$text  = '' !== trim( wp_strip_all_tags( $short ) ) ? $short : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $long ) ), 70, '…' );
	$can   = $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' );
	?>
	<article class="pcard spot" data-svc="<?php echo esc_attr( $key ); ?>" data-kicker="<?php esc_attr_e( 'Produkt', 'cod2026' ); ?>" data-view-event="cod_product_view" data-service-name="<?php echo esc_attr( $name ); ?>">
		<div class="pimg">
			<?php
			echo $product->get_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'woocommerce_thumbnail',
				array(
					'alt'     => '',
					'loading' => 'lazy',
					'sizes'   => '(max-width: 640px) 80vw, (max-width: 1100px) 45vw, 400px',
				)
			);
			?>
		</div>
		<?php if ( $cat ) : ?>
			<span class="cat"><?php echo esc_html( $cat ); ?></span>
		<?php endif; ?>
		<h3><?php echo esc_html( $name ); ?></h3>
		<div class="row">
			<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			<a class="more" href="<?php echo esc_url( $url ); ?>" data-svc-open="<?php echo esc_attr( $key ); ?>" data-track="cod_product_click" data-track-location="<?php echo esc_attr( $location ); ?>">
				<?php esc_html_e( 'Szczegóły', 'cod2026' ); ?><span class="sr-only">: <?php echo esc_html( $name ); ?></span>
				<span class="arr"><?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</a>
		</div>
		<template class="svc-full">
			<figure class="d-media d-media--product">
				<?php echo $product->get_image( 'woocommerce_single', array( 'alt' => '', 'sizes' => '640px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
			<?php if ( $cat ) : ?>
				<p class="d-for"><?php echo esc_html( $cat ); ?></p>
			<?php endif; ?>
			<h3><?php echo esc_html( $name ); ?></h3>
			<p class="d-price d-price--shop"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
			<?php if ( '' !== trim( wp_strip_all_tags( $text ) ) ) : ?>
				<div class="d-text prose"><?php echo cod_prose( wpautop( $text ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
			<div class="d-cta">
				<div class="btn-row">
					<?php if ( $can ) : ?>
						<a class="btn btn--primary add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-product_id="<?php echo (int) $id; ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" data-quantity="1" rel="nofollow" data-track="cod_add_to_cart" data-track-location="<?php echo esc_attr( $location ); ?>"><?php echo cod_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Dodaj do koszyka', 'cod2026' ); ?></span></a>
					<?php endif; ?>
					<a class="btn <?php echo $can ? 'btn--ghost' : 'btn--primary'; ?>" href="<?php echo esc_url( $url ); ?>" data-track="cod_product_click" data-track-location="<?php echo esc_attr( $location ); ?>"><span><?php esc_html_e( 'Zobacz produkt', 'cod2026' ); ?></span></a>
				</div>
			</div>
		</template>
	</article>
	<?php
}

/**
 * Produkt WooCommerce, do którego prowadzi adres (strona produktu, ?add-to-cart=ID, koszyk/zamówienie z add-to-cart).
 * Pozwala pokazać aktualną cenę przy pakietach na stronach sprzedażowych.
 *
 * @param string $url Adres.
 * @return WC_Product|null
 */
function cod_product_from_url( $url ) {
	if ( '' === (string) $url ) {
		return null;
	}
	$query = wp_parse_url( $url, PHP_URL_QUERY );
	if ( $query ) {
		parse_str( $query, $q );
		foreach ( array( 'add-to-cart', 'product_id' ) as $k ) {
			if ( ! empty( $q[ $k ] ) && is_numeric( $q[ $k ] ) ) {
				$p = wc_get_product( (int) $q[ $k ] );
				if ( $p ) {
					return $p;
				}
			}
		}
	}
	$id = url_to_postid( $url );
	if ( $id && 'product' === get_post_type( $id ) ) {
		return wc_get_product( $id );
	}
	return null;
}

add_action( 'woocommerce_before_main_content', 'cod_shop_open', 5 );
/**
 * Początek kontenera sklepu.
 */
function cod_shop_open() {
	echo '<div class="shop-main section section--tight"><div class="wrap">';
	if ( is_product() ) {
		cod_breadcrumbs();
	}
}

add_action( 'woocommerce_after_main_content', 'cod_shop_close', 50 );
/**
 * Koniec kontenera sklepu.
 */
function cod_shop_close() {
	echo '</div></div>';
}

add_filter( 'woocommerce_checkout_fields', 'cod_checkout_fields' );
/**
 * Autouzupełnianie pól zamówienia (lepsza obsługa na telefonie).
 *
 * @param array $fields Pola.
 */
function cod_checkout_fields( $fields ) {
	$map = array(
		'billing_first_name' => 'given-name',
		'billing_last_name'  => 'family-name',
		'billing_email'      => 'email',
		'billing_phone'      => 'tel',
		'billing_postcode'   => 'postal-code',
		'billing_city'       => 'address-level2',
		'billing_address_1'  => 'address-line1',
	);
	foreach ( $map as $key => $ac ) {
		if ( isset( $fields['billing'][ $key ] ) ) {
			$fields['billing'][ $key ]['autocomplete'] = $ac;
		}
	}
	return $fields;
}
