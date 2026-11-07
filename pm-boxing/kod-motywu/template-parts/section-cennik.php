<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$onboarding = pmbs_zl(pms($s, 'onboarding', 0));
$ribbon     = (string) pms($s, 'ribbon');

$personal = array_values(array_filter(pmsa($s, 'personal'), static function ($r) { return !empty($r['name']); }));
$duo      = array_values(array_filter(pmsa($s, 'duo'), static function ($r) { return !empty($r['name']); }));
$groups   = array_values(array_filter(pmbs_groups(), static function ($r) { return !empty($r['name']); }));
$guide    = pmsa($s, 'guide');
?>
<!-- CENNIK -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Cennik'); ?><div class="wrap">
    <div class="head center" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>

    <div class="start-pair" data-reveal>
      <div class="sp sp-free"><div><h3><?php echo esc_html((string) pms($s, 'sp_free_title')); ?></h3><p class="muted"><?php echo esc_html((string) pms($s, 'sp_free_text')); ?></p></div><p class="sp-price"><?php echo esc_html((string) pms($s, 'sp_free_price')); ?></p></div>
      <div class="sp sp-paid"><div><h3><?php echo esc_html((string) pms($s, 'sp_paid_title')); ?></h3><p class="muted"><?php echo esc_html((string) pms($s, 'sp_paid_text')); ?></p></div><p class="sp-price"><?php echo esc_html($onboarding); ?></p></div>
    </div>

    <div class="tabs-wrap" data-reveal>
      <div class="tabs" role="tablist" aria-label="Rodzaj treningu">
        <button role="tab" id="tab-personal" aria-selected="true" aria-controls="panel-personal">Personalne</button>
        <button role="tab" id="tab-duo" aria-selected="false" aria-controls="panel-duo" tabindex="-1">Duet</button>
        <button role="tab" id="tab-groups" aria-selected="false" aria-controls="panel-groups" tabindex="-1">Grupy</button>
      </div>
    </div>

    <div class="tabpanel" role="tabpanel" id="panel-personal" aria-labelledby="tab-personal">
      <div class="prices p<?php echo (int) min(4, max(1, count($personal))); ?>">
        <?php
        $feats = pmbs_lines(pms($s, 'personal_feats', []));
        foreach ($personal as $i => $r) {
            $last = ($i === count($personal) - 1);
            $per  = (float) ($r['per'] ?? 0);
            $sub  = $per > 0 ? esc_html(pmbs_zl($per)) . ' za trening' : 'za jeden trening';
            echo pmbs_price_card($r['name'], esc_html(pmbs_zl($r['price'] ?? 0)), $sub, $feats, (string) pms($s, 'personal_cta'), pmbs_form_by_crm('personal', 'Trening personalny'), $last && count($personal) > 1, $last && count($personal) > 1 ? $ribbon : ''); // phpcs:ignore
        }
        ?>
      </div>
    </div>

    <div class="tabpanel" role="tabpanel" id="panel-duo" aria-labelledby="tab-duo" hidden>
      <p class="tabnote"><?php echo esc_html((string) pms($s, 'duo_note')); ?></p>
      <div class="prices p<?php echo (int) min(4, max(1, count($duo))); ?>">
        <?php
        $feats = pmbs_lines(pms($s, 'duo_feats', []));
        foreach ($duo as $i => $r) {
            $last = ($i === count($duo) - 1);
            $per  = (float) ($r['per'] ?? 0);
            $sub  = $per > 0 ? esc_html(pmbs_zl($per)) . ' za trening na osobę' : '';
            echo pmbs_price_card($r['name'], esc_html(pmbs_zl($r['price'] ?? 0)) . '<small>/os.</small>', $sub, $feats, (string) pms($s, 'duo_cta'), pmbs_form_by_crm('duet', 'Duet'), $last && count($duo) > 1, $last && count($duo) > 1 ? $ribbon : ''); // phpcs:ignore
        }
        ?>
      </div>
    </div>

    <div class="tabpanel" role="tabpanel" id="panel-groups" aria-labelledby="tab-groups" hidden>
      <p class="tabnote"><?php echo esc_html((string) pms($s, 'groups_note')); ?></p>
      <div class="prices p<?php echo (int) min(4, max(1, count($groups))); ?>">
        <?php
        $feats = pmbs_lines(pms($s, 'groups_feats', []));
        foreach ($groups as $g) {
            echo pmbs_price_card($g['name'], esc_html(pmbs_zl($g['price'] ?? 0)) . '<small>/mies.</small>', esc_html(trim(($g['days'] ?? '') . ', ' . ($g['time'] ?? ''), ', ')), $feats, (string) pms($s, 'groups_cta'), (string) ($g['form'] ?? '')); // phpcs:ignore
        }
        ?>
      </div>
    </div>

    <?php if ($guide) : ?>
    <div class="guide" data-reveal>
      <h3><?php echo esc_html((string) pms($s, 'guide_title')); ?></h3>
      <div class="guide-grid">
        <?php foreach ($guide as $c) : if (empty($c['title'])) { continue; } ?>
        <article><p class="gk"><?php echo esc_html((string) ($c['kicker'] ?? '')); ?></p><h4><?php echo esc_html($c['title']); ?></h4><p><?php echo pmbs_rich((string) ($c['text'] ?? '')); ?></p><a class="plink" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr((string) ($c['form'] ?? '')); ?>"><?php echo esc_html((string) ($c['cta'] ?? '')); ?> <?php echo pmbs_icon('arrow'); ?></a></article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
