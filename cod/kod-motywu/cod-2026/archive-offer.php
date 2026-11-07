<?php
/**
 * Lista usług (/oferta/) i kategorie usług (/kategoria-oferty/…).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cod_auds   = cod_audiences();
$cod_groups = cod_offers_grouped();
$cod_term   = is_tax( 'offer_cat' ) ? get_queried_object() : null;

get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'title'  => $cod_term ? $cod_term->name : __( 'Oferta', 'cod2026' ),
		'lead'   => $cod_term ? wp_strip_all_tags( $cod_term->description ) : __( 'Diagnoza, terapia i treningi dla dzieci, wsparcie dla dorosłych oraz coaching i szkolenia dla firm. Nie wiesz, od czego zacząć? Zadzwoń – pomożemy wybrać.', 'cod2026' ),
		'kicker' => $cod_term ? __( 'Oferta', 'cod2026' ) : '',
	)
);

if ( $cod_term ) {
	$cod_list = array();
	while ( have_posts() ) {
		the_post();
		$cod_list[] = cod_offer( get_the_ID() );
	}
	$cod_groups = array( 'term' => $cod_list );
}
?>
<div class="section section--tight">
	<div class="wrap">
		<?php foreach ( $cod_groups as $cod_key => $cod_list ) : ?>
			<?php
			if ( ! $cod_list ) {
				continue;
			}
			$cod_spans = cod_bento_spans( count( $cod_list ), ! empty( $cod_list[0]['featured'] ) );
			?>
			<section class="offer-group" aria-labelledby="h-g-<?php echo esc_attr( $cod_key ); ?>">
				<?php if ( isset( $cod_auds[ $cod_key ] ) ) : ?>
					<h2 class="fx-title fx-title--sm" id="h-g-<?php echo esc_attr( $cod_key ); ?>"><?php echo esc_html( $cod_auds[ $cod_key ]['tab'] ); ?></h2>
				<?php else : ?>
					<h2 class="sr-only" id="h-g-<?php echo esc_attr( $cod_key ); ?>"><?php esc_html_e( 'Usługi', 'cod2026' ); ?></h2>
				<?php endif; ?>
				<div class="bento">
					<?php
					foreach ( $cod_list as $cod_i => $cod_o ) {
						get_template_part( 'template-parts/cards/offer-card', null, array( 'offer' => $cod_o, 'span' => $cod_spans[ $cod_i ] ) );
					}
					?>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
</div>
<?php
get_footer();
