<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$hid = ($s['anchor'] ?? 'insp') . '-h';
?>
<!-- MOJE INSPIRACJE -->
<section<?php echo pmbs_sec_attrs($s, 'section clip'); ?> aria-labelledby="<?php echo esc_attr($hid); ?>">
  <?php echo pmbs_ghost($s, 'Inspiracje'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2 id="<?php echo esc_attr($hid); ?>"><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>

    <h3 class="igroup" data-reveal><?php echo esc_html((string) pms($s, 'styles_title')); ?></h3>
    <ul class="igrid i4">
      <?php foreach (pmsa($s, 'styles') as $x) : if (empty($x['title'])) { continue; } ?>
      <li class="icard" data-reveal><h4><?php echo esc_html($x['title']); ?></h4><p><?php echo esc_html((string) ($x['text'] ?? '')); ?></p></li>
      <?php endforeach; ?>
    </ul>

    <h3 class="igroup" data-reveal><?php echo esc_html((string) pms($s, 'people_title')); ?></h3>
    <ul class="igrid i3">
      <?php foreach (pmsa($s, 'people') as $x) : if (empty($x['name'])) { continue; } ?>
      <li class="icard person" data-reveal><span class="tag"><?php echo esc_html((string) ($x['tag'] ?? '')); ?></span><h4><?php echo esc_html($x['name']); ?></h4><p><?php echo esc_html((string) ($x['text'] ?? '')); ?></p></li>
      <?php endforeach; ?>
    </ul>

    <h3 class="igroup" data-reveal><?php echo esc_html((string) pms($s, 'ana_title')); ?></h3>
    <ul class="igrid i4">
      <?php $i = 0; foreach (pmsa($s, 'ana') as $x) : if (empty($x['title'])) { continue; } $i++; ?>
      <li class="icard" data-reveal><span class="num"><?php echo esc_html(sprintf('%02d', $i)); ?></span><h4><?php echo esc_html($x['title']); ?></h4><p><?php echo esc_html((string) ($x['text'] ?? '')); ?></p></li>
      <?php endforeach; ?>
    </ul>
    <p class="ana-close" data-reveal><?php echo esc_html((string) pms($s, 'ana_close')); ?></p>

    <div class="creed" data-reveal>
      <?php foreach (pmbs_lines(pms($s, 'creed', [])) as $p) : ?><p><?php echo pmbs_rich($p); ?></p><?php endforeach; ?>
      <?php if (pms($s, 'cta') !== '') : ?><a class="btn btn-dark" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form=""><?php echo esc_html((string) pms($s, 'cta')); ?></a><?php endif; ?>
    </div>
  </div>
</section>
