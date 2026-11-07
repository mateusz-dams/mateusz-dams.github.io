<?php
/**
 * Nagłówek podstrony (tytuł, okruszki, opcjonalny opis).
 *
 * @package cod2026
 *
 * @var array $args title, lead, compact (bool), kicker.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_a = wp_parse_args(
	$args,
	array(
		'title'   => '',
		'lead'    => '',
		'kicker'  => '',
		'compact' => false,
	)
);
if ( '' === $cod_a['title'] ) {
	if ( is_singular() ) {
		$cod_a['title'] = get_the_title();
	} elseif ( is_home() ) {
		$cod_a['title'] = get_the_title( (int) get_option( 'page_for_posts' ) );
	} elseif ( is_search() ) {
		/* translators: %s: szukana fraza. */
		$cod_a['title'] = sprintf( __( 'Wyniki wyszukiwania: %s', 'cod2026' ), get_search_query() );
	} else {
		$cod_a['title'] = wp_strip_all_tags( get_the_archive_title() );
	}
}
if ( '' === $cod_a['lead'] && is_singular( 'page' ) && has_excerpt() ) {
	$cod_a['lead'] = get_the_excerpt();
}
if ( '' === $cod_a['lead'] && ( is_category() || is_tax() || is_tag() ) ) {
	$cod_a['lead'] = wp_strip_all_tags( term_description() );
}
?>
<header class="page-hero<?php echo $cod_a['compact'] ? ' page-hero--compact' : ''; ?>">
	<div class="page-hero__shape" aria-hidden="true"></div>
	<div class="wrap">
		<?php cod_breadcrumbs(); ?>
		<?php if ( $cod_a['kicker'] ) : ?>
			<p class="num-tag"><?php echo esc_html( $cod_a['kicker'] ); ?></p>
		<?php endif; ?>
		<h1 class="page-hero__title"><?php echo esc_html( $cod_a['title'] ); ?></h1>
		<?php if ( $cod_a['lead'] ) : ?>
			<p class="lede page-hero__lead"><?php echo esc_html( $cod_a['lead'] ); ?></p>
		<?php endif; ?>
	</div>
</header>
