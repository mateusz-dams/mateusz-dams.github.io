<?php
/**
 * Standardowa strona informacyjna.
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
			'title' => get_the_title(),
			'lead'  => has_excerpt() ? get_the_excerpt() : '',
		)
	);
	?>
	<div class="section section--page tone-light">
		<div class="container">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'prose entry-content' ); ?>>
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Strony wpisu', 'bizlife' ) . '">',
						'after'  => '</nav>',
					)
				);
				?>
				<?php if ( get_edit_post_link() ) : ?>
					<p class="edit-link"><?php edit_post_link( __( 'Edytuj stronę', 'bizlife' ) ); ?></p>
				<?php endif; ?>
			</article>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
