<?php
/**
 * Nagłówek: pełne menu na stronie głównej, uproszczony na podstronach.
 */
if (!defined('ABSPATH')) {
    exit;
}
$phone_tel = (string) pmo('phone_tel');
$phone     = (string) pmo('phone_display');
$logo      = pmbs_logo_url();
$brand     = (string) pmo('brand');
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(pmbs_is_landing() ? 'landing' : (pmbs_is_site_page() ? '' : 'legal-body')); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main">Przejdź do treści</a>

<?php if (pmbs_is_landing()) : ?>
<header class="site-header scrolled lp-header">
  <div class="wrap bar">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($brand); ?> — strona główna"><img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($brand); ?> — logo" width="72" height="52"></a>
    <div class="head-actions" style="display:flex">
      <a class="btn btn-ghost btn-sm" href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo pmbs_icon('phone'); ?><span><?php echo esc_html($phone); ?></span></a>
      <a class="btn btn-primary btn-sm" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr(pmbs_form_default()); ?>"><?php echo esc_html((string) pmo('mobile_cta')); ?></a>
    </div>
  </div>
</header>
<?php elseif (is_front_page()) : ?>
<header class="site-header">
  <div class="wrap bar">
    <a class="brand" href="<?php echo esc_url(pmbs_top_href()); ?>" aria-label="<?php echo esc_attr($brand); ?> — strona główna"><img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($brand); ?> — logo" width="72" height="52"></a>
    <nav class="nav" id="nav" aria-label="Menu główne">
      <ul>
        <?php foreach (pmbs_menu_items() as $item) : ?>
        <li><a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a></li>
        <?php endforeach; ?>
      </ul>
      <div class="nav-actions-mobile">
        <a class="btn btn-ghost" href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo esc_html((string) pmo('header_call')); ?>: <?php echo esc_html($phone); ?></a>
        <a class="btn btn-primary" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form=""><?php echo esc_html((string) pmo('header_cta')); ?></a>
      </div>
    </nav>
    <div class="head-actions">
      <a class="btn btn-ghost btn-sm" href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo pmbs_icon('phone'); ?><span><?php echo esc_html($phone); ?></span></a>
      <a class="btn btn-primary btn-sm" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form=""><?php echo esc_html((string) pmo('header_cta')); ?></a>
    </div>
    <button class="burger" id="burger" aria-expanded="false" aria-controls="nav" aria-label="Otwórz menu"><span></span><span></span><span></span></button>
  </div>
</header>
<?php else : ?>
<header class="site-header scrolled">
  <div class="wrap bar">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($brand); ?> — strona główna"><img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($brand); ?> — logo" width="72" height="52"></a>
    <div class="head-actions" style="display:flex">
      <a class="btn btn-ghost btn-sm" href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo pmbs_icon('phone'); ?><span><?php echo esc_html($phone); ?></span></a>
      <a class="btn btn-primary btn-sm" href="<?php echo esc_url(home_url('/')); ?>">Strona główna</a>
    </div>
  </div>
</header>
<?php endif; ?>
