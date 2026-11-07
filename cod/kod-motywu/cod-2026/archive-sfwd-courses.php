<?php
/**
 * Lista szkoleń LearnDash.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'title' => __( 'Szkolenia', 'cod2026' ),
		'lead'  => __( 'Kursy online do pracy we własnym tempie. Kupione szkolenia znajdziesz po zalogowaniu na swoje konto.', 'cod2026' ),
	)
);
$cod_user = get_current_user_id();
?>
<div class="section section--tight">
	<div class="wrap">
		<?php do_action( 'cod_before_learning_content' ); ?>
		<?php if ( have_posts() ) : ?>
			<div class="course-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$cod_access = $cod_user && function_exists( 'sfwd_lms_has_access' ) && sfwd_lms_has_access( get_the_ID(), $cod_user );
					$cod_prog   = ( $cod_access && function_exists( 'learndash_course_progress' ) ) ? learndash_course_progress( array( 'user_id' => $cod_user, 'course_id' => get_the_ID(), 'array' => true ) ) : null;
					?>
					<article class="course-card spot">
						<a class="course-card__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'cod-wide', array( 'alt' => '', 'sizes' => '(max-width: 760px) 92vw, 560px' ) );
							} else {
								echo '<span class="post-card__ph"></span>';
							}
							?>
						</a>
						<div class="course-card__body">
							<?php if ( $cod_access ) : ?>
								<p class="course-card__badge"><?php echo cod_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Masz dostęp', 'cod2026' ); ?></p>
							<?php endif; ?>
							<h2 class="course-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php if ( has_excerpt() ) : ?>
								<p><?php echo esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>
							<?php if ( is_array( $cod_prog ) && isset( $cod_prog['percentage'] ) ) : ?>
								<div class="progress-line" role="progressbar" aria-valuenow="<?php echo (int) $cod_prog['percentage']; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php esc_attr_e( 'Postęp szkolenia', 'cod2026' ); ?>"><span style="width:<?php echo (int) $cod_prog['percentage']; ?>%"></span></div>
								<p class="fine">
									<?php
									/* translators: %d: procent. */
									echo esc_html( sprintf( __( 'Ukończono %d%%', 'cod2026' ), (int) $cod_prog['percentage'] ) );
									?>
								</p>
							<?php endif; ?>
							<a class="btn btn--primary btn--sm" href="<?php the_permalink(); ?>" data-track="cod_course_open" data-track-location="lista-szkolen"><span><?php echo $cod_access ? esc_html__( 'Kontynuuj szkolenie', 'cod2026' ) : esc_html__( 'Zobacz szkolenie', 'cod2026' ); ?></span></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p class="lede"><?php esc_html_e( 'Szkolenia pojawią się tutaj wkrótce.', 'cod2026' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
