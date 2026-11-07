<?php
/**
 * Sekcja: Usługi (pobierane z typu treści „Usługi”).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_q = bzl_services_query( 24 );
?>
<section class="section services <?php echo esc_attr( bzl_tone( 'services', 'dark' ) ); ?>" id="uslugi" aria-labelledby="services-title">
	<div class="container">
		<div class="section-top">
			<?php
			bzl_section_header(
				array(
					'eyebrow' => bzl_mod( 'services_eyebrow' ),
					'title'   => bzl_mod( 'services_title' ),
					'lead'    => bzl_mod( 'services_lead' ),
					'id'      => 'services-title',
					'ghost'   => __( 'Usługi', 'bizlife' ),
				)
			);
			?>
			<a class="link-arrow section-top__link" href="<?php echo esc_url( get_post_type_archive_link( 'bzl_service' ) ); ?>"><?php esc_html_e( 'Pełna oferta', 'bizlife' ); ?><?php echo bzl_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>

		<?php if ( $bzl_q->have_posts() ) : ?>
			<ul class="service-grid">
				<?php
				$bzl_n = 0;
				foreach ( $bzl_q->posts as $bzl_post ) {
					get_template_part(
						'template-parts/cards/service',
						null,
						array(
							'post'  => $bzl_post,
							'index' => $bzl_n++,
						)
					);
				}
				?>
				<?php if ( bzl_mod( 'services_cta_show' ) && bzl_mod( 'services_cta_title' ) ) : ?>
					<li class="service-card service-card--cta" data-reveal style="--d:<?php echo (int) $bzl_n; ?>">
						<h3 class="service-card__title"><?php echo esc_html( bzl_mod( 'services_cta_title' ) ); ?></h3>
						<p class="service-card__text"><?php echo esc_html( bzl_mod( 'services_cta_text' ) ); ?></p>
						<?php echo bzl_button( bzl_mod( 'services_cta_label' ), bzl_mod( 'services_cta_url' ), 'dark', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</li>
				<?php endif; ?>
			</ul>
		<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
			<p class="admin-hint"><?php esc_html_e( 'Brak usług. Dodaj je w kokpicie (Usługi → Dodaj usługę) lub uruchom kreator: Wygląd → Bizzlife: start.', 'bizlife' ); ?></p>
		<?php endif; ?>
	</div>
</section>
