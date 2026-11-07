<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$stars = str_repeat(pmbs_icon('star'), 5);
?>
<!-- OPINIE -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Opinie'); ?><div class="wrap">
    <div class="head row" data-reveal>
      <div>
        <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
        <p class="prose"><?php echo esc_html(trim(pmbs_opinion_label((int) pms($s, 'count', 0)) . '. ' . (string) pms($s, 'suffix'))); ?></p>
      </div>
      <?php if (pms($s, 'btn') !== '') : ?><a class="btn btn-ghost" href="<?php echo esc_url((string) pmo('maps_url')); ?>" rel="noopener" target="_blank"><?php echo esc_html((string) pms($s, 'btn')); ?></a><?php endif; ?>
    </div>
    <ul class="reviews">
      <?php foreach (pmsa($s, 'items') as $r) : if (empty($r['text'])) { continue; } ?>
      <li class="review" data-reveal><span class="stars" role="img" aria-label="Ocena: 5 na 5 gwiazdek"><?php echo $stars; ?></span><blockquote><p><?php echo esc_html($r['text']); ?></p></blockquote><p class="rev-by"><strong><?php echo esc_html((string) ($r['name'] ?? '')); ?></strong><span>Opinia z Google</span></p></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
