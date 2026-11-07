<?php
/**
 * SEO: tytuł, opis, Open Graph, favicon i dane strukturalne (JSON-LD).
 * Gdy aktywna jest wtyczka SEO (Yoast, Rank Math, AIOSEO, SEOPress), motyw nie dubluje jej znaczników.
 */
if (!defined('ABSPATH')) {
    exit;
}

function mvn_seo_plugin_active(): bool
{
    return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION')
        || defined('SEOPRESS_VERSION') || defined('THE_SEO_FRAMEWORK_VERSION');
}

add_filter('pre_get_document_title', static function ($title) {
    if (mvn_seo_plugin_active() || !is_front_page()) {
        return $title;
    }
    $v = trim((string) mvo('seo_title'));
    return $v !== '' ? $v : $title;
}, 20);

function mvn_og_image_url(): string
{
    $id = mvo('og_image');
    if ($id && is_numeric($id) && ($u = wp_get_attachment_image_url((int) $id, 'full'))) {
        return $u;
    }
    return mvn_theme_img('og-image.jpg');
}

/** Opis do meta description: strona główna z ustawień, podstrony z zajawki. */
function mvn_meta_description(): string
{
    if (is_front_page()) {
        return trim((string) mvo('seo_description'));
    }
    if (is_singular()) {
        return trim(wp_strip_all_tags((string) get_the_excerpt()));
    }
    return '';
}

/** Dni tygodnia i godziny z pól „Godziny otwarcia” -> specyfikacja schema.org. */
function mvn_opening_hours_spec(): array
{
    $map = ['pn' => 'Monday', 'wt' => 'Tuesday', 'śr' => 'Wednesday', 'sr' => 'Wednesday', 'czw' => 'Thursday', 'pt' => 'Friday', 'sb' => 'Saturday', 'nd' => 'Sunday'];
    $order = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $spec = [];
    foreach ((array) mvo('hours') as $r) {
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
function mvn_schema_sources(): array
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
        'founder' => 'Dane i kontakt → Imię i nazwisko właścicielki (opcjonalnie)',
        'openingHoursSpecification' => 'Dane i kontakt → Godziny otwarcia',
        'makesOffer' => 'Ustawienia → Zajęcia i Cennik (pakiety, pierwszy trening)',
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
function mvn_schema_extra(): array
{
    $raw = trim((string) mvo('schema_extra'));
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
function mvn_schema_graph(): array
{
    $on = static function (string $k): bool {
        $v = mvo($k);
        return $v === null || $v === '' ? true : (bool) (int) $v;
    };
    $home = home_url('/');
    $graph = [];
    $landing = mvn_is_landing(); // landing reklamowy: tylko FAQ, dane firmy są na stronie głównej

    if (!$landing && $on('schema_business_on')) {
        $count = (int) mvs(mvn_first('opinie'), 'count', 0);
        $rating = trim((string) mvo('rating_value'));
        $type = (string) (mvo('schema_type') ?: 'ExerciseGym');
        $name = trim((string) mvo('schema_name')) !== '' ? (string) mvo('schema_name') : (string) mvo('brand');
        $desc = trim((string) mvo('schema_description')) !== '' ? (string) mvo('schema_description') : (string) mvo('seo_description');
        $area = trim((string) mvo('schema_area')) !== '' ? (string) mvo('schema_area') : (string) mvo('city');
        $same = array_filter([mvo('facebook'), mvo('instagram')]);
        foreach ((array) mvo('schema_same_as') as $r) {
            if (!empty($r['url'])) {
                $same[] = $r['url'];
            }
        }
        $biz = [
            '@type' => [$type, 'LocalBusiness'], '@id' => $home . '#studio', 'name' => $name, 'url' => $home,
            'image' => mvn_og_image_url(), 'logo' => mvn_logo_url(), 'hasMap' => mvo('maps_url'),
            'description' => $desc, 'telephone' => mvo('phone_tel'), 'email' => mvo('email'),
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => mvo('address'), 'addressLocality' => mvo('city'), 'addressCountry' => 'PL'],
            'sameAs' => array_values(array_unique($same)),
            'areaServed' => $area, 'legalName' => mvo('company_name'), 'taxID' => mvo('nip'),
        ];
        if (trim((string) mvo('founder_name')) !== '') {
            $biz['founder'] = ['@type' => 'Person', 'name' => mvo('founder_name'), 'jobTitle' => 'Właścicielka studia'];
        }
        if ($on('schema_hours_on') && ($h = mvn_opening_hours_spec())) {
            $biz['openingHoursSpecification'] = $h;
        }
        if ($on('schema_offers_on')) {
            $offers = [];
            foreach (mvn_classes() as $c) {
                $offers[] = ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => $c['name'], 'areaServed' => mvo('city')]];
            }
            foreach (mvn_price_groups() as $pkgs) {
                foreach ($pkgs as $p) {
                    if (isset($p['price']) && is_numeric($p['price']) && (float) $p['price'] > 0) {
                        $offers[] = ['@type' => 'Offer', 'name' => (string) $p['name'], 'price' => (string) (float) $p['price'], 'priceCurrency' => 'PLN'];
                    }
                }
            }
            $biz['makesOffer'] = $offers;
        }
        if (is_numeric(mvo('geo_lat')) && is_numeric(mvo('geo_lng'))) {
            $biz['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float) mvo('geo_lat'), 'longitude' => (float) mvo('geo_lng')];
        }
        if (trim((string) mvo('price_range')) !== '') {
            $biz['priceRange'] = (string) mvo('price_range');
        }
        if ($count > 0 && is_numeric($rating) && (float) $rating >= 1 && (float) $rating <= 5) {
            $biz['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => (string) (float) $rating, 'reviewCount' => (string) $count, 'bestRating' => '5'];
        }
        $graph[] = $biz;
    }
    if (!$landing && $on('schema_website_on')) {
        $site = ['@type' => 'WebSite', '@id' => $home . '#website', 'url' => $home, 'name' => mvo('brand'), 'inLanguage' => 'pl-PL'];
        if ($on('schema_business_on')) {
            $site['publisher'] = ['@id' => $home . '#studio'];
        }
        $graph[] = $site;
    }
    if ($on('schema_faq_on')) {
        $faq = [];
        foreach (mvsa(mvn_first('faq'), 'items') as $r) {
            if (!empty($r['q'])) {
                $faq[] = ['@type' => 'Question', 'name' => wp_strip_all_tags(mvn_tokens((string) $r['q'])), 'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(wp_strip_all_tags(mvn_rich((string) ($r['a'] ?? ''))))]];
            }
        }
        if ($faq) {
            $graph[] = ['@type' => 'FAQPage', 'mainEntity' => $faq];
        }
    }
    [$extra] = mvn_schema_extra();
    return array_merge($graph, $extra);
}

function mvn_jsonld(): string
{
    $graph = mvn_schema_graph();
    if (!$graph) {
        return '';
    }
    // Puste właściwości (np. NIP przed uzupełnieniem) nie trafiają do kodu strony
    foreach ($graph as $i => $node) {
        $graph[$i] = array_filter($node, static fn($v) => $v !== '' && $v !== null && $v !== []);
    }
    return wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/** HTML podglądu w panelu: tabela „właściwość → wartość → skąd” + lista tego, co NIE jest danymi strukturalnymi. */
function mvn_schema_preview_html(): string
{
    $src = mvn_schema_sources();
    $short = static function ($v): string {
        if (is_array($v)) {
            $v = wp_json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $v = (string) $v;
        return mb_strlen($v) > 110 ? mb_substr($v, 0, 110) . '…' : $v;
    };
    $h = '<div style="max-width:980px">';
    $graph = mvn_schema_graph();
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
    [, $err] = mvn_schema_extra();
    if ($err !== '') {
        $h .= '<p style="color:#b32d2e"><strong>' . esc_html($err) . '</strong></p>';
    }
    $h .= '<p style="margin:16px 0 4px"><strong>Czego NIE ma w danych strukturalnych</strong> (jest tylko widoczną treścią strony): grafik zajęć (jest w kalendarzu zapisów, pobieranym z CRM), treści opinii klientów, opisy sekcji i zdjęcia z sali. Ceny pakietów trafiają do „makesOffer” jako oferty.</p>';
    $h .= '<p style="margin:6px 0 0">Sprawdzenie na żywo: <a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">test wyników z elementami rozszerzonymi Google</a> (po zapisaniu ustawień i wejściu na stronę). Podgląd odświeża się po zapisaniu.</p></div>';
    return $h;
}

add_action('wp_head', static function () {
    echo '<meta name="theme-color" content="#FAF6EF">' . "\n";

    if (!has_site_icon()) {
        $i = MVN_URI . '/assets/img/';
        echo '<link rel="icon" href="' . esc_url($i . 'favicon.ico') . '" sizes="32x32">' . "\n";
        echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url($i . 'icons/favicon-32.png') . '">' . "\n";
        echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url($i . 'icons/icon-192.png') . '">' . "\n";
        echo '<link rel="apple-touch-icon" href="' . esc_url($i . 'icons/apple-touch-icon.png') . '">' . "\n";
    }

    if (!mvn_seo_plugin_active()) {
        $desc = mvn_meta_description();
        $title = wp_get_document_title();
        $url = is_front_page() ? home_url('/') : (is_singular() ? get_permalink() : home_url('/'));
        if ($desc !== '') {
            echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
        }
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:locale" content="pl_PL">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr((string) mvo('brand')) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        echo '<meta property="og:image" content="' . esc_url(mvn_og_image_url()) . '">' . "\n";
        echo '<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">' . "\n";
        if (trim((string) mvo('og_image_alt')) !== '') {
            echo '<meta property="og:image:alt" content="' . esc_attr((string) mvo('og_image_alt')) . '">' . "\n";
        }
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        if ($desc !== '') {
            echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
        }
        echo '<meta name="twitter:image" content="' . esc_url(mvn_og_image_url()) . '">' . "\n";
    }

    if (mvn_is_site_page()) {
        if (($ld = mvn_jsonld()) !== '') {
            echo '<script type="application/ld+json">' . $ld . '</script>' . "\n";
        }
    }
}, 2);
