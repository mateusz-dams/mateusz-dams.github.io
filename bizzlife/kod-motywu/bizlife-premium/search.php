<?php
/**
 * Wyniki wyszukiwania.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/components/page-hero',
	null,
	array(
		'eyebrow' => __( 'Wyszukiwanie', 'bizlife' ),
		/* translators: %s: fraza. */
		'title'   => sprintf( __( 'Wyniki dla: %s', 'bizlife' ), get_search_query() ),
		'actions' => get_search_form( array( 'echo' => false ) ),
	)
);
?>
<section class="section tone-light" aria-label="<?php esc_attr_e( 'Wyniki', 'bizlife' ); ?>">
	<div class="container container--narrow">
		<?php if ( have_posts() ) : ?>
			<ol class="search-results">
				<?php
				while ( have_posts() ) :
					the_post();
					$bzl_type = get_post_type_object( get_post_type() );
					?>
					<li class="search-result">
						<p class="search-result__type"><?php echo esc_html( 'bzl_service' === get_post_type() ? __( 'Usługa', 'bizlife' ) : ( 'post' === get_post_type() ? __( 'Artykuł', 'bizlife' ) : $bzl_type->labels->singular_name ) ); ?></p>
						<h2 class="search-result__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="search-result__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28, '…' ) ); ?></p>
					</li>
				<?php endwhile; ?>
			</ol>
			<?php
			the_posts_pagination(
				array(
					'mid_size'   => 1,
					'prev_text'  => bzl_icon( 'arrow-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Poprzednia strona', 'bizlife' ) . '</span>',
					'next_text'  => '<span class="screen-reader-text">' . esc_html__( 'Następna strona', 'bizlife' ) . '</span>' . bzl_icon( 'arrow-right' ),
					'aria_label' => __( 'Strony wyników', 'bizlife' ),
					'class'      => 'pagination',
				)
			);
			?>
		<?php else : ?>
			<div class="empty-state">
				<p><?php esc_html_e( 'Brak wyników. Spróbuj innej frazy lub skontaktuj się z nami.', 'bizlife' ); ?></p>
				<?php echo bzl_button( __( 'Kontakt', 'bizlife' ), '{kontakt}', 'dark', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
