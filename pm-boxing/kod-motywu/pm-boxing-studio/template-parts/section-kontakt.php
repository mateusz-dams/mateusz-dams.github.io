<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$phone_tel = (string) pmo('phone_tel');
$phone     = (string) pmo('phone_display');
$maps      = (string) pmo('maps_url');
$wa        = pmbs_wa_url();
$label     = pmbs_opinion_label((int) pms(pmbs_first('opinie'), 'count', 0));
$mapEmbed  = 'https://www.google.com/maps?q=' . rawurlencode(pmo('address') . ', ' . pmo('city')) . '&output=embed';
$callTimes = pmbs_lines(pmo('call_times'));
$mapTitle  = 'Mapa: ' . pmo('brand') . ', ' . pmo('address') . ', ' . pmo('city');
?>
<!-- KONTAKT -->
<section<?php echo pmbs_sec_attrs($s, 'section contact clip'); ?>>
  <?php echo pmbs_ghost($s, 'Kontakt'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <div class="contact-grid">
      <div class="contact-left">
        <ul class="cinfo">
          <li data-reveal><a href="tel:<?php echo esc_attr($phone_tel); ?>"><span class="cic"><?php echo pmbs_icon('phone'); ?></span><span class="ctx"><small>Telefon</small><strong><?php echo esc_html($phone); ?></strong></span></a></li>
          <li data-reveal><a href="<?php echo esc_url($wa); ?>" rel="noopener" target="_blank"><span class="cic"><?php echo pmbs_icon('wa'); ?></span><span class="ctx"><small>WhatsApp</small><strong>Napisz do nas: <?php echo esc_html($phone); ?></strong></span></a></li>
          <li data-reveal><a href="mailto:<?php echo esc_attr((string) pmo('email')); ?>"><span class="cic"><?php echo pmbs_icon('mail'); ?></span><span class="ctx"><small>E-mail</small><strong><?php echo esc_html((string) pmo('email')); ?></strong></span></a></li>
          <li data-reveal><a href="<?php echo esc_url($maps); ?>" rel="noopener" target="_blank"><span class="cic"><?php echo pmbs_icon('pin'); ?></span><span class="ctx"><small>Lokalizacja</small><strong><?php echo esc_html(pmo('address') . ', ' . pmo('city')); ?></strong></span></a></li>
          <li data-reveal><div class="crow"><span class="cic"><?php echo pmbs_icon('clock'); ?></span><span class="ctx"><small>Godziny otwarcia</small><strong><?php echo pmbs_hours_br(); ?></strong></span></div></li>
        </ul>
        <article class="after" data-reveal>
          <h3><?php echo esc_html((string) pms($s, 'after_title')); ?></h3>
          <ol class="after-steps">
            <?php $i = 0; foreach (pmsa($s, 'after_steps') as $st) : if (empty($st['title'])) { continue; } $i++; ?>
            <li><span class="hex sm" aria-hidden="true"><span><?php echo (int) $i; ?></span></span><div><h4><?php echo esc_html($st["title"]); ?></h4><p><?php echo esc_html((string) ($st["text"] ?? "")); ?></p></div></li>
            <?php endforeach; ?>
          </ol>
          <a class="proof" href="<?php echo esc_url($maps); ?>" rel="noopener" target="_blank">
            <span class="stars" aria-hidden="true"><?php echo str_repeat(pmbs_icon('star'), 5); ?></span>
            <span class="proof-t"><strong><?php echo esc_html($label); ?></strong><small><?php echo esc_html((string) pms($s, 'proof_sub')); ?></small></span>
            <?php echo pmbs_icon('arrow'); ?>
          </a>
        </article>
      </div>
      <div class="form-card" data-reveal>
        <form id="lead" novalidate>
          <h3><?php echo esc_html((string) pms($s, 'form_title')); ?></h3>
          <p class="form-lead"><?php echo esc_html((string) pms($s, 'form_lead')); ?></p>
          <div class="field"><label for="f-name">Imię</label><input id="f-name" name="imie" type="text" autocomplete="given-name" required><span class="err" id="e-name" role="alert"></span></div>
          <div class="field"><label for="f-phone">Numer telefonu</label><input id="f-phone" name="telefon" type="tel" inputmode="tel" autocomplete="tel" required><span class="err" id="e-phone" role="alert"></span></div>
          <div class="field"><label for="f-email">E-mail <span class="opt">(opcjonalnie)</span></label><input id="f-email" name="email" type="email" inputmode="email" autocomplete="email"><span class="err" id="e-email" role="alert"></span></div>
          <div class="field"><label for="f-type">Forma treningu</label>
            <select id="f-type" name="forma" required><option value="">Wybierz formę treningu</option><?php $pre = pmbs_is_landing() ? pmbs_form_default() : ''; foreach (pmbs_form_labels() as $o) : ?><option value="<?php echo esc_attr($o); ?>"<?php echo $o === $pre ? ' selected' : ''; ?>><?php echo esc_html($o); ?></option><?php endforeach; ?></select><span class="err" id="e-type" role="alert"></span></div>
          <div class="field"><label for="f-when">Kiedy mamy oddzwonić? <span class="opt">(opcjonalnie)</span></label>
            <select id="f-when" name="kiedy"><?php foreach ($callTimes as $o) : ?><option value="<?php echo esc_attr($o); ?>"><?php echo esc_html($o); ?></option><?php endforeach; ?></select></div>
          <div class="field"><label for="f-msg">Wiadomość <span class="opt">(opcjonalnie)</span></label><textarea id="f-msg" name="wiadomosc" rows="3"></textarea></div>
          <div class="field check"><input id="f-consent" name="zgoda" type="checkbox" required><label for="f-consent"><?php echo esc_html((string) pms($s, 'form_consent')); ?> <a class="consent-link" href="<?php echo esc_url(pmbs_legal_url('polityka-prywatnosci')); ?>" target="_blank" rel="noopener">Polityka prywatności</a></label><span class="err" id="e-consent" role="alert"></span></div>
          <div class="hp" aria-hidden="true"><label>Nie wypełniaj tego pola <input type="text" name="pole_kontrolne" tabindex="-1" autocomplete="off"></label></div>
          <p class="form-error" id="form-error" role="alert" hidden></p>
          <button class="btn btn-primary btn-lg full" type="submit"><?php echo esc_html((string) pms($s, 'form_button')); ?></button>
          <p class="wa-line">Wolisz napisać? <a class="link-gold" href="<?php echo esc_url($wa); ?>" rel="noopener" target="_blank">Napisz na WhatsAppie</a></p>
          <p class="form-note">Administrator danych: <?php echo esc_html(rtrim((string) pmo('company_name'), '.')); ?>. <?php echo esc_html((string) pms($s, 'form_note')); ?></p>
        </form>
        <div class="thanks" id="thanks" role="status" aria-live="polite" hidden>
          <h3><?php echo esc_html((string) pms($s, 'thanks_title')); ?></h3>
          <p id="thanks-text"><?php echo esc_html((string) pms($s, 'thanks_text')); ?></p>
          <p class="muted">Wolisz nie czekać? Zadzwoń: <a class="link-gold" href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo esc_html($phone); ?></a></p>
        </div>
      </div>
    </div>
    <div class="mapcard" data-reveal>
      <div class="map-viz" aria-hidden="true"><span class="pin"><?php echo pmbs_icon('pin'); ?></span></div>
      <div class="map-info">
        <h3><?php echo esc_html((string) pms($s, 'map_title')); ?></h3>
        <p class="muted"><?php echo esc_html((string) pms($s, 'map_text')); ?></p>
        <div class="cta-row"><a class="btn btn-primary" href="<?php echo esc_url($maps); ?>" rel="noopener" target="_blank">Wyznacz trasę</a><button type="button" class="btn btn-ghost" id="map-load">Pokaż mapę</button></div>
      </div>
      <div class="map-frame" id="map-frame" data-src="<?php echo esc_url($mapEmbed); ?>" data-title="<?php echo esc_attr($mapTitle); ?>" hidden></div>
    </div>
  </div>
</section>
