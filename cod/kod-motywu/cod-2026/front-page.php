<?php
/**
 * Strona główna.
 *
 * Teksty: Strony → Strona główna (pola „Strona główna (COD 2026)”). Puste pole = tekst domyślny.
 * Usługi: menu „Oferta”. Zespół: Opcje motywu → COD 2026.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cod_groups = cod_offers_grouped();
$cod_auds   = cod_audiences();
$cod_tabs   = array_filter( $cod_groups );

get_template_part( 'template-parts/home/hero' );
?>

<section class="section finder" id="znajdz" aria-labelledby="h-finder">
	<div class="wrap">
		<div class="sec-head reveal">
			<div><span class="num-tag"><?php echo esc_html( cod_home( 'cod_finder_kicker' ) ); ?></span><h2 id="h-finder"><?php echo esc_html( cod_home( 'cod_finder_title' ) ); ?></h2></div>
			<p class="lede"><?php echo esc_html( cod_home( 'cod_finder_lead' ) ); ?></p>
		</div>
		<div class="wiz reveal on-light" id="wiz">
			<div class="wiz-steps" aria-hidden="true"><span class="on"></span><span></span><span></span></div>
			<div class="wiz-panel" data-step="1">
				<h3 tabindex="-1"><?php esc_html_e( 'Kogo dotyczy sprawa?', 'cod2026' ); ?></h3>
				<div class="opts">
					<?php foreach ( $cod_auds as $cod_key => $cod_a ) : ?>
						<?php
						if ( empty( $cod_groups[ $cod_key ] ) ) {
							continue;
						}
						?>
						<button class="opt" type="button" data-who="<?php echo esc_attr( $cod_key ); ?>"><span class="ico"><?php echo cod_icon( $cod_a['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span><?php echo esc_html( $cod_a['who'] ); ?><small><?php echo esc_html( $cod_a['hint'] ); ?></small></span></button>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="wiz-panel" data-step="2" hidden>
				<h3 tabindex="-1"><?php esc_html_e( 'Co jest teraz najważniejsze?', 'cod2026' ); ?></h3>
				<div class="opts" id="wiz-opts"></div>
				<button class="wiz-back" type="button" data-back="1">← <?php esc_html_e( 'Wróć', 'cod2026' ); ?></button>
			</div>
			<div class="wiz-panel" data-step="3" hidden aria-live="polite">
				<div class="result">
					<div>
						<span class="tag"><?php esc_html_e( 'Proponowany pierwszy krok', 'cod2026' ); ?></span>
						<h3 tabindex="-1" id="wiz-title"></h3>
						<p id="wiz-desc"></p>
						<p class="price-big" id="wiz-price"></p>
					</div>
					<div class="btn-row">
						<?php echo cod_phone_button( 'kreator', 'primary', __( 'Zadzwoń i umów', 'cod2026' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a class="btn btn--ghost" href="#kontakt" id="wiz-form" data-track="cod_form_open" data-track-location="kreator"><span><?php esc_html_e( 'Napisz wiadomość', 'cod2026' ); ?></span></a>
						<a class="btn btn--ghost" href="#oferta" id="wiz-more"><span><?php esc_html_e( 'Szczegóły usługi', 'cod2026' ); ?></span></a>
					</div>
				</div>
				<button class="wiz-back" type="button" data-back="2">← <?php esc_html_e( 'Zmień odpowiedź', 'cod2026' ); ?></button>
			</div>
			<script type="application/json" id="cod-wizard"><?php echo wp_json_encode( cod_wizard_data(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ); ?></script>
		</div>
	</div>
</section>

<section class="section" id="oferta" aria-labelledby="h-oferta">
	<div class="wrap">
		<div class="sec-head reveal">
			<div><span class="num-tag"><?php echo esc_html( cod_home( 'cod_offer_kicker' ) ); ?></span><h2 id="h-oferta"><?php echo esc_html( cod_home( 'cod_offer_title' ) ); ?></h2></div>
			<p class="lede"><?php echo esc_html( cod_home( 'cod_offer_lead' ) ); ?></p>
		</div>
		<?php if ( $cod_tabs ) : ?>
			<div class="tabs reveal" role="tablist" aria-label="<?php esc_attr_e( 'Grupy usług', 'cod2026' ); ?>">
				<span class="tab-ink" aria-hidden="true"></span>
				<?php
				$cod_first = true;
				foreach ( $cod_tabs as $cod_key => $cod_list ) :
					?>
					<button class="tab" role="tab" id="t-<?php echo esc_attr( $cod_key ); ?>" aria-controls="p-<?php echo esc_attr( $cod_key ); ?>" aria-selected="<?php echo $cod_first ? 'true' : 'false'; ?>"<?php echo $cod_first ? '' : ' tabindex="-1"'; ?> data-tab="<?php echo esc_attr( $cod_key ); ?>"><?php echo esc_html( $cod_auds[ $cod_key ]['tab'] ); ?></button>
					<?php
					$cod_first = false;
				endforeach;
				?>
			</div>
			<?php
			$cod_first = true;
			foreach ( $cod_tabs as $cod_key => $cod_list ) :
				?>
				<div class="panel" id="p-<?php echo esc_attr( $cod_key ); ?>" role="tabpanel" aria-labelledby="t-<?php echo esc_attr( $cod_key ); ?>"<?php echo $cod_first ? '' : ' hidden'; ?>>
					<span class="anchor" id="<?php echo esc_attr( $cod_key ); ?>" data-tab-anchor="<?php echo esc_attr( $cod_key ); ?>"></span>
					<div class="bento">
						<?php
						$cod_spans = cod_bento_spans( count( $cod_list ), ! empty( $cod_list[0]['featured'] ) );
						foreach ( $cod_list as $cod_i => $cod_o ) {
							get_template_part(
								'template-parts/cards/offer-card',
								null,
								array(
									'offer' => $cod_o,
									'span'  => isset( $cod_spans[ $cod_i ] ) ? $cod_spans[ $cod_i ] : 1,
								)
							);
						}
						?>
					</div>
				</div>
				<?php
				$cod_first = false;
			endforeach;
			?>
		<?php else : ?>
			<p class="empty-note"><?php esc_html_e( 'Opisy usług pojawią się tutaj po dodaniu ich w menu „Oferta”.', 'cod2026' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_template_part( 'template-parts/home/about' );
get_template_part( 'template-parts/home/steps' );
get_template_part( 'template-parts/home/team' );
get_template_part( 'template-parts/home/reviews' );
get_template_part( 'template-parts/home/faq' );
get_template_part( 'template-parts/contact-block' );
get_template_part( 'template-parts/home/shop' );
get_template_part( 'template-parts/cta-band' );

get_footer();
