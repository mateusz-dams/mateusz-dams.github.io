<?php
/**
 * Sekcja: mapa – ładowana dopiero po kliknięciu (bez cookies Google przed zgodą).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_map = (string) get_sub_field( 'map' );
$cod_src = '';
if ( preg_match( '/src=["\']([^"\']+)["\']/i', $cod_map, $cod_m ) ) {
	$cod_src = $cod_m[1];
} elseif ( filter_var( trim( $cod_map ), FILTER_VALIDATE_URL ) ) {
	$cod_src = trim( $cod_map );
}
$cod_host = $cod_src ? wp_parse_url( $cod_src, PHP_URL_HOST ) : '';
if ( ! $cod_host || ! preg_match( '/(^|\.)google\.[a-z.]+$/', $cod_host ) ) {
	$cod_src = '';
}
?>
<section class="fx fx-map">
	<div class="wrap">
		<div class="map map--wide" data-map<?php echo $cod_src ? ' data-src="' . esc_url( $cod_src ) . '"' : ''; ?>>
			<div>
				<button class="btn btn--primary" type="button" data-map-load><?php echo cod_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Pokaż mapę', 'cod2026' ); ?></span></button>
				<p><?php echo esc_html( cod_contact( 'address' ) ); ?><br><?php esc_html_e( 'Mapa ładuje się z serwerów Google dopiero po kliknięciu.', 'cod2026' ); ?></p>
			</div>
		</div>
	</div>
</section>
