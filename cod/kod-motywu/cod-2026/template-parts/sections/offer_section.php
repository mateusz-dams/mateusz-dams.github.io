<?php
/**
 * Sekcja: wybrane usługi (karty z panelem „Szczegóły”).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_posts = get_sub_field( 'offer' );
$cod_list  = array();
foreach ( (array) $cod_posts as $cod_p ) {
	$cod_o = cod_offer( $cod_p );
	if ( $cod_o && 'publish' === get_post_status( $cod_o['id'] ) ) {
		$cod_list[] = $cod_o;
	}
}
if ( ! $cod_list ) {
	$cod_list = array();
	foreach ( cod_offers_grouped() as $cod_g ) {
		$cod_list = array_merge( $cod_list, $cod_g );
	}
}
$cod_spans = cod_bento_spans( count( $cod_list ), false );
?>
<section<?php echo cod_sec_attrs( 'offer', 'fx-offer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wrap">
		<?php cod_sec_head( 'offer' ); ?>
		<div class="bento reveal-stagger">
			<?php
			foreach ( $cod_list as $cod_i => $cod_o ) {
				get_template_part( 'template-parts/cards/offer-card', null, array( 'offer' => $cod_o, 'span' => $cod_spans[ $cod_i ] ) );
			}
			?>
		</div>
		<?php cod_sec_buttons( 'offer_section_buttons', 'offer_section_button', cod_sec_loc( 'offer' ), 'fx-buttons fx-buttons--center reveal' ); ?>
	</div>
</section>
