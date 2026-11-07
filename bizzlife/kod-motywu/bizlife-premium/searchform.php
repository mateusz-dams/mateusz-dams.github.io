<?php
/**
 * Formularz wyszukiwania.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;
$bzl_sid = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $bzl_sid ); ?>"><?php esc_html_e( 'Szukaj w serwisie', 'bizlife' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $bzl_sid ); ?>" class="search-form__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Czego szukasz?', 'bizlife' ); ?>">
	<button type="submit" class="search-form__btn"><?php echo bzl_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'Szukaj', 'bizlife' ); ?></span></button>
</form>
