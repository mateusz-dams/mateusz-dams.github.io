<?php
/**
 * Akordeon FAQ (przyciski z aria-expanded, panele region).
 *
 * @package Bizlife
 *
 * @var array $args items (WP_Post[]), heading (poziom nagłówka pytań), id.
 */

defined( 'ABSPATH' ) || exit;

$bzl_items = $args['items'] ?? array();
$bzl_level = (int) ( $args['heading'] ?? 3 );
$bzl_uid   = $args['id'] ?? wp_unique_id( 'faq-' );
if ( ! $bzl_items ) {
	return;
}
?>
<div class="accordion" data-accordion>
	<?php foreach ( $bzl_items as $bzl_i => $bzl_faq ) : ?>
		<?php
		$bzl_btn   = $bzl_uid . '-q' . $bzl_i;
		$bzl_panel = $bzl_uid . '-a' . $bzl_i;
		?>
		<div class="accordion__item" data-reveal style="--d:<?php echo (int) min( $bzl_i, 6 ); ?>">
			<h<?php echo (int) $bzl_level; ?> class="accordion__heading">
				<button class="accordion__trigger" type="button" id="<?php echo esc_attr( $bzl_btn ); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $bzl_panel ); ?>">
					<span class="accordion__q"><?php echo esc_html( get_the_title( $bzl_faq ) ); ?></span>
					<span class="accordion__icon" aria-hidden="true"><?php echo bzl_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</button>
			</h<?php echo (int) $bzl_level; ?>>
			<div class="accordion__panel" id="<?php echo esc_attr( $bzl_panel ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $bzl_btn ); ?>">
				<div class="accordion__inner">
					<div class="accordion__answer prose-sm">
						<?php echo wp_kses_post( apply_filters( 'the_content', $bzl_faq->post_content ) ); ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
