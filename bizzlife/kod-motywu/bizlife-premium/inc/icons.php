<?php
/**
 * Biblioteka ikon SVG (siatka 24 px, linia 1.5 px).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ścieżki ikon.
 *
 * @return array
 */
function bzl_icon_paths() {
	return array(
		'ledger'         => '<path d="M4 5.5A1.5 1.5 0 0 1 5.5 4H18a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5.5A1.5 1.5 0 0 1 4 18.5z"/><path d="M8 4v16"/><path d="M11.5 9h5M11.5 12.5h5M11.5 16h3"/>',
		'percent'        => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="m9 17 6-6"/><circle cx="9.5" cy="11.5" r="1"/><circle cx="14.5" cy="16.5" r="1"/>',
		'payroll'        => '<circle cx="8.5" cy="8.5" r="3.25"/><path d="M3 19.5c.6-3.2 2.8-5 5.5-5s4.9 1.8 5.5 5"/><circle cx="17.25" cy="9" r="3.25"/><path d="M17.25 7.6v2.8"/><path d="M16.5 15.2c2.2.3 3.9 1.9 4.4 4.3"/>',
		'building'       => '<path d="M3.5 20h17"/><path d="M6 20V6.5L13 4v16"/><path d="M13 9h5v11"/><path d="M9 8.5v.01M9 11.5v.01M9 14.5v.01M15.75 12.5v.01M15.75 15.5v.01"/>',
		'scales'         => '<path d="M12 4v16M8 20h8M5 7h14"/><path d="M5 7 2.75 13M5 7l2.25 6M19 7l-2.25 6M19 7l2.25 6"/><path d="M2.5 13a2.5 2 0 0 0 5 0zM16.5 13a2.5 2 0 0 0 5 0z"/>',
		'truck'          => '<path d="M3 6.5h10.5V16H3z"/><path d="M13.5 9.5h3.8l3.2 3.5v3h-7"/><circle cx="7" cy="17.5" r="1.75"/><circle cx="17" cy="17.5" r="1.75"/>',
		'sprout'         => '<path d="M12 20v-8"/><path d="M12 12c0-3.5 2.5-6 6.5-6 0 3.5-2.5 6-6.5 6z"/><path d="M12 14.5c0-2.8-2-4.8-5.5-4.8 0 2.8 2 4.8 5.5 4.8z"/><path d="M7 20h10"/>',
		'chat'           => '<path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v8a2.5 2.5 0 0 1-2.5 2.5H10l-4.5 3.5V17h-1A.5.5 0 0 1 4 16.5z"/><path d="M8.5 9h7M8.5 12.5h4.5"/>',
		'person'         => '<circle cx="12" cy="8" r="3.5"/><path d="M5 20c.8-3.8 3.6-6 7-6s6.2 2.2 7 6"/>',
		'folder'         => '<path d="M3.5 7a2 2 0 0 1 2-2H9l2 2.5h7.5a2 2 0 0 1 2 2V17a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2z"/><path d="M3.5 10.5h17"/>',
		'layers'         => '<path d="M12 4 3.5 8.5 12 13l8.5-4.5z"/><path d="m3.5 12.25 8.5 4.5 8.5-4.5"/><path d="m3.5 15.75 8.5 4.5 8.5-4.5"/>',
		'shield'         => '<path d="M12 3.5 5 6v5.5c0 4.3 2.9 7.6 7 9 4.1-1.4 7-4.7 7-9V6z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
		'calendar'       => '<rect x="4" y="5.5" width="16" height="14.5" rx="2"/><path d="M4 10h16M8.5 3.5v4M15.5 3.5v4"/><path d="m9.5 15 1.8 1.8 3.4-3.4"/>',
		'doc-check'      => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="m9 14.5 2 2 4-4"/>',
		'cart'           => '<path d="M3.5 4.5h2l2.2 10.2a1.5 1.5 0 0 0 1.5 1.2h7.6a1.5 1.5 0 0 0 1.5-1.1L20 8H6.4"/><circle cx="9.5" cy="19.5" r="1.25"/><circle cx="17" cy="19.5" r="1.25"/>',
		'briefcase'      => '<rect x="3.5" y="7" width="17" height="12.5" rx="2"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/><path d="M3.5 12.5h17"/>',
		'calculator'     => '<rect x="5" y="3.5" width="14" height="17" rx="2"/><path d="M8 7.5h8"/><path d="M8.5 12v.01M12 12v.01M15.5 12v.01M8.5 16v.01M12 16v.01M15.5 16v.01"/>',
		'phone'          => '<path d="M8.2 4.5 6 4.6a2 2 0 0 0-1.8 2.3c.8 6.6 6.3 12.1 12.9 12.9a2 2 0 0 0 2.3-1.8l.1-2.2a1.5 1.5 0 0 0-1-1.5l-2.6-.9a1.5 1.5 0 0 0-1.6.4l-1 1.1a10.6 10.6 0 0 1-4.6-4.6l1.1-1a1.5 1.5 0 0 0 .4-1.6l-.9-2.6a1.5 1.5 0 0 0-1.5-1z"/>',
		'mail'           => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4 7 8 6 8-6"/>',
		'pin'            => '<path d="M12 21s-6.5-5.8-6.5-11a6.5 6.5 0 0 1 13 0c0 5.2-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'          => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'id'             => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6.5 15.5c.5-1.3 1.4-2 2.5-2s2 .7 2.5 2"/><path d="M14 10h3.5M14 13.5h2.5"/>',
		'map'            => '<path d="M9 4.5 3.5 6.5v13L9 17.5l6 2 5.5-2v-13L15 6.5z"/><path d="M9 4.5v13M15 6.5v13"/>',
		'route'          => '<path d="M12 3.5 20.5 12 12 20.5 3.5 12z"/><path d="M9.5 14v-2.5a1 1 0 0 1 1-1h4M13 8.5l2 2-2 2"/>',
		'arrow-right'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-left'     => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9"/>',
		'chevron-down'   => '<path d="m6 9 6 6 6-6"/>',
		'plus'           => '<path d="M12 5v14M5 12h14"/>',
		'menu'           => '<path d="M4 7h16M4 12h16M4 17h10"/>',
		'close'          => '<path d="M6 6l12 12M18 6 6 18"/>',
		'check'          => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'search'         => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/>',
		'link'           => '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
		'monogram'       => '<rect x="3.5" y="3.5" width="17" height="17" rx="1"/><path d="M9 7.5h3.75a2 2 0 0 1 0 4H9zM9 11.5h4.35a2.25 2.25 0 0 1 0 4.5H9zM9 7.5V16"/>',
		'facebook'       => '<path fill="currentColor" stroke="none" d="M13.5 21v-7.5h2.5l.4-3h-2.9V8.6c0-.9.3-1.5 1.5-1.5h1.5V4.4c-.3 0-1.2-.1-2.2-.1-2.2 0-3.7 1.3-3.7 3.8v2.4H8v3h2.6V21z"/>',
		'instagram'      => '<rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.75"/><circle cx="16.8" cy="7.2" r=".9" fill="currentColor" stroke="none"/>',
		'whatsapp'       => '<path d="M12 3.75a8.25 8.25 0 0 0-7.1 12.45L3.8 20.2l4.1-1.07A8.25 8.25 0 1 0 12 3.75z"/><path fill="currentColor" stroke="none" d="M9.2 8.4c.2-.4.5-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.6c.1.2 0 .4-.1.6l-.5.6c-.1.1-.1.3 0 .5.6 1 1.4 1.8 2.4 2.4.2.1.4.1.5 0l.6-.6c.2-.2.4-.2.6-.1l1.6.7c.3.1.4.3.4.5v.5c0 .3-.1.6-.5.8-.6.4-1.4.6-2.3.4-2.6-.6-4.6-2.6-5.3-5.2-.2-.9 0-1.7.4-2.3z"/>',
		'google'         => '<path d="M19 12a7 7 0 1 1-2.05-4.95"/><path d="M19 12h-6.5"/>',
		'linkedin'       => '<rect x="4" y="4" width="16" height="16" rx="2.5"/><path d="M8 10.5V16M8 7.75v.01M11.5 16v-5.5M11.5 13c0-1.5 1-2.5 2.25-2.5S16 11.5 16 13v3"/>',
	);
}

/**
 * Zwraca kod SVG ikony.
 *
 * @param string $name  Nazwa ikony.
 * @param string $class Dodatkowa klasa.
 * @return string
 */
function bzl_icon( $name, $class = '' ) {
	$paths = bzl_icon_paths();
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon icon--%1$s %2$s" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		$paths[ $name ] // Statyczne, zaufane dane z motywu.
	);
}

/**
 * Ikony dostępne do wyboru w panelu.
 *
 * @return array
 */
function bzl_icon_choices() {
	return array(
		''           => __( '— brak —', 'bizlife' ),
		'ledger'     => __( 'Księga', 'bizlife' ),
		'calculator' => __( 'Kalkulator', 'bizlife' ),
		'percent'    => __( 'Podatki (%)', 'bizlife' ),
		'payroll'    => __( 'Kadry i płace', 'bizlife' ),
		'building'   => __( 'Budynek / spółka', 'bizlife' ),
		'scales'     => __( 'Waga / prawo', 'bizlife' ),
		'truck'      => __( 'Transport', 'bizlife' ),
		'sprout'     => __( 'Start / rozwój', 'bizlife' ),
		'cart'       => __( 'Sklep / e-commerce', 'bizlife' ),
		'briefcase'  => __( 'Teczka / biznes', 'bizlife' ),
		'chat'       => __( 'Rozmowa', 'bizlife' ),
		'person'     => __( 'Osoba', 'bizlife' ),
		'folder'     => __( 'Dokumenty', 'bizlife' ),
		'layers'     => __( 'Warstwy / komplet', 'bizlife' ),
		'shield'     => __( 'Bezpieczeństwo', 'bizlife' ),
		'calendar'   => __( 'Kalendarz', 'bizlife' ),
		'doc-check'  => __( 'Dokument z potwierdzeniem', 'bizlife' ),
		'clock'      => __( 'Zegar', 'bizlife' ),
		'id'         => __( 'Dane / identyfikator', 'bizlife' ),
	);
}
