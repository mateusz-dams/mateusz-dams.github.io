<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$cel = (string) mvs($s, 'btn_cel', 'fitssey:vouchers');
$items = array_values(array_filter(mvsa($s, 'items'), static fn($v) => !empty($v['price'])));
?>
<!-- VOUCHERY -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <div class="voucher-box">
      <div class="voucher-head">
        <?php if (mvs($s, 'eyebrow') !== '') : ?><p class="eyebrow"><?php echo esc_html((string) mvs($s, 'eyebrow')); ?></p><?php endif; ?>
        <h2><?php echo mvn_rich((string) mvs($s, 'title')); ?></h2>
        <?php if (mvs($s, 'lead') !== '') : ?><p class="lead"><?php echo mvn_rich((string) mvs($s, 'lead')); ?></p><?php endif; ?>
      </div>
      <?php if ($items) : ?>
      <div class="voucher-grid">
        <?php foreach ($items as $v) : $n = (int) ($v['sessions'] ?? 0); $name = 'Voucher · ' . $n . ' ' . ($n === 1 ? 'wejście' : (($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 12 || $n % 100 > 14)) ? 'wejścia' : 'wejść')); ?>
        <a class="voucher-item" href="<?php echo esc_url(mvn_href($cel, '#')); ?>" target="_blank" rel="noopener" data-track="click_fitssey" data-offer-type="voucher" data-pass-name="<?php echo esc_attr($name); ?>" data-value="<?php echo esc_attr((string) $v['price']); ?>" data-sessions="<?php echo (int) $n; ?>">
          <?php echo mvn_pack_img(mvn_pack_slug($v, true), $name); ?>
          <span class="voucher-cap"><?php echo esc_html($name); ?> <strong><?php echo esc_html(mvn_zl($v['price'])); ?></strong></span>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="voucher-cta"><?php echo mvn_btn((string) mvs($s, 'btn'), $cel, 'btn btn-primary', ['offer' => 'voucher', 'name' => 'Voucher']); ?></div>
    </div>
  </div>
</section>
