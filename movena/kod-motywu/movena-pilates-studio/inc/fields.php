<?php
/**
 * Pola Secure Custom Fields.
 *  - Ustawienia globalne: strona opcji „Movena”.
 *  - Strona główna: jedno pole elastyczne „Sekcje” (każda sekcja to osobny układ; elementy powtarzalne to repeatery).
 *
 * Definicje grup są zapisane jako pliki JSON w folderze acf-json/ (generuje je tools/gen-acf-json.php),
 * dzięki czemu widać je w SCF → Grupy pól. Gdy plików brak, grupy rejestrują się z kodu.
 */
if (!defined('ABSPATH')) {
    exit;
}

/* ------------------------------------------------------------ budowniczy pól */

function mvn_key(string $prefix, string $name): string
{
    return 'field_mvn_' . ($prefix !== '' ? $prefix . '__' : '') . $name;
}

/** Pole ogólne. */
function mvn_f(string $name, string $label, string $type = 'text', array $extra = [], string $prefix = ''): array
{
    return array_merge(['key' => mvn_key($prefix, $name), 'name' => $name, 'label' => $label, 'type' => $type], $extra);
}

/** Budowniczy pól w kontekście jednego układu (prefiks kluczy). */
class MVN_FB
{
    public $p;

    public function __construct(string $prefix = '')
    {
        $this->p = $prefix;
    }

    public function text(string $n, string $l, array $x = []): array { return mvn_f($n, $l, 'text', $x, $this->p); }
    public function url(string $n, string $l, array $x = []): array { return mvn_f($n, $l, 'url', $x, $this->p); }
    public function num(string $n, string $l, array $x = []): array { return mvn_f($n, $l, 'number', $x, $this->p); }
    public function area(string $n, string $l, array $x = [], int $rows = 3): array { return mvn_f($n, $l, 'textarea', array_merge(['rows' => $rows, 'new_lines' => ''], $x), $this->p); }
    public function yes(string $n, string $l, array $x = []): array { return mvn_f($n, $l, 'true_false', array_merge(['ui' => 1, 'message' => '', 'default_value' => 1], $x), $this->p); }
    public function wys(string $n, string $l, array $x = []): array { return mvn_f($n, $l, 'wysiwyg', array_merge(['tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0], $x), $this->p); }
    public function msg(string $n, string $text): array { return mvn_f($n, 'Wskazówka', 'message', ['message' => $text, 'esc_html' => 0], $this->p); }

    public function img(string $n, string $l, string $help = 'Zostaw puste, aby użyć zdjęcia dołączonego do motywu.'): array
    {
        return mvn_f($n, $l, 'image', ['return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all', 'instructions' => $help], $this->p);
    }

    /** Lista rozwijana; wartości można też wpisać ręcznie. */
    public function sel(string $n, string $l, array $choices = [], array $x = []): array
    {
        return mvn_f($n, $l, 'select', array_merge(['choices' => $choices, 'allow_null' => 0, 'ui' => 1, 'allow_custom' => 1, 'return_format' => 'value'], $x), $this->p);
    }

    /** Cel linku: kotwica sekcji (lista uzupełniana z sekcji strony) lub dowolny adres. */
    public function cel(string $n, string $l, string $default = ''): array
    {
        return $this->sel($n, $l, [], ['default_value' => $default, 'instructions' => 'Wybierz sekcję z listy albo wpisz własny adres (np. https://…).']);
    }

    /** Forma treningu powiązana z formularzem (lista uzupełniana z ustawień). */
    public function form(string $n = 'form', string $l = 'Forma treningu w formularzu'): array
    {
        return $this->sel($n, $l, [], ['default_value' => '', 'instructions' => 'Wybór ustawia pole „Forma treningu” w formularzu po kliknięciu. Puste = klient wybiera sam.']);
    }

    /**
     * Repeater. $subs: lista [name, label, type?, extra?]. Podpole 'form' i 'cel' traktowane specjalnie.
     */
    public function rep(string $n, string $l, array $subs, array $x = []): array
    {
        $sp = $this->p . '__' . $n;
        $b = new MVN_FB($sp);
        $sub = [];
        foreach ($subs as $s) {
            $type = $s[2] ?? 'text';
            $extra = $s[3] ?? [];
            if ($s[0] === 'form') {
                $sub[] = $b->form('form', $s[1]);
            } elseif ($s[0] === 'cel') {
                $sub[] = $b->cel('cel', $s[1]);
            } elseif ($type === 'select') {
                $sub[] = $b->sel($s[0], $s[1], $extra['choices'] ?? [], $extra);
            } elseif ($type === 'textarea') {
                $sub[] = $b->area($s[0], $s[1], $extra, $extra['rows'] ?? 2);
            } elseif ($type === 'image') {
                $sub[] = $b->img($s[0], $s[1], $extra['instructions'] ?? 'PNG lub WebP.');
            } else {
                $sub[] = mvn_f($s[0], $s[1], $type, $extra, $sp);
            }
        }
        return array_merge([
            'key' => mvn_key($this->p, $n), 'name' => $n, 'label' => $l, 'type' => 'repeater',
            'layout' => 'table', 'button_label' => 'Dodaj pozycję', 'sub_fields' => $sub,
        ], $x);
    }

    /** Lista punktów: repeater z jednym polem tekstowym. */
    public function items(string $n, string $l, string $btn = 'Dodaj punkt'): array
    {
        return $this->rep($n, $l, [['text', 'Tekst']], ['button_label' => $btn]);
    }
}

function mvn_tab(string $id, string $label): array
{
    return ['key' => 'field_mvn_tab_' . $id, 'label' => $label, 'name' => '', 'type' => 'tab', 'placement' => 'top', 'endpoint' => 0];
}

/* ------------------------------------------------------------ układy sekcji */

/** Wspólne pola każdej sekcji. */
function mvn_common_fields(MVN_FB $b, string $layout, array $opt): array
{
    $f = [
        $b->text('anchor', 'Kotwica (ID sekcji)', ['instructions' => 'Adres sekcji na stronie, np. „oferta” → /#oferta. Małe litery, bez spacji. Używana w menu i w linkach.']),
        $b->yes('show', 'Pokaż sekcję'),
    ];
    if (!empty($opt['menu'])) {
        $f[] = $b->yes('in_menu', 'Pokaż w menu', ['default_value' => 0]);
        $f[] = $b->text('menu_label', 'Nazwa w menu');
    }
    if (!empty($opt['ghost'])) {
        $f[] = $b->text('ghost', 'Duży napis w tle', ['instructions' => 'Puste = bez napisu.']);
    }
    if (!empty($opt['tlo'])) {
        $f[] = $b->sel('tlo', 'Tło sekcji', ['' => 'Jasne', 'alt' => 'Beżowy panel'], ['allow_custom' => 0, 'default_value' => '']);
    }
    return $f;
}

/** Składa układ: wspólne pola + pola właściwe. */
function mvn_layout(string $slug, string $label, array $opt, callable $fields, int $max = 0): array
{
    $b = new MVN_FB('s_' . $slug);
    $sub = array_merge(mvn_common_fields($b, $slug, $opt), $fields($b));
    return [
        'key' => 'layout_mvn_' . $slug, 'name' => $slug, 'label' => $label, 'display' => 'block',
        'sub_fields' => $sub, 'min' => '', 'max' => $max ?: '',
    ];
}

function mvn_icon_choices(): array
{
    return ['users' => 'Ludzie', 'user' => 'Osoba', 'clock' => 'Zegar', 'leaf' => 'Liść', 'pin' => 'Pinezka', 'reformer' => 'Reformer', 'heart' => 'Serce',
        'calendar' => 'Kalendarz', 'check' => 'Ptaszek', 'star' => 'Gwiazdka', 'award' => 'Nagroda', 'phone' => 'Telefon', 'gift' => 'Prezent', 'info' => 'Informacja'];
}

function mvn_section_layouts(): array
{
    $std = ['menu' => 1, 'tlo' => 1];
    $icons = ['choices' => mvn_icon_choices(), 'allow_custom' => 0];
    $L = [];

    $L[] = mvn_layout('hero', 'Góra strony (hero)', ['menu' => 1], function (MVN_FB $b) use ($icons) {
        return [
            $b->msg('info', 'W tekstach możesz użyć <code>**pogrubienia**</code>, <code>[tekstu linku](#kotwica)</code> oraz znaczników <code>{cena}</code> i <code>{cena_przed}</code> (cena pierwszego treningu z ustawień → Cennik).'),
            $b->text('eyebrow', 'Mały napis nad nagłówkiem'),
            $b->text('title', 'Nagłówek (H1)'),
            $b->area('lead', 'Opis pod nagłówkiem', [], 4),
            $b->rep('facts', 'Wyróżniki (do 5)', [['icon', 'Ikona', 'select', $icons], ['text', 'Tekst']], ['max' => 5]),
            $b->text('cta_primary', 'Przycisk główny'), $b->cel('cta_primary_cel', 'Przycisk główny: cel', 'fitssey:schedule'),
            $b->text('cta_secondary', 'Przycisk dodatkowy'), $b->cel('cta_secondary_cel', 'Przycisk dodatkowy: cel', '#pricing'),
            $b->img('image', 'Zdjęcie w hero'), $b->text('image_alt', 'Opis zdjęcia (alt)'),
        ];
    }, 1);

    $L[] = mvn_layout('promo', 'Baner promocji', [], function (MVN_FB $b) {
        return [
            $b->area('text', 'Tekst (np. „**Pierwszy trening za {cena}** zamiast {cena_przed}”)', [], 2),
            $b->text('btn', 'Przycisk'), $b->cel('btn_cel', 'Przycisk: cel', '#pricing'),
        ];
    });

    $L[] = mvn_layout('trust', 'Pasek zaufania (krótkie atuty)', [], function (MVN_FB $b) use ($icons) {
        return [$b->rep('items', 'Atuty', [['icon', 'Ikona', 'select', $icons], ['text', 'Tekst']], ['max' => 6])];
    });

    $L[] = mvn_layout('pierwszy', 'Pierwszy raz (obawy + polecane zajęcia)', $std, function (MVN_FB $b) {
        return [
            $b->msg('info', 'Znaczniki <code>{cena}</code> i <code>{cena_przed}</code> zamieniają się w ceny z ustawień.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('lead', 'Wstęp', [], 2),
            $b->rep('items', 'Obawy i odpowiedzi', [['title', 'Pogrubiony tekst'], ['text', 'Opis', 'textarea', ['rows' => 2]]], ['layout' => 'block', 'button_label' => 'Dodaj punkt']),
            $b->text('box_tag', 'Ramka: plakietka z ceną'), $b->text('box_title', 'Ramka: tytuł'),
            $b->items('box_items', 'Ramka: polecane zajęcia'),
            $b->text('box_btn', 'Ramka: przycisk'), $b->cel('box_cel', 'Ramka: cel', '#rezerwacja'),
        ];
    });

    $L[] = mvn_layout('kroki', 'Jak zacząć (kroki)', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'),
            $b->rep('steps', 'Kroki (numerowane automatycznie)', [['title', 'Tytuł'], ['text', 'Opis', 'textarea', ['rows' => 2]], ['link', 'Napis linku (opcjonalnie)'], ['cel', 'Cel linku']], ['layout' => 'block']),
        ];
    });

    $L[] = mvn_layout('zajecia', 'Zajęcia (karty z filtrem)', $std, function (MVN_FB $b) {
        return [
            $b->msg('info', 'Listę zajęć (nazwy, opisy, czas, „dobry wybór na początek”) edytujesz w <strong>Movena → Zajęcia</strong>. Te same dane zasilają stronę docelową.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('lead', 'Wstęp', [], 2),
            $b->text('filter_all', 'Filtr: wszystkie'), $b->text('filter_rec', 'Filtr: polecane na początek'), $b->text('badge', 'Plakietka przy polecanych'),
            $b->text('btn', 'Przycisk na karcie'), $b->cel('btn_cel', 'Przycisk na karcie: cel', 'fitssey:schedule'),
        ];
    }, 1);

    $L[] = mvn_layout('rezerwacja', 'Zapisy online (kalendarz z płatnością)', $std, function (MVN_FB $b) {
        return [
            $b->msg('info', 'Terminy i wolne miejsca pobierają się na żywo z CRM (grafik zajęć grupowych). Sekcja pokazuje się tylko, gdy w <strong>Movena → CRM</strong> jest włączony „Kalendarz zapisów online”.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 3),
            $b->area('points', 'Punkty (każdy w osobnym wierszu)', [], 4),
            $b->text('call_title', 'Ramka „zadzwoń”: tytuł'), $b->text('call_text', 'Ramka „zadzwoń”: tekst'), $b->text('call_btn', 'Ramka „zadzwoń”: przycisk'),
            $b->text('hint', 'Mały druk pod przyciskiem płatności'),
        ];
    }, 1);

    $L[] = mvn_layout('formaty', 'Formy treningu (karty wyboru)', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'),
            $b->rep('cards', 'Karty', [['title', 'Tytuł'], ['bullets', 'Punkty (każdy w osobnym wierszu)', 'textarea', ['rows' => 4]], ['btn', 'Przycisk'], ['cel', 'Cel'], ['featured', 'Wyróżnij (ciemna karta)', 'true_false', ['ui' => 1, 'default_value' => 0, 'message' => '']]], ['layout' => 'block', 'max' => 4]),
        ];
    });

    $L[] = mvn_layout('cennik', 'Cennik (pakiety z grafikami)', $std, function (MVN_FB $b) {
        return [
            $b->msg('info', 'Ceny i pakiety edytujesz w <strong>Movena → Cennik</strong>. Przyciski „Kup” prowadzą do sklepu w CRM Moveny, „Poznaj REFORMER” otwiera kalendarz zapisów.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('lead', 'Wstęp', [], 2),
            $b->text('note_group', 'Uwaga nad pakietami'),
            $b->text('buy', 'Przycisk „Kup”'), $b->text('best', 'Plakietka „najczęściej wybierany”'),
            $b->text('help_title', 'Pasek pomocy: tytuł'), $b->text('help_text', 'Pasek pomocy: tekst'), $b->text('help_btn', 'Pasek pomocy: przycisk (dopisze numer telefonu)'),
        ];
    }, 1);

    $L[] = mvn_layout('dlaczego', 'Dlaczego kameralna grupa', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'),
            $b->rep('items', 'Powody (numerowane automatycznie)', [['title', 'Tytuł'], ['text', 'Opis', 'textarea', ['rows' => 2]]], ['layout' => 'block']),
            $b->area('disclaimer', 'Uwaga pod listą', [], 3),
        ];
    });

    $L[] = mvn_layout('o_studiu', 'O studiu i galeria', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('lead', 'Opis', [], 4),
            $b->img('image', 'Zdjęcie główne'), $b->text('image_alt', 'Opis zdjęcia (alt)'),
            $b->text('btn', 'Przycisk'), $b->cel('btn_cel', 'Przycisk: cel', 'fitssey:schedule'),
            $b->rep('gallery', 'Galeria', [['image', 'Zdjęcie', 'image'], ['alt', 'Opis (alt)']], ['max' => 12]),
            $b->text('caption', 'Podpis pod galerią'),
        ];
    });

    $L[] = mvn_layout('instruktorki', 'Instruktorki', $std, function (MVN_FB $b) {
        return [
            $b->msg('info', 'Bez dodanych osób sekcja pokazuje ogólny panel o opiece instruktorki. Dodaj osoby (zdjęcie, imię, krótki opis), gdy chcesz je przedstawić.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'),
            $b->text('panel_title', 'Panel: tytuł'), $b->area('panel_text', 'Panel: tekst', [], 3),
            $b->rep('people', 'Osoby (opcjonalnie)', [['photo', 'Zdjęcie', 'image'], ['name', 'Imię i nazwisko'], ['role', 'Funkcja'], ['bio', 'Krótki opis', 'textarea', ['rows' => 3]]], ['layout' => 'block', 'button_label' => 'Dodaj osobę']),
        ];
    });

    $L[] = mvn_layout('opinie', 'Opinie klientek', $std, function (MVN_FB $b) {
        return [
            $b->msg('info', 'Dodawaj wyłącznie prawdziwe opinie (z Google, Facebooka, wiadomości od klientek, za ich zgodą). Sekcja jest domyślnie wyłączona.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'),
            $b->num('count', 'Liczba opinii w Google', ['instructions' => 'Opcjonalnie. Wyświetla się jako „43 opinie w Google” oraz trafia do danych strukturalnych (razem ze średnią oceną z ustawień SEO).']),
            $b->text('btn', 'Przycisk do wszystkich opinii (adres Map z ustawień)'),
            $b->rep('items', 'Opinie', [['name', 'Imię i inicjał'], ['text', 'Treść', 'textarea', ['rows' => 3]]], ['layout' => 'block', 'button_label' => 'Dodaj opinię']),
        ];
    });

    $L[] = mvn_layout('vouchery', 'Vouchery prezentowe', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('lead', 'Wstęp', [], 2),
            $b->rep('items', 'Vouchery', [['kind', 'Rodzaj'], ['sessions', 'Liczba wejść', 'number'], ['price', 'Cena (zł)', 'number']]),
            $b->text('btn', 'Przycisk'), $b->cel('btn_cel', 'Przycisk: cel', 'fitssey:vouchers'),
        ];
    });

    $L[] = mvn_layout('faq', 'FAQ (pytania i odpowiedzi)', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'),
            $b->rep('items', 'Pytania i odpowiedzi', [['q', 'Pytanie'], ['a', 'Odpowiedź', 'textarea', ['rows' => 4]]], ['layout' => 'block', 'button_label' => 'Dodaj pytanie']),
        ];
    });

    $L[] = mvn_layout('lokalizacja', 'Lokalizacja (mapa i dane)', $std, function (MVN_FB $b) {
        return [
            $b->msg('info', 'Adres, telefon, profile społecznościowe i mapę edytujesz w <strong>Movena → Dane i kontakt</strong>.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'),
            $b->text('btn', 'Przycisk'), $b->cel('btn_cel', 'Przycisk: cel', 'fitssey:schedule'),
        ];
    });

    $L[] = mvn_layout('kontakt', 'Kontakt i formularz', $std, function (MVN_FB $b) {
        return [
            $b->msg('info', 'Telefon, e-mail, adres i profile edytujesz w <strong>Movena → Dane i kontakt</strong>. Zapisy i zakup karnetu odbywają się online w CRM Moveny; formularz służy do pytań i próśb o telefon.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 3),
            $b->text('aside_title', 'Obok formularza: tytuł'), $b->items('aside_items', 'Obok formularza: punkty'),
            $b->text('aside_btn', 'Obok formularza: przycisk zapisów (puste = bez)'), $b->cel('aside_cel', 'Obok formularza: cel', 'fitssey:schedule'),
            $b->text('form_title', 'Formularz: tytuł'), $b->area('form_lead', 'Formularz: opis', [], 2),
            $b->form('form_default', 'Formularz: domyślna forma treningu'),
            $b->text('form_button', 'Formularz: przycisk'), $b->area('form_consent', 'Formularz: treść zgody', [], 3),
            $b->text('form_note', 'Formularz: dopisek pod zgodą'),
            $b->text('thanks_title', 'Po wysłaniu: tytuł'), $b->area('thanks_text', 'Po wysłaniu: tekst', [], 2),
        ];
    }, 1);

    $L[] = mvn_layout('final', 'Sekcja końcowa (wezwanie do działania)', [], function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł'), $b->area('text', 'Tekst', [], 3),
            $b->text('btn1', 'Przycisk 1'), $b->cel('btn1_cel', 'Przycisk 1: cel', 'fitssey:schedule'),
            $b->text('btn2', 'Przycisk 2'), $b->cel('btn2_cel', 'Przycisk 2: cel', '#pricing'),
        ];
    });

    /* ---- układy używane głównie na stronie docelowej ---- */
    $L[] = mvn_layout('lp_hero', 'Landing: góra strony z ceną', ['menu' => 1], function (MVN_FB $b) use ($icons) {
        return [
            $b->msg('info', 'Cenę i cenę „przed” pobieramy z <strong>Movena → Cennik</strong> (pierwszy trening). W tekstach możesz użyć <code>{cena}</code> i <code>{cena_przed}</code>.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Nagłówek (H1)'), $b->area('lead', 'Obietnica (opis pod nagłówkiem)', [], 4),
            $b->rep('facts', 'Wyróżniki (do 4)', [['icon', 'Ikona', 'select', $icons], ['text', 'Tekst']], ['max' => 4]),
            $b->text('price_label', 'Napis przy cenie (np. „pierwszy trening”)'), $b->text('note', 'Dopisek pod przyciskami', ['instructions' => 'np. warunki oferty.']),
            $b->text('cta', 'Przycisk główny'), $b->cel('cta_cel', 'Przycisk główny: cel', 'fitssey:schedule'),
            $b->text('cta2', 'Przycisk dodatkowy'), $b->cel('cta2_cel', 'Przycisk dodatkowy: cel', '#kontakt'),
            $b->img('image', 'Zdjęcie'), $b->text('image_alt', 'Opis zdjęcia (alt)'),
        ];
    }, 1);

    $L[] = mvn_layout('lp_oferta', 'Landing: karta oferty z ceną', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 3),
            $b->text('badge', 'Plakietka nad kartą'), $b->text('card_title', 'Karta: nazwa'),
            $b->text('price_note', 'Karta: dopisek przy cenie'),
            $b->items('bullets', 'Karta: co dostajesz'),
            $b->text('cta', 'Przycisk główny'), $b->cel('cta_cel', 'Przycisk główny: cel', 'fitssey:schedule'),
            $b->text('cta2', 'Przycisk dodatkowy'), $b->cel('cta2_cel', 'Przycisk dodatkowy: cel', '#kontakt'),
            $b->area('note', 'Uwaga pod przyciskami', [], 2),
        ];
    });

    $L[] = mvn_layout('rf_hero', 'Program: góra strony z ceną programu', ['menu' => 1], function (MVN_FB $b) use ($icons) {
        return [
            $b->msg('info', 'Cena programu wpisana ręcznie poniżej (nie z cennika). Przyciski mogą prowadzić do rozmowy (cel „Napisz do nas”) albo do formularza.'),
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Nagłówek (H1)'), $b->area('lead', 'Obietnica (opis pod nagłówkiem)', [], 4),
            $b->text('price', 'Cena programu (np. 600 zł)'), $b->text('price_label', 'Napis przy cenie (np. „za cały program”)'), $b->text('price_note', 'Dopisek przy cenie (np. „10 treningów, 60 zł za trening”)'),
            $b->rep('facts', 'Wyróżniki (do 4)', [['icon', 'Ikona', 'select', $icons], ['text', 'Tekst']], ['max' => 4]),
            $b->text('cta', 'Przycisk główny'), $b->cel('cta_cel', 'Przycisk główny: cel', 'msg'),
            $b->text('cta2', 'Przycisk dodatkowy'), $b->cel('cta2_cel', 'Przycisk dodatkowy: cel', '#kontakt'),
            $b->text('note', 'Dopisek pod przyciskami'),
            $b->img('image', 'Zdjęcie'), $b->text('image_alt', 'Opis zdjęcia (alt)'),
        ];
    }, 1);

    $L[] = mvn_layout('rf_grupy', 'Program: wybór grupy (karty terminów)', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 3),
            $b->rep('groups', 'Grupy (karty)', [
                ['name', 'Nazwa grupy (np. Grupa A)'], ['days', 'Dni (np. Poniedziałki + środy)'], ['time', 'Godzina (np. 17:00)'],
                ['spots', 'Wolne miejsca (np. 4)'], ['start', 'Start (np. od 4 listopada) — puste = nie pokazujemy'],
                ['btn', 'Przycisk'], ['cel', 'Cel przycisku'],
            ], ['layout' => 'block', 'max' => 4]),
            $b->area('note', 'Uwaga pod kartami', [], 2),
        ];
    });

    $L[] = mvn_layout('przebieg', 'Jak wygląda trening (kroki ze zdjęciem)', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 3),
            $b->img('image', 'Zdjęcie'), $b->text('image_alt', 'Opis zdjęcia (alt)'),
            $b->rep('steps', 'Etapy (numerowane automatycznie)', [['title', 'Tytuł'], ['text', 'Opis', 'textarea', ['rows' => 2]]], ['layout' => 'block']),
        ];
    });

    $L[] = mvn_layout('tekst', 'Tekst + zdjęcie (własna sekcja)', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł'),
            $b->rep('paragraphs', 'Akapity', [['text', 'Treść', 'textarea', ['rows' => 4]]], ['layout' => 'block', 'button_label' => 'Dodaj akapit']),
            $b->img('image', 'Zdjęcie', 'Wybierz zdjęcie z biblioteki. Puste = sekcja bez zdjęcia.'), $b->text('image_alt', 'Opis zdjęcia (alt)'),
            $b->sel('side', 'Zdjęcie po stronie', ['left' => 'Lewej', 'right' => 'Prawej'], ['allow_custom' => 0, 'default_value' => 'left']),
            $b->text('btn', 'Przycisk (puste = bez przycisku)'), $b->cel('btn_cel', 'Przycisk: cel', '#kontakt'),
        ];
    });

    $L[] = mvn_layout('wlasna', 'Własna sekcja (nagłówek + edytor tekstu)', $std, function (MVN_FB $b) {
        return [
            $b->text('eyebrow', 'Mały napis'), $b->text('title', 'Tytuł'), $b->area('intro', 'Wstęp', [], 2),
            $b->wys('content', 'Treść'),
            $b->text('btn', 'Przycisk (puste = bez przycisku)'), $b->cel('btn_cel', 'Przycisk: cel', '#kontakt'),
        ];
    });

    return $L;
}

/* ------------------------------------------------------------ grupy pól */

/** Grupa „Sekcje” (strona główna i landing). */
function mvn_group_front(): array
{
    return [
        'key' => 'group_mvn_front', 'title' => 'Movena: sekcje strony',
        'fields' => [[
            'key' => 'field_mvn_sekcje', 'label' => 'Sekcje strony', 'name' => 'sekcje', 'type' => 'flexible_content',
            'instructions' => 'Każda sekcja to osobny blok. Przeciągaj, aby zmienić kolejność, dodawaj nowe lub wyłączaj istniejące. Sekcje z włączonym „Pokaż w menu” tworzą menu strony.',
            'layouts' => mvn_section_layouts(), 'button_label' => 'Dodaj sekcję', 'min' => '', 'max' => '',
        ]],
        'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']], [['param' => 'page_template', 'operator' => '==', 'value' => 'page-landing.php']]],
        'menu_order' => 0, 'position' => 'acf_after_title', 'style' => 'seamless', 'label_placement' => 'top', 'instruction_placement' => 'label',
        'hide_on_screen' => ['the_content', 'discussion', 'comments', 'slug', 'author', 'format', 'categories', 'tags', 'send-trackbacks'],
        'active' => true, 'show_in_rest' => 0,
    ];
}

/** Wiersz cennika (pakiet). */
function mvn_price_subs(): array
{
    return [
        ['name', 'Nazwa pakietu'], ['price', 'Cena (zł)', 'number'], ['sessions', 'Liczba treningów', 'number'],
        ['per', 'Cena za trening (zł; 0 = nie pokazuj)', 'number'], ['per_note', 'Dopisek zamiast ceny za trening (np. „150 zł / os. za trening”)'],
        ['validity', 'Ważność (np. „1 miesiąc od pierwszej wizyty”)'], ['recommended', 'Najczęściej wybierany', 'true_false', ['ui' => 1, 'default_value' => 0, 'message' => '']],
        ['badge', 'Własna plakietka (np. „Dla pierwszej wizyty”)'], ['restriction', 'Ograniczenie (mały druk pod przyciskiem)'], ['url', 'Adres „Kup” (puste = sklep w CRM)'],
        ['img', 'Grafika (nazwa pliku z assets/img/pakiety bez „.jpg”; puste = dobierze się sama)'],
    ];
}

/** Grupa „Ustawienia” (strona opcji). */
function mvn_group_site(): array
{
    $b = new MVN_FB('');
    $fields = [
        mvn_tab('dane', 'Dane i kontakt'),
        $b->text('brand', 'Nazwa studia'),
        $b->text('phone_display', 'Telefon (jak wyświetlać)', ['instructions' => 'np. 508 923 051']),
        $b->text('phone_tel', 'Telefon (do linku)', ['instructions' => 'Format międzynarodowy z plusem, np. +48508923051']),
        $b->text('email', 'E-mail kontaktowy (na stronie)'),
        $b->text('address', 'Adres (ulica i numer)'), $b->text('postal', 'Kod pocztowy'), $b->text('city', 'Miasto'),
        $b->url('facebook', 'Facebook (adres profilu)', ['instructions' => 'Puste = ikona się nie pokaże.']), $b->url('instagram', 'Instagram (adres profilu)'),
        $b->url('maps_url', 'Link „Wyznacz trasę” (Mapy Google)'),
        $b->url('maps_embed', 'Adres mapy do osadzenia (iframe)', ['instructions' => 'Mapy Google → Udostępnij → Umieść mapę → sam adres z src="…".']),
        $b->url('reviews_url', 'Link do opinii w Google (opcjonalnie)'),
        $b->url('message_url', 'Link do rozmowy (Messenger lub Instagram DM)', ['instructions' => 'Przyciski „Napisz do nas” otwierają ten adres. Messenger: https://m.me/NAZWA-STRONY, Instagram: https://ig.me/m/movenapilates. Puste = Instagram.']),
        $b->text('whatsapp_number', 'Numer WhatsApp (opcjonalnie)', ['instructions' => 'Format międzynarodowy bez plusa, np. 48508923051. Puste = bez WhatsAppa.']),
        $b->area('whatsapp_message', 'Pierwsza wiadomość na WhatsAppie'),
        $b->rep('hours', 'Godziny otwarcia (opcjonalnie)', [['days', 'Dni'], ['hours', 'Godziny']], ['instructions' => 'Pokazują się w stopce i w danych strukturalnych. Format godzin: 8:00–21:00.']),
        $b->text('company_name', 'Pełna nazwa firmy (do stron prawnych)'), $b->text('company_address', 'Adres siedziby firmy'),
        $b->text('nip', 'NIP'), $b->text('regon', 'REGON'), $b->text('krs', 'KRS (jeśli dotyczy)'),
        $b->text('gdpr_email', 'E-mail do spraw danych osobowych (RODO)'),
        $b->text('founder_name', 'Imię i nazwisko właścicielki (do danych strukturalnych, opcjonalnie)'),

        mvn_tab('fitssey', 'Zapisy i zakupy (CRM)'),
        $b->msg('info_fitssey', 'Zapisy na zajęcia i zakup karnetów działają w CRM Moveny. Puste adresy uzupełniają się same na podstawie adresu CRM z zakładki „CRM” (kalendarz zapisów na tej stronie, sklep, logowanie i vouchery w CRM).'),
        $b->url('fitssey_schedule', 'Grafik zajęć (puste = kalendarz zapisów na tej stronie)'), $b->url('fitssey_pricing', 'Cennik / zakup karnetu (puste = cennik na tej stronie)'),
        $b->url('fitssey_login', 'Logowanie klientek (puste = panel klientki w CRM)'), $b->url('fitssey_vouchers', 'Vouchery (puste = vouchery w CRM)'),

        mvn_tab('zajecia', 'Zajęcia'),
        $b->msg('info_zajecia', 'Lista zasila dane SEO i polecane zajęcia na stronie docelowej. W studiu jest jeden rodzaj zajęć — Pilates na reformerach.'),
        $b->rep('zajecia', 'Zajęcia', [
            ['name', 'Nazwa'], ['tagline', 'Krótki opis', 'textarea', ['rows' => 2]], ['for', 'Dla kogo', 'textarea', ['rows' => 2]],
            ['duration', 'Czas trwania (minuty)', 'number'], ['recommended', 'Dobry wybór na początek', 'true_false', ['ui' => 1, 'default_value' => 0, 'message' => '']],
            ['url', 'Własny adres „Sprawdź terminy” (puste = kalendarz zapisów)'],
        ], ['layout' => 'block', 'button_label' => 'Dodaj zajęcia']),

        mvn_tab('cennik', 'Cennik'),
        $b->msg('info_cennik', 'Ceny wpisuj jako liczby (bez „zł”). <strong>Pierwszy trening</strong> (cena promocyjna) pojawia się w hero, banerze promocji i na stronie docelowej jako <code>{cena}</code>.'),
        $b->num('first_price', 'Cena pierwszego treningu (zł)'), $b->num('first_was', 'Cena zwykła przed promocją (zł)', ['instructions' => 'Pokazuje się jako „zamiast …”. 0 = bez przekreślonej ceny.']),
        $b->rep('cennik_grupa', 'Zajęcia grupowe: pakiety', mvn_price_subs(), ['layout' => 'block', 'button_label' => 'Dodaj pakiet']),

        mvn_tab('wyglad', 'Nagłówek i stopka'),
        $b->img('logo', 'Logo (ciemne, do nagłówka)', 'Zostaw puste, aby użyć logo z motywu.'),
        $b->img('logo_light', 'Logo (jasne, do ciemnej stopki)', 'Zostaw puste, aby użyć logo z motywu.'),
        $b->msg('info_menu', 'Menu główne tworzy się automatycznie z sekcji strony głównej, które mają włączone „Pokaż w menu” (Strony → Strona główna).'),
        $b->text('header_cta', 'Przycisk w nagłówku'), $b->cel('header_cta_cel', 'Przycisk w nagłówku: cel', 'fitssey:schedule'),
        $b->text('header_call', 'Napis przy telefonie w menu mobilnym'),
        $b->text('mobile_call', 'Dolny pasek na telefonie: „Zadzwoń”'), $b->text('mobile_cta', 'Dolny pasek na telefonie: przycisk główny'),
        $b->cel('mobile_cta_cel', 'Dolny pasek na telefonie: cel przycisku', 'fitssey:schedule'),
        $b->area('foot_tagline', 'Opis pod logo w stopce'),
        $b->text('foot_photo_credit', 'Podpis pod galerią lub w stopce (np. autor wizualizacji)'),

        mvn_tab('seo', 'SEO i udostępnianie'),
        $b->text('seo_title', 'Tytuł strony głównej (title)', ['instructions' => 'Do ok. 60 znaków, z frazą lokalną (np. „Pilates Radom”). Wtyczki SEO (Yoast, Rank Math) mają pierwszeństwo, jeśli są aktywne.']),
        $b->area('seo_description', 'Opis strony głównej (meta description)', ['instructions' => 'Do ok. 160 znaków.']),
        $b->img('og_image', 'Obraz przy udostępnianiu (Facebook, Messenger)', 'Zalecane 1200×630 px. Puste = obraz z motywu.'),
        $b->text('og_image_alt', 'Opis obrazu przy udostępnianiu (alt)'),
        $b->text('gsc_verification', 'Google Search Console: kod weryfikacyjny', ['instructions' => 'Sama wartość z metatagu google-site-verification (content="…"). Puste = weryfikuj inną metodą.']),
        $b->text('bing_verification', 'Bing Webmaster: kod weryfikacyjny', ['instructions' => 'Wartość z metatagu msvalidate.01 (opcjonalnie).']),
        $b->text('geo_lat', 'Szerokość geograficzna (do danych lokalnych)', ['instructions' => 'np. 51.3989. Poprawia dane strukturalne dla wyników lokalnych.']),
        $b->text('geo_lng', 'Długość geograficzna', ['instructions' => 'np. 21.1470']),
        $b->text('price_range', 'Przedział cen (dane strukturalne)', ['instructions' => 'np. 69–1938 zł. Puste = bez.']),
        $b->text('rating_value', 'Średnia ocena z Google (np. 5.0)', ['instructions' => 'Wpisz tylko prawdziwą ocenę z wizytówki Google. Liczba opinii pochodzi z sekcji Opinie. Puste = bez oceny w danych strukturalnych.']),

        mvn_tab('schema', 'Dane strukturalne'),
        $b->msg('schema_info', 'Dane strukturalne (JSON-LD) to ukryty opis strony dla Google: kim jesteście, gdzie, kiedy otwarci, jakie pytania i odpowiedzi macie. Google może na tej podstawie pokazać dane firmy i rozwijane pytania w wynikach. <strong>Nie są widoczne dla odwiedzających</strong>, ale muszą zgadzać się z treścią strony. Poniżej widzisz, co dokładnie jest wysyłane i skąd pochodzi każda wartość.'),
        mvn_f('schema_preview', 'Podgląd: co jest teraz w danych strukturalnych', 'message', ['message' => '', 'esc_html' => 0]),
        $b->yes('schema_business_on', 'Firma lokalna (nazwa, adres, telefon, godziny…)'),
        $b->yes('schema_website_on', 'Strona WWW (nazwa i adres strony)'),
        $b->yes('schema_faq_on', 'FAQ (pytania i odpowiedzi z sekcji FAQ)', ['instructions' => 'Odpowiedzi muszą być też widoczne na stronie; sekcja FAQ spełnia ten warunek.']),
        $b->yes('schema_offers_on', 'Oferta (pakiety z cennika, z cenami w zł)'),
        $b->yes('schema_hours_on', 'Godziny otwarcia (z „Dane i kontakt”)'),
        $b->sel('schema_type', 'Typ firmy', ['ExerciseGym' => 'Studio ćwiczeń / klub fitness (ExerciseGym) — zalecane', 'HealthClub' => 'Klub zdrowia (HealthClub)', 'SportsActivityLocation' => 'Obiekt sportowy (SportsActivityLocation)', 'SportsClub' => 'Klub sportowy (SportsClub)'], ['allow_custom' => 0, 'default_value' => 'ExerciseGym', 'instructions' => 'Zawsze dodawany jest też ogólny typ LocalBusiness.']),
        $b->text('schema_name', 'Nazwa firmy (nadpisanie)', ['instructions' => 'Puste = nazwa studia z „Dane i kontakt”.']),
        $b->area('schema_description', 'Opis firmy (nadpisanie)', ['instructions' => 'Puste = opis strony głównej z zakładki SEO.']),
        $b->text('schema_area', 'Obszar działania (nadpisanie)', ['instructions' => 'Puste = miasto z „Dane i kontakt”. Można wpisać np. „Radom i okolice”.']),
        $b->rep('schema_same_as', 'Dodatkowe profile (sameAs)', [['url', 'Adres profilu', 'url']], ['button_label' => 'Dodaj profil']),
        $b->area('schema_extra', 'Własne dane (zaawansowane, JSON-LD)', ['instructions' => 'Opcjonalnie: wklej gotowy obiekt JSON-LD (np. dodatkowy typ). Zostanie dołączony do danych strony; błędny JSON jest pomijany i oznaczony w podglądzie. Bez znaczników <script>.'], 8),

        mvn_tab('tracking', 'Analityka i reklamy'),
        $b->msg('tracking_info', 'Wszystkie narzędzia ładują się dopiero po zgodzie z banera cookies i respektują Consent Mode v2 (domyślnie wszystko odrzucone). <strong>Dwa tryby:</strong> (1) wpisz <em>GTM</em> — wtedy GA4, piksel Meta, Google Ads i Hotjar konfigurujesz w Menedżerze Tagów, a motyw wysyła do niego zdarzenia: <code>generate_lead</code> (wysłany formularz), <code>click_fitssey</code> (przejście do zakupu w CRM), <code>contact</code> (telefon, e-mail, WhatsApp), <code>cta_click</code> — każde z <code>event_id</code>; (2) bez GTM — motyw ładuje bezpośrednio GA4, Meta Pixel, Google Ads i Hotjar z poniższych pól. <strong>Gdy wpiszesz GTM, pola bezpośrednie są ignorowane</strong> (żeby nic nie liczyło się podwójnie). Po zmianach zaktualizuj strony Pliki cookies i Polityka prywatności (Narzędzia → Przywróć strony prawne).'),
        $b->text('gtm_id', 'Google Tag Manager: identyfikator kontenera', ['instructions' => 'np. GTM-XXXXXXX. Puste = bez GTM.', 'placeholder' => 'GTM-XXXXXXX']),
        $b->text('ga4', 'Google Analytics 4: identyfikator pomiaru', ['instructions' => 'np. G-XXXXXXXXXX (tryb bez GTM).', 'placeholder' => 'G-XXXXXXXXXX']),
        $b->text('ads_id', 'Google Ads: identyfikator konwersji', ['instructions' => 'np. AW-123456789 (tryb bez GTM).', 'placeholder' => 'AW-123456789']),
        $b->text('ads_label_lead', 'Google Ads: etykieta konwersji „Lead” (wysłany formularz)', ['instructions' => 'Z Google Ads → Cele → Konwersje → fragment zdarzenia: wartość po ukośniku w send_to.']),
        $b->text('ads_label_contact', 'Google Ads: etykieta konwersji „Kontakt” (telefon, WhatsApp, e-mail)'),
        $b->text('ads_label_fitssey', 'Google Ads: etykieta konwersji „Przejście do zakupu” (opcjonalnie)'),
        $b->text('meta_pixel', 'Meta Pixel: identyfikator piksela', ['instructions' => '15–16 cyfr (tryb bez GTM). Do Conversions API potrzebny także w trybie GTM.', 'placeholder' => '1234567890123456']),
        $b->text('meta_capi_token', 'Meta Conversions API: token dostępu', ['instructions' => 'Bezpieczniej: wpisz token w pliku wp-config.php jako define(\'MVN_CAPI_TOKEN\', \'...\'); — wtedy to pole jest ignorowane. Wysyłka zdarzenia Lead z serwera, odporna na blokery, z deduplikacją po event_id. Działa tylko, gdy odwiedzający zgodził się na cookies marketingowe.']),
        $b->text('meta_test_code', 'Meta: kod zdarzeń testowych', ['instructions' => 'Tylko na czas testów (np. TEST12345). Potem usuń.']),
        $b->text('fb_domain_verification', 'Meta: weryfikacja domeny', ['instructions' => 'Wartość z metatagu facebook-domain-verification (Business Manager → Domeny).']),
        $b->text('hotjar_id', 'Hotjar: identyfikator witryny (Site ID)', ['instructions' => 'Same cyfry (tryb bez GTM). Hotjar liczony jako analityczny.']),

        mvn_tab('form', 'Formularz kontaktowy'),
        $b->rep('form_options', 'Formy treningu (lista w formularzu)', [
            ['label', 'Nazwa w formularzu'],
            ['crm', 'Wartość dla CRM', 'text', ['instructions' => 'Wartość pola „interest” w CRM: group, first, other.']],
        ]),
        $b->rep('call_times', 'Pory oddzwonienia (pierwsza jest domyślna)', [['label', 'Pora']]),
        $b->text('mail_to', 'Dokąd wysyłać zgłoszenia (e-mail)'),
        $b->text('mail_from', 'Nadawca wiadomości (najlepiej ta sama skrzynka co login SMTP)'),
        $b->yes('smtp_enabled', 'Wysyłaj przez SMTP', ['instructions' => 'Zalecane. Bez SMTP wiadomości idą zwykłą funkcją mail() i często trafiają do spamu.']),
        $b->text('smtp_host', 'SMTP: serwer'),
        $b->num('smtp_port', 'SMTP: port', ['instructions' => '587 (TLS) lub 465 (SSL)']),
        $b->sel('smtp_secure', 'SMTP: szyfrowanie', ['tls' => 'TLS (STARTTLS), port 587', 'ssl' => 'SSL, port 465', '' => 'Bez szyfrowania (niezalecane)'], ['allow_custom' => 0, 'default_value' => 'tls']),
        $b->text('smtp_user', 'SMTP: login'),
        $b->msg('smtp_pass_info', 'Hasło SMTP wpisz <strong>tylko w pliku wp-config.php</strong>: <code>define(\'MVN_SMTP_PASS\', \'…\');</code> — nigdy w panelu ani w wiadomościach.'),
        $b->yes('smtp_verify', 'SMTP: sprawdzaj certyfikat serwera'),
        $b->yes('autoreply_enabled', 'Wysyłaj klientce automatyczne potwierdzenie (gdy poda e-mail)'),
        $b->num('autoreply_max', 'Limit potwierdzeń na dobę', ['instructions' => 'Ochrona przed nadużyciami.']),
        $b->num('min_fill', 'Minimalny czas wypełniania formularza (sekundy)', ['instructions' => 'Szybsze wysłanie uznajemy za bota.']),
        $b->num('rate_max', 'Maks. zgłoszeń z jednego adresu IP na godzinę'),

        mvn_tab('crm', 'CRM'),
        $b->yes('crm_enabled', 'Przekazuj zgłoszenia do CRM'),
        $b->sel('booking_mode', 'Tryb zapisów i zakupów', ['fitssey' => 'Fitssey (przejściowo — strona prowadzi do Fitssey)', 'crm' => 'CRM (własny kalendarz i sklep)'], ['instructions' => 'Dopóki zapisy działają w Fitssey, zostaw „Fitssey”. Gdy CRM będzie gotowy (Stripe, grafik), przełącz na „CRM”.', 'default_value' => 'fitssey', 'allow_custom' => 0]),
        $b->sel('grafik_mode', 'Podstrona „Grafik” (/grafik/)', ['auto' => 'Tak jak tryb zapisów powyżej', 'crm' => 'Własny grafik z CRM (test — reszta strony zostaje na Fitssey)', 'fitssey' => 'Przycisk do grafiku w Fitssey'], ['instructions' => 'Pozwala włączyć własny grafik i zapis przez e-mail tylko na tej podstronie, zanim przełączysz całą stronę na CRM. Wymaga adresu CRM poniżej.']),
        $b->yes('booking_on', 'Kalendarz zapisów online (wolne miejsca z CRM + płatność)', ['instructions' => 'Pokazuje sekcję „Zapisy online”. Wymaga włączonego CRM i adresu CRM poniżej; w CRM muszą być podłączone płatności Stripe.']),
        $b->url('crm_url', 'CRM: adres przyjmowania leadów', ['instructions' => 'Bez tokena, np. https://crm.movenapilates.pl/leady/przyjmij/ (token dopisujemy automatycznie).']),
        mvn_f('crm_token', 'CRM: token', 'password', ['instructions' => 'Token z CRM → Leady → Podłączenie. Bezpieczniej: wpisz go w pliku wp-config.php jako define(\'MVN_CRM_TOKEN\', \'…\'); — wtedy to pole jest ignorowane. Bez tokena wysyłka do CRM jest pomijana, a zgłoszenie i tak trafia e-mailem.']),

        mvn_tab('redirects', 'Przekierowania 301'),
        $b->msg('redirects_info', 'Stare adresy (z poprzedniej strony) są przekierowywane na nowe automatycznie — lista domyślna jest w pliku <code>inc/redirects.php</code>. Tutaj możesz dopisać własne: „z” to ścieżka, np. <code>/stary-adres.html</code>, „na” to ścieżka lub pełny adres.'),
        $b->rep('redirects', 'Dodatkowe przekierowania', [['from', 'Z (ścieżka)'], ['to', 'Na (ścieżka lub adres)']], ['button_label' => 'Dodaj przekierowanie']),
    ];
    return [
        'key' => 'group_mvn_site', 'title' => 'Movena: ustawienia strony', 'fields' => $fields,
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'mvn-ustawienia']]],
        'menu_order' => 0, 'position' => 'normal', 'style' => 'seamless', 'label_placement' => 'top', 'instruction_placement' => 'label',
        'hide_on_screen' => '', 'active' => true, 'show_in_rest' => 0,
    ];
}

/* ------------------------------------------------------------ rejestracja */

add_action('acf/init', 'mvn_register_fields');
function mvn_register_fields(): void
{
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => 'Movena Pilates Studio — ustawienia strony', 'menu_title' => 'Movena', 'menu_slug' => 'mvn-ustawienia',
            'capability' => 'manage_options', 'icon_url' => 'dashicons-heart', 'position' => 3, 'redirect' => false,
            'update_button' => 'Zapisz ustawienia', 'updated_message' => 'Ustawienia zapisane.',
        ]);
    }
    // Zapasowo: gdy brak plików JSON, rejestrujemy grupy z kodu.
    if (function_exists('acf_add_local_field_group') && !is_file(MVN_DIR . '/acf-json/group_mvn_front.json')) {
        acf_add_local_field_group(mvn_group_site());
        acf_add_local_field_group(mvn_group_front());
    }
}

/** Pliki JSON grup pól wczytywane z motywu (widoczne w SCF → Grupy pól). */
add_filter('acf/settings/load_json', static function ($paths) {
    $paths[] = MVN_DIR . '/acf-json';
    return $paths;
});
add_filter('acf/settings/save_json', static function ($path) {
    return is_dir(MVN_DIR . '/acf-json') && is_writable(MVN_DIR . '/acf-json') ? MVN_DIR . '/acf-json' : $path;
});

/** Podgląd danych strukturalnych w panelu (zakładka „Dane strukturalne”). */
add_filter('acf/load_field/name=schema_preview', static function ($field) {
    if (is_admin() && function_exists('mvn_schema_preview_html')) {
        $field['message'] = mvn_schema_preview_html();
    }
    return $field;
});

/** Lista form treningu w polach „Forma treningu w formularzu”. */
function mvn_form_load($field)
{
    static $busy = false;
    if ($busy || !is_admin()) {
        return $field;
    }
    $busy = true;
    $c = ['' => '(bez wyboru: klientka wybiera sama)'];
    foreach (mvn_form_labels() as $l) {
        $c[$l] = $l;
    }
    $busy = false;
    $field['choices'] = $c;
    return $field;
}
add_filter('acf/load_field/name=form', 'mvn_form_load');
add_filter('acf/load_field/name=form_default', 'mvn_form_load');

/** Lista celów linków: kotwice sekcji strony oraz adresy zapisów i sklepu. */
function mvn_cel_load($field)
{
    static $busy = false;
    if ($busy || !is_admin()) {
        return $field;
    }
    $busy = true;
    $c = [
        'fitssey:schedule' => 'Zapisy — kalendarz zajęć', 'fitssey:pricing' => 'Cennik pakietów',
        'fitssey:login' => 'Logowanie klientek (CRM)', 'fitssey:vouchers' => 'Vouchery (CRM)',
        'tel' => 'Zadzwoń (numer telefonu z ustawień)', 'msg' => 'Napisz do nas (rozmowa na Instagramie / Messengerze)',
    ];
    $names = mvn_layout_names();
    foreach (mvn_sections() as $s) {
        $a = trim((string) ($s['anchor'] ?? ''));
        if ($a === '') {
            continue;
        }
        $label = trim((string) ($s['menu_label'] ?? ''));
        $c['#' . $a] = '#' . $a . ' — ' . ($label !== '' ? $label : ($names[$s['acf_fc_layout'] ?? ''] ?? $a));
    }
    $busy = false;
    $field['choices'] = $c;
    return $field;
}
foreach (['cel', 'cta_primary_cel', 'cta_secondary_cel', 'btn_cel', 'box_cel', 'cta_cel', 'cta2_cel', 'btn1_cel', 'btn2_cel', 'aside_cel', 'header_cta_cel', 'mobile_cta_cel'] as $__n) {
    add_filter('acf/load_field/name=' . $__n, 'mvn_cel_load');
}

/** Przyjazne nazwy układów (do list wyboru). */
function mvn_layout_names(): array
{
    return [
        'hero' => 'Hero', 'promo' => 'Promocja', 'trust' => 'Atuty', 'pierwszy' => 'Pierwszy raz', 'kroki' => 'Jak zacząć', 'zajecia' => 'Zajęcia', 'rezerwacja' => 'Zapisy online',
        'formaty' => 'Formy treningu', 'cennik' => 'Cennik', 'dlaczego' => 'Dlaczego mała grupa', 'o_studiu' => 'O studiu', 'instruktorki' => 'Instruktorki',
        'opinie' => 'Opinie', 'vouchery' => 'Vouchery', 'faq' => 'FAQ', 'lokalizacja' => 'Lokalizacja', 'kontakt' => 'Kontakt', 'final' => 'Sekcja końcowa',
        'lp_hero' => 'Hero', 'lp_oferta' => 'Oferta', 'rf_hero' => 'Hero programu', 'rf_grupy' => 'Wybór grupy', 'przebieg' => 'Jak wygląda trening', 'tekst' => 'Tekst + zdjęcie', 'wlasna' => 'Własna sekcja',
    ];
}
