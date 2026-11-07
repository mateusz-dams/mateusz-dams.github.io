<?php
/**
 * Karta usługi.
 *
 * @package Bizlife
 *
 * @var array $args post, index, heading (poziom nagłówka).
 */

defined( 'ABSPATH' ) || exit;

$bzl_post  = $args['post'] ?? null;
$bzl_index = (int) ( $args['index'] ?? 0 );
$bzl_level = (int) ( $args['heading'] ?? 3 );
if ( ! $bzl_post ) {
	return;
}
$bzl_icon  = (string) get_post_meta( $bzl_post->ID, 'bzl_icon', true );
$bzl_url   = get_permalink( $bzl_post );
$bzl_title = get_the_title( $bzl_post );
$bzl_id    = 'service-' . $bzl_post->ID . '-' . wp_unique_id();
?>
<li class="service-card" data-reveal style="--d:<?php echo (int) min( $bzl_index, 8 ); ?>">
	<span class="service-card__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $bzl_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
	<span class="service-card__icon"><?php echo bzl_icon( $bzl_icon ? $bzl_icon : 'doc-check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	<h<?php echo (int) $bzl_level; ?> class="service-card__title" id="<?php echo esc_attr( $bzl_id ); ?>">
		<a class="service-card__link" href="<?php echo esc_url( $bzl_url ); ?>"><?php echo esc_html( $bzl_title ); ?></a>
	</h<?php echo (int) $bzl_level; ?>>
	<p class="service-card__text"><?php echo esc_html( bzl_service_excerpt( $bzl_post->ID ) ); ?></p>
	<span class="service-card__more" aria-hidden="true"><?php esc_html_e( 'Szczegóły', 'bizlife' ); ?><?php echo bzl_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
</li>
