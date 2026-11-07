<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- DLACZEGO KAMERALNA GRUPA -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <div class="why-small-group">
      <?php echo mvn_head($s); ?>
      <div class="why-list">
        <?php $n = 0; foreach (mvsa($s, 'items') as $row) : if (empty($row['title'])) { continue; } $n++; ?>
        <div class="why-item">
          <span class="why-item-num"><?php echo esc_html(sprintf('%02d', $n)); ?></span>
          <div><h3><?php echo esc_html((string) $row['title']); ?></h3><p><?php echo esc_html((string) ($row['text'] ?? '')); ?></p></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (mvs($s, 'disclaimer') !== '') : ?><p class="why-disclaimer"><?php echo esc_html((string) mvs($s, 'disclaimer')); ?></p><?php endif; ?>
    </div>
  </div>
</section>
