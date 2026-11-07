<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- PROCES -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Proces'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <ol class="steps">
      <?php $i = 0; foreach (pmsa($s, 'steps') as $x) : if (empty($x['title'])) { continue; } $i++; ?>
      <li class="step" data-reveal><span class="hex" aria-hidden="true"><span><?php echo (int) $i; ?></span></span><h3><?php echo esc_html($x['title']); ?></h3><p><?php echo esc_html((string) ($x['text'] ?? '')); ?></p></li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
