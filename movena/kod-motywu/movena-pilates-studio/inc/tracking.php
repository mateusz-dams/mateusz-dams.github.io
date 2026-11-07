<?php
/**
 * Analityka i reklamy: Google Analytics 4 + Meta Pixel ładowane dopiero po zgodzie (baner cookies).
 */
if (!defined('ABSPATH')) {
    exit;
}

function mvn_gtm_id(): string
{
    $v = strtoupper(trim((string) mvo('gtm_id')));
    return preg_match('/^GTM-[A-Z0-9]{4,12}$/', $v) ? $v : '';
}

/** W trybie GTM kontener obsługuje GA4, Meta, Google Ads i Hotjar; motyw ładuje je bezpośrednio tylko bez GTM. */
function mvn_direct(): bool
{
    return mvn_gtm_id() === '';
}

function mvn_ga_id(): string
{
    $v = trim((string) mvo('ga4'));
    return mvn_direct() && preg_match('/^G-[A-Z0-9]{4,20}$/i', $v) ? $v : '';
}

function mvn_ads_id(): string
{
    $v = trim((string) mvo('ads_id'));
    return mvn_direct() && preg_match('/^AW-\d{6,12}$/i', $v) ? strtoupper($v) : '';
}

function mvn_meta_id(): string
{
    $v = trim((string) mvo('meta_pixel'));
    return mvn_direct() && preg_match('/^\d{8,20}$/', $v) ? $v : '';
}

function mvn_hotjar_id(): string
{
    $v = trim((string) mvo('hotjar_id'));
    return mvn_direct() && preg_match('/^\d{4,10}$/', $v) ? $v : '';
}

/** Czy jest cokolwiek analitycznego (GA4, Hotjar) lub w trybie GTM kontener. */
function mvn_has_analytics(): bool
{
    return mvn_gtm_id() !== '' || mvn_ga_id() !== '' || mvn_hotjar_id() !== '';
}

/** Czy są narzędzia marketingowe (Meta Pixel, Google Ads) lub kontener GTM. */
function mvn_has_marketing(): bool
{
    return mvn_gtm_id() !== '' || mvn_meta_id() !== '' || mvn_ads_id() !== '';
}

function mvn_tracking_on(): bool
{
    return mvn_has_analytics() || mvn_has_marketing();
}

/** Meta tagi weryfikacyjne (Search Console, Bing, domena Meta). */
add_action('wp_head', static function () {
    if (!mvn_is_site_page()) {
        return;
    }
    foreach (['gsc_verification' => 'google-site-verification', 'bing_verification' => 'msvalidate.01', 'fb_domain_verification' => 'facebook-domain-verification'] as $opt => $name) {
        $v = trim((string) mvo($opt));
        if ($v !== '' && preg_match('/^[\w\-]{8,100}$/', $v)) {
            echo '<meta name="' . esc_attr($name) . '" content="' . esc_attr($v) . '">' . "\n";
        }
    }
}, 1);

add_action('wp_enqueue_scripts', static function () {
    if (!mvn_tracking_on() || is_customize_preview()) {
        return;
    }
    $js = MVN_DIR . '/assets/js/tracking.js';
    wp_enqueue_script('mvn-tracking', MVN_URI . '/assets/js/tracking.js', [], file_exists($js) ? (string) filemtime($js) : MVN_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
    wp_localize_script('mvn-tracking', 'MVN_TRACK', [
        'gtm'      => mvn_gtm_id(),
        'ga4'      => mvn_ga_id(),
        'ads'      => mvn_ads_id(),
        'adsLead'  => preg_match('/^[\w\-]{6,60}$/', (string) mvo('ads_label_lead')) ? (string) mvo('ads_label_lead') : '',
        'adsContact' => preg_match('/^[\w\-]{6,60}$/', (string) mvo('ads_label_contact')) ? (string) mvo('ads_label_contact') : '',
        'adsFitssey' => preg_match('/^[\w\-]{6,60}$/', (string) mvo('ads_label_fitssey')) ? (string) mvo('ads_label_fitssey') : '',
        'meta'     => mvn_meta_id(),
        'hotjar'   => mvn_hotjar_id(),
        'hasA'     => mvn_has_analytics(),
        'hasM'     => mvn_has_marketing(),
    ]);
}, 21);

/** Baner zgody na cookies (tylko gdy skonfigurowano GA4 lub Meta Pixel). */
add_action('wp_footer', static function () {
    if (!mvn_tracking_on() || is_customize_preview()) {
        return;
    }
    $goals = [];
    if (mvn_has_analytics()) {
        $goals[] = 'zbierać statystyki odwiedzin (Google Analytics' . (mvn_hotjar_id() !== '' ? ', Hotjar' : '') . ')';
    }
    if (mvn_has_marketing()) {
        $goals[] = 'mierzyć skuteczność reklam (Meta, Google Ads)';
    }
    $rows = '';
    if (mvn_has_analytics()) {
        $rows .= '<label class="ck-row"><span class="ck-info"><strong>Analityczne</strong><span>Statystyki odwiedzin strony (Google Analytics' . (mvn_hotjar_id() !== '' ? ', Hotjar: mapy kliknięć' : '') . ').</span></span>'
            . '<input type="checkbox" id="ck-a" class="ck-sw"><span class="ck-track" aria-hidden="true"></span></label>';
    }
    if (mvn_has_marketing()) {
        $rows .= '<label class="ck-row"><span class="ck-info"><strong>Marketingowe</strong><span>Pomiar skuteczności reklam na Facebooku, Instagramie i w Google (Meta Pixel, Google Ads).</span></span>'
            . '<input type="checkbox" id="ck-m" class="ck-sw"><span class="ck-track" aria-hidden="true"></span></label>';
    }
    ?>
<div class="ck-banner" id="ck-banner" role="region" aria-label="Zgoda na pliki cookies" hidden>
  <div class="ck-card">
    <p class="ck-title">Twoja prywatność</p>
    <p class="ck-text">Chcemy używać plików cookies, aby <?php echo esc_html(implode(' i ', $goals)); ?>. Bez Twojej zgody nie zapisujemy ich na Twoim urządzeniu, a wybór możesz zmienić w każdej chwili. <a href="<?php echo esc_url(mvn_legal_url('polityka-cookies')); ?>">Polityka cookies</a></p>
    <div class="ck-panel" id="ck-panel" hidden>
      <div class="ck-row ck-fixed"><span class="ck-info"><strong>Niezbędne</strong><span>Zapamiętanie Twojego wyboru w tej przeglądarce.</span></span><span class="ck-always">Zawsze aktywne</span></div>
      <?php echo $rows; // phpcs:ignore WordPress.Security.EscapeOutput -- treść statyczna zbudowana wyżej ?>
    </div>
    <div class="ck-actions">
      <button type="button" class="btn btn-ghost btn-sm" data-ck="reject">Odrzuć wszystkie</button>
      <button type="button" class="btn btn-ghost btn-sm" data-ck="settings" id="ck-toggle">Ustawienia</button>
      <button type="button" class="btn btn-primary btn-sm" data-ck="accept">Akceptuję wszystkie</button>
    </div>
  </div>
</div>
    <?php
}, 50);
