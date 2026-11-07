<?php
/**
 * Strona 404.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="error-page tone-darker" aria-labelledby="error-title">
	<div class="hero__lines" aria-hidden="true"></div>
	<div class="container error-page__inner">
		<p class="error-page__code" aria-hidden="true">404</p>
		<h1 class="error-page__title" id="error-title"><?php esc_html_e( 'Nie znaleźliśmy tej strony', 'bizlife' ); ?></h1>
		<p class="error-page__lead"><?php esc_html_e( 'Adres mógł się zmienić albo strona została usunięta. Skorzystaj z wyszukiwarki lub przejdź do jednej z sekcji poniżej.', 'bizlife' ); ?></p>
		<?php get_search_form(); ?>
		<div class="btn-row">
			<?php
			echo bzl_button( __( 'Strona główna', 'bizlife' ), home_url( '/' ), 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo bzl_button( __( 'Kontakt', 'bizlife' ), '{kontakt}', 'ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput
			?>
		</div>
		<?php $bzl_q = bzl_services_query( 8 ); ?>
		<?php if ( $bzl_q->have_posts() ) : ?>
			<nav class="error-page__links" aria-labelledby="error-services">
				<h2 class="error-page__subtitle" id="error-services"><?php esc_html_e( 'Nasze usługi', 'bizlife' ); ?></h2>
				<ul>
					<?php foreach ( $bzl_q->posts as $bzl_s ) : ?>
						<li><a class="pill" href="<?php echo esc_url( get_permalink( $bzl_s ) ); ?>"><?php echo esc_html( get_the_title( $bzl_s ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
