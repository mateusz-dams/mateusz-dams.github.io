<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- OFERTA -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Oferta'); ?><div class="wrap">
    <div class="head row" data-reveal>
      <div>
        <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
        <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
      </div>
      <div class="arrows" aria-label="Przewijanie kart">
        <button type="button" class="arrow" id="prog-prev" aria-label="Poprzednie"><?php echo pmbs_icon('arrow'); ?></button>
        <button type="button" class="arrow gold" id="prog-next" aria-label="Następne"><?php echo pmbs_icon('arrow'); ?></button>
      </div>
    </div>
    <div class="program" id="program">
      <?php $i = 0; foreach (pmsa($s, 'cards') as $c) : if (empty($c['title'])) { continue; } ?>
      <article class="pcard<?php echo $i++ === 0 ? ' active' : ''; ?>" tabindex="0" data-reveal>
        <span class="pic"><?php echo pmbs_icon((string) ($c['icon'] ?? 'user')); ?></span>
        <h3><?php echo esc_html($c['title']); ?></h3>
        <p class="pfor"><?php echo esc_html((string) ($c['for'] ?? '')); ?></p>
        <ul class="plist">
          <?php foreach (array_slice(pmbs_lines($c['bullets'] ?? ''), 0, 4) as $b) : ?><li><?php echo esc_html($b); ?></li><?php endforeach; ?>
        </ul>
        <div class="pfoot">
          <a class="plink" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr((string) ($c['form'] ?? '')); ?>"><?php echo esc_html((string) ($c['cta'] ?? '')); ?> <?php echo pmbs_icon('arrow'); ?></a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php if (pms($s, 'strip') !== '') : ?>
    <div class="strip" data-reveal>
      <p><?php echo pmbs_rich(pms($s, 'strip')); ?></p>
      <a class="btn btn-primary" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form=""><?php echo esc_html((string) pms($s, 'strip_cta')); ?></a>
    </div>
    <?php endif; ?>
  </div>
</section>
