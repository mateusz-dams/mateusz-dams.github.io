<?php
/**
 * Strona główna – opinie.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_reviews = cod_all_reviews();
get_template_part( 'template-parts/cards/reviews', null, array( 'reviews' => $cod_reviews, 'title' => __( 'Co mówią klienci', 'cod2026' ), 'subtitle' => __( 'Opinie', 'cod2026' ) ) );
