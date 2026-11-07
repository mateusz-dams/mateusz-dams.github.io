<?php
/**
 * Analityka i reklamy: Google Analytics 4 + Meta Pixel ładowane dopiero po zgodzie (baner cookies).
 */
if (!defined('ABSPATH')) {
    exit;
}

function pmbs_gtm_id(): string
{
    $v = strtoupper(trim((string) pmo('gtm_id')));
    return preg_match('/^GTM-[A-Z0-9]{4,12}$/', $v) ? $v : '';
}

/** W trybie GTM kontener obsługuje GA4, Meta, Google Ads i Hotjar; motyw ładuje je bezpośrednio tylko bez GTM. */
function pmbs_direct(): bool
{
    return pmbs_gtm_id() === '';
}

function pmbs_ga_id(): string
{
    $v = trim((string) pmo('ga4'));
    return pmbs_direct() && preg_match('/^G-[A-Z0-9]{4,20}$/i', $v) ? $v : '';
}

function pmbs_ads_id(): string
{
    $v = trim((string) pmo('ads_id'));
    return pmbs_direct() && preg_match('/^AW-\d{6,12}$/i', $v) ? strtoupper($v) : '';
}

function pmbs_meta_id(): string
{
    $v = trim((string) pmo('meta_pixel'));
    return pmbs_direct() && preg_match('/^\d{8,20}$/', $v) ? $v : '';
}

function pmbs_hotjar_id(): string
{
    $v = trim((string) pmo('hotjar_id'));
    return pmbs_direct() && preg_match('/^\d{4,10}$/', $v) ? $v : '';
}

/** Czy jest cokolwiek analitycznego (GA4, Hotjar) lub w trybie GTM kontener. */
function pmbs_has_analytics(): bool
{
    return pmbs_gtm_id() !== '' || pmbs_ga_id() !== '' || pmbs_hotjar_id() !== '';
}

/** Czy są narzędzia marketingowe (Meta Pixel, Google Ads) lub kontener GTM. */
function pmbs_has_marketing(): bool
{
    return pmbs_gtm_id() !== '' || pmbs_meta_id() !== '' || pmbs_ads_id() !== '';
}

function pmbs_tracking_on(): bool
{
    return pmbs_has_analytics() || pmbs_has_marketing();
}

/** Meta tagi weryfikacyjne (Search Console, Bing, domena Meta). */
add_action('wp_head', static function () {
    if (!pmbs_is_site_page()) {
        return;
    }
    foreach (['gsc_verification' => 'google-site-verification', 'bing_verification' => 'msvalidate.01', 'fb_domain_verification' => 'facebook-domain-verification'] as $opt => $name) {
        $v = trim((string) pmo($opt));
        if ($v !== '' && preg_match('/^[\w\-]{8,100}$/', $v)) {
            echo '<meta name="' . esc_attr($name) . '" content="' . esc_attr($v) . '">' . "\n";
        }
    }
}, 1);

add_action('wp_enqueue_scripts', static function () {
    if (!pmbs_tracking_on() || is_customize_preview()) {
        return;
    }
    $js = PMBS_DIR . '/assets/js/tracking.js';
    wp_enqueue_script('pmbs-tracking', PMBS_URI . '/assets/js/tracking.js', [], file_exists($js) ? (string) filemtime($js) : PMBS_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
    wp_localize_script('pmbs-tracking', 'PMBS_TRACK', [
        'gtm'      => pmbs_gtm_id(),
        'ga4'      => pmbs_ga_id(),
        'ads'      => pmbs_ads_id(),
        'adsLead'  => preg_match('/^[\w\-]{6,60}$/', (string) pmo('ads_label_lead')) ? (string) pmo('ads_label_lead') : '',
        'adsContact' => preg_match('/^[\w\-]{6,60}$/', (string) pmo('ads_label_contact')) ? (string) pmo('ads_label_contact') : '',
        'meta'     => pmbs_meta_id(),
        'hotjar'   => pmbs_hotjar_id(),
        'hasA'     => pmbs_has_analytics(),
        'hasM'     => pmbs_has_marketing(),
    ]);
}, 21);

/** Baner zgody na cookies (tylko gdy skonfigurowano GA4 lub Meta Pixel). */
add_action('wp_footer', static function () {
    if (!pmbs_tracking_on() || is_customize_preview()) {
        return;
    }
    $goals = [];
    if (pmbs_has_analytics()) {
        $goals[] = 'zbierać statystyki odwiedzin (Google Analytics' . (pmbs_hotjar_id() !== '' ? ', Hotjar' : '') . ')';
    }
    if (pmbs_has_marketing()) {
        $goals[] = 'mierzyć skuteczność reklam (Meta, Google Ads)';
    }
    $rows = '';
    if (pmbs_has_analytics()) {
        $rows .= '<label class="ck-row"><span class="ck-info"><strong>Analityczne</strong><span>Statystyki odwiedzin strony (Google Analytics' . (pmbs_hotjar_id() !== '' ? ', Hotjar: mapy kliknięć' : '') . ').</span></span>'
            . '<input type="checkbox" id="ck-a" class="ck-sw"><span class="ck-track" aria-hidden="true"></span></label>';
    }
    if (pmbs_has_marketing()) {
        $rows .= '<label class="ck-row"><span class="ck-info"><strong>Marketingowe</strong><span>Pomiar skuteczności reklam na Facebooku, Instagramie i w Google (Meta Pixel, Google Ads).</span></span>'
            . '<input type="checkbox" id="ck-m" class="ck-sw"><span class="ck-track" aria-hidden="true"></span></label>';
    }
    ?>
<div class="ck-banner" id="ck-banner" role="region" aria-label="Zgoda na pliki cookies" hidden>
  <div class="ck-card">
    <p class="ck-title">Twoja prywatność</p>
    <p class="ck-text">Chcemy używać plików cookies, aby <?php echo esc_html(implode(' i ', $goals)); ?>. Bez Twojej zgody nie zapisujemy ich na Twoim urządzeniu, a wybór możesz zmienić w każdej chwili. <a href="<?php echo esc_url(pmbs_legal_url('cookies')); ?>">Polityka cookies</a></p>
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
