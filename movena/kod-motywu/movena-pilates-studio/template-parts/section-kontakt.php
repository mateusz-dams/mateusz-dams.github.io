<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$phoneTel  = (string) mvo('phone_tel');
$phone     = (string) mvo('phone_display');
$callTimes = mvn_lines(mvo('call_times'));
$preset    = mvn_form_default();
$asideItems = mvn_lines(mvsa($s, 'aside_items'));
$wa        = mvn_wa_url();
$privacy   = mvn_legal_url('polityka-prywatnosci');
?>
<!-- KONTAKT I FORMULARZ -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php echo mvn_head($s, false, 'intro'); ?>
    <div class="contact-grid">
      <div class="contact-aside">
        <ul class="cinfo">
          <li><a href="tel:<?php echo esc_attr($phoneTel); ?>" data-track="click_phone"><?php echo mvn_icon('phone'); ?><span><small>Telefon</small><strong><?php echo esc_html($phone); ?></strong></span></a></li>
          <?php if (trim((string) mvo('email')) !== '') : ?><li><a href="mailto:<?php echo esc_attr((string) mvo('email')); ?>"><?php echo mvn_icon('mail'); ?><span><small>E-mail</small><strong><?php echo esc_html((string) mvo('email')); ?></strong></span></a></li><?php endif; ?>
          <?php if ($wa !== '') : ?><li><a href="<?php echo esc_url($wa); ?>" target="_blank" rel="noopener"><?php echo mvn_icon('wa'); ?><span><small>WhatsApp</small><strong>Napisz do nas</strong></span></a></li><?php endif; ?>
          <li><span class="cinfo-row"><?php echo mvn_icon('pin'); ?><span><small>Adres</small><strong><?php echo esc_html(trim(mvo('address') . ', ' . mvo('city'), ' ,')); ?></strong></span></span></li>
        </ul>
        <?php if ($asideItems || mvs($s, 'aside_title') !== '') : ?>
        <div class="aside-card">
          <h3><?php echo esc_html((string) mvs($s, 'aside_title')); ?></h3>
          <ol class="after-steps">
            <?php foreach ($asideItems as $k => $line) : ?><li><span class="step-num" aria-hidden="true"><?php echo (int) ($k + 1); ?></span><span><?php echo esc_html(mvn_tokens($line)); ?></span></li><?php endforeach; ?>
          </ol>
          <?php echo mvn_btn((string) mvs($s, 'aside_btn'), mvs($s, 'aside_cel', 'fitssey:schedule'), 'btn btn-secondary btn-block', ['offer' => 'first']); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="form-card">
        <form id="lead" novalidate>
          <h3><?php echo esc_html((string) mvs($s, 'form_title')); ?></h3>
          <p class="form-lead"><?php echo esc_html((string) mvs($s, 'form_lead')); ?></p>
          <div class="field"><label for="f-name">Imię</label><input id="f-name" name="imie" type="text" autocomplete="given-name" required><span class="err" id="e-name" role="alert"></span></div>
          <div class="field"><label for="f-phone">Numer telefonu</label><input id="f-phone" name="telefon" type="tel" inputmode="tel" autocomplete="tel" required><span class="err" id="e-phone" role="alert"></span></div>
          <div class="field"><label for="f-email">E-mail <span class="opt">(opcjonalnie)</span></label><input id="f-email" name="email" type="email" inputmode="email" autocomplete="email"><span class="err" id="e-email" role="alert"></span></div>
          <div class="field"><label for="f-type">Czego dotyczy zgłoszenie</label>
            <select id="f-type" name="forma" required><option value="">Wybierz z listy</option><?php foreach (mvn_form_labels() as $opt) : ?><option value="<?php echo esc_attr($opt); ?>"<?php echo $opt === $preset ? ' selected' : ''; ?>><?php echo esc_html($opt); ?></option><?php endforeach; ?></select><span class="err" id="e-type" role="alert"></span></div>
          <div class="field"><label for="f-when">Kiedy mamy oddzwonić? <span class="opt">(opcjonalnie)</span></label>
            <select id="f-when" name="kiedy"><?php foreach ($callTimes as $opt) : ?><option value="<?php echo esc_attr($opt); ?>"><?php echo esc_html($opt); ?></option><?php endforeach; ?></select></div>
          <div class="field"><label for="f-msg">Wiadomość <span class="opt">(opcjonalnie)</span></label><textarea id="f-msg" name="wiadomosc" rows="3"></textarea></div>
          <div class="field check"><input id="f-consent" name="zgoda" type="checkbox" required><label for="f-consent"><?php echo esc_html(mvn_tokens((string) mvs($s, 'form_consent'))); ?> <a class="consent-link" href="<?php echo esc_url($privacy); ?>" target="_blank" rel="noopener">Polityka prywatności</a></label><span class="err" id="e-consent" role="alert"></span></div>
          <div class="hp" aria-hidden="true"><label>Nie wypełniaj tego pola <input type="text" name="pole_kontrolne" tabindex="-1" autocomplete="off"></label></div>
          <p class="form-error" id="form-error" role="alert" hidden></p>
          <button class="btn btn-primary btn-block" type="submit"><?php echo esc_html((string) mvs($s, 'form_button')); ?></button>
          <?php if (mvs($s, 'form_note') !== '') : ?><p class="form-note"><?php echo esc_html(mvn_tokens((string) mvs($s, 'form_note'))); ?></p><?php endif; ?>
        </form>
        <div class="thanks" id="thanks" role="status" aria-live="polite" hidden>
          <h3><?php echo esc_html((string) mvs($s, 'thanks_title')); ?></h3>
          <p id="thanks-text"><?php echo esc_html((string) mvs($s, 'thanks_text')); ?></p>
          <p class="muted">Wolisz nie czekać? Zadzwoń: <a class="step-link" href="tel:<?php echo esc_attr($phoneTel); ?>"><?php echo esc_html($phone); ?></a></p>
        </div>
      </div>
    </div>
  </div>
</section>
