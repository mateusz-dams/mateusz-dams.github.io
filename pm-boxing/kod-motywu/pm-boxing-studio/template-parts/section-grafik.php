<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<!-- GRAFIK -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Grafik'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <div class="panel table-wrap" data-reveal>
      <table class="tbl schedule">
        <caption class="sr">Grafik zajęć grupowych <?php echo esc_html((string) pmo('brand')); ?></caption>
        <thead><tr><th scope="col">Grupa</th><th scope="col">Dni</th><th scope="col">Godzina</th><th scope="col">Czas trwania</th><th scope="col"><span class="sr">Zapis</span></th></tr></thead>
        <tbody>
          <?php foreach (pmbs_groups() as $g) : ?>
          <tr>
            <th scope="row"><?php echo esc_html($g['name']); ?></th>
            <td><?php echo esc_html((string) ($g['days'] ?? '')); ?></td>
            <td class="time"><?php echo esc_html((string) ($g['time'] ?? '')); ?></td>
            <td class="len" data-label="Czas trwania"><?php echo esc_html((string) ($g['length'] ?? '')); ?></td>
            <td class="act"><a class="link-gold" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr((string) ($g['form'] ?? '')); ?>"><?php echo esc_html((string) pms($s, 'cta')); ?></a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if (pms($s, 'note') !== '') : ?><p class="table-note" data-reveal><?php echo pmbs_rich(pms($s, 'note')); ?></p><?php endif; ?>
  </div>
</section>
