<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- PIERWSZY KROK -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Start'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <div class="paths">
      <article class="path path-hl" data-reveal>
        <h3><?php echo esc_html((string) pms($s, 'a_title')); ?></h3>
        <p class="path-lead"><?php echo pmbs_rich(pms($s, 'a_lead')); ?></p>
        <p class="path-note"><?php echo esc_html((string) pms($s, 'a_note')); ?></p>
        <?php echo pmbs_feats(pmbs_lines(pms($s, 'a_feats', []))); ?>
        <a class="btn btn-dark" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr(pmbs_form_default()); ?>"><?php echo esc_html((string) pms($s, 'a_cta')); ?></a>
      </article>
      <article class="path" data-reveal>
        <h3><?php echo esc_html((string) pms($s, 'b_title')); ?></h3>
        <p class="path-lead"><?php echo pmbs_rich(pms($s, 'b_lead')); ?></p>
        <p class="path-note"><?php echo esc_html((string) pms($s, 'b_note')); ?></p>
        <?php echo pmbs_feats(pmbs_lines(pms($s, 'b_feats', []))); ?>
        <a class="btn btn-primary" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form=""><?php echo esc_html((string) pms($s, 'b_cta')); ?></a>
      </article>
    </div>
    <?php if (pms($s, 'bring') !== '') : ?>
    <div class="strip bring" data-reveal>
      <p><?php echo pmbs_rich(pms($s, 'bring')); ?></p>
    </div>
    <?php endif; ?>
  </div>
</section>
