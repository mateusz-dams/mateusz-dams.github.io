<?php
/**
 * Template Name: O nas
 * Template Post Type: page
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'eyebrow' => bzl_mod( 'about_eyebrow' ),
			'title'   => get_the_title(),
			'lead'    => has_excerpt() ? get_the_excerpt() : bzl_mod( 'footer_about' ),
			'ghost'   => __( 'O nas', 'bizlife' ),
		)
	);

	$bzl_img_id = has_post_thumbnail() ? get_post_thumbnail_id() : (int) bzl_mod( 'about_image' );
	$bzl_img    = bzl_image(
		$bzl_img_id,
		'bzl-portrait',
		array(
			'class'         => 'about__img',
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'sizes'         => '(min-width: 1024px) 40vw, 100vw',
		)
	);
	?>
	<section class="section about about--page tone-light" aria-label="<?php esc_attr_e( 'Kim jesteśmy', 'bizlife' ); ?>">
		<div class="container about__grid">
			<figure class="about__media about__media--sticky">
				<div class="about__frame">
					<?php echo $bzl_img ? $bzl_img : bzl_image_placeholder( __( 'Ustaw zdjęcie wyróżniające strony', 'bizlife' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<?php if ( bzl_mod( 'about_person_name' ) ) : ?>
					<figcaption class="about__person">
						<span class="about__name"><?php echo esc_html( bzl_mod( 'about_person_name' ) ); ?></span>
						<span class="about__role"><?php echo esc_html( bzl_mod( 'about_person_role' ) ); ?></span>
					</figcaption>
				<?php endif; ?>
			</figure>
			<article <?php post_class( 'prose entry-content about__content' ); ?>>
				<?php the_content(); ?>
			</article>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/sections/why' );

	$bzl_services = bzl_services_query( 20 );
	if ( $bzl_services->have_posts() ) :
		?>
		<section class="section tone-light" aria-labelledby="about-services-title">
			<div class="container split">
				<?php
				bzl_section_header(
					array(
						'eyebrow' => bzl_mod( 'services_eyebrow' ),
						'title'   => __( 'W czym możemy pomóc', 'bizlife' ),
						'id'      => 'about-services-title',
					)
				);
				?>
				<ul class="link-list">
					<?php foreach ( $bzl_services->posts as $bzl_s ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $bzl_s ) ); ?>"><span class="link-list__icon"><?php echo bzl_icon( get_post_meta( $bzl_s->ID, 'bzl_icon', true ) ? (string) get_post_meta( $bzl_s->ID, 'bzl_icon', true ) : 'doc-check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="link-list__text"><?php echo esc_html( get_the_title( $bzl_s ) ); ?></span><?php echo bzl_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php
	endif;

	get_template_part( 'template-parts/sections/cta', null, array( 'tone' => 'tone-dark' ) );
endwhile;

get_footer();
