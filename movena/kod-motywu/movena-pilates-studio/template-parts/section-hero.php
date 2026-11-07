<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- HERO -->
<section<?php echo mvn_sec_attrs($s, 'hero', 'top'); ?>>
  <div class="container">
    <div class="hero-copy">
      <?php if (mvs($s, 'eyebrow') !== '') : ?><p class="eyebrow"><?php echo mvn_rich((string) mvs($s, 'eyebrow')); ?></p><?php endif; ?>
      <h1><?php echo mvn_rich((string) mvs($s, 'title')); ?></h1>
      <p class="lead"><?php echo mvn_rich((string) mvs($s, 'lead')); ?></p>
      <?php echo mvn_icon_list(mvsa($s, 'facts'), 'hero-facts'); ?>
      <div class="cta-row">
        <?php echo mvn_btn((string) mvs($s, 'cta_primary'), mvs($s, 'cta_primary_cel', 'fitssey:schedule'), 'btn btn-primary', ['offer' => 'group']); ?>
        <?php echo mvn_btn((string) mvs($s, 'cta_secondary'), mvs($s, 'cta_secondary_cel', '#pricing'), 'btn btn-secondary btn-on-dark'); ?>
      </div>
    </div>
    <div class="hero-media"><?php echo mvn_pic(mvs($s, 'image'), (string) mvs($s, 'image_alt'), 1000, 1230, ['loading' => 'eager', 'fetchpriority' => 'high'], 'large'); ?></div>
  </div>
</section>
