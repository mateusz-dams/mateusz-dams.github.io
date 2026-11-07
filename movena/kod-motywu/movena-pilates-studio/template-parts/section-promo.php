<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
if (mvn_first_price() <= 0 && strpos((string) mvs($s, 'text'), '{cena') !== false) {
    return;
}
?>
<section<?php echo mvn_sec_attrs($s, 'promo-banner'); ?>>
  <div class="container promo-banner-inner">
    <p class="promo-banner-text"><?php echo mvn_rich((string) mvs($s, 'text')); ?></p>
    <?php echo mvn_btn((string) mvs($s, 'btn'), mvs($s, 'btn_cel', '#pricing'), 'btn btn-primary btn-sm'); ?>
  </div>
</section>
