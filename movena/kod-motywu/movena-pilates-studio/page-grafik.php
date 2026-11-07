<?php
/**
 * Template Name: Grafik zajęć i zapisy
 * Publiczny grafik z wolnymi miejscami. Klient z karnetem zapisuje się podając e-mail — dostaje link potwierdzający (bez logowania do CRM).
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$phone = (string) mvo('phone_display');
$tel = (string) mvo('phone_tel');
$live = mvn_grafik_on();
?>
<main id="main">
<section class="section gr" aria-labelledby="gr-h">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Grafik zajęć</p>
      <h1 id="gr-h" class="h2">Wybierz termin i zapisz się</h1>
      <p class="lead">Pilates na reformerach w grupach do 4 osób. Masz karnet? Zapis zajmuje chwilę — bez zakładania konta i bez hasła.</p>
    </div>

<?php if (!$live && (mvn_fitssey_mode() || (string) mvo('grafik_mode') === 'fitssey')) : ?>
    <div class="gr-card gr-msg">
      <span class="bk-msg-ic"><?php echo mvn_icon('calendar'); ?></span>
      <h2 class="bk-msg-t">Zapisy online</h2>
      <p class="bk-msg-p">Grafik, rezerwacja i płatność działają w systemie zapisów studia. Wybierz termin, zapisz się i przyjdź na reformer.</p>
      <div class="bk-msg-a">
        <a class="btn btn-primary" href="<?php echo esc_url(mvn_fitssey_url('schedule')); ?>" target="_blank" rel="noopener" data-track="click_fitssey" data-offer-type="schedule">Przejdź do grafiku</a>
        <a class="btn btn-secondary" href="tel:<?php echo esc_attr($tel); ?>">Zadzwoń: <?php echo esc_html($phone); ?></a>
      </div>
    </div>
<?php elseif (!$live) : ?>
    <div class="gr-card gr-msg">
      <span class="bk-msg-ic"><?php echo mvn_icon('calendar'); ?></span>
      <h2 class="bk-msg-t">Zapisy przez telefon</h2>
      <p class="bk-msg-p">Zadzwoń, a dobierzemy termin zajęć i zapiszemy Cię od ręki.</p>
      <div class="bk-msg-a"><a class="btn btn-primary" href="tel:<?php echo esc_attr($tel); ?>">Zadzwoń: <?php echo esc_html($phone); ?></a></div>
    </div>
<?php else : ?>
    <div class="gr-grid" data-grafik data-api="<?php echo esc_url(mvn_booking_api()); ?>" data-phone="<?php echo esc_attr($phone); ?>" data-tel="<?php echo esc_attr($tel); ?>">
      <div class="gr-card" aria-live="polite">
        <div class="gr-state" data-state="loading">
          <div class="bk-skel"><i></i><i></i><i></i><i></i></div>
          <div class="bk-skel bk-skel-t"><i></i><i></i><i></i><i></i></div>
        </div>
        <div class="gr-state" data-state="ready" hidden>
          <p class="bk-step"><span>1</span>Wybierz dzień</p>
          <div class="bk-days" role="radiogroup" aria-label="Dzień"></div>
          <p class="bk-step"><span>2</span>Wybierz zajęcia</p>
          <ul class="gr-list"></ul>
        </div>
        <div class="gr-state gr-msg" data-state="msg" hidden>
          <span class="bk-msg-ic"><?php echo mvn_icon('calendar'); ?></span>
          <h2 class="bk-msg-t"></h2>
          <p class="bk-msg-p"></p>
          <div class="bk-msg-a"></div>
        </div>
      </div>

      <aside class="gr-side">
        <div class="gr-info">
          <h2 class="gr-info-t">Jak się zapisać</h2>
          <ol class="gr-steps">
            <li>Wybierz dzień i zajęcia.</li>
            <li>Podaj e-mail, z którym kupujesz karnet w studiu.</li>
            <li>Kliknij link, który do Ciebie wyślemy — i gotowe.</li>
          </ol>
          <p class="gr-fine">Zapis odejmuje jedno wejście z karnetu. Odwołać możesz najpóźniej 12 godzin przed zajęciami — wtedy wejście wraca.</p>
        </div>
        <div class="gr-info">
          <h2 class="gr-info-t">Pierwszy raz w studiu?</h2>
          <p>Zarezerwuj zajęcia Poznaj REFORMER — pierwsze zajęcia w promocyjnej cenie, płatność online.</p>
          <a class="btn btn-secondary" href="<?php echo esc_url(home_url('/#rezerwacja')); ?>">Zarezerwuj pierwsze zajęcia</a>
        </div>
        <div class="gr-info">
          <h2 class="gr-info-t">Nie masz jeszcze karnetu?</h2>
          <p>Wybierz pakiet, a potem zapisz się na dowolne zajęcia.</p>
          <a class="btn btn-secondary" href="<?php echo esc_url(home_url('/#pricing')); ?>">Zobacz cennik</a>
        </div>
      </aside>
    </div>
<?php endif; ?>
  </div>
</section>
</main>
<?php get_footer(); ?>
