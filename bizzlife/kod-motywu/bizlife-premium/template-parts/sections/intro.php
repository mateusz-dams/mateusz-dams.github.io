<?php
/**
 * Sekcja: Wprowadzenie (komu pomagamy).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_items = bzl_slots( 'intro' );
?>
<section class="section intro <?php echo esc_attr( bzl_tone( 'intro', 'light' ) ); ?>" id="wprowadzenie" aria-labelledby="intro-title">
	<div class="container split split--intro">
		<?php
		bzl_section_header(
			array(
				'eyebrow' => bzl_mod( 'intro_eyebrow' ),
				'title'   => bzl_mod( 'intro_title' ),
				'lead'    => bzl_mod( 'intro_lead' ),
				'id'      => 'intro-title',
			)
		);
		?>
		<?php if ( $bzl_items ) : ?>
			<ul class="feature-grid">
				<?php foreach ( $bzl_items as $bzl_i => $bzl_item ) : ?>
					<li class="feature" data-reveal style="--d:<?php echo (int) $bzl_i; ?>">
						<?php if ( $bzl_item['icon'] ) : ?>
							<span class="feature__icon"><?php echo bzl_icon( $bzl_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>
						<h3 class="feature__title"><?php echo esc_html( $bzl_item['title'] ); ?></h3>
						<?php if ( $bzl_item['text'] ) : ?>
							<p class="feature__text"><?php echo esc_html( $bzl_item['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
