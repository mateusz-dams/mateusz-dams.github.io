<?php
/**
 * Stopka (wspólna dla wszystkich stron) + dolny pasek kontaktowy na telefonie.
 */
if (!defined('ABSPATH')) {
    exit;
}
$phone_tel = (string) mvo('phone_tel');
$phone     = (string) mvo('phone_display');
$brand     = (string) mvo('brand');
$company   = trim((string) mvo('company_name')) !== '' ? (string) mvo('company_name') : $brand;
$addr      = trim(mvo('address') . ', ' . mvo('city'), ' ,');
$route     = trim((string) mvo('maps_url'));
$ig        = trim((string) mvo('instagram'));
$fb        = trim((string) mvo('facebook'));
$wa        = mvn_wa_url();
$menu      = mvn_menu_items(); // takie samo menu na każdej stronie
$legalBits = array_filter([trim((string) mvo('company_address')) !== '' ? (string) mvo('company_address') : '', trim((string) mvo('nip')) !== '' ? 'NIP: ' . mvo('nip') : '', trim((string) mvo('regon')) !== '' ? 'REGON: ' . mvo('regon') : '', trim((string) mvo('krs')) !== '' ? 'KRS: ' . mvo('krs') : '']);
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand"><img src="<?php echo esc_url(mvn_logo_url(true)); ?>" alt="<?php echo esc_attr($brand); ?>" class="brand-full-logo" loading="lazy" width="150" height="72"></div>
        <p class="footer-tagline"><?php echo esc_html((string) mvo('foot_tagline')); ?></p>
      </div>
      <?php if ($menu) : ?>
      <div class="footer-col"><h4>Nawigacja</h4>
        <ul><?php foreach ($menu as $item) : ?><li><a href="<?php echo esc_url(mvn_anchor($item['href'])); ?>"><?php echo esc_html($item['label']); ?></a></li><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>
      <div class="footer-col"><h4>Kontakt</h4>
        <ul>
          <li><?php if ($route !== '') : ?><a href="<?php echo esc_url($route); ?>" target="_blank" rel="noopener" data-track="click_directions"><?php echo esc_html($addr); ?></a><?php else : ?><?php echo esc_html($addr); ?><?php endif; ?></li>
          <li><a href="tel:<?php echo esc_attr($phone_tel); ?>" data-track="click_phone"><?php echo esc_html($phone); ?></a></li>
          <?php if (trim((string) mvo('email')) !== '') : ?><li><a href="mailto:<?php echo esc_attr((string) mvo('email')); ?>"><?php echo esc_html((string) mvo('email')); ?></a></li><?php endif; ?>
          <?php if ($ig !== '') : ?><li><a href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener" data-track="click_social" data-network="instagram">Instagram</a></li><?php endif; ?>
          <?php if ($fb !== '') : ?><li><a href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener" data-track="click_social" data-network="facebook">Facebook</a></li><?php endif; ?>
          <?php if ($wa !== '') : ?><li><a href="<?php echo esc_url($wa); ?>" target="_blank" rel="noopener">WhatsApp</a></li><?php endif; ?>
        </ul>
      </div>
      <div class="footer-col"><h4>Zapisy</h4>
        <ul>
          <li><a href="<?php echo esc_url(mvn_fitssey_url('schedule')); ?>">Zapisz się na zajęcia</a></li>
          <li><a href="<?php echo esc_url(mvn_fitssey_url('pricing')); ?>">Cennik pakietów</a></li>
          <li><a href="<?php echo esc_url(mvn_fitssey_url('login')); ?>" target="_blank" rel="noopener">Panel klientki</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($company); ?><?php echo substr($company, -1) === '.' ? '' : '.'; ?> Wszelkie prawa zastrzeżone.<?php if ($legalBits) : ?> <span class="footer-legal-data"><?php echo esc_html(implode(' · ', $legalBits)); ?></span><?php endif; ?></span>
      <ul>
        <li><a href="<?php echo esc_url(mvn_legal_url('regulamin')); ?>">Regulamin</a></li>
        <li><a href="<?php echo esc_url(mvn_legal_url('polityka-prywatnosci')); ?>">Polityka prywatności</a></li>
        <li><a href="<?php echo esc_url(mvn_legal_url('polityka-cookies')); ?>">Polityka cookies</a></li>
        <?php if (mvn_tracking_on()) : ?><li><a href="#ustawienia-cookies" data-cookie-settings>Ustawienia cookies</a></li><?php endif; ?>
      </ul>
    </div>
  </div>
</footer>

<div class="mobile-sticky-cta" role="group" aria-label="Szybki kontakt">
  <a class="btn btn-secondary" href="tel:<?php echo esc_attr($phone_tel); ?>" data-track="click_phone"><?php echo mvn_icon('phone'); ?><span><?php echo esc_html((string) mvo('mobile_call')); ?></span></a>
  <?php echo mvn_btn((string) mvo('mobile_cta'), (string) mvo('mobile_cta_cel') ?: 'fitssey:schedule', 'btn btn-primary'); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
