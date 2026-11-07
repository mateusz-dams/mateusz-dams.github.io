<?php
/**
 * Pojedyncza usługa (/oferta/…).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$cod_o     = cod_offer( get_the_ID() );
	$cod_topic = cod_topic_for( $cod_o['audience'] );
	$cod_auds  = cod_audiences();
	?>
	<article class="offer-page">
		<header class="offer-hero on-dark">
			<div class="aurora" aria-hidden="true"><span class="a1"></span><span class="a2"></span></div>
			<div class="wrap offer-hero__grid">
				<div>
					<?php cod_breadcrumbs(); ?>
					<p class="num-tag"><?php echo esc_html( $cod_o['label'] ? $cod_o['label'] : ( isset( $cod_auds[ $cod_o['audience'] ] ) ? $cod_auds[ $cod_o['audience'] ]['tab'] : '' ) ); ?></p>
					<h1 class="offer-hero__title"><?php the_title(); ?></h1>
					<?php if ( $cod_o['short'] ) : ?>
						<p class="lede"><?php echo esc_html( $cod_o['short'] ); ?></p>
					<?php endif; ?>
				</div>
				<aside class="offer-hero__card">
					<p class="offer-hero__price-label"><?php esc_html_e( 'Cena', 'cod2026' ); ?></p>
					<p class="offer-hero__price"><?php echo esc_html( $cod_o['price'] ); ?></p>
					<?php echo cod_status(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="btn-row">
						<?php echo cod_phone_button( 'usluga-' . get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a class="btn btn--ghost" href="<?php echo esc_url( add_query_arg( array( 'temat' => rawurlencode( $cod_topic ), 'usluga' => rawurlencode( $cod_o['title'] ) ), home_url( '/' ) ) . '#kontakt' ); ?>" data-track="cod_form_open" data-track-location="usluga-<?php the_ID(); ?>"><span><?php esc_html_e( 'Napisz wiadomość', 'cod2026' ); ?></span></a>
					</div>
				</aside>
			</div>
			<div class="ridge" aria-hidden="true"></div>
		</header>

		<div class="section">
			<div class="wrap offer-body">
				<?php if ( $cod_o['details'] ) : ?>
					<div class="offer-details">
						<?php foreach ( $cod_o['details'] as $cod_d ) : ?>
							<section class="offer-detail reveal">
								<?php if ( $cod_d['heading'] ) : ?>
									<h2><?php echo esc_html( $cod_d['heading'] ); ?></h2>
								<?php endif; ?>
								<div class="prose"><?php echo cod_prose( $cod_d['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
							</section>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( '' !== trim( get_the_content() ) ) : ?>
					<div class="prose entry-content offer-content<?php echo $cod_o['details'] ? ' offer-content--after' : ''; ?>">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
	$cod_more = array();
	$cod_grp  = cod_offers_grouped();
	if ( ! empty( $cod_grp[ $cod_o['audience'] ] ) ) {
		foreach ( $cod_grp[ $cod_o['audience'] ] as $cod_x ) {
			if ( $cod_x['id'] !== $cod_o['id'] ) {
				$cod_more[] = $cod_x;
			}
		}
	}
	$cod_more = array_slice( $cod_more, 0, 3 );
	if ( $cod_more ) :
		$cod_spans = cod_bento_spans( count( $cod_more ), false );
		?>
		<section class="section surface-sand" aria-labelledby="h-more">
			<div class="wrap">
				<div class="more-head">
					<h2 id="h-more" class="fx-title fx-title--sm"><?php esc_html_e( 'Zobacz też', 'cod2026' ); ?></h2>
					<a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'offer' ) ); ?>"><?php esc_html_e( 'Wszystkie usługi', 'cod2026' ); ?> <?php echo cod_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
				<div class="bento">
					<?php
					foreach ( $cod_more as $cod_i => $cod_x ) {
						get_template_part( 'template-parts/cards/offer-card', null, array( 'offer' => $cod_x, 'span' => $cod_spans[ $cod_i ] ) );
					}
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
