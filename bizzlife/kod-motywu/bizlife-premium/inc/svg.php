<?php
/**
 * Bezpieczne wgrywanie plików SVG (np. oryginalnego logo).
 *
 * Tylko administratorzy (manage_options). Każdy plik jest oczyszczany: usuwane są skrypty,
 * obiekty zewnętrzne, atrybuty zdarzeń (on*) i odnośniki javascript:/data:.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Dodaje typ SVG dla administratorów.
 *
 * @param array $mimes Typy.
 * @return array
 */
function bzl_svg_mimes( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'bzl_svg_mimes' );

/**
 * Poprawne rozpoznanie typu SVG (WordPress nie wykrywa go z zawartości).
 *
 * @param array  $data     Dane.
 * @param string $file     Ścieżka.
 * @param string $filename Nazwa.
 * @return array
 */
function bzl_svg_filetype( $data, $file, $filename ) {
	if ( current_user_can( 'manage_options' ) && 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'bzl_svg_filetype', 10, 3 );

/**
 * Oczyszcza kod SVG.
 *
 * @param string $svg Kod.
 * @return string|false Oczyszczony kod lub false, gdy plik jest niepoprawny.
 */
function bzl_svg_sanitize( $svg ) {
	if ( ! class_exists( 'DOMDocument' ) || '' === trim( (string) $svg ) ) {
		return false;
	}
	// Blokada encji zewnętrznych (XXE).
	if ( preg_match( '/<!ENTITY/i', $svg ) ) {
		return false;
	}
	$svg = preg_replace( '/<!DOCTYPE[^>]*>/i', '', $svg );

	$dom      = new DOMDocument();
	$internal = libxml_use_internal_errors( true );
	$loaded   = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_NOBLANKS );
	libxml_clear_errors();
	libxml_use_internal_errors( $internal );
	if ( ! $loaded || ! $dom->documentElement || 'svg' !== strtolower( $dom->documentElement->localName ) ) {
		return false;
	}

	$blocked = array( 'script', 'foreignobject', 'iframe', 'embed', 'object', 'audio', 'video', 'handler', 'listener', 'set', 'animate' );
	$xpath   = new DOMXPath( $dom );
	$remove  = array();
	foreach ( $xpath->query( '//*' ) as $node ) {
		if ( in_array( strtolower( $node->localName ), $blocked, true ) ) {
			$remove[] = $node;
			continue;
		}
		$attrs = array();
		foreach ( $node->attributes as $attr ) {
			$attrs[] = $attr;
		}
		foreach ( $attrs as $attr ) {
			$name  = strtolower( $attr->nodeName );
			$value = strtolower( preg_replace( '/[\s\x00-\x1f]+/', '', $attr->nodeValue ) );
			if ( 0 === strpos( $name, 'on' ) ) {
				$node->removeAttributeNode( $attr );
			} elseif ( in_array( $name, array( 'href', 'xlink:href', 'src' ), true ) && '' !== $value && '#' !== $value[0] && 0 !== strpos( $value, 'data:image/' ) ) {
				$node->removeAttributeNode( $attr );
			} elseif ( false !== strpos( $value, 'javascript:' ) || ( 'style' === $name && false !== strpos( $value, 'url(' ) && false === strpos( $value, 'url(#' ) ) ) {
				$node->removeAttributeNode( $attr );
			}
		}
	}
	foreach ( $remove as $node ) {
		$node->parentNode->removeChild( $node );
	}
	return $dom->saveXML( $dom->documentElement );
}

/**
 * Oczyszcza SVG przed zapisaniem w bibliotece mediów.
 *
 * @param array $file Plik.
 * @return array
 */
function bzl_svg_upload_prefilter( $file ) {
	if ( 'image/svg+xml' !== ( $file['type'] ?? '' ) && 'svg' !== strtolower( pathinfo( $file['name'] ?? '', PATHINFO_EXTENSION ) ) ) {
		return $file;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		$file['error'] = __( 'Tylko administrator może wgrywać pliki SVG.', 'bizlife' );
		return $file;
	}
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$clean = bzl_svg_sanitize( (string) file_get_contents( $file['tmp_name'] ) );
	if ( false === $clean ) {
		$file['error'] = __( 'Ten plik SVG jest niepoprawny lub zawiera niedozwolone elementy.', 'bizlife' );
		return $file;
	}
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	file_put_contents( $file['tmp_name'], $clean );
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'bzl_svg_upload_prefilter' );

/**
 * Adres i wymiary SVG dla funkcji obrazów WordPressa (logo, biblioteka mediów).
 *
 * @param array|false $out           Wynik.
 * @param int         $attachment_id ID.
 * @return array|false
 */
function bzl_svg_downsize( $out, $attachment_id ) {
	if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
		return $out;
	}
	$size = bzl_svg_dimensions( get_attached_file( $attachment_id ) );
	$size = $size ? $size : array( 300, 100 );
	return array( wp_get_attachment_url( $attachment_id ), $size[0], $size[1], false );
}
add_filter( 'image_downsize', 'bzl_svg_downsize', 10, 2 );

/**
 * Odczyt wymiarów SVG.
 *
 * @param string $path Ścieżka pliku.
 * @return array|false [szerokość, wysokość]
 */
function bzl_svg_dimensions( $path ) {
	if ( ! $path || ! file_exists( $path ) ) {
		return false;
	}
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$head = (string) file_get_contents( $path, false, null, 0, 4096 );
	if ( preg_match( '/viewBox=["\']\s*[-\d.]+[\s,]+[-\d.]+[\s,]+([\d.]+)[\s,]+([\d.]+)/i', $head, $m ) && (float) $m[2] > 0 ) {
		return array( (int) round( (float) $m[1] ), (int) round( (float) $m[2] ) );
	}
	if ( preg_match( '/\swidth=["\']([\d.]+)/i', $head, $w ) && preg_match( '/\sheight=["\']([\d.]+)/i', $head, $h ) ) {
		return array( (int) $w[1], (int) $h[1] );
	}
	return false;
}

/**
 * Metadane wymiarów dla nowo wgranych SVG (podgląd w bibliotece i w Customizerze).
 *
 * @param array $metadata      Metadane.
 * @param int   $attachment_id ID.
 * @return array
 */
function bzl_svg_metadata( $metadata, $attachment_id ) {
	if ( 'image/svg+xml' === get_post_mime_type( $attachment_id ) ) {
		$size = bzl_svg_dimensions( get_attached_file( $attachment_id ) );
		if ( $size ) {
			$metadata           = is_array( $metadata ) ? $metadata : array();
			$metadata['width']  = $size[0];
			$metadata['height'] = $size[1];
			$metadata['file']   = _wp_relative_upload_path( get_attached_file( $attachment_id ) );
		}
	}
	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'bzl_svg_metadata', 10, 2 );
