<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- KO COACH -->
<section<?php echo pmbs_sec_attrs($s, 'section ko clip'); ?>>
  <?php echo pmbs_ghost($s, 'KO', 'ghost ko-ghost'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <div class="ko-grid">
      <figure class="ko-photo" data-reveal>
        <?php echo pmbs_img(pms($s, 'photo'), 'ko-coach.webp', (string) pms($s, 'photo_alt'), 1000, 1250); ?>
      </figure>
      <div class="ko-body">
        <ul class="ko-nums" data-reveal>
          <?php foreach (pmsa($s, 'nums') as $n) : if (!isset($n['num']) || $n['num'] === '') { continue; } ?>
          <li><span class="n"><?php echo esc_html((string) $n['num']); ?></span><span><?php echo esc_html((string) ($n['label'] ?? '')); ?></span></li>
          <?php endforeach; ?>
        </ul>
        <div class="ko-cols" data-reveal>
          <div class="kocard"><h3><?php echo esc_html((string) pms($s, 'measures_title')); ?></h3><ul class="ticks ko-ticks"><?php foreach (pmbs_lines(pms($s, 'measures', [])) as $x) : ?><li><?php echo esc_html($x); ?></li><?php endforeach; ?></ul></div>
          <div class="kocard"><h3><?php echo esc_html((string) pms($s, 'uses_title')); ?></h3><ul class="ticks ko-ticks"><?php foreach (pmbs_lines(pms($s, 'uses', [])) as $x) : ?><li><?php echo esc_html($x); ?></li><?php endforeach; ?></ul></div>
        </div>
        <p class="ko-app" data-reveal><?php echo pmbs_rich(pms($s, 'app')); ?></p>
        <p class="ko-note" data-reveal><?php echo pmbs_rich(pms($s, 'note')); ?></p>
      </div>
    </div>
  </div>
</section>
