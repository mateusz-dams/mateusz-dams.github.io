<?php
/**
 * Akordeon pytań i odpowiedzi.
 *
 * @package cod2026
 *
 * @var array $args items – lista (q, a).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="acc reveal">
	<?php foreach ( $args['items'] as $cod_item ) : ?>
		<?php
		if ( empty( $cod_item['q'] ) ) {
			continue;
		}
		$cod_pid = cod_uid( 'acc' );
		?>
		<div class="acc-item">
			<h3><button class="acc-btn" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $cod_pid ); ?>"><span><?php echo esc_html( wp_strip_all_tags( $cod_item['q'] ) ); ?></span><span class="pm" aria-hidden="true"></span></button></h3>
			<div class="acc-panel" id="<?php echo esc_attr( $cod_pid ); ?>" role="region"><div><div class="pad prose"><?php echo cod_prose( wpautop( $cod_item['a'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></div></div>
		</div>
	<?php endforeach; ?>
</div>
