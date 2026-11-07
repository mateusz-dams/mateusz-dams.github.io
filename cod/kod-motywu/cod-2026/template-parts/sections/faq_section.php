<?php
/**
 * Sekcja: pytania i odpowiedzi.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_items = array();
$cod_rows  = get_sub_field( 'faq' );
if ( is_array( $cod_rows ) ) {
	foreach ( $cod_rows as $cod_r ) {
		$cod_items[] = array(
			'q' => isset( $cod_r['faq_title'] ) ? $cod_r['faq_title'] : '',
			'a' => isset( $cod_r['faq_desc'] ) ? $cod_r['faq_desc'] : '',
		);
	}
}
if ( ! $cod_items ) {
	return;
}
?>
<section<?php echo cod_sec_attrs( 'faq', 'fx-faq' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wrap fx-narrow fx-narrow--wide">
		<?php cod_sec_head( 'faq' ); ?>
		<?php get_template_part( 'template-parts/cards/accordion', null, array( 'items' => $cod_items ) ); ?>
	</div>
</section>
