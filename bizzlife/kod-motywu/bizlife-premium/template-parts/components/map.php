<?php
/**
 * Mapa ładowana dopiero po kliknięciu (brak cookies Google przed zgodą, lepsza wydajność).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_embed = (string) bzl_mod( 'map_embed_url' );
$bzl_a     = bzl_address();
?>
<div class="map-card" data-reveal>
	<div class="map-card__info">
		<p class="eyebrow"><?php esc_html_e( 'Dojazd', 'bizlife' ); ?></p>
		<h3 class="map-card__title">
			<?php
			/* translators: %s: adres. */
			printf( esc_html__( 'Znajdziesz nas przy %s', 'bizlife' ), esc_html( $bzl_a['street'] ) );
			?>
		</h3>
		<p class="map-card__text"><?php echo esc_html( trim( $bzl_a['postal'] . ' ' . $bzl_a['city'] ) ); ?></p>
		<div class="btn-row">
			<a class="btn btn--primary" href="<?php echo esc_url( bzl_directions_url() ); ?>" target="_blank" rel="noopener noreferrer"><span><?php esc_html_e( 'Wyznacz trasę', 'bizlife' ); ?></span><?php echo bzl_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"> <?php esc_html_e( '(Google Maps, nowa karta)', 'bizlife' ); ?></span></a>
			<?php if ( $bzl_embed ) : ?>
				<button class="btn btn--ghost" type="button" data-map-load aria-controls="bzl-map"><span><?php esc_html_e( 'Pokaż mapę', 'bizlife' ); ?></span><?php echo bzl_icon( 'map' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			<?php endif; ?>
		</div>
	</div>
	<div class="map-card__map" id="bzl-map" data-map-src="<?php echo esc_url( $bzl_embed ); ?>" data-map-title="<?php echo esc_attr( sprintf( /* translators: %s: nazwa firmy. */ __( 'Mapa dojazdu – %s', 'bizlife' ), bzl_mod( 'company_name' ) ) ); ?>">
		<div class="map-card__placeholder" aria-hidden="true">
			<span class="map-card__pin"><?php echo bzl_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</div>
		<?php if ( $bzl_embed ) : ?>
			<p class="map-card__note"><?php esc_html_e( 'Mapa Google ładuje się po kliknięciu „Pokaż mapę”. Google może wtedy zapisać pliki cookies.', 'bizlife' ); ?></p>
		<?php endif; ?>
	</div>
</div>
