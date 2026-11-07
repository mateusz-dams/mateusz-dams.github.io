<?php
/**
 * Sekcja: wezwanie do działania (turkusowy pasek z górami).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_title = (string) get_sub_field( 'call_to_action_section_title' );
$cod_sub   = (string) get_sub_field( 'call_to_action_section_subtitle' );
$cod_id    = sanitize_title( (string) get_sub_field( 'call_to_action_section_id' ) );
?>
<section class="fx fx-cta cta-band on-aqua"<?php echo $cod_id ? ' id="' . esc_attr( $cod_id ) . '"' : ''; ?>>
	<div class="peaks" aria-hidden="true"><span class="p1"></span><span class="p2"></span><span class="p3"></span><span class="flag2"></span></div>
	<div class="wrap fx-narrow fx-cta__in">
		<?php if ( '' !== trim( $cod_sub ) ) : ?>
			<p class="fx-cta__sub reveal"><?php echo wp_kses( $cod_sub, cod_inline_tags() ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== trim( $cod_title ) ) : ?>
			<h2 class="fx-cta__title reveal"><?php echo wp_kses( $cod_title, cod_inline_tags() ); ?></h2>
		<?php endif; ?>
		<?php if ( get_sub_field( 'call_to_action_section_desc' ) ) : ?>
			<div class="prose fx-cta__desc reveal"><?php echo cod_prose( get_sub_field( 'call_to_action_section_desc' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
		<?php cod_sec_buttons( 'call_to_action_section_buttons', 'call_to_action_section_button', cod_sec_loc( 'call_to_action' ), 'fx-buttons reveal' ); ?>
	</div>
</section>
