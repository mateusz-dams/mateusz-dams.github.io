<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$price = mvn_first_price();
$offer = ['offer' => 'first', 'name' => 'Pierwszy trening', 'value' => (string) $price, 'sessions' => '1'];
?>
<!-- LANDING: KARTA OFERTY -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container container--narrow">
    <?php echo mvn_head($s, true, 'intro'); ?>
    <div class="offer-card">
      <?php if (mvs($s, 'badge') !== '') : ?><span class="price-card-tag"><?php echo esc_html((string) mvs($s, 'badge')); ?></span><?php endif; ?>
      <h3><?php echo esc_html(mvn_tokens((string) mvs($s, 'card_title'))); ?></h3>
      <?php if ($price > 0) : ?><p class="offer-price"><span class="lp-price"><?php echo esc_html(mvn_zl($price)); ?></span><?php if (mvs($s, 'price_note') !== '') : ?> <span class="lp-was"><?php echo esc_html(mvn_tokens((string) mvs($s, 'price_note'))); ?></span><?php endif; ?></p><?php endif; ?>
      <?php echo mvn_feats(mvn_lines(mvsa($s, 'bullets'))); ?>
      <div class="cta-row">
        <?php echo mvn_btn((string) mvs($s, 'cta'), mvs($s, 'cta_cel', 'fitssey:schedule'), 'btn btn-primary', $offer); ?>
        <?php echo mvn_btn((string) mvs($s, 'cta2'), mvs($s, 'cta2_cel', '#kontakt'), 'btn btn-secondary'); ?>
      </div>
      <?php if (mvs($s, 'note') !== '') : ?><p class="lp-note"><?php echo esc_html(mvn_tokens((string) mvs($s, 'note'))); ?></p><?php endif; ?>
    </div>
  </div>
</section>
