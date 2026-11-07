# Bizzlife – redesign strony biura rachunkowego

Redesign strony biura rachunkowego **Bizzlife** z Radomia. Wdrożona wersja działa na [bizzlife.pl](https://bizzlife.pl/). W tym folderze jest wariant tego redesignu przygotowany jako **motyw WordPress** w dwóch wersjach kolorystycznych: ciemnej (czerń i złoto) i jasnej (ciepła biel i złoto).

**Współtworzenie redesignu i wdrożenie:** Mateusz Dams (w zespole) · [dams.mateusz@gmail.com](mailto:dams.mateusz@gmail.com) · [portfolio](https://mateusz-dams.github.io/)

| | |
|---|---|
| **Strona w działaniu** | [bizzlife.pl](https://bizzlife.pl/) |
| **Motyw WordPress – wersja ciemna** | [mateusz-dams.github.io/bizzlife](https://mateusz-dams.github.io/bizzlife/) – statyczna kopia wygenerowana z motywu |
| **Motyw WordPress – wersja jasna** | [mateusz-dams.github.io/bizzlife/jasna](https://mateusz-dams.github.io/bizzlife/jasna/) |
| **Kod** | [kod-motywu](kod-motywu) – wybrane fragmenty kodu motywu (5 plików z opisem), do wglądu |

## Mój udział

| | |
|---|---|
| **Cel** | Nowoczesna strona biura rachunkowego, która jasno pokazuje usługi i zachęca do kontaktu, a biuro może ją edytować samo, bez page buildera. |
| **Mój zakres** | Współtworzenie redesignu i jego wdrożenie na bizzlife.pl. Przygotowanie wariantu jako motyw WordPress w dwóch wersjach kolorystycznych, z treściami startowymi tworzonymi automatycznie. |
| **Weryfikacja** | Wszystkie 19 podstron sprawdzone w przeglądarce na komputerze i telefonie: brak błędów skryptów, brak przewijania w bok, działające linki i kotwice, formularz w trybie pokazowym. |
| **Efekt** | Redesign wdrożony na [bizzlife.pl](https://bizzlife.pl/). |

Podgląd motywu to statyczna kopia wygenerowana z motywu uruchomionego na WordPressie. Formularz kontaktowy działa w niej w trybie pokazowym i niczego nie wysyła.

![Strona główna – wersja ciemna](zrzuty/strona-glowna.jpg)

## Co robi motyw

- **Natywny WordPress, bez page buildera i bez wymaganych wtyczek** – teksty, zdjęcia, kolejność i tło sekcji strony głównej edytuje się w panelu *Dostosuj*.
- **Treści startowe tworzą się same po aktywacji**: 7 usług z własnymi podstronami, 14 pytań FAQ w tematach, strony O nas, Kontakt, FAQ i Aktualności oraz menu. Kreator w panelu może przenieść treść ze starych podstron i ustawić przekierowania 301.
- **Usługi i FAQ jako osobne typy treści**, z polami „Zakres usługi”, „Dla kogo” i powiązanym tematem pytań.
- **Formularz kontaktowy** z walidacją na żywo i ochroną przed spamem (nonce odświeżany przy stronach z cache, honeypot, pułapka czasowa, limit wiadomości z jednego IP). Kopia każdego zgłoszenia trafia do panelu i jest automatycznie usuwana po ustalonym czasie.
- **SEO**: własne meta i Open Graph wyłączane automatycznie, gdy działa Rank Math lub Yoast, dane strukturalne, okruszki, mapa przekierowań ze starych adresów.
- **Prywatność i szybkość**: fonty hostowane lokalnie (bez Google Fonts), mapa Google ładowana dopiero po kliknięciu, obrazy WebP, JavaScript ok. 10 KB bez bibliotek.
- **Dostępność**: nawigacja klawiaturą, link „Przejdź do treści”, ograniczenie animacji przy ustawieniu „ogranicz ruch”.

![Wersja jasna](zrzuty/wersja-jasna.jpg)

## Technologie

PHP 8 · WordPress 6.4+ · edytor bloków (`theme.json`) · HTML · CSS bez frameworka · JavaScript bez bibliotek · fonty Manrope i EB Garamond (licencja OFL).

| Telefon: wersja ciemna i jasna |
|---|
| ![Widok na telefonie](zrzuty/telefon.jpg) |

![Podstrona usługi](zrzuty/podstrona-uslugi.jpg)

## O kodzie

W folderze `kod-motywu` jest 5 wybranych plików z opisem, co pokazuje każdy z nich. Pełny motyw nie jest publikowany. Kod jest wyłącznie do wglądu (zobacz [LICENSE](../LICENSE)) i nie zawiera kluczy, tokenów, adresów CRM ani identyfikatorów pomiaru.
