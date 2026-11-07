<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$items = array_filter(mvsa($s, 'items'), static fn($r) => is_array($r) && !empty($r['text']));
$count = (int) mvs($s, 'count', 0);
$reviews = trim((string) mvo('reviews_url'));
if (!$items) {
    return;
}
?>
<!-- OPINIE -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php echo mvn_head($s, true); ?>
    <div class="reviews-grid">
      <?php foreach ($items as $r) : ?>
      <figure class="review">
        <div class="stars" aria-hidden="true"><?php echo str_repeat(mvn_icon('star'), 5); ?></div>
        <blockquote><?php echo esc_html((string) $r['text']); ?></blockquote>
        <?php if (!empty($r['name'])) : ?><figcaption><?php echo esc_html((string) $r['name']); ?></figcaption><?php endif; ?>
      </figure>
      <?php endforeach; ?>
    </div>
    <?php if ($count > 0 || ($reviews !== '' && mvs($s, 'btn') !== '')) : ?>
    <p class="reviews-more"><?php if ($count > 0) : ?><strong><?php echo esc_html(mvn_opinion_label($count)); ?></strong><?php endif; ?>
      <?php if ($reviews !== '' && mvs($s, 'btn') !== '') : ?> <a class="step-link" href="<?php echo esc_url($reviews); ?>" target="_blank" rel="noopener"><?php echo esc_html((string) mvs($s, 'btn')); ?></a><?php endif; ?></p>
    <?php endif; ?>
  </div>
</section>
