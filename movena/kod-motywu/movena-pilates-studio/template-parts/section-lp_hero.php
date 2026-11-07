<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$price = mvn_first_price(); $was = mvn_first_was();
$offer = ['offer' => 'first', 'name' => 'Pierwszy trening', 'value' => (string) $price, 'sessions' => '1'];
?>
<!-- LANDING: HERO -->
<section<?php echo mvn_sec_attrs($s, 'hero lp-hero', 'top'); ?>>
  <div class="container">
    <div class="hero-copy">
      <?php if (mvs($s, 'eyebrow') !== '') : ?><p class="eyebrow"><?php echo mvn_rich((string) mvs($s, 'eyebrow')); ?></p><?php endif; ?>
      <h1><?php echo mvn_rich((string) mvs($s, 'title')); ?></h1>
      <?php if ($price > 0) : ?>
      <p class="lp-price-line"><span class="lp-price"><?php echo esc_html(mvn_zl($price)); ?></span><?php if ($was > $price) : ?> <span class="lp-was">zamiast <?php echo esc_html(mvn_zl($was)); ?></span><?php endif; ?><?php if (mvs($s, 'price_label') !== '') : ?> <span class="lp-label"><?php echo esc_html((string) mvs($s, 'price_label')); ?></span><?php endif; ?></p>
      <?php endif; ?>
      <p class="lead"><?php echo mvn_rich((string) mvs($s, 'lead')); ?></p>
      <?php echo mvn_icon_list(mvsa($s, 'facts'), 'hero-facts'); ?>
      <div class="cta-row">
        <?php echo mvn_btn((string) mvs($s, 'cta'), mvs($s, 'cta_cel', 'fitssey:schedule'), 'btn btn-primary', $offer); ?>
        <?php echo mvn_btn((string) mvs($s, 'cta2'), mvs($s, 'cta2_cel', '#kontakt'), 'btn btn-secondary btn-on-dark'); ?>
      </div>
      <?php if (mvs($s, 'note') !== '') : ?><p class="lp-note"><?php echo esc_html(mvn_tokens((string) mvs($s, 'note'))); ?></p><?php endif; ?>
    </div>
    <div class="hero-media"><?php echo mvn_pic(mvs($s, 'image'), (string) mvs($s, 'image_alt'), 1000, 1230, ['loading' => 'eager', 'fetchpriority' => 'high'], 'large'); ?></div>
  </div>
</section>
