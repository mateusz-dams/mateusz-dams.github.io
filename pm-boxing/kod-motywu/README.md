# Wybrane fragmenty kodu motywu PM Boxing Studio

To nie jest cały motyw, tylko wybrane pliki, które najlepiej pokazują, jak powstawał kod. Pełny motyw nie jest publikowany, bo prawa do niego, do treści i do zdjęć należą do PM Boxing Studio. Kod jest pokazany wyłącznie do wglądu (zobacz [LICENSE](../../LICENSE)) i nie zawiera kluczy, tokenów, adresów CRM ani identyfikatorów pomiaru.

Opis projektu i mojego udziału: [README projektu](../README.md).

| Plik | Co pokazuje |
|---|---|
| [`inc/form.php`](inc/form.php) | Obsługa formularza po stronie serwera: walidacja, ochrona przed spamem (honeypot, czas wypełnienia, limit z jednego adresu IP), wysyłka przez SMTP, przekazanie zgłoszenia do CRM i zdarzenie Lead do Meta Conversions API z danymi zahashowanymi SHA-256. |
| [`inc/tracking.php`](inc/tracking.php) | Ładowanie Google Analytics 4 i Meta Pixel dopiero po zgodzie na cookies (Google Consent Mode v2). |
| [`assets/js/tracking.js`](assets/js/tracking.js) | Baner zgody na cookies i zdarzenia konwersji w przeglądarce (formularz, telefon, kliknięcia w przyciski). |
| [`inc/seo.php`](inc/seo.php) | Tytuły, opisy, Open Graph i dane strukturalne JSON-LD; motyw nie dubluje znaczników, gdy działa wtyczka SEO. |
| [`template-parts/section-cennik.php`](template-parts/section-cennik.php) | Przykład sekcji strony głównej: cennik w zakładkach z treściami edytowanymi w panelu (Secure Custom Fields). |
