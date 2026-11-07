<?php
/**
 * Sekcja: zdjęcie i tekst obok siebie (50/50).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_img = get_sub_field( 'fifty_fifty_section_img' );
?>
<section<?php echo cod_sec_attrs( 'fifty_fifty', 'fx-split' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wrap fx-split__grid<?php echo get_sub_field( 'fifty_fifty_section_reverse' ) ? ' is-reverse' : ''; ?><?php echo $cod_img ? '' : ' no-media'; ?>">
		<?php if ( $cod_img ) : ?>
			<figure class="fx-split__media reveal">
				<?php echo cod_img( $cod_img, 'large', array( 'sizes' => '(max-width: 900px) 92vw, 600px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
		<?php endif; ?>
		<div class="fx-split__copy">
			<?php cod_sec_head( 'fifty_fifty', 'left' ); ?>
			<?php if ( get_sub_field( 'fifty_fifty_section_desc' ) ) : ?>
				<div class="prose reveal"><?php echo cod_prose( get_sub_field( 'fifty_fifty_section_desc' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
			<?php cod_sec_buttons( 'fifty_fifty_section_buttons', 'fifty_fifty_section_button', cod_sec_loc( 'fifty_fifty' ), 'fx-buttons reveal' ); ?>
		</div>
	</div>
</section>
