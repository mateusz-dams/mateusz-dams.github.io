<?php
/**
 * Sekcja: treść strony głównej z edytora bloków (opcjonalna).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

if ( 'page' !== get_option( 'show_on_front' ) ) {
	return;
}
$bzl_front = get_post( (int) get_option( 'page_on_front' ) );
if ( ! $bzl_front || '' === trim( $bzl_front->post_content ) ) {
	return;
}
?>
<section class="section section--content tone-light" aria-label="<?php echo esc_attr( get_the_title( $bzl_front ) ); ?>">
	<div class="container">
		<div class="prose entry-content">
			<?php echo apply_filters( 'the_content', $bzl_front->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</section>
