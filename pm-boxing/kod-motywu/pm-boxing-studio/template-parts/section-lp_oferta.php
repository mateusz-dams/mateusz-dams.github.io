<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$hid = ($s['anchor'] ?? 'oferta') . '-h';
$price = pmbs_zl(pms($s, 'price', 0));
$form = trim((string) pms($s, 'form', ''));
if ($form === '') {
    $form = pmbs_form_default();
}
?>
<!-- OFERTA Z CENĄ -->
<section<?php echo pmbs_sec_attrs($s); ?> aria-labelledby="<?php echo esc_attr($hid); ?>">
  <?php echo pmbs_ghost($s, 'Oferta'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2 id="<?php echo esc_attr($hid); ?>"><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <article class="lp-offer" data-reveal>
      <div class="lp-offer-main">
        <?php if (pms($s, 'badge') !== '') : ?><p class="badge"><i aria-hidden="true"></i><?php echo esc_html((string) pms($s, 'badge')); ?></p><?php endif; ?>
        <h3><?php echo esc_html((string) pms($s, 'card_title')); ?></h3>
        <ul class="ticks">
          <?php foreach (pmbs_lines(pmsa($s, 'bullets')) as $b) : ?><li><?php echo esc_html($b); ?></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="lp-offer-buy">
        <p class="lp-price"><?php echo esc_html($price); ?></p>
        <?php if (pms($s, 'price_note') !== '') : ?><p class="muted"><?php echo esc_html((string) pms($s, 'price_note')); ?></p><?php endif; ?>
        <a class="btn btn-primary btn-lg full" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr($form); ?>"><?php echo esc_html((string) pms($s, 'cta')); ?></a>
        <?php if (pms($s, 'note') !== '') : ?><p class="lp-note"><?php echo esc_html((string) pms($s, 'note')); ?></p><?php endif; ?>
      </div>
    </article>
  </div>
</section>
