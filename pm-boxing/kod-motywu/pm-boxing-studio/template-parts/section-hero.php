<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$chips = array_slice(pmsa($s, 'chips'), 0, 3);
?>
<!-- HERO -->
<section<?php echo pmbs_sec_attrs($s, 'hero clip'); ?>>
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <p class="badge"><i aria-hidden="true"></i><?php echo esc_html((string) pms($s, 'badge')); ?></p>
      <h1><?php echo esc_html((string) pms($s, 'title_a')); ?> <span class="grad"><?php echo esc_html((string) pms($s, 'title_grad')); ?></span></h1>
      <p class="lead"><?php echo esc_html((string) pms($s, 'lead')); ?></p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-lg" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr(pmbs_form_default()); ?>"><?php echo esc_html((string) pms($s, 'cta_primary')); ?></a>
        <a class="btn btn-ghost btn-lg" href="<?php echo esc_url(pmbs_href(pms($s, 'cta_secondary_cel'), '#oferta')); ?>"><?php echo esc_html((string) pms($s, 'cta_secondary')); ?></a>
      </div>
      <ul class="hero-meta">
        <?php foreach (pmsa($s, 'meta') as $m) : if (empty($m['text'])) { continue; } ?>
        <li><?php echo pmbs_icon((string) ($m['icon'] ?? 'award')); ?><span><?php echo esc_html($m['text']); ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="hero-visual" id="hv">
      <div class="hv-wrap">
        <div class="hv-frame"><?php echo pmbs_img(pms($s, 'image'), 'sala-lustra.webp', (string) pms($s, 'image_alt'), 1086, 1448, ['loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async']); ?></div>
      </div>
      <?php if (pms($s, 'orbit_price') !== '') : ?>
      <a class="hv-badge" href="<?php echo esc_url(pmbs_href(pms($s, 'orbit_cel'), '#grafik')); ?>" aria-label="<?php echo esc_attr(ucfirst(mb_strtolower(trim(str_replace('•', '', (string) pms($s, 'orbit_text')))))); ?>">
        <svg viewBox="0 0 120 120" aria-hidden="true"><defs><path id="hv-circle" d="M60,60 m-45,0 a45,45 0 1,1 90,0 a45,45 0 1,1 -90,0"/></defs><text class="hv-ring" font-size="9.4" letter-spacing="1.6" textLength="278" lengthAdjust="spacing"><textPath href="#hv-circle"><?php echo esc_html((string) pms($s, 'orbit_text')); ?></textPath></text></svg>
        <span class="hv-zero"><?php echo esc_html((string) pms($s, 'orbit_price')); ?></span>
      </a>
      <?php endif; ?>
      <?php $n = 0; foreach ($chips as $c) : if (empty($c['label'])) { continue; } $n++; ?>
      <a class="hv-chip c<?php echo (int) $n; ?><?php echo $n === 2 ? ' ko' : ''; ?>" href="<?php echo esc_url(pmbs_href($c['cel'] ?? '')); ?>"><i aria-hidden="true"></i><?php echo esc_html($c['label']); ?><?php echo pmbs_icon('arrow'); ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
