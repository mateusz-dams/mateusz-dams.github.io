<?php
/**
 * Sekcja: Jak wygląda współpraca (oś procesu).
 *
 * @package Bizlife
 *
 * @var array $args compact – wersja skrócona na podstronach.
 */

defined( 'ABSPATH' ) || exit;

$bzl_steps   = bzl_slots( 'process' );
$bzl_compact = ! empty( $args['compact'] );
if ( ! $bzl_steps ) {
	return;
}
$bzl_tone = $bzl_compact ? 'tone-dark' : bzl_tone( 'process', 'dark' );
?>
<section class="section process <?php echo esc_attr( $bzl_tone ); ?><?php echo $bzl_compact ? ' process--compact' : ''; ?>" id="wspolpraca" aria-labelledby="process-title">
	<div class="container">
		<?php
		bzl_section_header(
			array(
				'eyebrow' => bzl_mod( 'process_eyebrow' ),
				'title'   => bzl_mod( 'process_title' ),
				'lead'    => $bzl_compact ? '' : bzl_mod( 'process_lead' ),
				'id'      => 'process-title',
				'ghost'   => $bzl_compact ? '' : __( 'Proces', 'bizlife' ),
			)
		);
		?>
		<ol class="timeline" style="--steps:<?php echo (int) count( $bzl_steps ); ?>">
			<?php foreach ( $bzl_steps as $bzl_i => $bzl_step ) : ?>
				<li class="timeline__step" data-reveal style="--d:<?php echo (int) $bzl_i; ?>">
					<span class="timeline__marker" aria-hidden="true"><?php echo (int) ( $bzl_i + 1 ); ?></span>
					<h3 class="timeline__title"><span class="screen-reader-text"><?php /* translators: %d: numer. */ printf( esc_html__( 'Krok %d:', 'bizlife' ), (int) ( $bzl_i + 1 ) ); ?> </span><?php echo esc_html( $bzl_step['title'] ); ?></h3>
					<?php if ( $bzl_step['text'] ) : ?>
						<p class="timeline__text"><?php echo esc_html( $bzl_step['text'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
		<?php if ( ! $bzl_compact ) : ?>
			<div class="process__cta" data-reveal>
				<p><?php esc_html_e( 'Pierwszy krok zajmuje kilka minut.', 'bizlife' ); ?></p>
				<?php echo bzl_button( bzl_mod( 'hero_cta1_label' ), bzl_mod( 'hero_cta1_url' ), 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		<?php endif; ?>
	</div>
</section>
