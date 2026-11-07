<?php
/**
 * Kafelek usługi: krótko (etykieta, tytuł, 2–3 linijki, cena). Pełny opis w panelu z boku.
 *
 * @package cod2026
 *
 * @var array $args offer (dane z cod_offer()), span ('lead' = wyróżniony pierwszy krok), heading (h2|h3).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$o       = $args['offer'];
$lead    = isset( $args['span'] ) && 'lead' === $args['span'];
$tag     = isset( $args['heading'] ) ? $args['heading'] : 'h3';
$classes = 'card spot' . ( $lead ? ' card--lead on-dark' : '' );
$topic   = cod_topic_for( $o['audience'] );
$label   = $o['label'] ? $o['label'] : ( $lead ? __( 'Najczęstszy pierwszy krok', 'cod2026' ) : '' );
?>
<article class="<?php echo esc_attr( $classes ); ?>" data-svc="<?php echo esc_attr( $o['key'] ); ?>" data-topic-value="<?php echo esc_attr( $topic ); ?>" data-service-name="<?php echo esc_attr( $o['title'] ); ?>" data-url="<?php echo esc_url( $o['url'] ); ?>" data-kicker="<?php esc_attr_e( 'Szczegóły usługi', 'cod2026' ); ?>">
	<?php if ( $label ) : ?>
		<p class="for"><?php echo esc_html( $label ); ?></p>
	<?php endif; ?>
	<<?php echo esc_html( $tag ); ?> class="card__title"><?php echo esc_html( $o['title'] ); ?></<?php echo esc_html( $tag ); ?>>
	<?php if ( $o['card_text'] ) : ?>
		<p class="card__text"><?php echo esc_html( $o['card_text'] ); ?></p>
	<?php endif; ?>
	<div class="foot">
		<span class="price<?php echo $o['price_text'] ? ' price--text' : ''; ?>"><?php echo esc_html( $o['price'] ); ?></span>
		<a class="more" href="<?php echo esc_url( $o['url'] ); ?>" data-svc-open="<?php echo esc_attr( $o['key'] ); ?>">
			<?php esc_html_e( 'Szczegóły', 'cod2026' ); ?>
			<span class="sr-only">: <?php echo esc_html( $o['title'] ); ?></span>
			<span class="arr"><?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</a>
	</div>
	<template class="svc-full">
		<?php if ( $o['label'] ) : ?>
			<p class="d-for"><?php echo esc_html( $o['label'] ); ?></p>
		<?php endif; ?>
		<h3><?php echo esc_html( $o['title'] ); ?></h3>
		<p class="d-price"><?php echo esc_html( $o['price'] ); ?></p>
		<?php if ( $o['bullets'] ) : ?>
			<ul class="d-bullets">
				<?php foreach ( $o['bullets'] as $b ) : ?>
					<li><?php echo esc_html( $b ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $o['details'] ) : ?>
			<?php foreach ( $o['details'] as $d ) : ?>
				<?php if ( $d['heading'] ) : ?>
					<h4><?php echo esc_html( $d['heading'] ); ?></h4>
				<?php endif; ?>
				<div class="d-text prose"><?php echo cod_prose( $d['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endforeach; ?>
		<?php elseif ( '' !== trim( wp_strip_all_tags( $o['content'] ) ) ) : ?>
			<div class="d-text prose"><?php echo cod_prose( wpautop( strip_shortcodes( $o['content'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php else : ?>
			<p><?php echo esc_html( $o['short'] ); ?></p>
		<?php endif; ?>
	</template>
</article>
