# Mateusz Dams – portfolio

Uczeń technikum w Radomiu (kierunek technik informatyk). Współtworzę strony i motywy WordPress dla firm, a w ramach nauki koduję strony w HTML, CSS i JavaScript na podstawie profesjonalnych projektów graficznych.

**Portfolio:** [mateusz-dams.github.io](https://mateusz-dams.github.io/) · **CV:** [CV-Mateusz-Dams.pdf](cv/CV-Mateusz-Dams.pdf) · **Kontakt:** [dams.mateusz@gmail.com](mailto:dams.mateusz@gmail.com), tel. 505 309 362

## Strony dla firm

Każdy projekt ma w swoim folderze opis „Mój udział”: cel, mój zakres, najtrudniejszą część i sposób sprawdzenia przed publikacją.

| Projekt | Status | Mój udział | Strona | Kod do przejrzenia |
|---|---|---|---|---|
| [Centrum Odkrywania Doskonałości](cod/) | Wdrożona | Współtworzenie redesignu i motywu WordPress, wdrożenie, CRM | [cod.biz.pl](https://cod.biz.pl/) | [cod/kod-motywu](cod/kod-motywu/cod-2026) |
| [PM Boxing Studio](pm-boxing/) | Wdrożona | Współtworzenie strony, przeniesienie na motyw WordPress, landing, analityka, CRM | [pmboxingstudio.pl](https://pmboxingstudio.pl/) | [pm-boxing/kod-motywu](pm-boxing/kod-motywu/pm-boxing-studio) |
| [Movena Pilates Studio](movena/) | Wdrożona | Współtworzenie strony i motywu WordPress, landingi i kampanie Meta Ads | [movenapilates.pl](https://movenapilates.pl/) | [movena/kod-motywu](movena/kod-motywu/movena-pilates-studio) |
| [Bizzlife](bizzlife/) | Wdrożona (motyw WordPress jako wariant) | Współtworzenie redesignu i wdrożenie | [bizzlife.pl](https://bizzlife.pl/) | [bizzlife/kod-motywu](bizzlife/kod-motywu/bizlife-premium) |

W folderach tych projektów są też statyczne kopie stron wygenerowane z motywów (podgląd bez bazy danych, formularze w trybie pokazowym) oraz zrzuty ekranu. Z kodu usunięte są klucze, tokeny, adresy CRM i identyfikatory pomiaru.

## Projekty treningowe (HTML, CSS, JavaScript)

Kodowanie na podstawie projektów graficznych agencji Ajmer. Projekt graficzny i treści nie są moje, moja praca to kod. Elementy spoza zakresu ćwiczenia (wysyłka formularzy, wyszukiwanie, przełącznik języka) pokazują komunikat zamiast prowadzić donikąd.

| Projekt | Podstrony | Podgląd |
|---|---|---|
| [Topblind](topblind/) – sklep z roletami | 9 | [podgląd](https://mateusz-dams.github.io/topblind/) |
| [Silver Medica](silvermedica/) – klinika medyczna | 9 | [podgląd](https://mateusz-dams.github.io/silvermedica/) |
| [Portal leczenia AZS](azs/) – serwis medyczny | 13 | [podgląd](https://mateusz-dams.github.io/azs/) |
| [Austrowaren](austrowaren/) – firma przemysłowa | 5 | [podgląd](https://mateusz-dams.github.io/austrowaren/) |
| [Leadtrust Group](leadtrust/) – agencja pracy | 10 | [podgląd](https://mateusz-dams.github.io/leadtrust/) |
| [Staxx Polska](staxx/) – katalog produktów | 4 | [podgląd](https://mateusz-dams.github.io/staxx/) |

## Podgląd na własnym komputerze

Wszystkie podglądy to zwykłe pliki HTML. Wystarczy pobrać repozytorium i w jego folderze uruchomić prosty serwer:

```
python3 -m http.server 8000
```

a potem otworzyć `http://localhost:8000/`. Motywy WordPress instaluje się z paczek zip (opis w README każdego projektu); wymagają WordPressa 6.3+ i PHP 7.4+.

## Struktura

```
index.html          strona główna portfolio
cv/                 CV w PDF
assets/             miniatury projektów i wspólne dodatki do projektów treningowych
cod/, pm-boxing/, movena/, bizzlife/
  ├── README.md     opis projektu i mój udział
  ├── kod-motywu/   kod motywu WordPress do przejrzenia
  ├── zrzuty/       zrzuty ekranu
  └── …             statyczna kopia strony
azs/, leadtrust/, silvermedica/, topblind/, austrowaren/, staxx/
                    projekty treningowe
```
