<?php
/**
 * Sekcja: Hero.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_img    = bzl_image(
	(int) bzl_mod( 'hero_image' ),
	'bzl-hero',
	array(
		'class'         => 'hero__img',
		'loading'       => 'eager',
		'fetchpriority' => 'high',
		'decoding'      => 'async',
		'sizes'         => '(min-width: 1024px) 44vw, 100vw',
	)
);
$bzl_points = bzl_slots( 'hero_point' );
?>
<section class="hero tone-darker" aria-labelledby="hero-title">
	<div class="hero__lines" aria-hidden="true"></div>
	<div class="container hero__grid">
		<div class="hero__content">
			<?php if ( bzl_mod( 'hero_eyebrow' ) ) : ?>
				<p class="eyebrow eyebrow--line"><?php echo esc_html( bzl_mod( 'hero_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h1 class="hero__title" id="hero-title"><?php echo bzl_rich_title( bzl_mod( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
			<?php if ( bzl_mod( 'hero_lead' ) ) : ?>
				<p class="hero__lead"><?php echo esc_html( bzl_mod( 'hero_lead' ) ); ?></p>
			<?php endif; ?>
			<div class="btn-row">
				<?php
				echo bzl_button( bzl_mod( 'hero_cta1_label' ), bzl_mod( 'hero_cta1_url' ), 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo bzl_button( bzl_mod( 'hero_cta2_label' ), bzl_mod( 'hero_cta2_url' ), 'ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</div>
			<?php if ( $bzl_points ) : ?>
				<ul class="hero__points">
					<?php foreach ( $bzl_points as $bzl_p ) : ?>
						<li><?php echo bzl_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $bzl_p['text'] ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="hero__media">
			<div class="hero__frame">
				<?php echo $bzl_img ? $bzl_img : bzl_image_placeholder( __( 'Dodaj zdjęcie: Dostosuj → Bizzlife → Hero', 'bizlife' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( bzl_mod( 'hero_image_caption' ) ) : ?>
					<p class="hero__caption"><?php echo esc_html( bzl_mod( 'hero_image_caption' ) ); ?></p>
				<?php endif; ?>
			</div>
			<div class="hero__card">
				<p class="hero__card-label"><?php esc_html_e( 'Biuro', 'bizlife' ); ?></p>
				<?php $bzl_a = bzl_address(); ?>
				<p class="hero__card-value"><?php echo esc_html( $bzl_a['street'] ); ?>, <?php echo esc_html( $bzl_a['city'] ); ?></p>
				<?php if ( bzl_mod( 'hours' ) ) : ?>
					<p class="hero__card-meta"><?php echo bzl_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( strtok( (string) bzl_mod( 'hours' ), "\n" ) ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
