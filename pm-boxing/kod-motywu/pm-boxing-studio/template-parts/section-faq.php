<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- FAQ -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'FAQ'); ?><div class="wrap">
    <div class="head" data-reveal><h2><?php echo esc_html((string) pms($s, 'title')); ?></h2></div>
    <div class="faq" data-reveal>
      <?php foreach (pmsa($s, 'items') as $f) : if (empty($f['q'])) { continue; } ?>
      <details class="faq-item"><summary><?php echo esc_html($f['q']); ?></summary><div class="faq-a"><p><?php echo esc_html((string) ($f['a'] ?? '')); ?></p></div></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
