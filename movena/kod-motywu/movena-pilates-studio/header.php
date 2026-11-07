<?php
/**
 * Nagłówek: pełne menu na stronie głównej, uproszczony (bez menu) na stronie docelowej i podstronach.
 */
if (!defined('ABSPATH')) {
    exit;
}
$phone_tel = (string) mvo('phone_tel');
$phone     = (string) mvo('phone_display');
$brand     = (string) mvo('brand');
$logo      = mvn_logo_url();
$home      = esc_url(home_url('/'));
$cta       = (string) mvo('header_cta');
$ctaCel    = (string) mvo('header_cta_cel') ?: 'fitssey:schedule';
$menu      = mvn_menu_items();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(mvn_is_landing() ? 'landing' : (mvn_is_site_page() ? '' : 'legal-body')); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Przejdź do treści</a>

<header class="site-header">
  <div class="container">
    <a href="<?php echo $home; ?>" class="brand" aria-label="<?php echo esc_attr($brand); ?> — strona główna"><img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($brand); ?>" class="brand-full-logo" width="160" height="58"></a>

    <?php if ($menu) : ?>
    <nav class="nav-desktop" aria-label="Nawigacja główna">
      <ul>
        <?php foreach ($menu as $item) : ?><li><a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a></li><?php endforeach; ?>
      </ul>
    </nav>
    <?php endif; ?>

    <a class="btn btn-ghost btn-sm header-phone" href="tel:<?php echo esc_attr($phone_tel); ?>" data-track="click_phone"><?php echo mvn_icon('phone'); ?><span><?php echo esc_html($phone); ?></span></a>
    <?php echo mvn_btn($cta, $ctaCel, 'btn btn-primary btn-sm header-cta'); ?>

    <?php if ($menu) : ?>
    <button class="nav-toggle" type="button" aria-label="Otwórz menu" aria-expanded="false" aria-controls="nav-mobile"><?php echo mvn_icon('menu'); ?></button>
    <?php endif; ?>
  </div>
</header>

<?php if ($menu) : ?>
<nav class="nav-mobile" id="nav-mobile" aria-label="Nawigacja mobilna">
  <ul>
    <?php foreach ($menu as $item) : ?><li><a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a></li><?php endforeach; ?>
  </ul>
  <?php echo mvn_btn($cta, $ctaCel, 'btn btn-primary btn-block'); ?>
  <p class="nav-mobile-contact"><?php echo esc_html((string) mvo('header_call')); ?>? <a href="tel:<?php echo esc_attr($phone_tel); ?>" data-track="click_phone"><?php echo esc_html($phone); ?></a></p>
</nav>
<?php endif; ?>
