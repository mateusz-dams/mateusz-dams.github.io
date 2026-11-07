<?php
/**
 * Template Name: Kontakt
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
			'eyebrow' => bzl_mod( 'contact_eyebrow' ),
			'title'   => get_the_title(),
			'lead'    => has_excerpt() ? get_the_excerpt() : bzl_mod( 'contact_lead' ),
			'ghost'   => __( 'Kontakt', 'bizlife' ),
			'media'   => has_post_thumbnail() ? '<div class="page-hero__frame">' . get_the_post_thumbnail(
				null,
				'bzl-card',
				array(
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'sizes'         => '(min-width: 1024px) 40vw, 100vw',
				)
			) . '</div>' : '',
		)
	);

	get_template_part( 'template-parts/sections/contact', null, array( 'show_header' => false ) );

	if ( '' !== trim( get_the_content() ) ) :
		?>
		<div class="section section--page tone-light">
			<div class="container">
				<article <?php post_class( 'prose entry-content' ); ?>><?php the_content(); ?></article>
			</div>
		</div>
		<?php
	endif;
endwhile;

get_footer();
