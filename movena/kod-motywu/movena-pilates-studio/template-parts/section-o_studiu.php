<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$main = mvn_pic(mvs($s, 'image'), (string) mvs($s, 'image_alt'), 900, 1125, [], 'large');
$gallery = array_filter(mvsa($s, 'gallery'), static fn($g) => is_array($g) && mvn_pic_url($g['image'] ?? '') !== '');
?>
<!-- O STUDIU -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php if ($main !== '') : ?>
    <div class="about-grid">
      <div class="about-media"><?php echo $main; ?></div>
      <div>
        <?php echo mvn_head($s); ?>
        <?php if (mvs($s, 'btn') !== '') : ?><div class="cta-row"><?php echo mvn_btn((string) mvs($s, 'btn'), mvs($s, 'btn_cel', 'fitssey:schedule'), 'btn btn-primary'); ?></div><?php endif; ?>
      </div>
    </div>
    <?php else : ?>
    <?php echo mvn_head($s, true); ?>
    <?php endif; ?>
    <?php if ($gallery) : ?>
    <div class="gallery-grid">
      <?php foreach ($gallery as $g) : ?>
      <a href="<?php echo esc_url(mvn_pic_url($g['image'])); ?>" target="_blank" rel="noopener"><?php echo mvn_pic($g['image'], (string) ($g['alt'] ?? ''), 640, 480, [], 'medium_large'); ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if (mvs($s, 'caption') !== '') : ?><p class="gallery-caption"><?php echo esc_html((string) mvs($s, 'caption')); ?></p><?php endif; ?>
  </div>
</section>
