<?php
/**
 * Stopka strony.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_last     = is_singular() && cod_has_sections( get_queried_object_id() ) ? cod_last_layout( get_queried_object_id() ) : '';
$cod_show_cta = apply_filters( 'cod_show_cta_band', ! is_front_page() && ! cod_is_shop_context() && ! cod_is_learning_context() && ! in_array( $cod_last, array( 'call_to_action_section', 'contact_section', 'map_section' ), true ) );
?>
</main>

<?php
if ( $cod_show_cta ) {
	get_template_part( 'template-parts/cta-band' );
}
?>

<footer class="footer">
	<div class="wrap">
		<div class="f-grid">
			<div class="f-brand">
				<a class="site-logo site-logo--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' – ' . __( 'strona główna', 'cod2026' ) ); ?>">
					<?php echo cod_logo( 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<p>
					<?php echo esc_html( cod_contact( 'company' ) ); ?><br>
					<?php echo esc_html( cod_contact( 'address' ) ); ?><br>
					<?php if ( cod_contact( 'nip' ) ) : ?>
						<?php echo esc_html( 'NIP: ' . cod_contact( 'nip' ) ); ?><br>
					<?php endif; ?>
					<?php if ( cod_contact( 'regon' ) ) : ?>
						<?php echo esc_html( 'REGON: ' . cod_contact( 'regon' ) ); ?>
					<?php endif; ?>
				</p>
			</div>
			<div>
				<h2><?php esc_html_e( 'Kontakt', 'cod2026' ); ?></h2>
				<ul>
					<li><a href="<?php echo esc_attr( cod_contact( 'phone_href' ) ); ?>" data-track-location="stopka"><?php echo esc_html( cod_contact( 'phone' ) ); ?></a></li>
					<li><a href="mailto:<?php echo esc_attr( cod_contact( 'email' ) ); ?>" data-track-location="stopka"><?php echo esc_html( cod_contact( 'email' ) ); ?></a></li>
					<li><?php echo esc_html( cod_contact( 'hours' ) ); ?></li>
					<li><a href="<?php echo esc_url( cod_contact( 'maps_url' ) ); ?>" target="_blank" rel="noopener" data-track="cod_directions_click" data-track-location="stopka"><?php esc_html_e( 'Jak dojechać', 'cod2026' ); ?><span class="sr-only"> <?php esc_html_e( '(otworzy się w nowej karcie)', 'cod2026' ); ?></span></a></li>
				</ul>
				<?php if ( cod_has_acf() && have_rows( 'social_media', 'option' ) ) : ?>
					<ul class="socials">
						<?php
						while ( have_rows( 'social_media', 'option' ) ) :
							the_row();
							$cod_url  = (string) get_sub_field( 'social_media_url' );
							$cod_icon = get_sub_field( 'social_media_icon' );
							$cod_name = is_array( $cod_icon ) && ! empty( $cod_icon['title'] ) ? $cod_icon['title'] : wp_parse_url( $cod_url, PHP_URL_HOST );
							if ( '' === $cod_url ) {
								continue;
							}
							?>
							<li>
								<a href="<?php echo esc_url( $cod_url ); ?>" target="_blank" rel="noopener" data-track="cod_social_click" data-track-location="stopka">
									<?php
									$cod_net = '';
									foreach ( array( 'facebook', 'instagram', 'linkedin', 'youtube', 'tiktok' ) as $cod_n ) {
										if ( false !== stripos( $cod_url, $cod_n ) || ( 'youtube' === $cod_n && false !== stripos( $cod_url, 'youtu.be' ) ) ) {
											$cod_net = $cod_n;
											break;
										}
									}
									if ( $cod_net ) {
										echo cod_icon( $cod_net ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										$cod_name = ucfirst( $cod_net );
									} else {
										echo cod_img( $cod_icon, 'thumbnail', array( 'alt' => '', 'width' => 22, 'height' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
									<span class="sr-only"><?php echo esc_html( $cod_name ); ?></span>
								</a>
							</li>
						<?php endwhile; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div>
				<h2><?php esc_html_e( 'Na stronie', 'cod2026' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer_menu' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer_menu',
							'container'      => false,
							'menu_class'     => 'f-links',
							'depth'          => 1,
						)
					);
				} else {
					echo '<ul>';
					foreach ( cod_default_menu_items() as $cod_item ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( $cod_item['url'] ), esc_html( $cod_item['label'] ) );
					}
					echo '</ul>';
				}
				?>
			</div>
			<div>
				<h2><?php esc_html_e( 'Informacje', 'cod2026' ); ?></h2>
				<?php
				if ( has_nav_menu( 'links_menu' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'links_menu',
							'container'      => false,
							'menu_class'     => 'f-links',
							'depth'          => 1,
						)
					);
				} else {
					echo '<ul>';
					$cod_privacy = get_privacy_policy_url();
					if ( $cod_privacy ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( $cod_privacy ), esc_html__( 'Polityka prywatności', 'cod2026' ) );
					}
					if ( class_exists( 'WooCommerce' ) ) {
						$cod_terms = wc_terms_and_conditions_page_id();
						if ( $cod_terms ) {
							printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $cod_terms ) ), esc_html( get_the_title( $cod_terms ) ) );
						}
						printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_page_permalink( 'myaccount' ) ), esc_html__( 'Moje konto', 'cod2026' ) );
					}
					echo '</ul>';
				}
				?>
			</div>
		</div>
		<div class="f-bottom">
			<span><?php echo wp_kses_post( cod_option( 'copyright', '© ' . gmdate( 'Y' ) . ' ' . cod_contact( 'company' ) ) ); ?></span>
			<a href="#tresc"><?php esc_html_e( 'Wróć na górę', 'cod2026' ); ?></a>
		</div>
	</div>
</footer>

<?php if ( ! cod_is_learning_context() ) : ?>
<div class="fab" id="fab" role="region" aria-label="<?php esc_attr_e( 'Szybki kontakt', 'cod2026' ); ?>">
	<a class="fab-mail" href="<?php echo esc_url( cod_contact_url() ); ?>" data-track="cod_form_open" data-track-location="przycisk-plywajacy"><?php echo cod_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="sr-only"><?php esc_html_e( 'Napisz wiadomość', 'cod2026' ); ?></span></a>
	<a class="fab-call" href="<?php echo esc_attr( cod_contact( 'phone_href' ) ); ?>" data-track-location="przycisk-plywajacy"><span class="ring" aria-hidden="true"></span><?php echo cod_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Zadzwoń', 'cod2026' ); ?></a>
</div>
<?php endif; ?>

<dialog class="drawer modal on-light" id="svc-dialog" aria-labelledby="d-title">
	<div class="m-box">
		<div class="m-media" id="d-media" hidden></div>
		<div class="m-main">
			<div class="m-top">
				<span class="m-handle" aria-hidden="true"></span>
				<span class="num-tag num-tag--flat" id="d-kicker"><?php esc_html_e( 'Szczegóły usługi', 'cod2026' ); ?></span>
				<button class="x" type="button" data-close aria-label="<?php esc_attr_e( 'Zamknij', 'cod2026' ); ?>"><?php echo cod_icon( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</div>
			<div class="m-scroll" id="d-scroll" tabindex="0">
				<div id="d-body"></div>
			</div>
			<div class="m-foot">
				<div class="d-cta" id="d-cta">
					<div class="btn-row">
						<?php echo cod_phone_button( 'szczegoly-uslugi', 'primary', __( 'Zadzwoń i umów', 'cod2026' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a class="btn btn--ghost" href="<?php echo esc_url( cod_contact_url() ); ?>" id="d-form" data-track="cod_form_open" data-track-location="szczegoly-uslugi"><span><?php esc_html_e( 'Napisz wiadomość', 'cod2026' ); ?></span></a>
					</div>
					<a class="link-arrow d-page" id="d-page" href="#" hidden><span><?php esc_html_e( 'Zobacz pełną stronę usługi', 'cod2026' ); ?></span><?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
				<div id="d-own"></div>
			</div>
		</div>
	</div>
</dialog>

<?php wp_footer(); ?>
</body>
</html>
