<?php
/**
 * Sekcja: pełna szerokość (nagłówek, tekst, przyciski).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section<?php echo cod_sec_attrs( 'full_width', 'fx-text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wrap fx-narrow">
		<?php cod_sec_head( 'full_width' ); ?>
		<?php if ( get_sub_field( 'full_width_section_desc' ) ) : ?>
			<div class="prose fx-prose reveal"><?php echo cod_prose( get_sub_field( 'full_width_section_desc' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
		<?php cod_sec_buttons( 'full_width_section_buttons', 'full_width_section_button', cod_sec_loc( 'full_width' ), 'fx-buttons reveal' ); ?>
	</div>
</section>
