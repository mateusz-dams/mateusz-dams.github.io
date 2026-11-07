<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$img  = (int) pms($s, 'image', 0);
if ($img && !wp_attachment_is_image($img)) {
    $img = 0;
}
$side = pms($s, 'side', 'left') === 'right' ? ' rev' : '';
?>
<!-- TEKST + ZDJĘCIE -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s); ?><div class="wrap<?php echo $img ? ' split about' . $side : ''; ?>">
    <?php if ($img) : ?><figure class="about-photo" data-reveal><?php echo pmbs_img($img, '', (string) pms($s, 'image_alt'), 1170, 1170); ?></figure><?php endif; ?>
    <div>
      <h2 data-reveal><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <?php foreach (pmbs_lines(pms($s, 'paragraphs', [])) as $p) : ?><p class="prose" data-reveal><?php echo pmbs_rich($p); ?></p><?php endforeach; ?>
      <?php if (pms($s, 'btn') !== '') : ?><a class="btn btn-primary" data-reveal href="<?php echo esc_url(pmbs_href(pms($s, 'btn_cel'), pmbs_contact_href())); ?>" data-form="<?php echo esc_attr((string) pms($s, 'btn_form')); ?>"><?php echo esc_html((string) pms($s, 'btn')); ?></a><?php endif; ?>
    </div>
  </div>
</section>
