<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$people = array_filter(mvsa($s, 'people'), static fn($p) => is_array($p) && !empty($p['name']));
?>
<!-- INSTRUKTORKI -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container container--narrow">
    <?php echo mvn_head($s, true); ?>
    <?php if (mvs($s, 'panel_title') !== '' || mvs($s, 'panel_text') !== '') : ?>
    <div class="instructors-panel">
      <?php echo mvn_icon('users'); ?>
      <h3><?php echo esc_html((string) mvs($s, 'panel_title')); ?></h3>
      <p><?php echo esc_html((string) mvs($s, 'panel_text')); ?></p>
    </div>
    <?php endif; ?>
    <?php if ($people) : ?>
    <div class="people-grid">
      <?php foreach ($people as $p) : ?>
      <article class="person">
        <?php $ph = mvn_pic($p['photo'] ?? '', (string) $p['name'], 400, 500, [], 'medium'); if ($ph !== '') : ?><div class="person-photo"><?php echo $ph; ?></div><?php endif; ?>
        <h3><?php echo esc_html((string) $p['name']); ?></h3>
        <?php if (!empty($p['role'])) : ?><p class="person-role"><?php echo esc_html((string) $p['role']); ?></p><?php endif; ?>
        <?php if (!empty($p['bio'])) : ?><p><?php echo esc_html((string) $p['bio']); ?></p><?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
