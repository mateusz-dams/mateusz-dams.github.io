<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- SALA I WYPOSAŻENIE -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Sala'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <div class="sala-photos">
      <figure data-reveal><?php echo pmbs_img(pms($s, 'photo1'), 'sala-panorama.webp', (string) pms($s, 'photo1_alt'), 1942, 809); ?></figure>
      <figure data-reveal><?php echo pmbs_img(pms($s, 'photo2'), 'ring.webp', (string) pms($s, 'photo2_alt'), 1448, 1086); ?></figure>
    </div>
    <ul class="gear">
      <?php $i = 0; foreach (pmsa($s, 'gear') as $g) : if (empty($g['title'])) { continue; } $i++; ?>
      <li data-reveal><span class="num"><?php echo esc_html(sprintf('%02d', $i)); ?></span><h3><?php echo esc_html($g['title']); ?></h3><p><?php echo esc_html((string) ($g['text'] ?? '')); ?></p></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
