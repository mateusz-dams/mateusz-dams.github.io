<?php
/**
 * Pojedynczy wpis blogu.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$cod_avatar = cod_option( 'avatar', '' );
	$cod_author = (string) cod_option( 'name', '' );
	$cod_bio    = (string) cod_option( 'biogram', '' );
	?>
	<article <?php post_class( 'article' ); ?>>
		<header class="page-hero page-hero--article">
			<div class="page-hero__shape" aria-hidden="true"></div>
			<div class="wrap fx-narrow">
				<?php cod_breadcrumbs(); ?>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
				<p class="article__meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<?php if ( $cod_author ) : ?>
						<span><?php echo esc_html( $cod_author ); ?></span>
					<?php endif; ?>
				</p>
			</div>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="wrap article__cover">
				<?php the_post_thumbnail( 'cod-wide', array( 'loading' => 'eager', 'sizes' => '(max-width: 1240px) 92vw, 1160px' ) ); ?>
			</figure>
		<?php endif; ?>
		<div class="section section--tight">
			<div class="wrap fx-narrow">
				<div class="prose entry-content">
					<?php the_content(); ?>
				</div>
				<?php if ( $cod_author || $cod_bio ) : ?>
					<aside class="author-box">
						<?php echo cod_img( $cod_avatar, 'thumbnail', array( 'alt' => '', 'width' => 88, 'height' => 88 ), 'zespol-ewa-gardyanczyk.webp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div>
							<p class="author-box__name"><?php echo esc_html( $cod_author ? $cod_author : 'Ewa Gardyańczyk' ); ?></p>
							<div class="prose"><?php echo cod_prose( $cod_bio ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						</div>
					</aside>
				<?php endif; ?>
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</div>
	</article>
	<?php
	$cod_related = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'category__in'        => wp_get_post_categories( get_the_ID() ),
		)
	);
	if ( $cod_related->have_posts() ) :
		?>
		<section class="section surface-sand" aria-labelledby="h-related">
			<div class="wrap">
				<h2 id="h-related" class="fx-title fx-title--sm"><?php esc_html_e( 'Przeczytaj także', 'cod2026' ); ?></h2>
				<div class="post-grid">
					<?php
					while ( $cod_related->have_posts() ) {
						$cod_related->the_post();
						get_template_part( 'template-parts/cards/post-card' );
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
