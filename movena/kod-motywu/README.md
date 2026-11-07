# Wybrane fragmenty kodu motywu Movena Pilates Studio

To nie jest cały motyw, tylko wybrane pliki, które najlepiej pokazują, jak powstawał kod. Pełny motyw nie jest publikowany, bo prawa do niego, do treści i do zdjęć należą do Movena Pilates Studio. Kod jest pokazany wyłącznie do wglądu (zobacz [LICENSE](../../LICENSE)) i nie zawiera kluczy, tokenów, adresów CRM ani identyfikatorów pomiaru.

Opis projektu i mojego udziału: [README projektu](../README.md).

| Plik | Co pokazuje |
|---|---|
| [`inc/helpers.php`](inc/helpers.php) | Funkcje pomocnicze, m.in. przełącznik trybu zapisów: jedno ustawienie w panelu zmienia przyciski i teksty na całej stronie między Fitssey a własnym CRM. |
| [`inc/form.php`](inc/form.php) | Formularz „Poproś o telefon”: walidacja, ochrona przed spamem, SMTP, przekazanie zgłoszenia do CRM. |
| [`inc/migrate.php`](inc/migrate.php) | Automatyczna migracja treści po aktualizacji motywu: poprawia zapisane w panelu treści raz, nie ruszając tego, co zmieniono ręcznie. |
| [`template-parts/section-rezerwacja.php`](template-parts/section-rezerwacja.php) | Sekcja zapisów, która wygląda inaczej w trybie Fitssey i w trybie CRM. |
| [`assets/js/grafik.js`](assets/js/grafik.js) | Grafik zajęć z wolnymi miejscami i zapis na zajęcia przez API CRM, w czystym JavaScripcie. |
