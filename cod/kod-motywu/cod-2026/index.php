<?php
/**
 * Lista wpisów (blog, archiwa, wyniki wyszukiwania) – szablon zapasowy.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
get_template_part( 'template-parts/page-hero' );
?>
<div class="section section--tight">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/cards/post-card' );
				}
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => __( 'Poprzednie', 'cod2026' ),
					'next_text'          => __( 'Następne', 'cod2026' ),
					'screen_reader_text' => __( 'Nawigacja po wpisach', 'cod2026' ),
				)
			);
			?>
		<?php else : ?>
			<div class="empty">
				<p class="lede"><?php esc_html_e( 'Nic tu jeszcze nie ma.', 'cod2026' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
