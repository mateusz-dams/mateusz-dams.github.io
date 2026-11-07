# Bizzlife Premium – motyw WordPress

Autorski motyw dla biura rachunkowego **Bizzlife Sp. z o.o.** (Radom). Czarno-złota identyfikacja, jasne sekcje w ciepłej bieli, natywny WordPress – bez Elementora, bez page buildera, **bez wymaganych wtyczek**.

## Wymagania

- WordPress 6.4+ (testowane na 7.1.3), PHP 8.0+ (testowane na 8.3).
- Wtyczki wymagane: **brak**.
- Wtyczki zalecane:
  - **WP Mail SMTP** (lub inna wtyczka SMTP) – żeby wiadomości z formularza trafiały do skrzynki, a nie do spamu.
  - **Rank Math** lub **Yoast SEO** – tytuły, meta description, mapa witryny. Motyw automatycznie wyłącza własne meta/Open Graph, gdy wykryje wtyczkę SEO.
  - **Redirection** – opcjonalnie, do importu mapy przekierowań (`docs/przekierowania-301.csv`).
  - Wtyczka cache (np. LiteSpeed Cache, WP Super Cache) – formularz pobiera świeży token, więc działa także na stronach z cache.

## Instalacja

1. **Zrób kopię zapasową** bazy i plików obecnej strony.
2. Kokpit → Wygląd → Motywy → Dodaj nowy → Wyślij motyw → wybierz `bizlife-premium.zip` → Zainstaluj → Włącz.
3. Usługi (7) i pytania FAQ (14) tworzą się **automatycznie** przy pierwszym wczytaniu strony po aktywacji, jeśli w bazie nie ma jeszcze żadnych – menu „Usługi”, sekcja FAQ i stopka działają od razu. Następnie pojawi się komunikat z przyciskiem **„Otwórz kreator”** (lub: Wygląd → Bizzlife: start). Zaznacz kroki i kliknij „Uruchom kreator”. Kreator:
   - tworzy 7 usług z obecnej oferty (`/uslugi/…`),
   - opcjonalnie **przenosi treść ze starych stron usług** (np. `/ksiegowosc/`) i ustawia je jako szkic – stare adresy zaczną automatycznie przekierowywać 301 na nowe,
   - tworzy pytania FAQ (z obecnej strony + kilka nowych) pogrupowane w tematy,
   - tworzy brakujące strony: O nas, Kontakt, FAQ, Aktualności i ustawia je w Ustawienia → Czytanie (bez nadpisywania istniejących ustawień),
   - tworzy menu główne i menu stopki,
   - podpina logo i zdjęcia z biblioteki mediów (szuka plików `bizzlife_logo.svg`, `PHOTO-2025-11-13-08-16-41-2`, `490939324_…`). Gdy ich brak, opcjonalnie pobiera je z bizzlife.pl.
   Kreator można uruchamiać wielokrotnie – nie tworzy duplikatów.
4. Ustawienia → Bezpośrednie odnośniki → **Zapisz** (odświeża adresy). Obecna struktura `/aktualnosci/nazwa-wpisu/` pozostaje bez zmian.
5. Ustawienia → Prywatność – wskaż stronę polityki prywatności (link pojawi się w formularzu i stopce).
6. Wyślij testową wiadomość z formularza i sprawdź, czy dotarła. Kopia każdego zgłoszenia trafia też do **Kokpit → Zgłoszenia**.

## Gdzie co edytować

| Co | Gdzie |
|---|---|
| Teksty i nagłówki sekcji strony głównej, zdjęcia, przyciski CTA | Wygląd → Dostosuj → **Bizzlife – ustawienia motywu** |
| Kolejność i ukrywanie sekcji strony głównej | … → Strona główna – kolejność sekcji (lista kluczy; usunięcie klucza ukrywa sekcję) oraz przełącznik „Pokaż sekcję” w każdej sekcji |
| Tło sekcji (grafit / czerń / ciepła biel) | w każdej sekcji pole „Tło sekcji” |
| Karty „Wprowadzenie”, „Dlaczego Bizzlife”, kroki współpracy, „Dla kogo” | w odpowiedniej sekcji Customizera; **puste pole tytułu ukrywa kartę** |
| Dane firmy, telefon, e-mail, godziny, KRS/NIP/REGON, mapa | … → Dane firmy i kontakt |
| Social media | … → Media społecznościowe (puste = ukryte) |
| Formularz: odbiorca, treść zgody, klauzula RODO, komunikaty | … → Kontakt i formularz |
| Logo | … → Marka i nagłówek. Domyślnie motyw wyświetla oryginalny plik `bizzlife_logo.svg` z bizzlife.pl. Możesz też wgrać plik SVG/PNG w polu „Logo” – administrator może wgrywać SVG (pliki są automatycznie oczyszczane z kodu wykonywalnego). |
| Kolor akcentu, kroje szeryfowe, animacje, dane strukturalne, WebP | … → Wygląd i technika |
| Usługi (karty + podstrony) | Kokpit → **Usługi**: tytuł, treść (edytor bloków), Zajawka = opis na karcie, Zdjęcie wyróżniające, pole „Szczegóły usługi” (ikona, zakres, dla kogo, temat FAQ), Atrybuty → Kolejność |
| FAQ | Kokpit → **FAQ** (pytanie = tytuł, odpowiedź = treść), Tematy, „Pokaż na stronie głównej”, Kolejność |
| Aktualności | Wpisy (natywne), kategorie = filtry na liście artykułów |
| Menu | Wygląd → Menu (lokalizacje: Menu główne, Stopka – menu, Stopka – linki prawne) |
| Dodatkowe bloki na stronie głównej | edytuj stronę ustawioną jako strona główna – jej bloki pojawią się w miejscu klucza `content` |

**Tokeny linków** w polach „link”: `{kontakt}`, `{uslugi}`, `{o-nas}`, `{faq}`, `{aktualnosci}`, `{tel}`, `{email}`, `{usluga:slug}` – motyw sam zamienia je na właściwe adresy (na stronie głównej `{kontakt}` przewija do sekcji kontaktu).

**Wyróżnienie w nagłówku:** fragment w `*gwiazdkach*` wyświetla się kursywą w kolorze złota.

Szablony stron (Atrybuty strony → Szablon): **O nas**, **Kontakt**, **FAQ**. Pozostałe strony (np. polityka prywatności) używają szablonu standardowego.

## Struktura

```
functions.php            ładowanie modułów
inc/                     defaults, helpers, icons, setup, enqueue, post-types, meta-boxes,
                         customizer, walker, breadcrumbs, seo, contact-form, leads, editor,
                         performance, importer
template-parts/sections  sekcje strony głównej (hero, intro, services, why, about, process,
                         audience, content, news, faq, cta, contact)
template-parts/components page-hero, accordion, contact-info, map, post-archive
template-parts/cards     karta usługi, karta wpisu
page-templates/          O nas, Kontakt, FAQ
assets/css               fonts.css, main.css, editor.css
assets/js/main.js        ~10 KB, bez zależności
assets/fonts             Manrope + EB Garamond (lokalnie, OFL)
theme.json               paleta i typografia edytora bloków
```

## Bezpieczeństwo i prywatność

- Formularz: nonce (odświeżany przez AJAX przy stronach z cache), honeypot, podpisana pułapka czasowa, limit 5 wiadomości / 15 min / IP (hash), walidacja po stronie serwera i przeglądarki, ochrona nagłówków e-mail. Działa także bez JavaScriptu.
- Zgłoszenia zapisywane jako prywatne wpisy (tylko administrator/redaktor), automatyczne usuwanie po X dniach (domyślnie 180, ustawialne).
- Mapa Google ładuje się dopiero po kliknięciu „Pokaż mapę”. Fonty hostowane lokalnie – brak połączeń z Google Fonts.
- Wszystkie dane wyjściowe escapowane, ustawienia sanityzowane, zapisy meta z nonce i sprawdzeniem uprawnień.

## Dane strukturalne

`AccountingService` (strona główna i kontakt; wyłączalne), `BreadcrumbList` i `BlogPosting` (tylko gdy brak wtyczki SEO), `FAQPage` na stronie FAQ.

## Aktualizacje

Motyw nie modyfikuje plików WordPressa ani wtyczek. Własne zmiany CSS najlepiej dodawać w Wygląd → Dostosuj → Dodatkowy CSS lub w motywie potomnym (child theme), aby nie zginęły przy aktualizacji motywu.
