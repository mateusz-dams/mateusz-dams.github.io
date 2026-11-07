<?php
/**
 * Sekcja: Kontakt (strona główna i strona Kontakt).
 *
 * @package Bizlife
 *
 * @var array $args heading_level, show_header, preselect.
 */

defined( 'ABSPATH' ) || exit;

$bzl_show_header = $args['show_header'] ?? true;
?>
<section class="section contact tone-dark" id="kontakt" aria-labelledby="contact-title">
	<div class="container">
		<?php
		if ( $bzl_show_header ) {
			bzl_section_header(
				array(
					'eyebrow' => bzl_mod( 'contact_eyebrow' ),
					'title'   => bzl_mod( 'contact_title' ),
					'lead'    => bzl_mod( 'contact_lead' ),
					'id'      => 'contact-title',
					'ghost'   => __( 'Kontakt', 'bizlife' ),
				)
			);
		} else {
			echo '<h2 class="screen-reader-text" id="contact-title">' . esc_html( bzl_mod( 'form_title' ) ) . '</h2>';
		}
		?>
		<div class="contact__grid">
			<?php get_template_part( 'template-parts/components/contact-info' ); ?>
			<div class="contact__form panel" data-reveal>
				<h3 class="panel__title"><?php echo esc_html( bzl_mod( 'form_title' ) ); ?></h3>
				<?php if ( bzl_mod( 'form_intro' ) ) : ?>
					<p class="panel__intro"><?php echo esc_html( bzl_mod( 'form_intro' ) ); ?></p>
				<?php endif; ?>
				<?php bzl_contact_form( array( 'preselect' => $args['preselect'] ?? '' ) ); ?>
			</div>
		</div>
		<?php get_template_part( 'template-parts/components/map' ); ?>
	</div>
</section>
