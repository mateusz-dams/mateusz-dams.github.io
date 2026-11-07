<?php
/**
 * SEO: tytuł, opis, Open Graph, favicon i dane strukturalne (JSON-LD).
 * Gdy aktywna jest wtyczka SEO (Yoast, Rank Math, AIOSEO, SEOPress), motyw nie dubluje jej znaczników.
 */
if (!defined('ABSPATH')) {
    exit;
}

function pmbs_seo_plugin_active(): bool
{
    return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION')
        || defined('SEOPRESS_VERSION') || defined('THE_SEO_FRAMEWORK_VERSION');
}

add_filter('pre_get_document_title', static function ($title) {
    if (pmbs_seo_plugin_active() || !is_front_page()) {
        return $title;
    }
    $v = trim((string) pmo('seo_title'));
    return $v !== '' ? $v : $title;
}, 20);

function pmbs_og_image_url(): string
{
    $id = pmo('og_image');
    if ($id && is_numeric($id) && ($u = wp_get_attachment_image_url((int) $id, 'full'))) {
        return $u;
    }
    return pmbs_theme_img('og-image.jpg');
}

/** Opis do meta description: strona główna z ustawień, podstrony z zajawki. */
function pmbs_meta_description(): string
{
    if (is_front_page()) {
        return trim((string) pmo('seo_description'));
    }
    if (is_singular()) {
        return trim(wp_strip_all_tags((string) get_the_excerpt()));
    }
    return '';
}

/** Dni tygodnia i godziny z pól „Godziny otwarcia” -> specyfikacja schema.org. */
function pmbs_opening_hours_spec(): array
{
    $map = ['pn' => 'Monday', 'wt' => 'Tuesday', 'śr' => 'Wednesday', 'sr' => 'Wednesday', 'czw' => 'Thursday', 'pt' => 'Friday', 'sb' => 'Saturday', 'nd' => 'Sunday'];
    $order = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $spec = [];
    foreach ((array) pmo('hours') as $r) {
        $days = mb_strtolower(trim((string) ($r['days'] ?? '')));
        $hours = (string) ($r['hours'] ?? '');
        if (!preg_match('/(\d{1,2}):(\d{2})\s*[–\-]\s*(\d{1,2}):(\d{2})/u', $hours, $t)) {
            continue;
        }
        $list = [];
        if (preg_match('/^(\p{L}+)\s*[–\-]\s*(\p{L}+)$/u', $days, $m) && isset($map[$m[1]], $map[$m[2]])) {
            $a = array_search($map[$m[1]], $order, true);
            $b = array_search($map[$m[2]], $order, true);
            for ($i = $a; $i <= $b; $i++) {
                $list[] = $order[$i];
            }
        } elseif (isset($map[$days])) {
            $list[] = $map[$days];
        }
        if ($list) {
            $spec[] = [
                '@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $list,
                'opens' => sprintf('%02d:%s', (int) $t[1], $t[2]), 'closes' => sprintf('%02d:%s', (int) $t[3], $t[4]),
            ];
        }
    }
    return $spec;
}

/** Mapa: właściwość danych strukturalnych → skąd pochodzi wartość (do podglądu w panelu). */
function pmbs_schema_sources(): array
{
    return [
        'name' => 'Dane i kontakt → Nazwa studia (lub nadpisanie w „Dane strukturalne”)',
        'url' => 'Adres strony głównej WordPressa',
        'image' => 'SEO → Obraz przy udostępnianiu',
        'logo' => 'Nagłówek i stopka → Logo',
        'hasMap' => 'Dane i kontakt → Link do Map Google',
        'description' => 'SEO → Opis strony głównej (lub nadpisanie)',
        'telephone' => 'Dane i kontakt → Telefon (do linku)',
        'email' => 'Dane i kontakt → E-mail',
        'address' => 'Dane i kontakt → Adres i Miasto',
        'sameAs' => 'Dane i kontakt → Facebook, Instagram + profile z „Dane strukturalne”',
        'areaServed' => 'Dane i kontakt → Miasto (lub nadpisanie)',
        'legalName' => 'Dane i kontakt → Pełna nazwa firmy',
        'taxID' => 'Dane i kontakt → NIP',
        'founder' => 'Dane i kontakt → Imię i nazwisko trenera',
        'openingHoursSpecification' => 'Dane i kontakt → Godziny otwarcia',
        'makesOffer' => 'Strona główna → Oferta (nazwy kart) i Cennik (trening wdrożeniowy)',
        'geo' => 'SEO → Szerokość i długość geograficzna',
        'priceRange' => 'SEO → Przedział cen',
        'aggregateRating' => 'SEO → Średnia ocena + Strona główna → Opinie (liczba)',
        'inLanguage' => 'Stałe: pl-PL',
        'publisher' => 'Automatycznie (powiązanie ze studiem)',
        'mainEntity' => 'Strona główna → FAQ (pytania i odpowiedzi)',
        '@type' => 'Dane strukturalne → Typ firmy',
        '@id' => 'Automatycznie',
    ];
}

/** Dodatkowy JSON-LD z panelu: zwraca [lista węzłów, komunikat błędu]. */
function pmbs_schema_extra(): array
{
    $raw = trim((string) pmo('schema_extra'));
    if ($raw === '') {
        return [[], ''];
    }
    $raw = preg_replace('~^\s*<script[^>]*>|</script>\s*$~i', '', $raw);
    $d = json_decode((string) $raw, true);
    if (!is_array($d)) {
        return [[], 'Własne dane: niepoprawny JSON, pominięto.'];
    }
    unset($d['@context']);
    $nodes = isset($d['@type']) ? [$d] : array_values(array_filter($d, 'is_array'));
    return [$nodes, ''];
}

/** Węzły @graph zgodnie z ustawieniami w zakładce „Dane strukturalne”. */
function pmbs_schema_graph(): array
{
    $on = static function (string $k): bool {
        $v = pmo($k);
        return $v === null || $v === '' ? true : (bool) (int) $v;
    };
    $home = home_url('/');
    $graph = [];

    if ($on('schema_business_on')) {
        $count = (int) pms(pmbs_first('opinie'), 'count', 0);
        $rating = trim((string) pmo('rating_value'));
        $type = (string) (pmo('schema_type') ?: 'SportsActivityLocation');
        $name = trim((string) pmo('schema_name')) !== '' ? (string) pmo('schema_name') : (string) pmo('brand');
        $desc = trim((string) pmo('schema_description')) !== '' ? (string) pmo('schema_description') : (string) pmo('seo_description');
        $area = trim((string) pmo('schema_area')) !== '' ? (string) pmo('schema_area') : (string) pmo('city');
        $same = array_filter([pmo('facebook'), pmo('instagram')]);
        foreach ((array) pmo('schema_same_as') as $r) {
            if (!empty($r['url'])) {
                $same[] = $r['url'];
            }
        }
        $biz = [
            '@type' => [$type, 'LocalBusiness'], '@id' => $home . '#studio', 'name' => $name, 'url' => $home,
            'image' => pmbs_og_image_url(), 'logo' => pmbs_logo_url(), 'hasMap' => pmo('maps_url'),
            'description' => $desc, 'telephone' => pmo('phone_tel'), 'email' => pmo('email'),
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => pmo('address'), 'addressLocality' => pmo('city'), 'addressCountry' => 'PL'],
            'sameAs' => array_values(array_unique($same)),
            'areaServed' => $area, 'legalName' => pmo('company_name'), 'taxID' => pmo('nip'),
            'founder' => ['@type' => 'Person', 'name' => pmo('trainer_name'), 'jobTitle' => 'Trener boksu'],
        ];
        if ($on('schema_hours_on') && ($h = pmbs_opening_hours_spec())) {
            $biz['openingHoursSpecification'] = $h;
        }
        if ($on('schema_offers_on')) {
            $offers = [];
            foreach (pmsa(pmbs_first('oferta'), 'cards') as $c) {
                if (!empty($c['title'])) {
                    $offers[] = ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => $c['title'], 'areaServed' => pmo('city')]];
                }
            }
            $offers[] = ['@type' => 'Offer', 'name' => 'Trening wdrożeniowy', 'price' => (string) (float) pmbs_onboarding(), 'priceCurrency' => 'PLN'];
            $biz['makesOffer'] = $offers;
        }
        if (is_numeric(pmo('geo_lat')) && is_numeric(pmo('geo_lng'))) {
            $biz['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float) pmo('geo_lat'), 'longitude' => (float) pmo('geo_lng')];
        }
        if (trim((string) pmo('price_range')) !== '') {
            $biz['priceRange'] = (string) pmo('price_range');
        }
        if ($count > 0 && is_numeric($rating) && (float) $rating >= 1 && (float) $rating <= 5) {
            $biz['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => (string) (float) $rating, 'reviewCount' => (string) $count, 'bestRating' => '5'];
        }
        $graph[] = $biz;
    }
    if ($on('schema_website_on')) {
        $site = ['@type' => 'WebSite', '@id' => $home . '#website', 'url' => $home, 'name' => pmo('brand'), 'inLanguage' => 'pl-PL'];
        if ($on('schema_business_on')) {
            $site['publisher'] = ['@id' => $home . '#studio'];
        }
        $graph[] = $site;
    }
    if ($on('schema_faq_on')) {
        $faq = [];
        foreach (pmsa(pmbs_first('faq'), 'items') as $r) {
            if (!empty($r['q'])) {
                $faq[] = ['@type' => 'Question', 'name' => $r['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $r['a'] ?? '']];
            }
        }
        if ($faq) {
            $graph[] = ['@type' => 'FAQPage', 'mainEntity' => $faq];
        }
    }
    [$extra] = pmbs_schema_extra();
    return array_merge($graph, $extra);
}

function pmbs_jsonld(): string
{
    $graph = pmbs_schema_graph();
    if (!$graph) {
        return '';
    }
    return wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/** HTML podglądu w panelu: tabela „właściwość → wartość → skąd” + lista tego, co NIE jest danymi strukturalnymi. */
function pmbs_schema_preview_html(): string
{
    $src = pmbs_schema_sources();
    $short = static function ($v): string {
        if (is_array($v)) {
            $v = wp_json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $v = (string) $v;
        return mb_strlen($v) > 110 ? mb_substr($v, 0, 110) . '…' : $v;
    };
    $h = '<div style="max-width:980px">';
    $graph = pmbs_schema_graph();
    if (!$graph) {
        $h .= '<p><strong>Wszystkie bloki są wyłączone.</strong> Strona nie wysyła danych strukturalnych.</p>';
    }
    foreach ($graph as $node) {
        $t = $node['@type'] ?? '';
        $t = is_array($t) ? implode(' + ', $t) : (string) $t;
        $h .= '<p style="margin:14px 0 4px"><strong style="font-size:14px">Blok: ' . esc_html($t) . '</strong></p>';
        $h .= '<table class="widefat striped" style="font-size:12px"><thead><tr><th style="width:170px">Właściwość</th><th>Wartość w danych</th><th style="width:300px">Skąd (gdzie edytować)</th></tr></thead><tbody>';
        foreach ($node as $k => $v) {
            if ($k === '@type' && $t === '') {
                continue;
            }
            $s = $src[$k] ?? 'Własne dane (JSON-LD) lub automatycznie';
            $empty = ($v === '' || $v === [] || $v === null);
            $h .= '<tr><td><code>' . esc_html((string) $k) . '</code></td><td>' . ($empty ? '<span style="color:#b32d2e">puste, Google tego nie użyje</span>' : esc_html($short($v))) . '</td><td>' . esc_html($s) . '</td></tr>';
        }
        $h .= '</tbody></table>';
    }
    [, $err] = pmbs_schema_extra();
    if ($err !== '') {
        $h .= '<p style="color:#b32d2e"><strong>' . esc_html($err) . '</strong></p>';
    }
    $h .= '<p style="margin:16px 0 4px"><strong>Czego NIE ma w danych strukturalnych</strong> (jest tylko widoczną treścią strony): ceny pakietów i grup, grafik zajęć, treści opinii klientów, opisy sekcji (jak trenujemy, KO Coach, inspiracje), partnerzy, zdjęcia z sali.</p>';
    $h .= '<p style="margin:6px 0 0">Sprawdzenie na żywo: <a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">test wyników z elementami rozszerzonymi Google</a> (po zapisaniu ustawień i wejściu na stronę). Podgląd odświeża się po zapisaniu.</p></div>';
    return $h;
}

add_action('wp_head', static function () {
    echo '<meta name="theme-color" content="#0a0a0a">' . "\n";

    if (!has_site_icon()) {
        $i = PMBS_URI . '/assets/img/';
        echo '<link rel="icon" href="' . esc_url($i . 'favicon.ico') . '" sizes="32x32">' . "\n";
        echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url($i . 'icons/favicon-32.png') . '">' . "\n";
        echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url($i . 'icons/icon-192.png') . '">' . "\n";
        echo '<link rel="apple-touch-icon" href="' . esc_url($i . 'icons/apple-touch-icon.png') . '">' . "\n";
    }

    if (!pmbs_seo_plugin_active()) {
        $desc = pmbs_meta_description();
        $title = wp_get_document_title();
        $url = is_front_page() ? home_url('/') : (is_singular() ? get_permalink() : home_url('/'));
        if ($desc !== '') {
            echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
        }
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:locale" content="pl_PL">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr((string) pmo('brand')) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        echo '<meta property="og:image" content="' . esc_url(pmbs_og_image_url()) . '">' . "\n";
        echo '<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">' . "\n";
        if (trim((string) pmo('og_image_alt')) !== '') {
            echo '<meta property="og:image:alt" content="' . esc_attr((string) pmo('og_image_alt')) . '">' . "\n";
        }
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        if ($desc !== '') {
            echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
        }
        echo '<meta name="twitter:image" content="' . esc_url(pmbs_og_image_url()) . '">' . "\n";
    }

    if (pmbs_is_site_page()) {
        if (($ld = pmbs_jsonld()) !== '') {
            echo '<script type="application/ld+json">' . $ld . '</script>' . "\n";
        }
        echo '<script>document.documentElement.classList.add("js");setTimeout(function(){if(!window.pmbsOk){document.documentElement.classList.remove("js")}},3000)</script>' . "\n";
    }
}, 2);
