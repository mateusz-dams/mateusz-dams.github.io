# Mateusz Dams – portfolio

Uczeń technikum w Radomiu (kierunek technik informatyk). Współtworzę strony i motywy WordPress dla firm, a w ramach nauki koduję strony w HTML, CSS i JavaScript na podstawie profesjonalnych projektów graficznych. Szukam pracy lub stażu w IT, serwisie lub doradztwie technicznym.

**Portfolio:** [mateusz-dams.github.io](https://mateusz-dams.github.io/) · **CV:** [CV-Mateusz-Dams.pdf](cv/CV-Mateusz-Dams.pdf) · **Kontakt:** [dams.mateusz@gmail.com](mailto:dams.mateusz@gmail.com), tel. 505 309 362

## Strony dla firm

Każdy projekt ma w swoim folderze opis „Mój udział”: cel, mój zakres, najtrudniejszą część i sposób sprawdzenia przed publikacją.

| Projekt | Status | Mój udział | Strona | Fragmenty kodu |
|---|---|---|---|---|
| [Centrum Odkrywania Doskonałości](cod/) | Wdrożona | Współtworzenie redesignu i motywu WordPress, wdrożenie, CRM | [cod.biz.pl](https://cod.biz.pl/) | [cod/kod-motywu](cod/kod-motywu) |
| [PM Boxing Studio](pm-boxing/) | Wdrożona | Współtworzenie strony, przeniesienie na motyw WordPress, landing, analityka, CRM | [pmboxingstudio.pl](https://pmboxingstudio.pl/) | [pm-boxing/kod-motywu](pm-boxing/kod-motywu) |
| [Movena Pilates Studio](movena/) | Wdrożona | Współtworzenie strony i motywu WordPress, landingi i kampanie Meta Ads | [movenapilates.pl](https://movenapilates.pl/) | [movena/kod-motywu](movena/kod-motywu) |
| [Bizzlife](bizzlife/) | Wdrożona (podgląd: wariant jako motyw WordPress) | Współtworzenie redesignu i wdrożenie | [bizzlife.pl](https://bizzlife.pl/) | [bizzlife/kod-motywu](bizzlife/kod-motywu) |

W folderach tych projektów są statyczne kopie stron wygenerowane z motywów (podgląd bez bazy danych, formularze w trybie pokazowym), zrzuty ekranu oraz wybrane fragmenty kodu motywów. Pełne motywy nie są publikowane, a kod nie zawiera kluczy, tokenów, adresów CRM ani identyfikatorów pomiaru.

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

a potem otworzyć `http://localhost:8000/`.

Fragmenty kodu motywów WordPress są pokazane wyłącznie do wglądu. Prawa do motywów, treści i zdjęć należą do firm, dla których powstały (zobacz [LICENSE](LICENSE)).

## Struktura

```
index.html          strona główna portfolio
cv/                 CV w PDF
assets/             miniatury projektów i wspólne dodatki do projektów treningowych
cod/, pm-boxing/, movena/, bizzlife/
  ├── README.md     opis projektu i mój udział
  ├── kod-motywu/   wybrane fragmenty kodu motywu
  ├── zrzuty/       zrzuty ekranu
  └── …             statyczna kopia strony
azs/, leadtrust/, silvermedica/, topblind/, austrowaren/, staxx/
                    projekty treningowe
```
