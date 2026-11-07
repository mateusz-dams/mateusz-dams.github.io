<?php
/**
 * Panel danych kontaktowych + kroki „Co dalej?”.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_a     = bzl_address();
$bzl_phone = (string) bzl_mod( 'phone' );
$bzl_email = (string) bzl_mod( 'email' );
$bzl_steps = bzl_slots( 'contact_step' );
$bzl_wa    = (string) bzl_mod( 'social_whatsapp' );
?>
<div class="contact__info">
	<ul class="contact-list" data-reveal>
		<?php if ( $bzl_phone ) : ?>
			<li>
				<a class="contact-item" href="<?php echo esc_url( bzl_tel_href( $bzl_phone ), array( 'tel' ) ); ?>">
					<span class="contact-item__icon"><?php echo bzl_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span><span class="contact-item__label"><?php esc_html_e( 'Telefon', 'bizlife' ); ?></span><span class="contact-item__value"><?php echo esc_html( $bzl_phone ); ?></span></span>
				</a>
			</li>
		<?php endif; ?>
		<?php if ( $bzl_email ) : ?>
			<li>
				<a class="contact-item" href="<?php echo esc_url( 'mailto:' . $bzl_email, array( 'mailto' ) ); ?>">
					<span class="contact-item__icon"><?php echo bzl_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span><span class="contact-item__label"><?php esc_html_e( 'E-mail', 'bizlife' ); ?></span><span class="contact-item__value"><?php echo esc_html( $bzl_email ); ?></span></span>
				</a>
			</li>
		<?php endif; ?>
		<?php if ( $bzl_wa ) : ?>
			<li>
				<a class="contact-item" href="<?php echo esc_url( $bzl_wa ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="contact-item__icon"><?php echo bzl_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span><span class="contact-item__label">WhatsApp</span><span class="contact-item__value"><?php esc_html_e( 'Napisz wiadomość', 'bizlife' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(nowa karta)', 'bizlife' ); ?></span></span></span>
				</a>
			</li>
		<?php endif; ?>
		<li>
			<div class="contact-item">
				<span class="contact-item__icon"><?php echo bzl_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span><span class="contact-item__label"><?php esc_html_e( 'Adres biura', 'bizlife' ); ?></span><address class="contact-item__value"><?php echo esc_html( $bzl_a['street'] ); ?>, <?php echo esc_html( trim( $bzl_a['postal'] . ' ' . $bzl_a['city'] ) ); ?></address></span>
			</div>
		</li>
		<?php if ( bzl_mod( 'hours' ) ) : ?>
			<li>
				<div class="contact-item">
					<span class="contact-item__icon"><?php echo bzl_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span><span class="contact-item__label"><?php esc_html_e( 'Godziny pracy', 'bizlife' ); ?></span><span class="contact-item__value"><?php echo nl2br( esc_html( bzl_mod( 'hours' ) ) ); ?></span></span>
				</div>
			</li>
		<?php endif; ?>
	</ul>

	<?php if ( $bzl_steps ) : ?>
		<div class="next-steps" data-reveal>
			<?php if ( bzl_mod( 'contact_steps_title' ) ) : ?>
				<h3 class="next-steps__title"><?php echo esc_html( bzl_mod( 'contact_steps_title' ) ); ?></h3>
			<?php endif; ?>
			<ol class="next-steps__list">
				<?php foreach ( $bzl_steps as $bzl_step ) : ?>
					<li>
						<span class="next-steps__name"><?php echo esc_html( $bzl_step['title'] ); ?></span>
						<?php if ( $bzl_step['text'] ) : ?>
							<span class="next-steps__text"><?php echo esc_html( $bzl_step['text'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	<?php endif; ?>

	<?php bzl_social_links( 'socials--contact' ); ?>
</div>
