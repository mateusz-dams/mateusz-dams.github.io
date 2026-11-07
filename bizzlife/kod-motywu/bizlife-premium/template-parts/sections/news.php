<?php
/**
 * Sekcja: najnowsze wpisy.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_q = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => max( 1, min( 6, (int) bzl_mod( 'news_count' ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
if ( ! $bzl_q->have_posts() ) {
	return;
}
?>
<section class="section news <?php echo esc_attr( bzl_tone( 'news', 'dark' ) ); ?>" id="aktualnosci" aria-labelledby="news-title">
	<div class="container">
		<div class="section-top">
			<?php
			bzl_section_header(
				array(
					'eyebrow' => bzl_mod( 'news_eyebrow' ),
					'title'   => bzl_mod( 'news_title' ),
					'lead'    => bzl_mod( 'news_lead' ),
					'id'      => 'news-title',
					'ghost'   => __( 'Wiedza', 'bizlife' ),
				)
			);
			?>
			<?php if ( bzl_link_has_page( '{aktualnosci}' ) ) : ?>
				<?php echo bzl_button( bzl_mod( 'news_cta_label' ), '{aktualnosci}', 'ghost', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>
		</div>
		<ul class="post-grid">
			<?php
			$bzl_n = 0;
			while ( $bzl_q->have_posts() ) {
				$bzl_q->the_post();
				get_template_part( 'template-parts/cards/post', null, array( 'index' => $bzl_n++ ) );
			}
			wp_reset_postdata();
			?>
		</ul>
	</div>
</section>
