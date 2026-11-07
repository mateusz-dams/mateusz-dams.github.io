<?php
/**
 * Komentarze.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments prose">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments__title">
			<?php
			/* translators: %d: liczba komentarzy. */
			printf( esc_html( _n( '%d komentarz', '%d komentarze', get_comments_number(), 'bizlife' ) ), (int) get_comments_number() );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php
	if ( comments_open() ) {
		comment_form( array( 'class_submit' => 'btn btn--dark' ) );
	}
	?>
</section>
