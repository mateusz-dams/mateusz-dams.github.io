<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$hid = ($s['anchor'] ?? 'final') . '-h';
?>
<!-- SEKCJA KOŃCOWA -->
<section<?php echo pmbs_sec_attrs($s, 'final-wrap'); ?> aria-labelledby="<?php echo esc_attr($hid); ?>">
  <div class="wrap">
    <div class="final">
      <h2 id="<?php echo esc_attr($hid); ?>"><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p><?php echo esc_html((string) pms($s, 'text')); ?></p>
      <div class="cta-row">
        <?php if (pms($s, 'btn1') !== '') : ?><a class="btn btn-dark btn-lg" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr(pmbs_form_default()); ?>"><?php echo esc_html((string) pms($s, 'btn1')); ?></a><?php endif; ?>
        <?php if (pms($s, 'btn2') !== '') : ?><a class="btn btn-dark-outline btn-lg" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form=""><?php echo esc_html((string) pms($s, 'btn2')); ?></a><?php endif; ?>
        <a class="btn btn-dark-outline btn-lg" href="tel:<?php echo esc_attr((string) pmo('phone_tel')); ?>"><?php echo pmbs_icon('phone'); ?>Zadzwoń: <?php echo esc_html((string) pmo('phone_display')); ?></a>
      </div>
    </div>
  </div>
</section>
