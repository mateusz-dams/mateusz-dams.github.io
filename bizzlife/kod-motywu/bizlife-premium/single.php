<?php
/**
 * Pojedynczy artykuł.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$bzl_cats = get_the_category();
	ob_start();
	?>
	<ul class="meta-list">
		<?php if ( $bzl_cats ) : ?>
			<li><a class="tag" href="<?php echo esc_url( get_category_link( $bzl_cats[0] ) ); ?>"><?php echo esc_html( $bzl_cats[0]->name ); ?></a></li>
		<?php endif; ?>
		<li><?php echo bzl_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></li>
		<?php if ( get_the_modified_date( 'Ymd' ) > get_the_date( 'Ymd' ) ) : ?>
			<li><?php esc_html_e( 'Aktualizacja:', 'bizlife' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time></li>
		<?php endif; ?>
		<li><?php echo bzl_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php /* translators: %d: minuty. */ printf( esc_html( _n( '%d min czytania', '%d min czytania', bzl_reading_time(), 'bizlife' ) ), (int) bzl_reading_time() ); ?></li>
	</ul>
	<?php
	$bzl_meta = ob_get_clean();
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'title'   => get_the_title(),
			'meta'    => $bzl_meta,
			'variant' => 'article',
		)
	);
	?>
	<div class="section section--article tone-light">
		<div class="container">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="article-cover">
					<?php
					the_post_thumbnail(
						'bzl-wide',
						array(
							'class'         => 'article-cover__img',
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'sizes'         => '(min-width: 1200px) 1100px, 100vw',
						)
					);
					?>
					<?php if ( get_the_post_thumbnail_caption() ) : ?>
						<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>

			<div class="article-layout">
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'prose entry-content article-body' ); ?>>
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Strony wpisu', 'bizlife' ) . '">',
							'after'  => '</nav>',
						)
					);
					?>
					<?php
					$bzl_tags = get_the_tags();
					if ( $bzl_tags ) :
						?>
						<ul class="tag-list" aria-label="<?php esc_attr_e( 'Tagi', 'bizlife' ); ?>">
							<?php foreach ( $bzl_tags as $bzl_tag ) : ?>
								<li><a class="tag tag--light" href="<?php echo esc_url( get_tag_link( $bzl_tag ) ); ?>"><?php echo esc_html( $bzl_tag->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</article>

				<aside class="article-aside" aria-label="<?php esc_attr_e( 'Udostępnij i kontakt', 'bizlife' ); ?>">
					<div class="share">
						<p class="share__label"><?php esc_html_e( 'Udostępnij', 'bizlife' ); ?></p>
						<ul class="share__list">
							<li><a class="share__btn" href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo bzl_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'Udostępnij na Facebooku (nowa karta)', 'bizlife' ); ?></span></a></li>
							<li><a class="share__btn" href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo bzl_icon( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'Udostępnij na LinkedIn (nowa karta)', 'bizlife' ); ?></span></a></li>
							<li><button class="share__btn" type="button" data-copy-link="<?php echo esc_url( get_permalink() ); ?>"><?php echo bzl_icon( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text" data-copy-label><?php esc_html_e( 'Kopiuj link', 'bizlife' ); ?></span></button></li>
						</ul>
					</div>
					<div class="aside-card">
						<p class="aside-card__title"><?php esc_html_e( 'Masz pytanie do tego tematu?', 'bizlife' ); ?></p>
						<p class="aside-card__text"><?php esc_html_e( 'Omówimy sytuację Twojej firmy podczas bezpłatnej konsultacji.', 'bizlife' ); ?></p>
						<?php echo bzl_button( bzl_mod( 'header_cta_label' ), '{kontakt}', 'dark', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</aside>
			</div>

			<?php
			the_post_navigation(
				array(
					'class'     => 'post-nav',
					'prev_text' => '<span class="post-nav__label">' . esc_html__( 'Poprzedni artykuł', 'bizlife' ) . '</span><span class="post-nav__title">%title</span>',
					'next_text' => '<span class="post-nav__label">' . esc_html__( 'Następny artykuł', 'bizlife' ) . '</span><span class="post-nav__title">%title</span>',
				)
			);

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</div>

	<?php
	$bzl_related = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'category__in'        => wp_list_pluck( $bzl_cats, 'term_id' ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $bzl_related->have_posts() ) :
		?>
		<section class="section news tone-dark" aria-labelledby="related-title">
			<div class="container">
				<?php
				bzl_section_header(
					array(
						'eyebrow' => __( 'Czytaj dalej', 'bizlife' ),
						'title'   => __( 'Podobne artykuły', 'bizlife' ),
						'id'      => 'related-title',
					)
				);
				?>
				<ul class="post-grid">
					<?php
					$bzl_n = 0;
					while ( $bzl_related->have_posts() ) {
						$bzl_related->the_post();
						get_template_part( 'template-parts/cards/post', null, array( 'index' => $bzl_n++ ) );
					}
					wp_reset_postdata();
					?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

	<?php
	get_template_part( 'template-parts/sections/cta', null, array( 'tone' => 'tone-darker' ) );
endwhile;

get_footer();
