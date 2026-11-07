<?php
/**
 * Podstrony (polityka prywatności, cookies, regulamin i inne): treść z edytora WordPress.
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="legal-page">
  <div class="wrap">
    <?php while (have_posts()) : the_post(); ?>
    <nav class="crumbs" aria-label="Ścieżka"><a href="<?php echo esc_url(home_url('/')); ?>">Strona główna</a><span aria-hidden="true">/</span><span><?php the_title(); ?></span></nav>
    <article class="legal">
      <h1><?php the_title(); ?></h1>
      <?php the_content(); ?>
    </article>
    <?php endwhile; ?>
  </div>
</main>
<?php get_footer(); ?>
