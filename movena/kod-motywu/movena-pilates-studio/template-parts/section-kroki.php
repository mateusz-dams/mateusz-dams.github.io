<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- JAK ZACZĄĆ -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php echo mvn_head($s, true); ?>
    <div class="steps">
      <?php $n = 0; foreach (mvsa($s, 'steps') as $row) : if (empty($row['title'])) { continue; } $n++; ?>
      <div class="step">
        <span class="step-num"><?php echo (int) $n; ?></span>
        <div>
          <h3><?php echo esc_html(mvn_tokens((string) $row['title'])); ?></h3>
          <p><?php echo esc_html(mvn_tokens((string) ($row['text'] ?? ''))); ?><?php if (!empty($row['link']) && !empty($row['cel'])) : ?> <a class="step-link" href="<?php echo esc_url(mvn_anchor(mvn_href($row['cel']))); ?>"<?php echo mvn_track_attrs($row['cel']); ?>><?php echo esc_html((string) $row['link']); ?></a><?php endif; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
