<?php
/**
 * Strona 404.
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="notfound">
  <div class="container container--narrow">
    <p class="nf-code" aria-hidden="true">404</p>
    <h1>Tej strony nie ma</h1>
    <p class="prose">Adres mógł się zmienić albo został wpisany z błędem. Wróć na stronę główną albo zadzwoń, a pomożemy.</p>
    <div class="cta-row">
      <a class="btn btn-primary btn-lg" href="<?php echo esc_url(home_url('/')); ?>">Wróć na stronę główną</a>
      <a class="btn btn-ghost btn-lg" href="tel:<?php echo esc_attr((string) mvo('phone_tel')); ?>"><?php echo mvn_icon('phone'); ?>Zadzwoń: <?php echo esc_html((string) mvo('phone_display')); ?></a>
    </div>
  </div>
</main>
<?php get_footer(); ?>
