<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$img = mvn_pic(mvs($s, 'image'), (string) mvs($s, 'image_alt'), 900, 1125, [], 'large');
$right = mvs($s, 'side', 'left') === 'right';
?>
<!-- TEKST + ZDJĘCIE -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <div class="<?php echo $img !== '' ? 'about-grid' . ($right ? ' about-grid--rev' : '') : 'container--narrow'; ?>">
      <?php if ($img !== '') : ?><div class="about-media"><?php echo $img; ?></div><?php endif; ?>
      <div>
        <?php echo mvn_head($s); ?>
        <?php foreach (mvn_paras(implode("\n\n", array_map(static fn($r) => (string) ($r['text'] ?? ''), mvsa($s, 'paragraphs')))) as $para) : ?><p class="prose"><?php echo mvn_rich($para); ?></p><?php endforeach; ?>
        <?php if (mvs($s, 'btn') !== '') : ?><div class="cta-row"><?php echo mvn_btn((string) mvs($s, 'btn'), mvs($s, 'btn_cel', '#kontakt'), 'btn btn-primary'); ?></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>
