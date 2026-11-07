<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
if (mvn_fitssey_mode()) {
    // Przejściowo: zapisy i zakupy działają w Fitssey — jasna informacja i przycisk, bez „chwilowej niedostępności”.
    $fid = ($s['anchor'] ?? 'rezerwacja') . '-h';
    $sched = mvn_fitssey_url('schedule');
    $login = mvn_fitssey_url('login');
    $pts = ['Zajęcia trwają 55 minut, w grupie do 4 osób', 'Płatność online kartą lub BLIK-iem', 'Termin możesz zmienić najpóźniej 12 godzin wcześniej'];
    ?>
<!-- ZAPISY ONLINE (Fitssey) -->
<section<?php echo mvn_sec_attrs($s, 'section book'); ?> aria-labelledby="<?php echo esc_attr($fid); ?>">
  <div class="container">
    <div class="book-grid">
      <div class="book-info">
        <?php if (mvs($s, 'eyebrow') !== '') : ?><p class="eyebrow"><?php echo esc_html((string) mvs($s, 'eyebrow')); ?></p><?php endif; ?>
        <h2 id="<?php echo esc_attr($fid); ?>">Zapisz się na zajęcia online</h2>
        <p class="lead">Grafik, rezerwacja i płatność działają w systemie zapisów studia. Wybierz termin, zapłać kartą lub BLIK-iem i przyjdź na reformer.</p>
        <ul class="book-points">
          <?php foreach ($pts as $k => $pt) : ?><li><span class="bp-ic"><?php echo mvn_icon(['users', 'check', 'calendar'][$k]); ?></span><span><?php echo esc_html($pt); ?></span></li><?php endforeach; ?>
        </ul>
        <div class="book-call">
          <p><strong>Wolisz zadzwonić?</strong> Pomożemy wybrać termin i zajęcia.</p>
          <a class="btn btn-secondary" href="tel:<?php echo esc_attr((string) mvo('phone_tel')); ?>">Zadzwoń: <?php echo esc_html((string) mvo('phone_display')); ?></a>
        </div>
      </div>
      <div class="book-card">
        <div class="bk-state bk-msg">
          <span class="bk-msg-ic"><?php echo mvn_icon('calendar'); ?></span>
          <h3 class="bk-msg-t">Zapisy online</h3>
          <p class="bk-msg-p">Sprawdź wolne miejsca i zapisz się na wybrane zajęcia. Masz już konto? Zaloguj się i wybierz termin.</p>
          <div class="bk-msg-a">
            <a class="btn btn-primary" href="<?php echo esc_url($sched); ?>" target="_blank" rel="noopener" data-track="click_fitssey" data-offer-type="schedule">Przejdź do grafiku</a>
            <a class="btn btn-secondary" href="<?php echo esc_url($login); ?>" target="_blank" rel="noopener" data-track="click_fitssey" data-offer-type="login">Zaloguj się</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
    return;
}
if (!mvn_booking_on()) {
    return; // zapisy online wyłączone — przyciski prowadzą do formularza
}
$hid = ($s['anchor'] ?? 'rezerwacja') . '-h';
$points = mvn_lines((string) mvs($s, 'points'));
$icons = ['users', 'check', 'calendar'];
?>
<!-- ZAPISY ONLINE -->
<section<?php echo mvn_sec_attrs($s, 'section book'); ?> aria-labelledby="<?php echo esc_attr($hid); ?>" data-book data-api="<?php echo esc_url(mvn_booking_api()); ?>" data-contact="<?php echo esc_attr(mvn_contact_href()); ?>">
  <div class="container">
    <div class="book-grid">
      <div class="book-info">
        <?php if (mvs($s, 'eyebrow') !== '') : ?><p class="eyebrow"><?php echo esc_html((string) mvs($s, 'eyebrow')); ?></p><?php endif; ?>
        <h2 id="<?php echo esc_attr($hid); ?>"><?php echo esc_html(mvn_tokens((string) mvs($s, 'title'))); ?></h2>
        <p class="lead"><?php echo esc_html(mvn_tokens((string) mvs($s, 'intro'))); ?></p>
        <?php if ($points) : ?>
        <ul class="book-points">
          <?php foreach ($points as $k => $pt) : ?><li><span class="bp-ic"><?php echo mvn_icon($icons[$k % 3]); ?></span><span><?php echo esc_html(mvn_tokens($pt)); ?></span></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <div class="book-call">
          <p><strong><?php echo esc_html((string) mvs($s, 'call_title')); ?></strong> <?php echo esc_html((string) mvs($s, 'call_text')); ?></p>
          <a class="btn btn-secondary" href="<?php echo esc_url(mvn_contact_href()); ?>" data-book-skip><?php echo esc_html((string) mvs($s, 'call_btn')); ?></a>
        </div>
        <div class="book-call">
          <p><strong>Masz już karnet?</strong> Zobacz grafik i zapisz się na zajęcia.</p>
          <a class="btn btn-secondary" href="<?php echo esc_url(home_url('/grafik/')); ?>" data-book-skip>Zobacz grafik zajęć</a>
        </div>
      </div>

      <div class="book-card" aria-live="polite">
        <div class="bk-state bk-loading" data-state="loading">
          <p class="bk-step">Wybierz dzień</p>
          <div class="bk-skel"><i></i><i></i><i></i><i></i></div>
          <p class="bk-step">Wybierz godzinę</p>
          <div class="bk-skel bk-skel-t"><i></i><i></i><i></i><i></i></div>
        </div>

        <form class="bk-state bk-form" data-state="form" hidden novalidate>
          <div class="bk-flash" hidden></div>
          <p class="bk-step"><span>1</span>Wybierz dzień</p>
          <div class="bk-days" role="radiogroup" aria-label="Dzień"></div>
          <p class="bk-step"><span>2</span>Wybierz godzinę</p>
          <div class="bk-times" role="radiogroup" aria-label="Godzina"></div>
          <div class="bk-data" hidden>
            <p class="bk-step"><span>3</span>Twoje dane</p>
            <div class="bk-fields">
              <div class="field"><label for="bk-name">Imię i nazwisko</label><input id="bk-name" name="name" type="text" autocomplete="name" maxlength="80" required><small class="err" aria-live="polite"></small></div>
              <div class="field"><label for="bk-phone">Telefon</label><input id="bk-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" required><small class="err" aria-live="polite"></small></div>
              <div class="field bk-wide"><label for="bk-email">E-mail <span class="opt">(potwierdzenie płatności)</span></label><input id="bk-email" name="email" type="email" autocomplete="email" maxlength="120" required><small class="err" aria-live="polite"></small></div>
            </div>
            <div class="field check bk-consent"><input id="bk-zgoda" type="checkbox" name="zgoda" value="1" required><label for="bk-zgoda"><?php echo wp_kses(mvn_booking_consent(), ['a' => ['href' => [], 'target' => [], 'rel' => []]]); ?></label><small class="err" aria-live="polite"></small></div>
            <div class="hp" aria-hidden="true"><label>Strona www<input name="website" tabindex="-1" autocomplete="off"></label></div>
          </div>
          <div class="bk-sum">
            <p class="bk-pick">Wybierz termin, żeby przejść dalej.</p>
            <button type="submit" class="btn btn-primary bk-pay" disabled><span>Przejdź do płatności</span></button>
          </div>
          <p class="bk-hint"><?php echo esc_html((string) mvs($s, 'hint')); ?></p>
        </form>

        <div class="bk-state bk-msg" data-state="msg" hidden>
          <span class="bk-msg-ic"><?php echo mvn_icon('calendar'); ?></span>
          <h3 class="bk-msg-t"></h3>
          <p class="bk-msg-p"></p>
          <div class="bk-msg-a"></div>
        </div>
      </div>
    </div>
  </div>
</section>
