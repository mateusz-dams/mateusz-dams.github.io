<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- WŁASNA SEKCJA -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <?php if (pms($s, 'intro') !== '') : ?><p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p><?php endif; ?>
    </div>
    <?php if (pms($s, 'content') !== '') : ?><div class="prose-block" data-reveal><?php echo wp_kses_post(wpautop((string) pms($s, 'content'))); ?></div><?php endif; ?>
    <?php if (pms($s, 'btn') !== '') : ?><p data-reveal><a class="btn btn-primary" href="<?php echo esc_url(pmbs_href(pms($s, 'btn_cel'), pmbs_contact_href())); ?>" data-form="<?php echo esc_attr((string) pms($s, 'btn_form')); ?>"><?php echo esc_html((string) pms($s, 'btn')); ?></a></p><?php endif; ?>
  </div>
</section>
