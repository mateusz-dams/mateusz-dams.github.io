<?php
/**
 * Opinie: jedna – duży cytat; kilka – slider (strzałki, kropki, przesuwanie palcem i klawiaturą, bez autoprzewijania).
 *
 * @package cod2026
 *
 * @var array $args reviews (quote, name, role, photo), title, subtitle, id, surface.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_reviews = array_values(
	array_filter(
		$args['reviews'],
		function ( $r ) {
			return ! empty( $r['quote'] ) && '' !== trim( wp_strip_all_tags( (string) $r['quote'] ) );
		}
	)
);
if ( ! $cod_reviews ) {
	return;
}
$cod_id      = ! empty( $args['id'] ) ? $args['id'] : 'opinie';
$cod_title   = ! empty( $args['title'] ) ? $args['title'] : __( 'Opinie', 'cod2026' );
$cod_sub     = ! empty( $args['subtitle'] ) ? $args['subtitle'] : '';
$cod_surface = ! empty( $args['surface'] ) ? $args['surface'] : 'surface-sand';
$cod_count   = count( $cod_reviews );
$cod_photo   = function ( $r, $size ) {
	$p    = isset( $r['photo'] ) ? $r['photo'] : '';
	$file = is_string( $p ) && preg_match( '/\.(webp|png|jpe?g)$/', $p ) && false === strpos( $p, '/' ) ? $p : '';
	return cod_img( $file ? '' : $p, 'medium', array( 'alt' => '', 'width' => $size, 'height' => $size, 'sizes' => ( $size * 2 ) . 'px' ), $file );
};
?>
<section class="section voice <?php echo esc_attr( $cod_surface ); ?>" id="<?php echo esc_attr( $cod_id ); ?>" aria-labelledby="h-<?php echo esc_attr( $cod_id ); ?>">
	<div class="wrap">
		<?php if ( 1 === $cod_count ) : ?>
			<h2 id="h-<?php echo esc_attr( $cod_id ); ?>" class="sr-only"><?php echo esc_html( $cod_title ); ?></h2>
		<?php else : ?>
			<div class="qs-head reveal">
				<div>
					<?php if ( $cod_sub ) : ?>
						<span class="num-tag"><?php echo esc_html( $cod_sub ); ?></span>
					<?php endif; ?>
					<h2 id="h-<?php echo esc_attr( $cod_id ); ?>"><?php echo esc_html( $cod_title ); ?></h2>
				</div>
				<div class="qs-nav" aria-label="<?php esc_attr_e( 'Przewijanie opinii', 'cod2026' ); ?>">
					<button class="qs-btn" type="button" data-qs="-1" aria-label="<?php esc_attr_e( 'Poprzednia opinia', 'cod2026' ); ?>"><?php echo cod_icon( 'left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					<span class="qs-count" aria-live="polite"><b>1</b> / <?php echo (int) $cod_count; ?></span>
					<button class="qs-btn" type="button" data-qs="1" aria-label="<?php esc_attr_e( 'Następna opinia', 'cod2026' ); ?>"><?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				</div>
			</div>
		<?php endif; ?>
		<div class="qs reveal"<?php echo $cod_count > 1 ? ' data-qslider' : ''; ?>>
			<div class="qs-track"<?php echo $cod_count > 1 ? ' tabindex="0" role="region" aria-roledescription="karuzela" aria-label="' . esc_attr( $cod_title ) . '"' : ''; ?>>
				<?php foreach ( $cod_reviews as $cod_i => $cod_r ) : ?>
					<?php
					$cod_len = function_exists( 'mb_strlen' ) ? mb_strlen( wp_strip_all_tags( (string) $cod_r['quote'] ) ) : strlen( wp_strip_all_tags( (string) $cod_r['quote'] ) );
					$cod_sz  = $cod_len > 320 ? ' qs-long' : ( $cod_len > 170 ? ' qs-mid' : '' );
					?>
					<figure class="voice-in qs-slide<?php echo esc_attr( $cod_sz ); ?>"<?php echo $cod_count > 1 ? ' role="group" aria-roledescription="slajd" aria-label="' . esc_attr( sprintf( /* translators: 1: numer, 2: liczba. */ __( 'Opinia %1$d z %2$d', 'cod2026' ), $cod_i + 1, $cod_count ) ) . '"' : ''; ?>>
						<span class="qmark" aria-hidden="true">“</span>
						<div>
							<blockquote><?php echo cod_prose( wpautop( wp_strip_all_tags( (string) $cod_r['quote'], false ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></blockquote>
							<figcaption>
								<?php echo $cod_photo( $cod_r, 56 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span><strong><?php echo esc_html( $cod_r['name'] ); ?></strong><?php if ( ! empty( $cod_r['role'] ) ) : ?><span><?php echo esc_html( $cod_r['role'] ); ?></span><?php endif; ?></span>
							</figcaption>
						</div>
					</figure>
				<?php endforeach; ?>
			</div>
			<?php if ( $cod_count > 1 && $cod_count <= 12 ) : ?>
				<div class="qs-dots">
					<?php for ( $cod_d = 0; $cod_d < $cod_count; $cod_d++ ) : ?>
						<button type="button" data-qs-go="<?php echo (int) $cod_d; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: numer. */ __( 'Opinia %d', 'cod2026' ), $cod_d + 1 ) ); ?>"<?php echo 0 === $cod_d ? ' aria-current="true"' : ''; ?>></button>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
