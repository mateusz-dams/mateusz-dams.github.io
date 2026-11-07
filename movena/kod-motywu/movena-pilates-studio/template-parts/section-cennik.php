<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$items = mvn_price_groups()['group'];
$note = (string) mvs($s, 'note_group');
$phone = (string) mvo('phone_display');
?>
<!-- CENNIK -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php echo mvn_head($s); ?>
    <?php if ($note !== '') : ?><div class="pricing-note is-visible"><?php echo mvn_icon('info'); ?> <?php echo esc_html($note); ?></div><?php endif; ?>
    <div class="pricing-grid">
      <?php foreach ($items as $v) { echo mvn_pack_card($v, (string) mvs($s, 'buy', 'Kup online'), (string) mvs($s, 'best')); } ?>
    </div>
    <?php if (mvs($s, 'help_title') !== '') : ?>
    <div class="help-strip">
      <div><p><?php echo esc_html((string) mvs($s, 'help_title')); ?></p><span><?php echo esc_html((string) mvs($s, 'help_text')); ?></span></div>
      <?php if (mvs($s, 'help_btn') !== '') : ?><a class="btn btn-secondary" href="tel:<?php echo esc_attr((string) mvo('phone_tel')); ?>"><?php echo esc_html(rtrim((string) mvs($s, 'help_btn'), ': ') . ': ' . $phone); ?></a><?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
