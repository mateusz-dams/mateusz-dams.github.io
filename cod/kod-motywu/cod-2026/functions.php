<?php
/**
 * COD 2026 – funkcje motywu.
 *
 * Kod jest podzielony na pliki w katalogu inc/:
 * - helpers.php      funkcje pomocnicze (opcje, obrazy, przyciski, ikony)
 * - setup.php        obsługa motywu, menu, typ wpisu „Oferta”
 * - defaults.php     teksty domyślne strony głównej
 * - acf.php          strona opcji i nowe pola (zgodne z dotychczasowymi)
 * - assets.php       style, skrypty, fonty
 * - tracking.php     Google Tag Manager, Consent Mode, kody awaryjne
 * - sections.php     renderowanie sekcji ACF „sections” w nowym wyglądzie
 * - offers.php       usługi: pobieranie, grupowanie, kreator
 * - contact.php      formularz kontaktowy (wysyłka przez wp_mail)
 * - woocommerce.php  integracja ze sklepem
 * - learndash.php    integracja z kursami, przekierowanie po zakupie
 * - importer.php     narzędzie „Uzupełnij opisy usług” w panelu
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'COD_VERSION', '1.6.0' );
define( 'COD_DIR', get_template_directory() );
define( 'COD_URI', get_template_directory_uri() );

$cod_includes = array(
	'helpers',
	'setup',
	'defaults',
	'acf',
	'assets',
	'tracking',
	'sections',
	'offers',
	'contact',
	'woocommerce',
	'learndash',
	'importer',
);

foreach ( $cod_includes as $cod_file ) {
	require_once COD_DIR . '/inc/' . $cod_file . '.php';
}
unset( $cod_includes, $cod_file );
