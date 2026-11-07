<?php
/**
 * Strona główna – pierwszy ekran i pasek usług.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_front   = (int) get_option( 'page_on_front' );
$cod_image   = cod_field( 'cod_hero_image', $cod_front ? $cod_front : false, '' );
$cod_chips   = array_slice( cod_lines( cod_home( 'cod_hero_chips' ) ), 0, 3 );
$cod_booking = (string) cod_option( 'cod_booking_url', '' );
$cod_marquee = array();
foreach ( cod_offers_grouped() as $cod_list ) {
	foreach ( $cod_list as $cod_o ) {
		$cod_marquee[] = $cod_o['short_name'];
	}
}
$cod_marquee = array_values( array_unique( array_filter( $cod_marquee ) ) );
?>
<section class="hero on-dark" aria-labelledby="h-hero">
	<div class="aurora" aria-hidden="true"><span class="a1"></span><span class="a2"></span><span class="a3"></span></div>
	<div class="grain" aria-hidden="true"></div>
	<div class="glow" aria-hidden="true"></div>
	<div class="wrap hero-grid">
		<div class="hero-copy">
			<p class="hero-kicker fade-up d1"><span class="dot" aria-hidden="true"></span><?php echo esc_html( cod_home( 'cod_hero_kicker' ) ); ?></p>
			<h1 id="h-hero" data-split><?php echo esc_html( cod_home( 'cod_hero_title' ) ); ?></h1>
			<p class="lede fade-up d2"><?php echo esc_html( cod_home( 'cod_hero_lead' ) ); ?></p>
			<div class="btn-row fade-up d3">
				<?php echo cod_phone_button( 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<a class="btn btn--ghost" href="#znajdz" data-track="cod_finder_open" data-track-location="hero"><span><?php echo esc_html( cod_home( 'cod_hero_second' ) ); ?></span></a>
				<?php if ( $cod_booking ) : ?>
					<?php echo cod_button( $cod_booking, 'ghost', array( 'label' => __( 'Zarezerwuj termin online', 'cod2026' ), 'icon' => 'calendar', 'location' => 'hero', 'data' => array( 'track' => 'cod_booking_click' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</div>
			<div class="hero-meta fade-up d4">
				<?php echo cod_status(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php echo cod_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( cod_contact( 'address' ) ); ?></span>
			</div>
		</div>
		<figure class="hero-visual">
			<div class="mountain" aria-hidden="true"></div>
			<div class="hero-photo">
				<?php
				echo cod_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$cod_image,
					'large',
					array(
						'alt'           => __( 'Ewa Gardyańczyk, psycholożka, w jasnej marynarce, uśmiecha się', 'cod2026' ),
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'width'         => 880,
						'height'        => 1100,
						'sizes'         => '(max-width: 900px) 80vw, 470px',
					),
					'ewa-hero.webp'
				);
				?>
			</div>
			<?php foreach ( $cod_chips as $cod_i => $cod_chip ) : ?>
				<div class="chip-float c<?php echo (int) $cod_i + 1; ?>" aria-hidden="true"><b></b><?php echo esc_html( $cod_chip ); ?></div>
			<?php endforeach; ?>
			<figcaption class="hero-caption"><?php echo esc_html( cod_home( 'cod_hero_caption' ) ); ?></figcaption>
		</figure>
	</div>
	<div class="ridge" aria-hidden="true"></div>
	<?php if ( $cod_marquee ) : ?>
		<div class="marquee" aria-hidden="true">
			<div class="marquee-track">
				<?php for ( $cod_r = 0; $cod_r < 2; $cod_r++ ) : ?>
					<ul>
						<?php foreach ( $cod_marquee as $cod_m ) : ?>
							<li><?php echo esc_html( $cod_m ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endfor; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
