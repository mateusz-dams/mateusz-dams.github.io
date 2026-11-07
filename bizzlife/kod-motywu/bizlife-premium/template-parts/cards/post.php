<?php
/**
 * Karta wpisu (używana w pętli).
 *
 * @package Bizlife
 *
 * @var array $args index, featured, heading.
 */

defined( 'ABSPATH' ) || exit;

$bzl_index    = (int) ( $args['index'] ?? 0 );
$bzl_featured = ! empty( $args['featured'] );
$bzl_level    = (int) ( $args['heading'] ?? 3 );
$bzl_cats     = get_the_category();
$bzl_title_id = 'post-title-' . get_the_ID() . '-' . wp_unique_id();
?>
<li <?php post_class( 'post-card' . ( $bzl_featured ? ' post-card--featured' : '' ) ); ?> data-reveal style="--d:<?php echo (int) min( $bzl_index, 6 ); ?>">
	<div class="post-card__media">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail(
				$bzl_featured ? 'bzl-wide' : 'bzl-card',
				array(
					'class'   => 'post-card__img',
					'loading' => 'lazy',
					'sizes'   => $bzl_featured ? '(min-width: 1024px) 60vw, 100vw' : '(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw',
					'alt'     => '',
				)
			);
		} else {
			echo bzl_image_placeholder(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
	</div>
	<div class="post-card__body">
		<p class="post-card__meta">
			<?php if ( $bzl_cats ) : ?>
				<span class="post-card__cat"><?php echo esc_html( $bzl_cats[0]->name ); ?></span>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<h<?php echo (int) $bzl_level; ?> class="post-card__title" id="<?php echo esc_attr( $bzl_title_id ); ?>">
			<a class="post-card__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h<?php echo (int) $bzl_level; ?>>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $bzl_featured ? 34 : 20, '…' ) ); ?></p>
		<span class="post-card__more" aria-hidden="true"><?php esc_html_e( 'Czytaj więcej', 'bizlife' ); ?><?php echo bzl_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	</div>
</li>
