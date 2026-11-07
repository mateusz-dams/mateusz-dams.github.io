<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- SEKCJA KOŃCOWA -->
<section<?php echo mvn_sec_attrs($s, 'section section--dark final-cta'); ?>>
  <div class="container container--narrow">
    <?php if (mvs($s, 'eyebrow') !== '') : ?><p class="eyebrow"><?php echo esc_html((string) mvs($s, 'eyebrow')); ?></p><?php endif; ?>
    <h2><?php echo mvn_rich((string) mvs($s, 'title')); ?></h2>
    <?php if (mvs($s, 'text') !== '') : ?><p class="lead"><?php echo mvn_rich((string) mvs($s, 'text')); ?></p><?php endif; ?>
    <div class="cta-row">
      <?php echo mvn_btn((string) mvs($s, 'btn1'), mvs($s, 'btn1_cel', 'fitssey:schedule'), 'btn btn-primary', ['offer' => 'first', 'name' => 'Pierwszy trening', 'value' => (string) mvn_first_price()]); ?>
      <?php echo mvn_btn((string) mvs($s, 'btn2'), mvs($s, 'btn2_cel', '#pricing'), 'btn btn-secondary'); ?>
    </div>
  </div>
</section>
