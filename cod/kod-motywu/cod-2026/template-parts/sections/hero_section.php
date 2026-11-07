<?php
/**
 * Sekcja: hero (pierwszy ekran strony, np. strony sprzedażowej).
 *
 * Pola: hero (powtarzacz): hero_img, hero_title, hero_desc, hero_reverse, hero_buttons (hero_button, hero_button_primary).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! have_rows( 'hero' ) ) {
	return;
}
$cod_first = 0 === (int) $GLOBALS['cod_section_index'];
$cod_count = count( (array) get_sub_field( 'hero' ) );
$cod_n     = 0;
?>
<section class="fx fx-hero on-dark<?php echo $cod_count > 1 ? ' fx-hero--slides' : ''; ?>">
	<div class="aurora" aria-hidden="true"><span class="a1"></span><span class="a2"></span><span class="a3"></span></div>
	<div class="grain" aria-hidden="true"></div>
	<div class="fx-hero__track"<?php echo $cod_count > 1 ? ' data-slides tabindex="0" aria-roledescription="karuzela"' : ''; ?>>
		<?php
		while ( have_rows( 'hero' ) ) :
			the_row();
			$cod_tag = ( $cod_first && 0 === $cod_n ) ? 'h1' : 'h2';
			$cod_img = get_sub_field( 'hero_img' );
			?>
			<div class="fx-hero__slide">
				<div class="wrap fx-hero__grid<?php echo get_sub_field( 'hero_reverse' ) ? ' is-reverse' : ''; ?><?php echo $cod_img ? '' : ' no-media'; ?>">
					<div class="fx-hero__copy">
						<?php if ( 0 === $cod_n ) : ?>
							<?php cod_breadcrumbs(); ?>
						<?php endif; ?>
						<?php if ( get_sub_field( 'hero_title' ) ) : ?>
							<<?php echo esc_html( $cod_tag ); ?> class="fx-hero__title"><?php echo wp_kses( (string) get_sub_field( 'hero_title' ), cod_inline_tags() ); ?></<?php echo esc_html( $cod_tag ); ?>>
						<?php endif; ?>
						<?php if ( get_sub_field( 'hero_desc' ) ) : ?>
							<div class="prose prose--dark fx-hero__desc"><?php echo cod_prose( get_sub_field( 'hero_desc' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
						<?php cod_sec_buttons( 'hero_buttons', 'hero_button', 'hero-' . $cod_n ); ?>
					</div>
					<?php if ( $cod_img ) : ?>
						<figure class="fx-hero__media">
							<?php
							echo cod_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$cod_img,
								'large',
								array(
									'loading'       => 0 === $cod_n ? 'eager' : 'lazy',
									'fetchpriority' => 0 === $cod_n ? 'high' : 'auto',
									'sizes'         => '(max-width: 900px) 92vw, 560px',
								)
							);
							?>
						</figure>
					<?php endif; ?>
				</div>
			</div>
			<?php
			++$cod_n;
		endwhile;
		?>
	</div>
	<?php if ( $cod_count > 1 ) : ?>
		<div class="wrap fx-hero__nav">
			<button type="button" class="slide-btn" data-slide="-1" aria-label="<?php esc_attr_e( 'Poprzedni', 'cod2026' ); ?>"><?php echo cod_icon( 'left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<button type="button" class="slide-btn" data-slide="1" aria-label="<?php esc_attr_e( 'Następny', 'cod2026' ); ?>"><?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>
	<?php endif; ?>
	<div class="ridge" aria-hidden="true"></div>
</section>
