<?php
/**
 * Stopka (wspólna dla wszystkich stron) + dolny pasek kontaktowy na telefonie.
 */
if (!defined('ABSPATH')) {
    exit;
}
$phone_tel = (string) pmo('phone_tel');
$phone     = (string) pmo('phone_display');
$wa        = pmbs_wa_url();
$brand     = (string) pmo('brand');
$company   = (string) pmo('company_name');
?>
<footer class="site-footer">
  <div class="wrap foot<?php echo pmbs_menu_items() ? "" : " foot-3"; ?>">
    <div class="foot-brand">
      <img src="<?php echo esc_url(pmbs_logo_url()); ?>" alt="<?php echo esc_attr($brand); ?>" width="110" height="80" loading="lazy">
      <p><?php echo esc_html((string) pmo('foot_tagline')); ?></p>
      <div class="socials">
        <?php if (pmo('facebook')) : ?><a href="<?php echo esc_url((string) pmo('facebook')); ?>" rel="noopener" target="_blank" aria-label="Facebook"><?php echo pmbs_icon('fb'); ?></a><?php endif; ?>
        <?php if (pmo('instagram')) : ?><a href="<?php echo esc_url((string) pmo('instagram')); ?>" rel="noopener" target="_blank" aria-label="Instagram"><?php echo pmbs_icon('ig'); ?></a><?php endif; ?>
        <a href="<?php echo esc_url($wa); ?>" rel="noopener" target="_blank" aria-label="WhatsApp"><?php echo pmbs_icon('wa'); ?></a>
      </div>
    </div>
    <div class="foot-data">
      <h3>Dane firmy</h3>
      <ul>
        <li><?php echo pmbs_icon('award'); ?><span><?php echo esc_html($company); ?></span></li>
        <li><?php echo pmbs_icon('pin'); ?><span><?php echo esc_html(pmo('address') . ', ' . pmo('city')); ?></span></li>
        <li><?php echo pmbs_icon('phone'); ?><a href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo esc_html($phone); ?></a></li>
        <li><?php echo pmbs_icon('mail'); ?><a href="mailto:<?php echo esc_attr((string) pmo('email')); ?>"><?php echo esc_html((string) pmo('email')); ?></a></li>
        <li><?php echo pmbs_icon('clock'); ?><span><?php echo pmbs_hours_br(); ?></span></li>
        <li class="reg"><span>KRS: <?php echo esc_html((string) pmo('krs')); ?><br>NIP: <?php echo esc_html((string) pmo('nip')); ?><br>REGON: <?php echo esc_html((string) pmo('regon')); ?></span></li>
      </ul>
    </div>
    <?php $menu = pmbs_menu_items(); if ($menu) : ?>
    <nav aria-label="Stopka"><h3>Menu</h3>
      <ul>
        <?php foreach ($menu as $item) : ?>
        <li><a href="<?php echo esc_url(pmbs_anchor($item['href'])); ?>"><?php echo esc_html($item['label']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <?php endif; ?>
    <div>
      <h3>Oferta</h3>
      <ul>
        <?php foreach ((array) pmo('foot_offer') as $item) : if (empty($item['label'])) { continue; } ?>
        <li><a href="<?php echo esc_url(pmbs_anchor(pmbs_href($item['cel'] ?? '', pmbs_contact_href()))); ?>"<?php echo !empty($item['form']) ? ' data-form="' . esc_attr($item['form']) . '"' : ''; ?>><?php echo esc_html($item['label']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="foot-bottom">
    <div class="wrap copy">
      <p>© <?php echo esc_html(wp_date('Y')); ?> <strong><?php echo esc_html($company); ?></strong> Wszelkie prawa zastrzeżone.</p>
      <ul class="legal">
        <li><a href="<?php echo esc_url(pmbs_legal_url('polityka-prywatnosci')); ?>">Polityka prywatności</a></li>
        <li><a href="<?php echo esc_url(pmbs_legal_url('regulamin')); ?>">Regulamin</a></li>
        <li><a href="<?php echo esc_url(pmbs_legal_url('cookies')); ?>">Pliki cookies</a></li>
        <?php if (pmbs_tracking_on()) : ?><li><a href="#ustawienia-cookies" data-cookie-settings>Ustawienia cookies</a></li><?php endif; ?>
      </ul>
    </div>
  </div>
</footer>

<?php if (pmbs_is_site_page()) : ?>
<div class="mbar" role="group" aria-label="Szybki kontakt">
  <a class="btn btn-ghost" href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo pmbs_icon('phone') . esc_html((string) pmo('mobile_call')); ?></a>
  <a class="btn btn-ghost btn-wa" href="<?php echo esc_url($wa); ?>" rel="noopener" target="_blank" aria-label="Napisz na WhatsAppie"><?php echo pmbs_icon('wa'); ?></a>
  <a class="btn btn-primary" href="<?php echo esc_url(pmbs_contact_href()); ?>" data-form="<?php echo esc_attr(pmbs_form_default()); ?>"><?php echo esc_html((string) pmo('mobile_cta')); ?></a>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
