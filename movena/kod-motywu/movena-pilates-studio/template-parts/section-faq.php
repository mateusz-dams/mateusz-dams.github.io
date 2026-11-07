<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$uid = esc_attr((string) ($s['anchor'] ?? 'faq'));
?>
<!-- FAQ -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container container--narrow">
    <?php echo mvn_head($s, true); ?>
    <div class="faq-list">
      <?php $i = 0; foreach (mvsa($s, 'items') as $row) : if (empty($row['q'])) { continue; } $i++; ?>
      <div class="faq-item">
        <button type="button" class="faq-q" aria-expanded="false" aria-controls="<?php echo $uid; ?>-a-<?php echo (int) $i; ?>"><span><?php echo esc_html(mvn_tokens((string) $row['q'])); ?></span><?php echo mvn_icon('plus'); ?></button>
        <div class="faq-a" id="<?php echo $uid; ?>-a-<?php echo (int) $i; ?>"><div class="faq-a-inner"><?php echo mvn_rich((string) ($row['a'] ?? '')); ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
