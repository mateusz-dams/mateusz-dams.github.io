<?php
/**
 * Nagłówek strony.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;
$bzl_phone = (string) bzl_mod( 'phone' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Przejdź do treści', 'bizlife' ); ?></a>

<header class="site-header" data-header>
	<div class="container site-header__inner">
		<?php bzl_logo( 'header' ); ?>

		<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Menu główne', 'bizlife' ); ?>" data-nav>
			<div class="primary-nav__scroll">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'menu nav-menu',
							'depth'          => 2,
							'walker'         => new BZL_Nav_Walker(),
							'fallback_cb'    => false,
						)
					);
				} else {
					bzl_fallback_menu();
				}
				?>
				<div class="primary-nav__extra">
					<?php echo bzl_button( bzl_mod( 'header_cta_label' ), bzl_mod( 'header_cta_url' ), 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php if ( $bzl_phone ) : ?>
						<a class="nav-contact" href="<?php echo esc_url( bzl_tel_href( $bzl_phone ), array( 'tel' ) ); ?>"><?php echo bzl_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $bzl_phone ); ?></span></a>
					<?php endif; ?>
					<?php if ( bzl_mod( 'email' ) ) : ?>
						<a class="nav-contact" href="<?php echo esc_url( 'mailto:' . bzl_mod( 'email' ), array( 'mailto' ) ); ?>"><?php echo bzl_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( bzl_mod( 'email' ) ); ?></span></a>
					<?php endif; ?>
					<?php bzl_social_links( 'socials--nav' ); ?>
				</div>
			</div>
		</nav>

		<div class="site-header__actions">
			<?php if ( $bzl_phone && bzl_mod( 'header_show_phone' ) ) : ?>
				<a class="header-phone" href="<?php echo esc_url( bzl_tel_href( $bzl_phone ), array( 'tel' ) ); ?>">
					<?php echo bzl_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="header-phone__num"><?php echo esc_html( $bzl_phone ); ?></span>
				</a>
			<?php endif; ?>
			<span class="header-cta"><?php echo bzl_button( bzl_mod( 'header_cta_label' ), bzl_mod( 'header_cta_url' ), 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle>
				<span class="nav-toggle__box" aria-hidden="true"><span></span><span></span><span></span></span>
				<span class="screen-reader-text" data-nav-toggle-label><?php esc_html_e( 'Otwórz menu', 'bizlife' ); ?></span>
			</button>
		</div>
	</div>
</header>

<main id="main" class="site-main" tabindex="-1">
