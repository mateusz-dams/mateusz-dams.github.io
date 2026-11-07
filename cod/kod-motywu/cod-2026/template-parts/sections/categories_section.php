<?php
/**
 * Sekcja: obszary pomocy (kategorie usług) jako lista ścieżek.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_terms = get_sub_field( 'categories' );
if ( ! $cod_terms ) {
	return;
}
?>
<section<?php echo cod_sec_attrs( 'categories', 'fx-paths', 'surface-sand' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="wrap">
		<?php cod_sec_head( 'categories', 'left' ); ?>
		<ul class="paths reveal-stagger">
			<?php foreach ( (array) $cod_terms as $cod_t ) : ?>
				<?php
				$cod_t = is_numeric( $cod_t ) ? get_term( (int) $cod_t ) : $cod_t;
				if ( ! $cod_t || is_wp_error( $cod_t ) ) {
					continue;
				}
				$cod_link = get_term_link( $cod_t );
				if ( is_wp_error( $cod_link ) ) {
					continue;
				}
				$cod_img = cod_has_acf() ? get_field( 'cat_img', $cod_t ) : '';
				?>
				<li>
					<a href="<?php echo esc_url( $cod_link ); ?>" data-track="cod_path_select" data-track-location="<?php echo esc_attr( $cod_t->slug ); ?>">
						<span class="paths__ico" aria-hidden="true"><?php echo $cod_img ? cod_img( $cod_img, 'thumbnail', array( 'alt' => '' ) ) : '<span class="tri"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="say"><?php echo esc_html( $cod_t->name ); ?></span>
						<?php if ( $cod_t->description ) : ?>
							<span class="what"><?php echo esc_html( $cod_t->description ); ?></span>
						<?php endif; ?>
						<span class="go" aria-hidden="true"><?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
