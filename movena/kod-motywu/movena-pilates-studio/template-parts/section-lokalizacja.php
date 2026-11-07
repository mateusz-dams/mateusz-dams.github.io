<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$addr = trim(mvo('address') . ', ' . mvo('city'), ' ,');
$embed = trim((string) mvo('maps_embed'));
$route = trim((string) mvo('maps_url'));
$ig = trim((string) mvo('instagram'));
$fb = trim((string) mvo('facebook'));
$wa = mvn_wa_url();
?>
<!-- LOKALIZACJA -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <?php echo mvn_head($s); ?>
    <div class="location-grid">
      <div class="location-map" id="map-frame" data-src="<?php echo esc_url($embed); ?>" data-title="<?php echo esc_attr('Mapa — ' . mvo('brand') . ', ' . $addr); ?>">
        <div class="map-placeholder">
          <?php echo mvn_icon('pin'); ?>
          <p><?php echo esc_html($addr); ?></p>
          <?php if ($embed !== '') : ?><button type="button" class="btn btn-secondary btn-sm" id="map-load">Pokaż mapę</button>
          <span class="map-note">Mapa Google ładuje się po kliknięciu.</span><?php endif; ?>
        </div>
      </div>
      <div class="location-details">
        <div class="location-row"><?php echo mvn_icon('pin'); ?><div><strong>Adres</strong><?php if ($route !== '') : ?><a href="<?php echo esc_url($route); ?>" target="_blank" rel="noopener" data-track="click_directions"><?php echo esc_html($addr); ?></a><?php else : ?><span class="value"><?php echo esc_html($addr); ?></span><?php endif; ?></div></div>
        <div class="location-row"><?php echo mvn_icon('phone'); ?><div><strong>Telefon</strong><a href="tel:<?php echo esc_attr((string) mvo('phone_tel')); ?>"><?php echo esc_html((string) mvo('phone_display')); ?></a></div></div>
        <?php if (trim((string) mvo('email')) !== '') : ?><div class="location-row"><?php echo mvn_icon('mail'); ?><div><strong>E-mail</strong><a href="mailto:<?php echo esc_attr((string) mvo('email')); ?>"><?php echo esc_html((string) mvo('email')); ?></a></div></div><?php endif; ?>
        <?php if ((array) mvo('hours')) : ?><div class="location-row"><?php echo mvn_icon('clock'); ?><div><strong>Godziny otwarcia</strong><span class="value"><?php echo mvn_hours_br(); ?></span></div></div><?php endif; ?>
        <?php if ($ig !== '' || $fb !== '' || $wa !== '') : ?>
        <div class="location-row"><?php echo mvn_icon('share'); ?><div><strong>Social media</strong>
          <div class="social-row">
            <?php if ($ig !== '') : ?><a class="social-btn" href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener" aria-label="Instagram" data-track="click_social" data-network="instagram"><?php echo mvn_icon('ig'); ?></a><?php endif; ?>
            <?php if ($fb !== '') : ?><a class="social-btn" href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener" aria-label="Facebook" data-track="click_social" data-network="facebook"><?php echo mvn_icon('fb'); ?></a><?php endif; ?>
            <?php if ($wa !== '') : ?><a class="social-btn" href="<?php echo esc_url($wa); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?php echo mvn_icon('wa'); ?></a><?php endif; ?>
          </div></div></div>
        <?php endif; ?>
        <?php echo mvn_btn((string) mvs($s, 'btn'), mvs($s, 'btn_cel', 'fitssey:schedule'), 'btn btn-primary btn-block'); ?>
      </div>
    </div>
  </div>
</section>
