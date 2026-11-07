<?php
/**
 * Pojedyncza usługa.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$bzl_id       = get_the_ID();
	$bzl_icon     = (string) get_post_meta( $bzl_id, 'bzl_icon', true );
	$bzl_scope    = bzl_lines( get_post_meta( $bzl_id, 'bzl_scope', true ) );
	$bzl_audience = bzl_lines( get_post_meta( $bzl_id, 'bzl_audience', true ) );
	$bzl_topic    = (int) get_post_meta( $bzl_id, 'bzl_faq_topic', true );
	$bzl_phone    = (string) bzl_mod( 'phone' );

	$bzl_actions = bzl_button( bzl_mod( 'hero_cta1_label' ), '#kontakt', 'primary', 'arrow-right' );
	if ( $bzl_phone ) {
		$bzl_actions .= bzl_button( $bzl_phone, '{tel}', 'ghost', 'phone' );
	}
	$bzl_media = '';
	if ( has_post_thumbnail() ) {
		$bzl_media = '<div class="page-hero__frame">' . get_the_post_thumbnail(
			null,
			'bzl-card',
			array(
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'sizes'         => '(min-width: 1024px) 40vw, 100vw',
			)
		) . '</div>';
	}

	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'eyebrow' => __( 'Usługa', 'bizlife' ),
			'title'   => get_the_title(),
			'lead'    => has_excerpt() ? get_the_excerpt() : '',
			'actions' => $bzl_actions,
			'icon'    => $bzl_icon ? $bzl_icon : 'doc-check',
			'media'   => $bzl_media,
			'variant' => 'service',
			'art'     => true,
		)
	);
	?>
	<div class="section section--service tone-light">
		<div class="container service-layout">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'prose entry-content service-body' ); ?>>
				<?php the_content(); ?>
			</article>

			<aside class="service-aside" aria-label="<?php esc_attr_e( 'Podsumowanie usługi', 'bizlife' ); ?>">
				<?php if ( $bzl_scope ) : ?>
					<div class="aside-card">
						<h2 class="aside-card__title"><?php esc_html_e( 'Co obejmuje usługa', 'bizlife' ); ?></h2>
						<ul class="check-list">
							<?php foreach ( $bzl_scope as $bzl_line ) : ?>
								<li><?php echo bzl_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $bzl_line ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<?php if ( $bzl_audience ) : ?>
					<div class="aside-card aside-card--plain">
						<h2 class="aside-card__title"><?php esc_html_e( 'Dla kogo', 'bizlife' ); ?></h2>
						<ul class="dot-list">
							<?php foreach ( $bzl_audience as $bzl_line ) : ?>
								<li><?php echo esc_html( $bzl_line ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<div class="aside-card aside-card--dark">
					<h2 class="aside-card__title"><?php esc_html_e( 'Porozmawiajmy o Twojej firmie', 'bizlife' ); ?></h2>
					<p class="aside-card__text"><?php esc_html_e( 'Pierwsza konsultacja jest bezpłatna.', 'bizlife' ); ?></p>
					<?php if ( $bzl_phone ) : ?>
						<a class="aside-card__phone" href="<?php echo esc_url( bzl_tel_href( $bzl_phone ), array( 'tel' ) ); ?>"><?php echo bzl_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $bzl_phone ); ?></a>
					<?php endif; ?>
					<?php echo bzl_button( __( 'Napisz do nas', 'bizlife' ), '#kontakt', 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</aside>
		</div>
	</div>

	<?php
	$bzl_faqs = $bzl_topic ? bzl_faqs(
		array(
			'limit' => 8,
			'topic' => $bzl_topic,
		)
	) : array();
	if ( $bzl_faqs ) :
		?>
		<section class="section faq tone-darker" aria-labelledby="service-faq-title">
			<div class="container split split--faq">
				<div class="split__aside">
					<?php
					bzl_section_header(
						array(
							'eyebrow' => 'FAQ',
							'title'   => __( 'Pytania o tę usługę', 'bizlife' ),
							'id'      => 'service-faq-title',
						)
					);
					?>
					<?php if ( bzl_link_has_page( '{faq}' ) ) : ?>
						<div class="faq__actions"><?php echo bzl_button( bzl_mod( 'faq_cta_label' ), '{faq}', 'ghost', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<?php endif; ?>
				</div>
				<?php
				get_template_part(
					'template-parts/components/accordion',
					null,
					array(
						'items' => $bzl_faqs,
						'id'    => 'service-faq',
					)
				);
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$bzl_others = bzl_services_query( 3, array( $bzl_id ) );
	if ( $bzl_others->have_posts() ) :
		?>
		<section class="section services tone-dark" aria-labelledby="other-services-title">
			<div class="container">
				<div class="section-top">
					<?php
					bzl_section_header(
						array(
							'eyebrow' => __( 'Oferta', 'bizlife' ),
							'title'   => __( 'Pozostałe usługi', 'bizlife' ),
							'id'      => 'other-services-title',
						)
					);
					?>
					<a class="link-arrow section-top__link" href="<?php echo esc_url( get_post_type_archive_link( 'bzl_service' ) ); ?>"><?php esc_html_e( 'Wszystkie usługi', 'bizlife' ); ?><?php echo bzl_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				</div>
				<ul class="service-grid service-grid--3">
					<?php
					foreach ( $bzl_others->posts as $bzl_i => $bzl_post ) {
						get_template_part(
							'template-parts/cards/service',
							null,
							array(
								'post'  => $bzl_post,
								'index' => $bzl_i,
							)
						);
					}
					?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

	<?php
	get_template_part(
		'template-parts/sections/contact',
		null,
		array( 'preselect' => get_the_title() )
	);
endwhile;

get_footer();
