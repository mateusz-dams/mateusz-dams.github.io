<?php
/**
 * Nagłówek podstrony (H1 + okruszki).
 *
 * @package Bizlife
 *
 * @var array $args eyebrow, title, lead, actions (HTML), meta (HTML), media (HTML), icon, variant.
 */

defined( 'ABSPATH' ) || exit;

$bzl = wp_parse_args(
	$args,
	array(
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'actions' => '',
		'meta'    => '',
		'media'   => '',
		'icon'    => '',
		'variant' => '',
		'ghost'   => '',
	)
);
// Grafika zastępcza (np. podstrona usługi bez zdjęcia): duża ikona w złotych okręgach.
if ( ! $bzl['media'] && ! empty( $args['art'] ) && $bzl['icon'] ) {
	$bzl['media'] = '<div class="hero-art" aria-hidden="true"><span class="hero-art__ring"></span><span class="hero-art__ring hero-art__ring--outer"></span>' . bzl_icon( $bzl['icon'] ) . '</div>';
	$bzl['icon']  = '';
}
$bzl_classes = 'page-hero tone-darker' . ( $bzl['variant'] ? ' page-hero--' . $bzl['variant'] : '' ) . ( $bzl['media'] ? ' page-hero--has-media' : '' );
?>
<header class="<?php echo esc_attr( $bzl_classes ); ?>">
	<div class="hero__lines" aria-hidden="true"></div>
	<?php if ( $bzl['ghost'] && bzl_mod( 'ghost_words' ) ) : ?>
		<span class="ghost-word ghost-word--hero" aria-hidden="true"><?php echo esc_html( $bzl['ghost'] ); ?></span>
	<?php endif; ?>
	<div class="container page-hero__grid">
		<div class="page-hero__content">
			<?php bzl_breadcrumbs(); ?>
			<?php if ( $bzl['icon'] ) : ?>
				<span class="page-hero__icon"><?php echo bzl_icon( $bzl['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<?php endif; ?>
			<?php if ( $bzl['eyebrow'] ) : ?>
				<p class="eyebrow eyebrow--line"><?php echo esc_html( $bzl['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="page-hero__title"><?php echo bzl_rich_title( $bzl['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
			<?php if ( $bzl['lead'] ) : ?>
				<p class="page-hero__lead"><?php echo esc_html( $bzl['lead'] ); ?></p>
			<?php endif; ?>
			<?php if ( $bzl['meta'] ) : ?>
				<div class="page-hero__meta"><?php echo $bzl['meta']; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
			<?php if ( $bzl['actions'] ) : ?>
				<div class="btn-row"><?php echo $bzl['actions']; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
		</div>
		<?php if ( $bzl['media'] ) : ?>
			<div class="page-hero__media"><?php echo $bzl['media']; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<?php endif; ?>
	</div>
</header>
