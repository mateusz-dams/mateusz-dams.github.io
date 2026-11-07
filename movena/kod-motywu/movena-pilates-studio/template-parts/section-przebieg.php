<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$img = mvn_pic(mvs($s, 'image'), (string) mvs($s, 'image_alt'), 900, 1125, [], 'large');
?>
<!-- PRZEBIEG -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php if ($img !== '') : ?>
    <div class="about-grid">
      <div class="about-media"><?php echo $img; ?></div>
      <div>
        <?php echo mvn_head($s, false, 'intro'); ?>
        <div class="steps steps--stack"><?php $n = 0; foreach (mvsa($s, 'steps') as $row) : if (empty($row['title'])) { continue; } $n++; ?>
          <div class="step"><span class="step-num"><?php echo (int) $n; ?></span><div><h3><?php echo esc_html(mvn_tokens((string) $row['title'])); ?></h3><p><?php echo esc_html(mvn_tokens((string) ($row['text'] ?? ''))); ?></p></div></div>
        <?php endforeach; ?></div>
      </div>
    </div>
    <?php else : ?>
    <?php echo mvn_head($s, true, 'intro'); ?>
    <div class="steps"><?php $n = 0; foreach (mvsa($s, 'steps') as $row) : if (empty($row['title'])) { continue; } $n++; ?>
      <div class="step"><span class="step-num"><?php echo (int) $n; ?></span><div><h3><?php echo esc_html(mvn_tokens((string) $row['title'])); ?></h3><p><?php echo esc_html(mvn_tokens((string) ($row['text'] ?? ''))); ?></p></div></div>
    <?php endforeach; ?></div>
    <?php endif; ?>
  </div>
</section>
