<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$groups = array_values(array_filter(mvsa($s, 'groups'), static fn($g) => !empty($g['name']) || !empty($g['days'])));
?>
<!-- PROGRAM: WYBÓR GRUPY -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php echo mvn_head($s, true, 'intro'); ?>
    <div class="groups-grid groups-grid--<?php echo (int) min(4, max(1, count($groups))); ?>">
      <?php foreach ($groups as $g) :
          $spots = trim((string) ($g['spots'] ?? ''));
          $start = trim((string) ($g['start'] ?? ''));
          $cel = ($g['cel'] ?? '') !== '' ? $g['cel'] : 'msg';
      ?>
      <article class="group-card">
        <?php if (!empty($g['name'])) : ?><p class="group-name"><?php echo esc_html((string) $g['name']); ?></p><?php endif; ?>
        <h3><?php echo esc_html((string) ($g['days'] ?? '')); ?></h3>
        <?php if (!empty($g['time'])) : ?><p class="group-time"><?php echo mvn_icon('clock'); ?> <span>godz. <strong><?php echo esc_html((string) $g['time']); ?></strong></span></p><?php endif; ?>
        <ul class="group-meta">
          <?php if ($spots !== '') : ?><li><?php echo mvn_icon('users'); ?> <span>Wolne miejsca: <strong><?php echo esc_html($spots); ?></strong></span></li><?php endif; ?>
          <?php if ($start !== '') : ?><li><?php echo mvn_icon('calendar'); ?> <span><?php echo esc_html($start); ?></span></li><?php endif; ?>
        </ul>
        <?php echo mvn_btn((string) ($g['btn'] ?? ''), $cel, 'btn btn-primary btn-block'); ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php if (mvs($s, 'note') !== '') : ?><p class="groups-note"><?php echo esc_html(mvn_tokens((string) mvs($s, 'note'))); ?></p><?php endif; ?>
  </div>
</section>
