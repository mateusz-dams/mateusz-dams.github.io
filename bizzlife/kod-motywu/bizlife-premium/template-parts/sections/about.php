<?php
/**
 * Sekcja: O nas (strona główna).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

$bzl_img = bzl_image(
	(int) bzl_mod( 'about_image' ),
	'bzl-portrait',
	array(
		'class' => 'about__img',
		'sizes' => '(min-width: 1024px) 40vw, 100vw',
	)
);
?>
<section class="section about <?php echo esc_attr( bzl_tone( 'about', 'light' ) ); ?>" id="o-nas" aria-labelledby="about-title">
	<div class="container about__grid">
		<figure class="about__media" data-reveal>
			<div class="about__frame">
				<?php echo $bzl_img ? $bzl_img : bzl_image_placeholder( __( 'Dodaj zdjęcie: Dostosuj → Bizzlife → O nas', 'bizlife' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<?php if ( bzl_mod( 'about_person_name' ) ) : ?>
				<figcaption class="about__person">
					<span class="about__name"><?php echo esc_html( bzl_mod( 'about_person_name' ) ); ?></span>
					<?php if ( bzl_mod( 'about_person_role' ) ) : ?>
						<span class="about__role"><?php echo esc_html( bzl_mod( 'about_person_role' ) ); ?></span>
					<?php endif; ?>
				</figcaption>
			<?php endif; ?>
		</figure>
		<div class="about__content">
			<?php
			bzl_section_header(
				array(
					'eyebrow' => bzl_mod( 'about_eyebrow' ),
					'title'   => bzl_mod( 'about_title' ),
					'id'      => 'about-title',
				)
			);
			?>
			<div class="about__text" data-reveal>
				<?php echo bzl_paragraphs( bzl_mod( 'about_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<?php if ( bzl_link_has_page( bzl_mod( 'about_cta_url' ) ) ) : ?>
				<div class="btn-row" data-reveal>
					<?php echo bzl_button( bzl_mod( 'about_cta_label' ), bzl_mod( 'about_cta_url' ), 'dark', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
