<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- WŁASNA SEKCJA -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container container--narrow">
    <?php echo mvn_head($s, false, 'intro'); ?>
    <div class="prose-content"><?php echo wp_kses_post((string) mvs($s, 'content')); ?></div>
    <?php if (mvs($s, 'btn') !== '') : ?><div class="cta-row"><?php echo mvn_btn((string) mvs($s, 'btn'), mvs($s, 'btn_cel', '#kontakt'), 'btn btn-primary'); ?></div><?php endif; ?>
  </div>
</section>
