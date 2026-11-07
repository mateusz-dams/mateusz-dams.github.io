<?php
/**
 * Archiwum usług (/uslugi/).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

$bzl_actions = bzl_button( bzl_mod( 'hero_cta1_label' ), '{kontakt}', 'primary', 'arrow-right' );
if ( bzl_mod( 'phone' ) ) {
	$bzl_actions .= bzl_button( bzl_mod( 'phone' ), '{tel}', 'ghost', 'phone' );
}

get_template_part(
	'template-parts/components/page-hero',
	null,
	array(
		'eyebrow' => bzl_mod( 'services_eyebrow' ),
		'title'   => bzl_mod( 'services_title' ),
		'lead'    => bzl_mod( 'services_lead' ),
		'actions' => $bzl_actions,
		'ghost'   => __( 'Oferta', 'bizlife' ),
		'media'   => ( $bzl_media_img = bzl_image(
			(int) bzl_mod( 'services_image' ),
			'bzl-card',
			array(
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'sizes'         => '(min-width: 1024px) 40vw, 100vw',
			)
		) ) ? '<div class="page-hero__frame">' . $bzl_media_img . '</div>' : '',
	)
);
?>
<section class="section services tone-dark" aria-label="<?php esc_attr_e( 'Lista usług', 'bizlife' ); ?>">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<ul class="service-grid service-grid--archive">
				<?php
				$bzl_n = 0;
				while ( have_posts() ) {
					the_post();
					get_template_part(
						'template-parts/cards/service',
						null,
						array(
							'post'    => get_post(),
							'index'   => $bzl_n++,
							'heading' => 2,
						)
					);
				}
				?>
				<?php if ( bzl_mod( 'services_cta_title' ) ) : ?>
					<li class="service-card service-card--cta" data-reveal>
						<h2 class="service-card__title"><?php echo esc_html( bzl_mod( 'services_cta_title' ) ); ?></h2>
						<p class="service-card__text"><?php echo esc_html( bzl_mod( 'services_cta_text' ) ); ?></p>
						<?php echo bzl_button( bzl_mod( 'services_cta_label' ), '{kontakt}', 'dark', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</li>
				<?php endif; ?>
			</ul>
		<?php else : ?>
			<div class="empty-state"><p><?php esc_html_e( 'Lista usług jest w przygotowaniu.', 'bizlife' ); ?></p></div>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/sections/process', null, array( 'compact' => true ) );
get_template_part( 'template-parts/sections/cta', null, array( 'tone' => 'tone-darker' ) );

get_footer();
