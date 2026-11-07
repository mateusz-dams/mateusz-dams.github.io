<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$classes = mvn_classes();
usort($classes, static function ($a, $b) {
    return (int) !empty($b['recommended']) <=> (int) !empty($a['recommended']);
});
$btnCel = (string) mvs($s, 'btn_cel', 'fitssey:schedule');
?>
<!-- ZAJĘCIA -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php echo mvn_head($s); ?>
    <?php if (mvs($s, 'filter_rec') !== '') : ?>
    <div class="class-filters" id="class-filters">
      <button type="button" class="filter-chip is-active" data-filter="all"><?php echo esc_html((string) mvs($s, 'filter_all', 'Wszystkie zajęcia')); ?></button>
      <button type="button" class="filter-chip" data-filter="recommended"><?php echo esc_html((string) mvs($s, 'filter_rec')); ?></button>
    </div>
    <?php endif; ?>
    <div class="class-grid" id="class-grid">
      <?php foreach ($classes as $c) : $slug = sanitize_title((string) $c['name']); $rec = !empty($c['recommended']); $own = trim((string) ($c['url'] ?? '')); ?>
      <article class="class-card" data-recommended="<?php echo $rec ? 'true' : 'false'; ?>" data-id="<?php echo esc_attr($slug); ?>">
        <div class="class-card-top">
          <h3><?php echo esc_html((string) $c['name']); ?></h3>
          <?php if ($rec && mvs($s, 'badge') !== '') : ?><span class="badge"><?php echo esc_html((string) mvs($s, 'badge')); ?></span><?php endif; ?>
        </div>
        <?php if (!empty($c['tagline'])) : ?><p class="tagline"><?php echo esc_html((string) $c['tagline']); ?></p><?php endif; ?>
        <?php if (!empty($c['for'])) : ?><p class="for-whom"><?php echo esc_html((string) $c['for']); ?></p><?php endif; ?>
        <div class="class-card-footer">
          <?php if (!empty($c['duration'])) : ?><span class="class-duration"><?php echo mvn_icon('clock'); ?> <?php echo (int) $c['duration']; ?> min</span><?php endif; ?>
          <?php if (mvs($s, 'btn') !== '') : ?>
          <a class="btn btn-secondary btn-sm" href="<?php echo esc_url($own !== '' ? $own : mvn_href($btnCel)); ?>" target="_blank" rel="noopener" data-track="select_class" data-class="<?php echo esc_attr($slug); ?>"><?php echo esc_html((string) mvs($s, 'btn')); ?></a>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
