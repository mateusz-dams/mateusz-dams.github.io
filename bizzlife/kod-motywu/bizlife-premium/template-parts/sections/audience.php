<?php
/**
 * Sekcja: Dla kogo pracujemy.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_items = bzl_slots( 'audience' );
if ( ! $bzl_items ) {
	return;
}
?>
<section class="section audience <?php echo esc_attr( bzl_tone( 'audience', 'light' ) ); ?>" id="dla-kogo" aria-labelledby="audience-title">
	<div class="container">
		<?php
		bzl_section_header(
			array(
				'eyebrow' => bzl_mod( 'audience_eyebrow' ),
				'title'   => bzl_mod( 'audience_title' ),
				'lead'    => bzl_mod( 'audience_lead' ),
				'id'      => 'audience-title',
			)
		);
		?>
		<ul class="audience-grid" data-count="<?php echo (int) count( $bzl_items ); ?>">
			<?php foreach ( $bzl_items as $bzl_i => $bzl_item ) : ?>
				<?php $bzl_link = bzl_url( $bzl_item['url'] ); ?>
				<li class="audience-card<?php echo $bzl_link ? ' has-link' : ''; ?>" data-reveal style="--d:<?php echo (int) $bzl_i; ?>">
					<?php if ( $bzl_item['icon'] ) : ?>
						<span class="audience-card__icon"><?php echo bzl_icon( $bzl_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endif; ?>
					<h3 class="audience-card__title">
						<?php if ( $bzl_link ) : ?>
							<a href="<?php echo esc_url( $bzl_link ); ?>"><?php echo esc_html( $bzl_item['title'] ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $bzl_item['title'] ); ?>
						<?php endif; ?>
					</h3>
					<?php if ( $bzl_item['text'] ) : ?>
						<p class="audience-card__text"><?php echo esc_html( $bzl_item['text'] ); ?></p>
					<?php endif; ?>
					<?php if ( $bzl_link ) : ?>
						<span class="audience-card__arrow" aria-hidden="true"><?php echo bzl_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
