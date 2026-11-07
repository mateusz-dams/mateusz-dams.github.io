<?php
/**
 * Komentarze (gdy są włączone dla wpisu).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<section class="comments" id="komentarze" aria-labelledby="h-comments">
	<?php if ( have_comments() ) : ?>
		<h2 id="h-comments" class="fx-title fx-title--sm">
			<?php
			/* translators: %d: liczba komentarzy. */
			echo esc_html( sprintf( _n( '%d komentarz', '%d komentarze', get_comments_number(), 'cod2026' ), get_comments_number() ) );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php else : ?>
		<h2 id="h-comments" class="sr-only"><?php esc_html_e( 'Komentarze', 'cod2026' ); ?></h2>
	<?php endif; ?>
	<?php
	comment_form(
		array(
			'class_submit'  => 'btn btn--primary',
			'title_reply'   => __( 'Zostaw komentarz', 'cod2026' ),
			'label_submit'  => __( 'Dodaj komentarz', 'cod2026' ),
		)
	);
	?>
</section>
