<?php
/**
 * Strona główna – o Ewie, kwalifikacje i liczniki.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_front = (int) get_option( 'page_on_front' );
$cod_pid   = $cod_front ? $cod_front : false;
$cod_image = cod_field( 'cod_about_image', $cod_pid, '' );
$cod_stats = cod_field( 'cod_stats', $cod_pid, array() );
if ( ! is_array( $cod_stats ) || ! $cod_stats ) {
	$cod_stats = cod_default_stats();
}
?>
<section class="section about" id="o-mnie" aria-labelledby="h-about">
	<div class="wrap">
		<div class="about-grid">
			<div class="about-photo reveal">
				<div class="ph">
					<?php
					echo cod_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$cod_image,
						'large',
						array(
							'alt'           => __( 'Portret Ewy Gardyańczyk', 'cod2026' ),
							'width'         => 800,
							'height'        => 800,
							'data-parallax' => '',
							'sizes'         => '(max-width: 860px) 90vw, 480px',
						),
						'ewa-portret.webp'
					);
					?>
				</div>
			</div>
			<div class="reveal">
				<span class="num-tag"><?php echo esc_html( cod_home( 'cod_about_kicker' ) ); ?></span>
				<h2 id="h-about" class="mb-h2"><?php echo esc_html( cod_home( 'cod_about_title' ) ); ?></h2>
				<p class="mission"><?php echo esc_html( cod_home( 'cod_about_quote' ) ); ?></p>
				<div class="prose"><?php echo cod_prose( cod_home( 'cod_about_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<ul class="quals">
					<?php foreach ( cod_lines( cod_home( 'cod_about_quals' ) ) as $cod_q ) : ?>
						<li><?php echo esc_html( $cod_q ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="stats reveal-stagger">
			<?php foreach ( array_slice( $cod_stats, 0, 3 ) as $cod_s ) : ?>
				<?php
				$cod_num = isset( $cod_s['number'] ) ? (int) $cod_s['number'] : 0;
				$cod_suf = isset( $cod_s['suffix'] ) ? (string) $cod_s['suffix'] : '';
				?>
				<div class="stat"><strong data-count="<?php echo esc_attr( $cod_num ); ?>" data-suffix="<?php echo esc_attr( $cod_suf ); ?>"><?php echo esc_html( $cod_num . $cod_suf ); ?></strong><span><?php echo esc_html( isset( $cod_s['label'] ) ? $cod_s['label'] : '' ); ?></span></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
