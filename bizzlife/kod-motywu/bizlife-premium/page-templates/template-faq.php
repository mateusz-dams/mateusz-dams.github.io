<?php
/**
 * Template Name: FAQ
 * Template Post Type: page
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

// Grupowanie pytań wg tematów.
$bzl_groups = array();
$bzl_all    = array();
$bzl_terms  = get_terms(
	array(
		'taxonomy'   => 'bzl_faq_topic',
		'hide_empty' => true,
		'orderby'    => 'term_order',
	)
);
if ( ! is_wp_error( $bzl_terms ) ) {
	foreach ( $bzl_terms as $bzl_term ) {
		$bzl_items = bzl_faqs( array( 'topic' => $bzl_term->term_id ) );
		if ( $bzl_items ) {
			$bzl_groups[] = array(
				'id'    => 'temat-' . $bzl_term->slug,
				'name'  => $bzl_term->name,
				'items' => $bzl_items,
			);
			$bzl_all      = array_merge( $bzl_all, $bzl_items );
		}
	}
}
$bzl_rest = array_filter(
	bzl_faqs(),
	static function ( $f ) {
		return ! has_term( '', 'bzl_faq_topic', $f );
	}
);
if ( $bzl_rest ) {
	$bzl_groups[] = array(
		'id'    => 'temat-pozostale',
		'name'  => $bzl_groups ? __( 'Pozostałe pytania', 'bizlife' ) : __( 'Pytania i odpowiedzi', 'bizlife' ),
		'items' => array_values( $bzl_rest ),
	);
	$bzl_all      = array_merge( $bzl_all, $bzl_rest );
}

add_action(
	'wp_head',
	static function () use ( $bzl_all ) {
		bzl_faq_schema( $bzl_all );
	},
	30
);

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'eyebrow' => 'FAQ',
			'title'   => get_the_title(),
			'lead'    => has_excerpt() ? get_the_excerpt() : bzl_mod( 'faq_lead' ),
			'ghost'   => 'FAQ',
		)
	);
	?>
	<section class="section faq faq--page tone-dark" aria-label="<?php esc_attr_e( 'Pytania i odpowiedzi', 'bizlife' ); ?>">
		<div class="container faq-layout">
			<?php if ( count( $bzl_groups ) > 1 ) : ?>
				<nav class="faq-nav" aria-label="<?php esc_attr_e( 'Tematy pytań', 'bizlife' ); ?>">
					<p class="faq-nav__label"><?php esc_html_e( 'Tematy', 'bizlife' ); ?></p>
					<ul>
						<?php foreach ( $bzl_groups as $bzl_g ) : ?>
							<li><a href="#<?php echo esc_attr( $bzl_g['id'] ); ?>"><?php echo esc_html( $bzl_g['name'] ); ?><span class="faq-nav__count"><?php echo (int) count( $bzl_g['items'] ); ?></span></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
			<div class="faq-groups">
				<?php if ( '' !== trim( get_the_content() ) ) : ?>
					<div class="prose prose--dark faq-intro"><?php the_content(); ?></div>
				<?php endif; ?>
				<?php foreach ( $bzl_groups as $bzl_g ) : ?>
					<div class="faq-group" id="<?php echo esc_attr( $bzl_g['id'] ); ?>">
						<h2 class="faq-group__title"><?php echo esc_html( $bzl_g['name'] ); ?></h2>
						<?php
						get_template_part(
							'template-parts/components/accordion',
							null,
							array(
								'items' => $bzl_g['items'],
								'id'    => $bzl_g['id'],
							)
						);
						?>
					</div>
				<?php endforeach; ?>
				<?php if ( ! $bzl_groups && current_user_can( 'edit_posts' ) ) : ?>
					<p class="admin-hint"><?php esc_html_e( 'Dodaj pytania w kokpicie: FAQ → Dodaj pytanie.', 'bizlife' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	get_template_part(
		'template-parts/sections/cta',
		null,
		array(
			'title' => __( 'Nie znalazłeś odpowiedzi?', 'bizlife' ),
			'text'  => __( 'Zadzwoń lub napisz — odpowiemy na pytania dotyczące Twojej firmy.', 'bizlife' ),
			'tone'  => 'tone-darker',
		)
	);
endwhile;

get_footer();
