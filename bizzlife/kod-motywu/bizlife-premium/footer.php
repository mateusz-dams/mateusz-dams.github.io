<?php
/**
 * Stopka strony.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_a       = bzl_address();
$bzl_phone   = (string) bzl_mod( 'phone' );
$bzl_email   = (string) bzl_mod( 'email' );
$bzl_privacy = get_privacy_policy_url();
$bzl_cookies = bzl_mod( 'cookie_info_url' ) ? (string) bzl_mod( 'cookie_info_url' ) : $bzl_privacy;
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="site-footer__grid">
			<div class="site-footer__brand">
				<?php bzl_logo( 'footer' ); ?>
				<?php if ( bzl_mod( 'footer_about' ) ) : ?>
					<p class="site-footer__about"><?php echo esc_html( bzl_mod( 'footer_about' ) ); ?></p>
				<?php endif; ?>
				<?php bzl_social_links( 'socials--footer' ); ?>
			</div>

			<div class="site-footer__col">
				<h2 class="site-footer__title"><?php esc_html_e( 'Dane firmy', 'bizlife' ); ?></h2>
				<address class="footer-list">
					<span class="footer-list__item"><?php echo bzl_icon( 'id' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( bzl_mod( 'company_name' ) ); ?></span></span>
					<span class="footer-list__item"><?php echo bzl_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $bzl_a['street'] ); ?><br><?php echo esc_html( trim( $bzl_a['postal'] . ' ' . $bzl_a['city'] ) ); ?></span></span>
				</address>
				<dl class="company-ids">
					<?php
					foreach ( array(
						'krs'   => 'KRS',
						'nip'   => 'NIP',
						'regon' => 'REGON',
					) as $bzl_k => $bzl_label ) :
						?>
														<?php if ( bzl_mod( $bzl_k ) ) : ?>
							<div><dt><?php echo esc_html( $bzl_label ); ?></dt><dd><?php echo esc_html( bzl_mod( $bzl_k ) ); ?></dd></div>
						<?php endif; ?>
					<?php endforeach; ?>
				</dl>
			</div>

			<nav class="site-footer__col" aria-labelledby="footer-menu-title">
				<h2 class="site-footer__title" id="footer-menu-title"><?php esc_html_e( 'Menu', 'bizlife' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
						)
					);
				} else {
					echo '<ul class="footer-menu">';
					$bzl_links = array(
						array( __( 'Strona główna', 'bizlife' ), home_url( '/' ) ),
						array( __( 'Usługi', 'bizlife' ), get_post_type_archive_link( 'bzl_service' ) ),
						array( __( 'O nas', 'bizlife' ), bzl_url( '{o-nas}' ) ),
						array( __( 'Aktualności', 'bizlife' ), bzl_url( '{aktualnosci}' ) ),
						array( __( 'FAQ', 'bizlife' ), bzl_url( '{faq}' ) ),
						array( __( 'Kontakt', 'bizlife' ), bzl_url( '{kontakt}' ) ),
					);
					foreach ( $bzl_links as $bzl_l ) {
						echo '<li><a href="' . esc_url( $bzl_l[1] ) . '">' . esc_html( $bzl_l[0] ) . '</a></li>';
					}
					echo '</ul>';
				}
				?>
			</nav>

			<?php $bzl_footer_services = bzl_services_query( 12 )->posts; ?>
			<?php if ( $bzl_footer_services ) : ?>
			<nav class="site-footer__col" aria-labelledby="footer-services-title">
				<h2 class="site-footer__title" id="footer-services-title"><?php esc_html_e( 'Usługi', 'bizlife' ); ?></h2>
				<ul class="footer-menu">
					<?php foreach ( $bzl_footer_services as $bzl_s ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $bzl_s ) ); ?>"><?php echo esc_html( get_the_title( $bzl_s ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
			<?php endif; ?>

			<div class="site-footer__col">
				<h2 class="site-footer__title"><?php esc_html_e( 'Kontakt', 'bizlife' ); ?></h2>
				<ul class="footer-list">
					<?php if ( $bzl_phone ) : ?>
						<li><a class="footer-list__item" href="<?php echo esc_url( bzl_tel_href( $bzl_phone ), array( 'tel' ) ); ?>"><?php echo bzl_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $bzl_phone ); ?></span></a></li>
					<?php endif; ?>
					<?php if ( $bzl_email ) : ?>
						<li><a class="footer-list__item" href="<?php echo esc_url( 'mailto:' . $bzl_email, array( 'mailto' ) ); ?>"><?php echo bzl_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $bzl_email ); ?></span></a></li>
					<?php endif; ?>
					<?php if ( bzl_mod( 'hours' ) ) : ?>
						<li><span class="footer-list__item"><?php echo bzl_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo nl2br( esc_html( bzl_mod( 'hours' ) ) ); ?></span></span></li>
					<?php endif; ?>
					<li><a class="footer-list__item" href="<?php echo esc_url( bzl_directions_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php echo bzl_icon( 'route' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Wyznacz trasę', 'bizlife' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(Google Maps, nowa karta)', 'bizlife' ); ?></span></span></a></li>
				</ul>
			</div>
		</div>

		<div class="site-footer__bottom">
			<p class="site-footer__copy">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( rtrim( (string) bzl_mod( 'company_name' ), '.' ) ); ?>. <?php esc_html_e( 'Wszelkie prawa zastrzeżone.', 'bizlife' ); ?></p>
			<nav class="legal-nav" aria-label="<?php esc_attr_e( 'Informacje prawne', 'bizlife' ); ?>">
				<?php
				if ( has_nav_menu( 'legal' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'legal',
							'container'      => false,
							'menu_class'     => 'legal-menu',
							'depth'          => 1,
						)
					);
					if ( bzl_mod( 'cookie_info_url' ) ) {
						echo '<ul class="legal-menu"><li><a href="' . esc_url( $bzl_cookies ) . '">' . esc_html__( 'Informacja o cookies', 'bizlife' ) . '</a></li></ul>';
					}
				} else {
					echo '<ul class="legal-menu">';
					if ( $bzl_privacy ) {
						echo '<li><a href="' . esc_url( $bzl_privacy ) . '">' . esc_html__( 'Polityka prywatności', 'bizlife' ) . '</a></li>';
					}
					if ( $bzl_cookies ) {
						echo '<li><a href="' . esc_url( $bzl_cookies ) . '">' . esc_html__( 'Informacja o cookies', 'bizlife' ) . '</a></li>';
					}
					echo '</ul>';
				}
				?>
			</nav>
			<?php if ( bzl_mod( 'footer_credits' ) ) : ?>
				<p class="site-footer__credits"><?php echo esc_html( bzl_mod( 'footer_credits' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
