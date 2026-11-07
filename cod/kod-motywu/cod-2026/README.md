# COD 2026 – motyw WordPress

Motyw strony Centrum Odkrywania Doskonałości (cod.biz.pl). Zastępuje motyw „cod” i korzysta z tych samych danych: pól ACF, typu wpisu „Oferta”, menu, WooCommerce i LearnDash. 

## Gdzie co się edytuje

| Co | Gdzie w panelu |
|---|---|
| Teksty strony głównej | *Strony → Strona główna* (zakładki: Pierwszy ekran, Od czego zacząć?, Oferta, O mnie, Pierwszy kontakt, Zespół i opinie, Pytania, Kontakt, Sklep i kursy, Pasek końcowy). Puste pole = tekst domyślny. |
| Usługi, ceny, opisy, kreator | *Oferta → [usługa]* – cena (pole „offer_price”), karta, szczegóły, odpowiedź w kreatorze, „Wyróżnij jako pierwszy krok”. Kolejność: pole „Kolejność” w atrybutach. |
| Grupa usługi (zakładka) | Kategoria usługi (Rodzic i Dziecko / Dorośli / Biznes) albo pole „Dla kogo”. |
| Dane firmy, logo, social media | *Opcje motywu → Dane firmy i logo* (pola z poprzedniego motywu). |
| Zespół, godziny, formularz, pomiar, przekierowanie po zakupie | *Opcje motywu → COD 2026*. |
| Tryb ciemny, logo na ciemne tło | *Opcje motywu → COD 2026 → Wygląd*. |
| Strony sprzedażowe i pozostałe strony | Sekcje jak dotąd (hero, pełna szerokość, 50/50, zespół/pakiety, FAQ, CTA, opinie, kontakt, mapa…). Wygląd jest nowy, pola bez zmian. |
| Produkty i kursy | WooCommerce i LearnDash – bez zmian. |

## Sekcje stron – jak motyw je pokazuje

- **Kolor tła** sekcji jest mapowany na paletę motywu (jasne → kremowe / białe, niebieskie → miętowe, ciemne → granat).
- **„Zespół”** działa w trzech trybach, wybieranych automatycznie: osoby ze zdjęciami → karty zespołu; pozycje z ceną lub przyciskiem → **pakiety cenowe** (cena z WooCommerce, gdy przycisk prowadzi do produktu; co druga karta wyróżniona); bez zdjęć i cen → karty porównawcze (przed/po, „dla kogo”). Tekst w *kursywie* w opisie pakietu = etykieta (np. „Najczęściej wybierany”).
- **Hero** – gdy strona zaczyna się od hero albo kontaktu, nie ma dodatkowego nagłówka strony.
- **Mapa** ładuje się z Google dopiero po kliknięciu (bez cookies przed zgodą).
- Pasek „Zacznijmy od rozmowy” pojawia się na końcu stron, chyba że strona kończy się własnym CTA, kontaktem albo mapą.

## Kolory i tryb ciemny

- Kolory są zapisane jako zmienne CSS w trzech zestawach: **strona** (jasna albo ciemna), **ciemne sekcje** (granat) i **turkusowe pasy**. Każda sekcja i karta ustawia własny zestaw, więc tekst i przyciski zawsze mają czytelny kolor względem tła – także w sekcjach, którym w edytorze nadano ciemne albo turkusowe tło.
- **Tryb ciemny** włącza się automatycznie, gdy odwiedzający ma tryb ciemny w telefonie lub komputerze (jak w projekcie). Można go wyłączyć: *Opcje motywu → COD 2026 → Wygląd → Tryb ciemny: Wyłączony*.
- W trybie ciemnym kursy LearnDash dostają kolory strony; jasne zostają tylko testy (wpProQuiz) i tryb skupienia LearnDash.
- **Nagłówek** leży przezroczysty na pierwszym ekranie strony (jak w projekcie). Nad ciemnym ekranem ma jasne napisy i logo w wersji na ciemne tło (`assets/img/cod-logo-light.png` – ten sam znak, jasny napis). Po zjechaniu niżej dostaje tło.
- **Kolory z edytora**: gdy w treści wpisano kolor tekstu „na sztywno” (np. biały tekst ze starego projektu) i na danym tle byłby nieczytelny, skrypt motywu przywraca kolor sekcji.

## Struktura plików

```
cod-2026/
├── style.css, functions.php
├── header.php, footer.php, front-page.php, page.php, single.php, index.php, 404.php, comments.php, searchform.php
├── single-offer.php, archive-offer.php, taxonomy-offer_cat.php      usługi
├── archive-sfwd-courses.php, single-sfwd-*.php                       LearnDash
├── woocommerce/archive-product.php, content-product.php              sklep
├── inc/            logika (helpers, setup, acf, assets, tracking, sections, offers, contact, woocommerce, learndash, importer)
├── inc/data/services.php   treści usług z projektu (dla narzędzia „Uzupełnij treści”)
├── template-parts/ home/, sections/ (13 układów), cards/, contact-form, cta-band, page-hero, learning
└── assets/         css (main, shop, learn, editor), js/main.js, fonts (Fraunces, Manrope – SIL OFL), img
```

## Pomiar (dataLayer → GTM)

`cod_phone_click`, `cod_email_click`, `cod_form_submit`, `cod_form_invalid`, `cod_form_error`, `cod_form_open`, `cod_finder_open`, `cod_finder_result`, `cod_service_view`, `cod_tab_select`, `cod_faq_open`, `cod_package_click`, `cod_product_click`, `cod_shop_click`, `cod_courses_click`, `cod_account_click`, `cod_cart_click`, `cod_booking_click`, `cod_map_load`, `cod_directions_click`, `cod_social_click`. Każde zdarzenie ma parametr `location` (miejsce na stronie).

## Formularz kontaktowy

Domyślnie formularz motywu (wysyłka przez `wp_mail`, czyli przez skonfigurowaną wtyczkę SMTP; ochrona: nonce, honeypot, czas wypełnienia, limit z jednego IP; dane nie są zapisywane w bazie). Alternatywnie Contact Form 7 – *Opcje motywu → COD 2026 → Formularz*. Sekcja „Kontakt” na stronach używa shortcode’u wpisanego w sekcji.

## Zgodność

PHP 7.4–8.3 (sprawdzone PHPCompatibility), WordPress 6.4–7.1, Secure Custom Fields 6.8 / ACF PRO 6, WooCommerce 10.7 (klasyczne szablony i bloki Koszyka/Zamówienia), LearnDash 5 (szablony LD30).
