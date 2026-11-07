<?php
/**
 * Karta wpisu blogu.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'post-card spot' ); ?>>
	<a class="post-card__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'cod-wide', array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 760px) 92vw, 400px' ) );
		} else {
			echo '<span class="post-card__ph"></span>';
		}
		?>
	</a>
	<div class="post-card__body">
		<p class="post-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
	</div>
</article>
