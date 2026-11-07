<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$hid = ($s['anchor'] ?? 'about') . '-h';
?>
<!-- O MNIE I O STUDIU -->
<section<?php echo pmbs_sec_attrs($s); ?> aria-labelledby="<?php echo esc_attr($hid); ?>">
  <?php echo pmbs_ghost($s, 'Trener'); ?><div class="wrap split about">
    <figure class="about-photo" data-reveal>
      <?php echo pmbs_img(pms($s, 'photo'), 'trener-portret.webp', (string) pms($s, 'photo_alt'), 1170, 1170); ?>
    </figure>
    <div>
      <h2 id="<?php echo esc_attr($hid); ?>" data-reveal><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <?php foreach (pmbs_lines(pms($s, 'paragraphs', [])) as $p) : ?>
      <p class="prose" data-reveal><?php echo pmbs_rich($p); ?></p>
      <?php endforeach; ?>
      <?php if (pms($s, 'quote') !== '') : ?><blockquote class="about-quote" data-reveal><?php echo esc_html((string) pms($s, 'quote')); ?></blockquote><?php endif; ?>
      <?php if (pmo('qual_title')) : ?>
      <div class="cred" data-reveal>
        <span class="cred-ic"><?php echo pmbs_icon('award'); ?></span>
        <div><p class="cred-k">Kwalifikacje</p><p class="cred-t"><?php echo esc_html((string) pmo('qual_title')); ?></p><p class="cred-n"><?php echo esc_html((string) pmo('qual_note')); ?></p></div>
      </div>
      <?php endif; ?>
      <?php if (pms($s, 'cta') !== '') : ?><a class="btn btn-primary" data-reveal href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr(pmbs_form_default()); ?>"><?php echo esc_html((string) pms($s, 'cta')); ?></a><?php endif; ?>
    </div>
  </div>
</section>
