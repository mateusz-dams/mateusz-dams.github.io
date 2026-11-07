<?php
/**
 * Domyślne treści stron prawnych (regulamin, polityka prywatności, polityka cookies).
 * Dane firmy są pobierane z ustawień motywu; brakujące wyświetlają się jako oznaczone [UZUPEŁNIJ: …].
 * To szablon, nie porada prawna — przed publikacją niech przejrzy go prawnik.
 */
if (!defined('ABSPATH')) {
    exit;
}

$ph = static function (string $val, string $label): string {
    $val = trim($val);
    return $val !== '' ? esc_html($val) : '<mark>[UZUPEŁNIJ: ' . esc_html($label) . ']</mark>';
};
$firma  = $ph((string) mvo('company_name'), 'pełna nazwa i forma prawna firmy');
$adres  = $ph((string) mvo('company_address'), 'adres siedziby');
$nip    = $ph((string) mvo('nip'), 'NIP');
$regon  = trim((string) mvo('regon')) !== '' ? esc_html((string) mvo('regon')) : '<mark>[UZUPEŁNIJ: REGON]</mark>';
$krs    = trim((string) mvo('krs')) !== '' ? ', KRS: ' . esc_html((string) mvo('krs')) : '';
$rodo   = $ph((string) (trim((string) mvo('gdpr_email')) !== '' ? mvo('gdpr_email') : mvo('email')), 'e-mail do spraw RODO');
$email  = $ph((string) mvo('email'), 'adres e-mail');
$tel    = esc_html((string) mvo('phone_display'));
$brand  = esc_html((string) mvo('brand'));
$street = esc_html(trim(mvo('address') . ', ' . mvo('city'), ' ,'));
$data   = esc_html(wp_date('j.m.Y'));
$upd    = '<p class="updated">Ostatnia aktualizacja: ' . $data . '</p>';

$regulamin = $upd . <<<HTML
<h2>1. Postanowienia ogólne</h2>
<p>Niniejszy regulamin określa zasady korzystania z zajęć organizowanych przez {$firma}, z siedzibą: {$adres}, NIP: {$nip}{$krs}, prowadzącą {$brand} ({$street}).</p>

<h2>2. Zakup karnetów i rezerwacja</h2>
<ul>
<li>Zakup wejść i karnetów oraz zapis na zajęcia odbywa się online, w systemie rezerwacji i płatności Movena Pilates Studio (strona movenapilates.pl oraz panel klientki).</li>
<li>Karnet aktywuje się w dniu pierwszych zajęć i jest ważny przez 30 dni od tego dnia.</li>
<li>Zapisu na zajęcia można dokonać do 21 dni naprzód, o ile są wolne miejsca (w grupie jest maksymalnie 4 osoby). Gdy miejsc nie ma, można zapisać się na listę oczekujących — osoba z listy dostaje miejsce automatycznie, gdy ktoś się wypisze.</li>
<li>Zajęcia odbywają się od 2 osób. Jeśli na około 2 godziny przed zajęciami zapisana jest tylko jedna osoba, zajęcia zostają odwołane, a wejście wraca na karnet.</li>
</ul>

<h2>3. Odwoływanie zajęć</h2>
<p>Rezerwację można odwołać najpóźniej 12 godzin przed rozpoczęciem zajęć — wejście wraca wtedy na karnet. Odwołanie w późniejszym terminie lub nieobecność bez odwołania skutkuje wykorzystaniem wejścia z karnetu.</p>

<h2>4. Zasady uczestnictwa w zajęciach</h2>
<ul>
<li>Zajęcia grupowe odbywają się w grupach liczących maksymalnie 4 osoby.</li>
<li>Uczestniczka zobowiązana jest poinformować instruktorkę o przeciwwskazaniach zdrowotnych, kontuzjach lub ciąży przed rozpoczęciem zajęć.</li>
<li>Studio zastrzega sobie prawo do odmowy udziału w zajęciach osobie, której stan zdrowia może stanowić zagrożenie dla niej samej lub innych uczestniczek.</li>
</ul>

<h2>5. Płatności i zwroty</h2>
<p>Płatności za karnety i wejścia realizowane są online za pośrednictwem operatora płatności Stripe albo na miejscu w studiu. Zwroty za niewykorzystane karnety i wejścia oraz prawo odstąpienia od umowy zawartej na odległość przysługują na zasadach określonych w obowiązujących przepisach prawa konsumenckiego.</p>

<h2>6. Reklamacje</h2>
<p>Reklamacje dotyczące usług można zgłaszać pod adresem {$email} lub telefonicznie pod numerem {$tel}.</p>

<h2>7. Postanowienia końcowe</h2>
<p>W sprawach nieuregulowanych niniejszym regulaminem zastosowanie mają przepisy prawa polskiego, w szczególności Kodeksu cywilnego oraz ustawy o prawach konsumenta.</p>
HTML;

$prywatnosc = $upd . <<<HTML
<h2>1. Administrator danych</h2>
<p>Administratorem danych osobowych zbieranych za pośrednictwem strony movenapilates.pl jest {$firma}, z siedzibą: {$adres}, NIP: {$nip}, REGON: {$regon}{$krs}. Kontakt w sprawach ochrony danych: {$rodo}.</p>

<h2>2. Jakie dane zbieramy</h2>
<ul>
<li>Dane podawane dobrowolnie w formularzu kontaktowym (imię, numer telefonu, adres e-mail, treść wiadomości) oraz przy kontakcie telefonicznym lub przez komunikatory (numer telefonu).</li>
<li>Dane techniczne i marketingowe zbierane automatycznie (adres IP, typ urządzenia i przeglądarki, strona, z której przyszedłeś, parametry kampanii UTM, identyfikatory kliknięć reklam) — wyłącznie po wyrażeniu zgody w banerze cookies.</li>
<li>Dane związane z zakupem karnetów i zapisami na zajęcia (imię i nazwisko, telefon, e-mail, historia zajęć i płatności) są przetwarzane w systemie rezerwacji i płatności studia, a płatności online obsługuje Stripe.</li>
</ul>

<h2>3. Cel i podstawa przetwarzania</h2>
<ul>
<li>Odpowiedź na wiadomość z formularza lub zapytanie telefoniczne oraz przygotowanie oferty — art. 6 ust. 1 lit. b i f RODO (działania przed zawarciem umowy, prawnie uzasadniony interes administratora).</li>
<li>Analiza ruchu na stronie (Google Analytics 4, Google Tag Manager, Hotjar) — art. 6 ust. 1 lit. a RODO, na podstawie zgody z banera cookies.</li>
<li>Pomiar skuteczności i personalizacja reklam (Google Ads, Meta Pixel oraz Interfejs API Konwersji Meta, który przekazuje zdarzenia z naszego serwera, w tym zahaszowane dane kontaktowe) — art. 6 ust. 1 lit. a RODO, wyłącznie po wyrażeniu zgody marketingowej.</li>
<li>Dochodzenie roszczeń i obrona przed nimi oraz obowiązki podatkowe i księgowe — art. 6 ust. 1 lit. c i f RODO.</li>
</ul>

<h2>4. Odbiorcy danych</h2>
<p>Dane mogą być przekazywane dostawcom usług, z których korzystamy: hostingu i poczty e-mail, operatorowi płatności Stripe, dostawcy wiadomości SMS, systemowi rezerwacji i obsługi klientek (CRM), a po wyrażeniu zgody — firmom Google (Analytics, Tag Manager, Ads), Meta Platforms (Pixel, API Konwersji) i Hotjar. Część z tych podmiotów może przetwarzać dane poza Europejskim Obszarem Gospodarczym, na podstawie standardowych klauzul umownych lub decyzji Komisji Europejskiej.</p>
<p>Mapa Google na stronie ładuje się dopiero po kliknięciu „Pokaż mapę”; do tego momentu nie łączymy się z serwerami Google.</p>

<h2>5. Okres przechowywania</h2>
<p>Dane z formularza i zapytań przechowujemy do czasu zakończenia obsługi zapytania, a następnie przez okres przedawnienia ewentualnych roszczeń. Dane analityczne i reklamowe — zgodnie z okresami życia plików cookies opisanymi w Polityce cookies lub do wycofania zgody.</p>

<h2>6. Prawa osoby, której dane dotyczą</h2>
<p>Przysługuje Ci prawo dostępu do danych, ich sprostowania, usunięcia, ograniczenia przetwarzania, przenoszenia danych oraz wniesienia sprzeciwu, a także prawo do wycofania zgody w dowolnym momencie (bez wpływu na zgodność z prawem przetwarzania sprzed jej wycofania) oraz prawo wniesienia skargi do Prezesa Urzędu Ochrony Danych Osobowych. Wystarczy napisać na adres {$rodo}.</p>

<h2>7. Pliki cookies</h2>
<p>Szczegółowe informacje o wykorzystywanych plikach cookies znajdziesz w <a href="/polityka-cookies/">Polityce cookies</a>.</p>
HTML;

$cookies = $upd . <<<HTML
<h2>1. Czym są pliki cookies</h2>
<p>Pliki cookies to niewielkie pliki tekstowe zapisywane na Twoim urządzeniu podczas odwiedzania strony movenapilates.pl. Używamy ich, aby strona działała poprawnie oraz aby mierzyć jej skuteczność.</p>

<h2>2. Jakich cookies używamy</h2>
<table>
<thead><tr><th>Kategoria</th><th>Cel</th><th>Wymaga zgody</th></tr></thead>
<tbody>
<tr><td>Niezbędne</td><td>Podstawowe działanie strony (np. zapamiętanie decyzji o cookies, zabezpieczenie formularza)</td><td>Nie</td></tr>
<tr><td>Analityczne</td><td>Pomiar ruchu i zachowania na stronie (Google Analytics 4, Hotjar)</td><td>Tak</td></tr>
<tr><td>Marketingowe</td><td>Pomiar skuteczności i personalizacja reklam (Google Ads, Meta Pixel, API Konwersji Meta)</td><td>Tak</td></tr>
</tbody>
</table>

<h2>3. Zarządzanie zgodą</h2>
<p>Przy pierwszej wizycie na stronie możesz zaakceptować lub odrzucić cookies analityczne i marketingowe w widocznym banerze. Domyślnie wszystkie są wyłączone. Zgodę możesz zmienić w dowolnym momencie przez link „Ustawienia cookies” w stopce strony, usunąć dane strony w przeglądarce albo napisać do nas: {$rodo}.</p>

<h2>4. Podmioty trzecie</h2>
<p>Korzystamy z narzędzi dostarczanych przez Google (Google Analytics, Google Tag Manager, Google Ads), Meta (Meta Pixel i Interfejs API Konwersji) oraz Hotjar. Podmioty te mogą przetwarzać dane zgodnie z własnymi politykami prywatności. Rezerwacje i zakupy odbywają się w systemie Fitssey, który działa na własnych zasadach.</p>

<p>Więcej informacji o przetwarzaniu danych znajdziesz w <a href="/polityka-prywatnosci/">Polityce prywatności</a>.</p>
HTML;

return [
    'regulamin' => [
        'title'   => 'Regulamin świadczenia usług',
        'desc'    => 'Regulamin świadczenia usług ' . wp_strip_all_tags((string) mvo('brand')) . ': karnety, rezerwacje, odwoływanie zajęć, płatności i reklamacje.',
        'content' => $regulamin,
    ],
    'polityka-prywatnosci' => [
        'title'   => 'Polityka prywatności',
        'desc'    => 'Polityka prywatności ' . wp_strip_all_tags((string) mvo('brand')) . ': administrator danych, cele przetwarzania, odbiorcy i Twoje prawa.',
        'content' => $prywatnosc,
    ],
    'polityka-cookies' => [
        'title'   => 'Polityka cookies',
        'desc'    => 'Polityka cookies ' . wp_strip_all_tags((string) mvo('brand')) . ': jakich plików cookies używamy i jak zarządzać zgodą.',
        'content' => $cookies,
    ],
];
