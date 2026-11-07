<?php
/**
 * Sekcja: najnowsze wpisy (dla stron, na których ją zostawiono – blogu nie ma w menu).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_q = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
if ( ! $cod_q->have_posts() ) {
	return;
}
?>
<section<?php echo cod_sec_attrs( 'blog', 'fx-blog' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wrap">
		<?php cod_sec_head( 'blog', 'left' ); ?>
		<div class="post-grid reveal-stagger">
			<?php
			while ( $cod_q->have_posts() ) {
				$cod_q->the_post();
				get_template_part( 'template-parts/cards/post-card' );
			}
			wp_reset_postdata();
			?>
		</div>
		<?php cod_sec_buttons( 'blog_section_buttons', 'blog_section_button', cod_sec_loc( 'blog' ), 'fx-buttons reveal' ); ?>
	</div>
</section>
