<?php
/**
 * Szablon zapasowy (np. wpisy, archiwa). Strona jest jednostronicowa, więc wyświetlamy prostą listę.
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="legal-page">
  <div class="container container--narrow">
    <article class="legal">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
        <?php the_excerpt(); ?>
      <?php endwhile; else : ?>
        <h1>Brak treści</h1>
        <p><a class="link-gold" href="<?php echo esc_url(home_url('/')); ?>">Wróć na stronę główną</a></p>
      <?php endif; ?>
    </article>
  </div>
</main>
<?php get_footer(); ?>
