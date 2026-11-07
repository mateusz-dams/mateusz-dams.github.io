<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- FORMY TRENINGU -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php echo mvn_head($s, true); ?>
    <div class="formats-grid">
      <?php foreach (mvsa($s, 'cards') as $card) : if (empty($card['title'])) { continue; } $feat = !empty($card['featured']); ?>
      <div class="format-card<?php echo $feat ? ' is-featured' : ''; ?>">
        <h3><?php echo esc_html((string) $card['title']); ?></h3>
        <ul class="format-list">
          <?php foreach (mvn_lines((string) ($card['bullets'] ?? '')) as $line) : ?><li><?php echo esc_html(mvn_tokens($line)); ?></li><?php endforeach; ?>
        </ul>
        <?php echo mvn_btn((string) ($card['btn'] ?? ''), $card['cel'] ?? '#pricing', 'btn btn-secondary btn-block' . ($feat ? ' btn-on-dark' : ''), [], 'data-track="select_pass"'); ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
