<?php
/**
 * Układ kursu, lekcji, zagadnienia i testu LearnDash.
 * Treść (spis lekcji, postęp, przyciski „Oznacz jako ukończone”) generuje LearnDash – motyw ją tylko stylizuje.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$cod_type   = get_post_type();
	$cod_course = 0;
	if ( 'sfwd-courses' !== $cod_type && function_exists( 'learndash_get_course_id' ) ) {
		$cod_course = (int) learndash_get_course_id( get_the_ID() );
	}
	$cod_labels = array(
		'sfwd-courses' => __( 'Szkolenie', 'cod2026' ),
		'sfwd-lessons' => __( 'Lekcja', 'cod2026' ),
		'sfwd-topic'   => __( 'Zagadnienie', 'cod2026' ),
		'sfwd-quiz'    => __( 'Test', 'cod2026' ),
	);
	?>
	<header class="learn-hero<?php echo 'sfwd-courses' === $cod_type ? ' learn-hero--course on-dark' : ''; ?>">
		<?php if ( 'sfwd-courses' === $cod_type ) : ?>
			<div class="aurora" aria-hidden="true"><span class="a1"></span><span class="a2"></span></div>
		<?php endif; ?>
		<div class="wrap">
			<?php if ( $cod_course ) : ?>
				<p class="learn-hero__back"><a href="<?php echo esc_url( get_permalink( $cod_course ) ); ?>"><?php echo cod_icon( 'left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( get_the_title( $cod_course ) ); ?></a></p>
			<?php else : ?>
				<?php cod_breadcrumbs(); ?>
			<?php endif; ?>
			<p class="num-tag"><?php echo esc_html( isset( $cod_labels[ $cod_type ] ) ? $cod_labels[ $cod_type ] : '' ); ?></p>
			<h1 class="learn-hero__title"><?php the_title(); ?></h1>
		</div>
	</header>
	<?php $cod_status = 'sfwd-courses' === $cod_type ? cod_course_status( get_the_ID() ) : null; ?>
	<div class="section section--tight learn-body">
		<div class="wrap learn-wrap">
			<?php do_action( 'cod_before_learning_content' ); ?>
			<?php if ( $cod_status ) : ?>
				<div class="learn-status">
					<div class="learn-status__info">
						<p class="learn-status__label"><?php esc_html_e( 'Twój postęp', 'cod2026' ); ?></p>
						<div class="progress-line progress-line--lg" role="progressbar" aria-valuenow="<?php echo (int) $cod_status['percentage']; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php esc_attr_e( 'Postęp szkolenia', 'cod2026' ); ?>"><span style="width:<?php echo (int) $cod_status['percentage']; ?>%"></span></div>
						<p class="learn-status__meta">
							<strong><?php echo (int) $cod_status['percentage']; ?>%</strong>
							<?php if ( $cod_status['total'] ) : ?>
								<?php
								/* translators: 1: ukończone kroki, 2: wszystkie kroki. */
								echo esc_html( sprintf( __( '· ukończono %1$d z %2$d kroków', 'cod2026' ), $cod_status['completed'], $cod_status['total'] ) );
								?>
							<?php endif; ?>
						</p>
					</div>
					<?php if ( $cod_status['next_url'] ) : ?>
						<?php
						$cod_label = 100 === $cod_status['percentage'] ? __( 'Przejrzyj szkolenie od początku', 'cod2026' ) : ( $cod_status['started'] ? __( 'Kontynuuj naukę', 'cod2026' ) : __( 'Rozpocznij szkolenie', 'cod2026' ) );
						echo cod_button( $cod_status['next_url'], 'primary', array( 'label' => $cod_label, 'location' => 'kurs', 'icon' => '', 'class' => 'learn-status__go' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="prose entry-content learn-content<?php echo $cod_status ? ' learn-content--has-status' : ''; ?>">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
	<?php
endwhile;

get_footer();
