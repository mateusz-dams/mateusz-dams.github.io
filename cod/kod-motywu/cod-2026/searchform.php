<?php
/**
 * Formularz wyszukiwania.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_sid = cod_uid( 'szukaj' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="<?php echo esc_attr( $cod_sid ); ?>"><?php esc_html_e( 'Szukaj', 'cod2026' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $cod_sid ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Czego szukasz?', 'cod2026' ); ?>">
	<button type="submit" class="btn btn--primary btn--sm"><?php echo cod_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Szukaj', 'cod2026' ); ?></span></button>
</form>
