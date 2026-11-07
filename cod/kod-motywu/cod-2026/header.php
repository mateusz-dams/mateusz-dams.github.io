<?php
/**
 * Nagłówek strony.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_has_woo   = class_exists( 'WooCommerce' );
$cod_account   = $cod_has_woo ? wc_get_page_permalink( 'myaccount' ) : '';
$cod_cart      = $cod_has_woo ? wc_get_cart_url() : '';
$cod_shop      = $cod_has_woo ? wc_get_page_permalink( 'shop' ) : '';
$cod_cart_qty  = ( $cod_has_woo && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$cod_light    = 'light' === cod_option( 'cod_color_mode', 'auto' );
?><!doctype html>
<html <?php language_attributes(); ?><?php echo $cod_light ? ' data-theme="light"' : ''; ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php if ( $cod_light ) : ?>
<meta name="color-scheme" content="light">
<meta name="theme-color" content="#fbfaf7">
<?php else : ?>
<meta name="color-scheme" content="light dark">
<meta name="theme-color" content="#fbfaf7" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0b1d21" media="(prefers-color-scheme: dark)">
<?php endif; ?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#tresc"><?php esc_html_e( 'Przejdź do treści', 'cod2026' ); ?></a>
<div class="progress" aria-hidden="true"></div>
<?php get_template_part( 'template-parts/sprite' ); ?>

<header class="site-header" id="naglowek">
	<div class="wrap hdr">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' – ' . __( 'strona główna', 'cod2026' ) ); ?>">
			<?php echo cod_logo( 'header' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<nav class="nav" aria-label="<?php esc_attr_e( 'Główna nawigacja', 'cod2026' ); ?>">
			<?php cod_primary_menu( 'nav__list' ); ?>
		</nav>
		<div class="hdr-tools">
			<?php if ( $cod_account ) : ?>
				<a class="icon-link icon-link--account" href="<?php echo esc_url( $cod_account ); ?>" data-track="cod_account_click" data-track-location="naglowek">
					<?php echo cod_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="sr-only"><?php echo is_user_logged_in() ? esc_html__( 'Moje konto', 'cod2026' ) : esc_html__( 'Zaloguj się', 'cod2026' ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( $cod_cart ) : ?>
				<a class="icon-link icon-link--cart" href="<?php echo esc_url( $cod_cart ); ?>" data-track="cod_cart_click" data-track-location="naglowek">
					<?php echo cod_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="sr-only"><?php esc_html_e( 'Koszyk', 'cod2026' ); ?></span>
					<span class="cart-count<?php echo $cod_cart_qty ? '' : ' is-empty'; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: liczba produktów. */ _n( '%d produkt w koszyku', '%d produkty w koszyku', $cod_cart_qty, 'cod2026' ), $cod_cart_qty ) ); ?>"><?php echo (int) $cod_cart_qty; ?></span>
				</a>
			<?php endif; ?>
			<?php echo cod_phone_button( 'naglowek', 'primary', cod_contact( 'phone_display' ), 'btn--sm hdr-call' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button class="menu-btn" type="button" aria-expanded="false" aria-controls="menu" aria-label="<?php esc_attr_e( 'Otwórz menu', 'cod2026' ); ?>"><span aria-hidden="true"></span><span aria-hidden="true"></span></button>
		</div>
	</div>
</header>

<div class="mobile-menu" id="menu">
	<div class="menu-in">
		<nav aria-label="<?php esc_attr_e( 'Menu mobilne', 'cod2026' ); ?>">
			<?php cod_primary_menu( 'menu-links' ); ?>
		</nav>
		<div class="menu-foot">
			<div class="menu-cta">
				<?php echo cod_phone_button( 'menu-mobilne', 'primary', __( 'Zadzwoń', 'cod2026' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<a class="btn btn--ghost" href="<?php echo esc_url( cod_contact_url() ); ?>" data-track="cod_form_open" data-track-location="menu-mobilne"><?php echo cod_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Napisz wiadomość', 'cod2026' ); ?></span></a>
			</div>
			<?php if ( $cod_has_woo ) : ?>
				<p class="menu-shop">
					<a href="<?php echo esc_url( $cod_account ); ?>" data-track="cod_account_click" data-track-location="menu-mobilne"><?php echo cod_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo is_user_logged_in() ? esc_html__( 'Moje konto', 'cod2026' ) : esc_html__( 'Zaloguj się', 'cod2026' ); ?></a>
					<a href="<?php echo esc_url( $cod_cart ); ?>" data-track="cod_cart_click" data-track-location="menu-mobilne"><?php echo cod_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Koszyk', 'cod2026' ); ?><span class="cart-count cart-count--inline<?php echo $cod_cart_qty ? '' : ' is-empty'; ?>"><?php echo (int) $cod_cart_qty; ?></span></a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</div>

<main id="tresc" class="site-main">
