<?php
/**
 * Sekcja „Zespół” – w poprzednim motywie używana też do kart porównawczych (przed/po, „dla kogo”)
 * i do pakietów cenowych. Tryb wybierany automatycznie:
 * - wszystkie osoby mają zdjęcia i brak cen  → karty zespołu,
 * - którakolwiek pozycja ma cenę lub przycisk → pakiety (ceny z WooCommerce, gdy przycisk prowadzi do produktu),
 * - w pozostałych przypadkach                → karty porównawcze.
 *
 * Pola: our_team_section_desc_top, our_team (our_team_img, our_team_title, our_team_subtitle, our_team_desc,
 * our_team_old_price, our_team_regular_price, our_team_button), our_team_section_desc.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_rows = get_sub_field( 'our_team' );
$cod_rows = is_array( $cod_rows ) ? $cod_rows : array();
$cod_all_img = (bool) $cod_rows;
$cod_priced  = false;
foreach ( $cod_rows as $cod_r ) {
	if ( empty( $cod_r['our_team_img'] ) ) {
		$cod_all_img = false;
	}
	if ( ! empty( $cod_r['our_team_old_price'] ) || ! empty( $cod_r['our_team_regular_price'] ) || ! empty( $cod_r['our_team_button'] ) ) {
		$cod_priced = true;
	}
}
$cod_mode = $cod_priced ? 'tiers' : ( $cod_all_img ? 'team' : 'compare' );
$cod_loc  = cod_sec_loc( 'our_team' );
?>
<section<?php echo cod_sec_attrs( 'our_team', 'fx-cards fx-cards--' . $cod_mode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wrap">
		<div class="fx-narrow">
			<?php cod_sec_head( 'our_team' ); ?>
			<?php if ( get_sub_field( 'our_team_section_desc_top' ) ) : ?>
				<div class="prose fx-prose reveal"><?php echo cod_prose( get_sub_field( 'our_team_section_desc_top' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
		</div>

		<?php if ( 'team' === $cod_mode ) : ?>
			<?php
			$cod_people = array();
			foreach ( $cod_rows as $cod_r ) {
				$cod_people[] = array(
					'name'  => wp_strip_all_tags( (string) $cod_r['our_team_title'] ),
					'role'  => wp_strip_all_tags( (string) $cod_r['our_team_subtitle'] ),
					'scope' => cod_snippet( (string) $cod_r['our_team_desc'], 110 ),
					'bio'   => (string) $cod_r['our_team_desc'],
					'photo' => $cod_r['our_team_img'],
				);
			}
			get_template_part( 'template-parts/cards/team-list', null, array( 'people' => $cod_people ) );
			?>
		<?php elseif ( $cod_rows ) : ?>
			<div class="tiers tiers--<?php echo (int) min( count( $cod_rows ), 4 ); ?> reveal-stagger">
				<?php foreach ( $cod_rows as $cod_i => $cod_r ) : ?>
					<?php
					$cod_link    = ! empty( $cod_r['our_team_button'] ) ? $cod_r['our_team_button'] : null;
					$cod_product = ( $cod_link && function_exists( 'cod_product_from_url' ) ) ? cod_product_from_url( is_array( $cod_link ) ? $cod_link['url'] : $cod_link ) : null;
					$cod_alt     = 1 === $cod_i % 2;
					$cod_cls     = 'tier' . ( $cod_alt ? ( 'tiers' === $cod_mode ? ' is-featured on-dark' : ' is-alt' ) : '' );
					?>
					<article class="<?php echo esc_attr( $cod_cls ); ?>">
						<?php if ( ! empty( $cod_r['our_team_img'] ) ) : ?>
							<div class="tier__img"><?php echo cod_img( $cod_r['our_team_img'], 'cod-card', array( 'sizes' => '(max-width: 760px) 90vw, 360px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $cod_r['our_team_subtitle'] ) ) : ?>
							<p class="tier__label"><?php echo wp_kses( $cod_r['our_team_subtitle'], cod_inline_tags() ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $cod_r['our_team_title'] ) ) : ?>
							<h3 class="tier__title"><?php echo wp_kses( $cod_r['our_team_title'], cod_inline_tags() ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $cod_r['our_team_desc'] ) ) : ?>
							<div class="tier__desc prose"><?php echo cod_prose( $cod_r['our_team_desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
						<?php if ( $cod_product || ! empty( $cod_r['our_team_old_price'] ) || ! empty( $cod_r['our_team_regular_price'] ) ) : ?>
							<p class="tier__price">
								<?php if ( $cod_product ) : ?>
									<?php echo wp_kses_post( $cod_product->get_price_html() ); ?>
								<?php else : ?>
									<?php if ( ! empty( $cod_r['our_team_old_price'] ) ) : ?>
										<del><span class="sr-only"><?php esc_html_e( 'Cena regularna:', 'cod2026' ); ?> </span><?php echo esc_html( wp_strip_all_tags( $cod_r['our_team_old_price'] ) ); ?></del>
									<?php endif; ?>
									<?php if ( ! empty( $cod_r['our_team_regular_price'] ) ) : ?>
										<ins><span class="sr-only"><?php esc_html_e( 'Cena:', 'cod2026' ); ?> </span><?php echo esc_html( wp_strip_all_tags( $cod_r['our_team_regular_price'] ) ); ?></ins>
									<?php endif; ?>
								<?php endif; ?>
							</p>
						<?php endif; ?>
						<?php if ( $cod_link ) : ?>
							<div class="tier__cta">
								<?php
								echo cod_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									$cod_link,
									'primary',
									array(
										'location' => $cod_loc,
										'icon'     => '',
										'data'     => array( 'track' => 'cod_package_click', 'package' => wp_strip_all_tags( (string) $cod_r['our_team_title'] ) ),
									)
								);
								?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( get_sub_field( 'our_team_section_desc' ) ) : ?>
			<div class="fx-narrow">
				<div class="prose fx-prose fx-prose--after reveal"><?php echo cod_prose( get_sub_field( 'our_team_section_desc' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
		<?php endif; ?>
	</div>
</section>
