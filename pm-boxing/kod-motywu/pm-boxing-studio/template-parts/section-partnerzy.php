<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$items = array_filter(pmsa($s, 'items'), static function ($p) {
    return !empty($p['logo']);
});
if (!$items) {
    return;
}
?>
<!-- PARTNERZY -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Partnerzy'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <ul class="partners">
      <?php foreach ($items as $p) :
          $lid = (int) $p['logo'];
          if (!wp_attachment_is_image($lid)) { continue; }
          $logo = wp_get_attachment_image($lid, 'medium_large', false, ['alt' => (string) ($p['name'] ?? ''), 'loading' => 'lazy']);
          ?>
      <li class="partner" data-reveal><?php if (!empty($p['url'])) : ?><a href="<?php echo esc_url((string) $p['url']); ?>" rel="noopener nofollow" target="_blank"><?php echo $logo; ?></a><?php else : echo $logo; endif; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
