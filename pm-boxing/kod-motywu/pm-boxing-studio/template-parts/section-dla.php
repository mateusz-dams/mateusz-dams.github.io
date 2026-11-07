<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- DLA KOGO -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Dla kogo'); ?><div class="wrap">
    <div class="split">
      <div>
        <h2 data-reveal><?php echo esc_html((string) pms($s, 'title')); ?></h2>
        <p class="prose" data-reveal><?php echo esc_html((string) pms($s, 'intro')); ?></p>
        <ul class="audience" data-reveal>
          <?php foreach (pmbs_lines(pms($s, 'audience', [])) as $a) : ?><li><?php echo esc_html($a); ?></li><?php endforeach; ?>
        </ul>
        <p class="muted" data-reveal><?php echo esc_html((string) pms($s, 'muted')); ?></p>
      </div>
      <div class="concerns">
        <?php foreach (pmsa($s, 'concerns') as $c) : if (empty($c['q'])) { continue; } ?>
        <div class="concern" data-reveal><h3><?php echo esc_html($c['q']); ?></h3><p><?php echo esc_html((string) ($c['a'] ?? '')); ?></p></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
