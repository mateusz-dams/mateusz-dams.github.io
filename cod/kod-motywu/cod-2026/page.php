<?php
/**
 * Strona (także strony sprzedażowe zbudowane z sekcji, koszyk, zamówienie, „Moje konto”, „Moje szkolenia”).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$cod_id       = get_the_ID();
	$cod_sections = cod_has_sections( $cod_id );
	$cod_first    = $cod_sections ? cod_first_layout( $cod_id ) : '';
	$cod_banner   = ! ( cod_has_acf() && get_field( 'disable_banner', $cod_id ) ) && ! in_array( $cod_first, array( 'hero_section', 'contact_section' ), true );
	$cod_shop     = cod_is_shop_context();
	$cod_content  = '' !== trim( get_the_content() );

	if ( $cod_banner ) {
		get_template_part( 'template-parts/page-hero', null, array( 'compact' => $cod_shop ) );
	}

	if ( $cod_content ) :
		?>
		<div class="section page-body<?php echo $cod_shop ? ' page-body--shop' : ''; ?><?php echo $cod_sections ? ' page-body--before-sections' : ''; ?>">
			<div class="wrap<?php echo $cod_shop ? '' : ' fx-narrow'; ?>">
				<?php if ( ! $cod_banner ) : ?>
					<h1 class="sr-only"><?php the_title(); ?></h1>
				<?php endif; ?>
				<div class="prose entry-content">
					<?php the_content(); ?>
				</div>
				<?php
				wp_link_pages(
					array(
						'before' => '<nav class="page-links">',
						'after'  => '</nav>',
					)
				);
				?>
			</div>
		</div>
		<?php
	endif;

	cod_render_sections( $cod_id );

	if ( comments_open() || get_comments_number() ) {
		echo '<div class="section"><div class="wrap fx-narrow">';
		comments_template();
		echo '</div></div>';
	}
endwhile;

get_footer();
