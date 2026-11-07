<?php
/**
 * Sekcja: Dlaczego Bizzlife.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_items = bzl_slots( 'why' );
if ( ! $bzl_items ) {
	return;
}
?>
<section class="section why <?php echo esc_attr( bzl_tone( 'why', 'darker' ) ); ?>" id="dlaczego-my" aria-labelledby="why-title">
	<div class="container">
		<?php
		bzl_section_header(
			array(
				'eyebrow' => bzl_mod( 'why_eyebrow' ),
				'title'   => bzl_mod( 'why_title' ),
				'lead'    => bzl_mod( 'why_lead' ),
				'id'      => 'why-title',
				'ghost'   => __( 'Dlaczego', 'bizlife' ),
			)
		);
		?>
		<ol class="why-grid">
			<?php foreach ( $bzl_items as $bzl_i => $bzl_item ) : ?>
				<li class="why-item" data-reveal style="--d:<?php echo (int) $bzl_i; ?>">
					<div class="why-item__head">
						<span class="why-item__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $bzl_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php if ( $bzl_item['icon'] ) : ?>
							<span class="why-item__icon"><?php echo bzl_icon( $bzl_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>
					</div>
					<h3 class="why-item__title"><?php echo esc_html( $bzl_item['title'] ); ?></h3>
					<?php if ( $bzl_item['text'] ) : ?>
						<p class="why-item__text"><?php echo esc_html( $bzl_item['text'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
