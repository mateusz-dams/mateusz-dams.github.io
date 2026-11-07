<?php
/**
 * Pola Secure Custom Fields.
 *  - Ustawienia globalne: strona opcji „PM Boxing”.
 *  - Strona główna: jedno pole elastyczne „Sekcje” (każda sekcja to osobny układ; elementy powtarzalne to repeatery).
 *
 * Definicje grup są zapisane jako pliki JSON w folderze acf-json/ (generuje je tools/gen-acf-json.php),
 * dzięki czemu widać je w SCF → Grupy pól. Gdy plików brak, grupy rejestrują się z kodu.
 */
if (!defined('ABSPATH')) {
    exit;
}

/* ------------------------------------------------------------ budowniczy pól */

function pmbs_key(string $prefix, string $name): string
{
    return 'field_pmbs_' . ($prefix !== '' ? $prefix . '__' : '') . $name;
}

/** Pole ogólne. */
function pmbs_f(string $name, string $label, string $type = 'text', array $extra = [], string $prefix = ''): array
{
    return array_merge(['key' => pmbs_key($prefix, $name), 'name' => $name, 'label' => $label, 'type' => $type], $extra);
}

/** Budowniczy pól w kontekście jednego układu (prefiks kluczy). */
class PMBS_FB
{
    public $p;

    public function __construct(string $prefix = '')
    {
        $this->p = $prefix;
    }

    public function text(string $n, string $l, array $x = []): array { return pmbs_f($n, $l, 'text', $x, $this->p); }
    public function url(string $n, string $l, array $x = []): array { return pmbs_f($n, $l, 'url', $x, $this->p); }
    public function num(string $n, string $l, array $x = []): array { return pmbs_f($n, $l, 'number', $x, $this->p); }
    public function area(string $n, string $l, array $x = [], int $rows = 3): array { return pmbs_f($n, $l, 'textarea', array_merge(['rows' => $rows, 'new_lines' => ''], $x), $this->p); }
    public function yes(string $n, string $l, array $x = []): array { return pmbs_f($n, $l, 'true_false', array_merge(['ui' => 1, 'message' => '', 'default_value' => 1], $x), $this->p); }
    public function wys(string $n, string $l, array $x = []): array { return pmbs_f($n, $l, 'wysiwyg', array_merge(['tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0], $x), $this->p); }
    public function msg(string $n, string $text): array { return pmbs_f($n, 'Wskazówka', 'message', ['message' => $text, 'esc_html' => 0], $this->p); }

    public function img(string $n, string $l, string $help = 'Zostaw puste, aby użyć zdjęcia dołączonego do motywu.'): array
    {
        return pmbs_f($n, $l, 'image', ['return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all', 'instructions' => $help], $this->p);
    }

    /** Lista rozwijana; wartości można też wpisać ręcznie. */
    public function sel(string $n, string $l, array $choices = [], array $x = []): array
    {
        return pmbs_f($n, $l, 'select', array_merge(['choices' => $choices, 'allow_null' => 0, 'ui' => 1, 'allow_custom' => 1, 'return_format' => 'value'], $x), $this->p);
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
        $b = new PMBS_FB($sp);
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
                $sub[] = pmbs_f($s[0], $s[1], $type, $extra, $sp);
            }
        }
        return array_merge([
            'key' => pmbs_key($this->p, $n), 'name' => $n, 'label' => $l, 'type' => 'repeater',
            'layout' => 'table', 'button_label' => 'Dodaj pozycję', 'sub_fields' => $sub,
        ], $x);
    }

    /** Lista punktów: repeater z jednym polem tekstowym. */
    public function items(string $n, string $l, string $btn = 'Dodaj punkt'): array
    {
        return $this->rep($n, $l, [['text', 'Tekst']], ['button_label' => $btn]);
    }
}

function pmbs_tab(string $id, string $label): array
{
    return ['key' => 'field_pmbs_tab_' . $id, 'label' => $label, 'name' => '', 'type' => 'tab', 'placement' => 'top', 'endpoint' => 0];
}

/* ------------------------------------------------------------ układy sekcji */

/** Wspólne pola każdej sekcji. */
function pmbs_common_fields(PMBS_FB $b, string $layout, array $opt): array
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
        $f[] = $b->sel('tlo', 'Tło sekcji', ['' => 'Standardowe', 'alt' => 'Jaśniejsze'], ['allow_custom' => 0, 'default_value' => '']);
    }
    return $f;
}

/** Składa układ: wspólne pola + pola właściwe. */
function pmbs_layout(string $slug, string $label, array $opt, callable $fields, int $max = 0): array
{
    $b = new PMBS_FB('s_' . $slug);
    $sub = array_merge(pmbs_common_fields($b, $slug, $opt), $fields($b));
    return [
        'key' => 'layout_pmbs_' . $slug, 'name' => $slug, 'label' => $label, 'display' => 'block',
        'sub_fields' => $sub, 'min' => '', 'max' => $max ?: '',
    ];
}

function pmbs_section_layouts(): array
{
    $std = ['menu' => 1, 'ghost' => 1, 'tlo' => 1];
    $L = [];

    $L[] = pmbs_layout('hero', 'Góra strony (hero)', ['menu' => 1], function (PMBS_FB $b) {
        return [
            $b->msg('info', 'W tekstach możesz użyć <code>**pogrubienia**</code> i <code>[tekstu linku](#kotwica)</code>.'),
            $b->text('badge', 'Plakietka nad nagłówkiem'),
            $b->text('title_a', 'Nagłówek: pierwsza część'),
            $b->text('title_grad', 'Nagłówek: część złota'),
            $b->area('lead', 'Opis pod nagłówkiem'),
            $b->text('cta_primary', 'Przycisk główny (prowadzi do formularza)'),
            $b->text('cta_secondary', 'Przycisk dodatkowy'),
            $b->cel('cta_secondary_cel', 'Przycisk dodatkowy: cel', '#oferta'),
            $b->rep('meta', 'Wyróżniki pod przyciskami', [
                ['icon', 'Ikona', 'select', ['choices' => ['award' => 'Nagroda', 'steps' => 'Schody', 'pin' => 'Pinezka', 'clock' => 'Zegar', 'star' => 'Gwiazdka', 'users' => 'Ludzie', 'phone' => 'Telefon'], 'allow_custom' => 0]],
                ['text', 'Tekst'],
            ], ['max' => 4]),
            $b->img('image', 'Zdjęcie w hero'),
            $b->text('image_alt', 'Opis zdjęcia (alt)'),
            $b->text('orbit_text', 'Napis na okręgu przy zdjęciu'),
            $b->text('orbit_price', 'Cena w okręgu'),
            $b->cel('orbit_cel', 'Okrąg: cel', 'grafik'),
            $b->rep('chips', 'Plakietki przy zdjęciu (do 3)', [['label', 'Napis'], ['cel', 'Cel']], ['max' => 3]),
        ];
    }, 1);

    $L[] = pmbs_layout('oferta', 'Oferta (karty)', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 4),
            $b->rep('cards', 'Karty oferty', [
                ['icon', 'Ikona', 'select', ['choices' => ['user' => 'Osoba', 'users' => 'Dwie osoby', 'group' => 'Grupa', 'youth' => 'Młodzież'], 'allow_custom' => 0]],
                ['title', 'Tytuł'], ['for', 'Dla kogo', 'textarea'],
                ['bullets', 'Punkty (po jednym w osobnym wierszu, oddziel nową linią)', 'textarea', ['rows' => 4]],
                ['cta', 'Napis na przycisku'], ['form', 'Forma treningu w formularzu'],
            ], ['layout' => 'block']),
            $b->text('strip', 'Pasek pod kartami'), $b->text('strip_cta', 'Przycisk na pasku'),
        ];
    }, 1);

    $L[] = pmbs_layout('krok', 'Pierwszy krok (dwie ścieżki)', $std, function (PMBS_FB $b) {
        return [
            $b->msg('info', 'Znacznik <code>{cena}</code> zamienia się w cenę treningu wdrożeniowego (z sekcji Cennik). Dostępne też <code>**pogrubienie**</code>.'),
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 2),
            $b->text('a_title', 'Ścieżka A: tytuł'), $b->text('a_lead', 'Ścieżka A: zdanie wiodące'), $b->area('a_note', 'Ścieżka A: dopisek'),
            $b->items('a_feats', 'Ścieżka A: punkty'), $b->text('a_cta', 'Ścieżka A: przycisk'),
            $b->text('b_title', 'Ścieżka B: tytuł'), $b->text('b_lead', 'Ścieżka B: zdanie wiodące'), $b->area('b_note', 'Ścieżka B: dopisek'),
            $b->items('b_feats', 'Ścieżka B: punkty'), $b->text('b_cta', 'Ścieżka B: przycisk'),
            $b->text('bring', 'Pasek „co zabrać”'),
        ];
    });

    $L[] = pmbs_layout('dla', 'Dla kogo', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 3),
            $b->items('audience', 'Lista „dla kogo”'),
            $b->area('muted', 'Tekst pod listą', [], 4),
            $b->rep('concerns', 'Obawy i odpowiedzi', [['q', 'Obawa'], ['a', 'Odpowiedź', 'textarea', ['rows' => 3]]], ['layout' => 'block']),
        ];
    });

    $L[] = pmbs_layout('jak', 'Jak trenujemy (umiejętności)', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 4),
            $b->img('photo', 'Zdjęcie'), $b->text('photo_alt', 'Opis zdjęcia (alt)'),
            $b->rep('skills', 'Umiejętności (numerowane automatycznie)', [['title', 'Tytuł'], ['text', 'Opis', 'textarea']], ['layout' => 'block']),
            $b->text('pull_title', 'Ostatnia karta: tytuł (puste = bez karty)'), $b->area('pull_text', 'Ostatnia karta: tekst'),
            $b->text('pull_link', 'Ostatnia karta: napis linku'), $b->cel('pull_cel', 'Ostatnia karta: cel linku', 'inspiracje'),
        ];
    });

    $L[] = pmbs_layout('proces', 'Proces nauki (etapy)', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 2),
            $b->rep('steps', 'Etapy (numerowane automatycznie)', [['title', 'Tytuł'], ['text', 'Opis', 'textarea']], ['layout' => 'block']),
        ];
    });

    $L[] = pmbs_layout('ko', 'KO Coach ITS®', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 2),
            $b->img('photo', 'Zdjęcie'), $b->text('photo_alt', 'Opis zdjęcia (alt)'),
            $b->rep('nums', 'Liczby', [['num', 'Liczba'], ['label', 'Opis']], ['max' => 4]),
            $b->text('measures_title', 'Lista 1: tytuł'), $b->items('measures', 'Lista 1: punkty'),
            $b->text('uses_title', 'Lista 2: tytuł'), $b->items('uses', 'Lista 2: punkty'),
            $b->area('app', 'Akapit o aplikacji'), $b->area('note', 'Uwaga końcowa'),
        ];
    });

    $L[] = pmbs_layout('about', 'O trenerze i studiu', $std, function (PMBS_FB $b) {
        return [
            $b->msg('info', 'Kwalifikacje (tytuł i dopisek) edytujesz w ustawieniach „PM Boxing → Dane i kontakt”. W akapitach działa <code>**pogrubienie**</code> i <code>[link](#kotwica)</code>.'),
            $b->text('title', 'Tytuł sekcji'),
            $b->img('photo', 'Zdjęcie trenera'), $b->text('photo_alt', 'Opis zdjęcia (alt)'),
            $b->rep('paragraphs', 'Akapity', [['text', 'Treść', 'textarea', ['rows' => 4]]], ['layout' => 'block', 'button_label' => 'Dodaj akapit']),
            $b->area('quote', 'Cytat'), $b->text('cta', 'Przycisk'),
        ];
    });

    $L[] = pmbs_layout('insp', 'Inspiracje', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 3),
            $b->text('styles_title', 'Grupa 1: tytuł'),
            $b->rep('styles', 'Style boksu', [['title', 'Nazwa'], ['text', 'Opis', 'textarea']]),
            $b->text('people_title', 'Grupa 2: tytuł'),
            $b->rep('people', 'Ludzie', [['name', 'Imię i nazwisko'], ['tag', 'Etykieta'], ['text', 'Opis', 'textarea']]),
            $b->text('ana_title', 'Grupa 3: tytuł'),
            $b->rep('ana', 'Analogie', [['title', 'Nazwa'], ['text', 'Opis', 'textarea']]),
            $b->area('ana_close', 'Zdanie pod analogiami'),
            $b->rep('creed', 'Credo (akapity)', [['text', 'Treść', 'textarea', ['rows' => 4]]], ['layout' => 'block', 'button_label' => 'Dodaj akapit']),
            $b->text('cta', 'Przycisk'),
        ];
    });

    $L[] = pmbs_layout('sala', 'Sala i wyposażenie', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 5),
            $b->img('photo1', 'Zdjęcie 1 (panorama)'), $b->text('photo1_alt', 'Opis zdjęcia 1 (alt)'),
            $b->img('photo2', 'Zdjęcie 2'), $b->text('photo2_alt', 'Opis zdjęcia 2 (alt)'),
            $b->rep('gear', 'Wyposażenie (numerowane automatycznie)', [['title', 'Nazwa'], ['text', 'Opis', 'textarea']], ['layout' => 'block']),
        ];
    });

    $L[] = pmbs_layout('opinie', 'Opinie', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'),
            $b->num('count', 'Liczba opinii w Google', ['instructions' => 'Wyświetla się jako „43 opinie w Google” (z poprawną odmianą). Link „wszystkie opinie” bierze adres Map z ustawień.']),
            $b->text('suffix', 'Zdanie po liczbie opinii'), $b->text('btn', 'Przycisk do wszystkich opinii'),
            $b->rep('items', 'Opinie', [['name', 'Imię i inicjał'], ['text', 'Treść', 'textarea', ['rows' => 3]]], ['layout' => 'block']),
        ];
    });

    $L[] = pmbs_layout('grafik', 'Grafik zajęć grupowych', $std, function (PMBS_FB $b) {
        return [
            $b->msg('info', 'Grupy (nazwa, dni, godzina, cena) edytujesz w <strong>PM Boxing → Grupy zajęć</strong>. Te same dane zasilają Grafik i Cennik.'),
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 2),
            $b->text('cta', 'Napis linku zapisu'), $b->text('note', 'Uwaga pod tabelą'),
        ];
    });

    $L[] = pmbs_layout('cennik', 'Cennik', $std, function (PMBS_FB $b) {
        $pkg = [['name', 'Nazwa'], ['price', 'Cena (zł)', 'number'], ['per', 'Cena za trening (zł; 0 = nie pokazuj)', 'number']];
        return [
            $b->msg('info', 'Ceny wpisuj jako liczby (bez „zł”). Ceny grup miesięcznych edytujesz w <strong>PM Boxing → Grupy zajęć</strong>.'),
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 2),
            $b->num('onboarding', 'Cena treningu wdrożeniowego (zł)'),
            $b->text('sp_free_title', 'Kafel „bezpłatnie”: tytuł'), $b->area('sp_free_text', 'Kafel „bezpłatnie”: opis', [], 2), $b->text('sp_free_price', 'Kafel „bezpłatnie”: cena'),
            $b->text('sp_paid_title', 'Kafel „wdrożeniowy”: tytuł'), $b->area('sp_paid_text', 'Kafel „wdrożeniowy”: opis', [], 2),
            $b->text('ribbon', 'Wstęga na ostatnim pakiecie'),
            $b->rep('personal', 'Personalne: pakiety', $pkg),
            $b->items('personal_feats', 'Personalne: co zawiera'), $b->text('personal_cta', 'Personalne: przycisk'),
            $b->rep('duo', 'Duet: pakiety (ceny za osobę)', $pkg),
            $b->items('duo_feats', 'Duet: co zawiera'), $b->text('duo_cta', 'Duet: przycisk'), $b->text('duo_note', 'Duet: dopisek nad kartami'),
            $b->items('groups_feats', 'Grupy: co zawiera'), $b->text('groups_cta', 'Grupy: przycisk'), $b->text('groups_note', 'Grupy: dopisek nad kartami'),
            $b->text('guide_title', 'Poradnik „co wybrać”: tytuł'),
            $b->rep('guide', 'Poradnik: karty', [['kicker', 'Zdanie wstępne'], ['title', 'Tytuł'], ['text', 'Opis', 'textarea', ['rows' => 3]], ['cta', 'Przycisk'], ['form', 'Forma treningu w formularzu']], ['max' => 3, 'layout' => 'block']),
        ];
    }, 1);

    $L[] = pmbs_layout('partnerzy', 'Partnerzy', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 2),
            $b->rep('items', 'Partnerzy (logo na białej karcie)', [
                ['logo', 'Logo', 'image', ['instructions' => 'PNG lub WebP, najlepiej z przezroczystym tłem.']], ['name', 'Nazwa (opis alt)'], ['url', 'Link (opcjonalnie)', 'url'],
            ]),
        ];
    });

    $L[] = pmbs_layout('faq', 'FAQ (pytania i odpowiedzi)', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'),
            $b->rep('items', 'Pytania i odpowiedzi', [['q', 'Pytanie'], ['a', 'Odpowiedź', 'textarea', ['rows' => 4]]], ['layout' => 'block', 'button_label' => 'Dodaj pytanie']),
        ];
    });

    $L[] = pmbs_layout('final', 'Sekcja końcowa (wezwanie do działania)', [], function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł'), $b->area('text', 'Tekst', [], 4),
            $b->text('btn1', 'Przycisk 1 (formularz, trening personalny)'), $b->text('btn2', 'Przycisk 2 (formularz)'),
        ];
    });

    $L[] = pmbs_layout('kontakt', 'Kontakt i formularz', $std, function (PMBS_FB $b) {
        return [
            $b->msg('info', 'Telefon, e-mail, adres, godziny i WhatsApp edytujesz w <strong>PM Boxing → Dane i kontakt</strong>. Wszystkie przyciski „Umów…” prowadzą do tej sekcji.'),
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 2),
            $b->text('after_title', 'Tytuł „co dzieje się po wysłaniu”'),
            $b->rep('after_steps', 'Kroki po wysłaniu zgłoszenia', [['title', 'Tytuł'], ['text', 'Opis']], ['max' => 4]),
            $b->text('proof_sub', 'Podpis pod liczbą opinii'),
            $b->text('form_title', 'Formularz: tytuł'), $b->area('form_lead', 'Formularz: opis', [], 2),
            $b->form('form_default', 'Formularz: domyślna forma treningu'),
            $b->text('form_button', 'Formularz: przycisk'), $b->area('form_consent', 'Formularz: treść zgody', [], 2),
            $b->text('form_note', 'Formularz: dopisek (po nazwie administratora)'),
            $b->text('thanks_title', 'Po wysłaniu: tytuł'), $b->area('thanks_text', 'Po wysłaniu: tekst', [], 2),
            $b->text('map_title', 'Karta mapy: tytuł'), $b->area('map_text', 'Karta mapy: opis', [], 2),
        ];
    }, 1);

    $L[] = pmbs_layout('lp_oferta', 'Oferta z ceną (karta: trening wdrożeniowy)', ['menu' => 1, 'ghost' => 1, 'tlo' => 1], function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł sekcji'), $b->area('intro', 'Wstęp', [], 3),
            $b->text('badge', 'Plakietka nad kartą'),
            $b->text('card_title', 'Karta: nazwa'), $b->num('price', 'Karta: cena (zł)'), $b->text('price_note', 'Karta: dopisek przy cenie'),
            $b->items('bullets', 'Karta: co dostajesz'),
            $b->text('cta', 'Karta: przycisk'), $b->form(),
            $b->area('note', 'Uwaga pod przyciskiem', [], 2),
        ];
    });

    $L[] = pmbs_layout('tekst', 'Tekst + zdjęcie (własna sekcja)', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł'),
            $b->rep('paragraphs', 'Akapity', [['text', 'Treść', 'textarea', ['rows' => 4]]], ['layout' => 'block', 'button_label' => 'Dodaj akapit']),
            $b->img('image', 'Zdjęcie', 'Wybierz zdjęcie z biblioteki. Puste = sekcja bez zdjęcia.'), $b->text('image_alt', 'Opis zdjęcia (alt)'),
            $b->sel('side', 'Zdjęcie po stronie', ['left' => 'Lewej', 'right' => 'Prawej'], ['allow_custom' => 0, 'default_value' => 'left']),
            $b->text('btn', 'Przycisk (puste = bez przycisku)'), $b->cel('btn_cel', 'Przycisk: cel', 'kontakt'), $b->form('btn_form', 'Przycisk: forma treningu w formularzu'),
        ];
    });

    $L[] = pmbs_layout('wlasna', 'Własna sekcja (nagłówek + edytor tekstu)', $std, function (PMBS_FB $b) {
        return [
            $b->text('title', 'Tytuł'), $b->area('intro', 'Wstęp', [], 2),
            $b->wys('content', 'Treść'),
            $b->text('btn', 'Przycisk (puste = bez przycisku)'), $b->cel('btn_cel', 'Przycisk: cel', 'kontakt'), $b->form('btn_form', 'Przycisk: forma treningu w formularzu'),
        ];
    });

    return $L;
}

/* ------------------------------------------------------------ grupy pól */

/** Grupa „Sekcje” (strona główna). */
function pmbs_group_front(): array
{
    return [
        'key' => 'group_pmbs_front', 'title' => 'PM Boxing: sekcje strony głównej',
        'fields' => [[
            'key' => 'field_pmbs_sekcje', 'label' => 'Sekcje strony', 'name' => 'sekcje', 'type' => 'flexible_content',
            'instructions' => 'Każda sekcja to osobny blok. Przeciągaj, aby zmienić kolejność, dodawaj nowe lub wyłączaj istniejące. Sekcje z włączonym „Pokaż w menu” tworzą menu strony.',
            'layouts' => pmbs_section_layouts(), 'button_label' => 'Dodaj sekcję', 'min' => '', 'max' => '',
        ]],
        'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']], [['param' => 'page_template', 'operator' => '==', 'value' => 'page-landing.php']]],
        'menu_order' => 0, 'position' => 'acf_after_title', 'style' => 'seamless', 'label_placement' => 'top', 'instruction_placement' => 'label',
        'hide_on_screen' => ['the_content', 'discussion', 'comments', 'slug', 'author', 'format', 'categories', 'tags', 'send-trackbacks'],
        'active' => true, 'show_in_rest' => 0,
    ];
}

/** Grupa „Ustawienia” (strona opcji). */
function pmbs_group_site(): array
{
    $b = new PMBS_FB('');
    $fields = [
        pmbs_tab('dane', 'Dane i kontakt'),
        $b->text('brand', 'Nazwa studia'),
        $b->text('phone_display', 'Telefon (jak wyświetlać)', ['instructions' => 'np. 534 581 536']),
        $b->text('phone_tel', 'Telefon (do linku)', ['instructions' => 'Format międzynarodowy z plusem, np. +48534581536']),
        $b->text('email', 'E-mail kontaktowy (na stronie)'),
        $b->text('address', 'Adres (ulica i numer)'), $b->text('city', 'Miasto'),
        $b->url('facebook', 'Facebook (adres profilu)'), $b->url('instagram', 'Instagram (adres profilu)'),
        $b->url('maps_url', 'Link do Map Google (wyznacz trasę / opinie)'),
        $b->text('whatsapp_number', 'Numer WhatsApp', ['instructions' => 'Format międzynarodowy bez plusa, np. 48534581536']),
        $b->area('whatsapp_message', 'Pierwsza wiadomość na WhatsAppie'),
        $b->rep('hours', 'Godziny otwarcia', [['days', 'Dni'], ['hours', 'Godziny']]),
        $b->text('company_name', 'Pełna nazwa firmy'), $b->text('krs', 'KRS'), $b->text('nip', 'NIP'), $b->text('regon', 'REGON'),
        $b->text('trainer_name', 'Imię i nazwisko trenera (do danych strukturalnych)'),
        $b->text('qual_title', 'Kwalifikacje trenera: tytuł'), $b->text('qual_note', 'Kwalifikacje trenera: dopisek'),

        pmbs_tab('grupy', 'Grupy zajęć'),
        $b->msg('info_grupy', 'Grupy zasilają sekcję <strong>Grafik</strong> oraz zakładkę „Grupy” w sekcji <strong>Cennik</strong>.'),
        $b->rep('grupy', 'Grupy', [
            ['name', 'Nazwa grupy'], ['days', 'Dni'], ['time', 'Godzina'], ['length', 'Czas trwania'],
            ['price', 'Cena miesięczna (zł)', 'number'], ['form', 'Forma treningu w formularzu'],
        ]),

        pmbs_tab('wyglad', 'Nagłówek i stopka'),
        $b->img('logo', 'Logo (jasne, na ciemne tło)', 'Zostaw puste, aby użyć logo z motywu. Najlepiej PNG lub WebP z przezroczystym tłem.'),
        $b->msg('info_menu', 'Menu główne tworzy się automatycznie z sekcji strony głównej, które mają włączone „Pokaż w menu” (Strony → Strona główna).'),
        $b->text('header_cta', 'Przycisk w nagłówku'), $b->text('header_call', 'Napis przy telefonie w menu mobilnym'),
        $b->text('mobile_call', 'Dolny pasek na telefonie: „Zadzwoń”'), $b->text('mobile_cta', 'Dolny pasek na telefonie: przycisk główny'),
        $b->area('foot_tagline', 'Opis pod logo w stopce'),
        $b->rep('foot_offer', 'Stopka: linki „Oferta”', [['label', 'Nazwa'], ['cel', 'Cel'], ['form', 'Forma treningu w formularzu']]),

        pmbs_tab('seo', 'SEO i udostępnianie'),
        $b->text('seo_title', 'Tytuł strony głównej (title)', ['instructions' => 'Do ok. 60 znaków, z frazą lokalną (np. „Boks Radom”). Wtyczki SEO (Yoast, Rank Math) mają pierwszeństwo, jeśli są aktywne.']),
        $b->area('seo_description', 'Opis strony głównej (meta description)', ['instructions' => 'Do ok. 160 znaków.']),
        $b->img('og_image', 'Obraz przy udostępnianiu (Facebook, Messenger)', 'Zalecane 1200×630 px. Puste = obraz z motywu.'),
        $b->text('og_image_alt', 'Opis obrazu przy udostępnianiu (alt)'),
        $b->text('gsc_verification', 'Google Search Console: kod weryfikacyjny', ['instructions' => 'Sama wartość z metatagu google-site-verification (content="…"). Puste = weryfikuj inną metodą.']),
        $b->text('bing_verification', 'Bing Webmaster: kod weryfikacyjny', ['instructions' => 'Wartość z metatagu msvalidate.01 (opcjonalnie).']),
        $b->text('geo_lat', 'Szerokość geograficzna (do danych lokalnych)', ['instructions' => 'np. 51.4027. Poprawia dane strukturalne dla wyników lokalnych.']),
        $b->text('geo_lng', 'Długość geograficzna', ['instructions' => 'np. 21.1471']),
        $b->text('price_range', 'Przedział cen (dane strukturalne)', ['instructions' => 'np. 40–500 zł. Puste = bez.']),
        $b->text('rating_value', 'Średnia ocena z Google (np. 5.0)', ['instructions' => 'Wpisz tylko prawdziwą ocenę z wizytówki Google. Liczba opinii pochodzi z sekcji Opinie. Puste = bez oceny w danych strukturalnych.']),

        pmbs_tab('schema', 'Dane strukturalne'),
        $b->msg('schema_info', 'Dane strukturalne (JSON-LD) to ukryty opis strony dla Google: kim jesteście, gdzie, kiedy otwarci, jakie pytania i odpowiedzi macie. Google może na tej podstawie pokazać dane firmy i rozwijane pytania w wynikach. <strong>Nie są widoczne dla odwiedzających</strong>, ale muszą zgadzać się z treścią strony. Poniżej widzisz, co dokładnie jest wysyłane i skąd pochodzi każda wartość.'),
        pmbs_f('schema_preview', 'Podgląd: co jest teraz w danych strukturalnych', 'message', ['message' => '', 'esc_html' => 0]),
        $b->yes('schema_business_on', 'Firma lokalna (nazwa, adres, telefon, godziny…)'),
        $b->yes('schema_website_on', 'Strona WWW (nazwa i adres strony)'),
        $b->yes('schema_faq_on', 'FAQ (pytania i odpowiedzi z sekcji FAQ)', ['instructions' => 'Odpowiedzi muszą być też widoczne na stronie; sekcja FAQ spełnia ten warunek.']),
        $b->yes('schema_offers_on', 'Oferta (karty z sekcji Oferta i trening wdrożeniowy z ceną)'),
        $b->yes('schema_hours_on', 'Godziny otwarcia (z „Dane i kontakt”)'),
        $b->sel('schema_type', 'Typ firmy', ['SportsActivityLocation' => 'Obiekt sportowy (SportsActivityLocation)', 'ExerciseGym' => 'Siłownia / klub fitness (ExerciseGym)', 'SportsClub' => 'Klub sportowy (SportsClub)', 'HealthClub' => 'Klub zdrowia (HealthClub)'], ['allow_custom' => 0, 'default_value' => 'SportsActivityLocation', 'instructions' => 'Zawsze dodawany jest też ogólny typ LocalBusiness.']),
        $b->text('schema_name', 'Nazwa firmy (nadpisanie)', ['instructions' => 'Puste = nazwa studia z „Dane i kontakt”.']),
        $b->area('schema_description', 'Opis firmy (nadpisanie)', ['instructions' => 'Puste = opis strony głównej z zakładki SEO.']),
        $b->text('schema_area', 'Obszar działania (nadpisanie)', ['instructions' => 'Puste = miasto z „Dane i kontakt”. Można wpisać np. „Radom i okolice”.']),
        $b->rep('schema_same_as', 'Dodatkowe profile (sameAs)', [['url', 'Adres profilu', 'url']], ['button_label' => 'Dodaj profil']),
        $b->area('schema_extra', 'Własne dane (zaawansowane, JSON-LD)', ['instructions' => 'Opcjonalnie: wklej gotowy obiekt JSON-LD (np. dodatkowy typ). Zostanie dołączony do danych strony; błędny JSON jest pomijany i oznaczony w podglądzie. Bez znaczników <script>.'], 8),

        pmbs_tab('tracking', 'Analityka i reklamy'),
        $b->msg('tracking_info', 'Wszystkie narzędzia ładują się dopiero po zgodzie z banera cookies i respektują Consent Mode v2. <strong>Dwa tryby:</strong> (1) wpisz tylko <em>GTM</em> — wtedy GA4, piksel Meta, Google Ads i Hotjar konfigurujesz w Menedżerze Tagów, a motyw wysyła do niego zdarzenia (<code>generate_lead</code>, <code>contact</code>, <code>cta_click</code>) z <code>event_id</code>; (2) bez GTM — motyw ładuje bezpośrednio GA4, Meta Pixel, Google Ads i Hotjar z poniższych pól. Gdy wpiszesz GTM, pola bezpośrednie są ignorowane (żeby nie dublować zdarzeń). Po zmianach zaktualizuj strony Pliki cookies i Polityka prywatności.'),
        $b->text('gtm_id', 'Google Tag Manager: identyfikator kontenera', ['instructions' => 'np. GTM-XXXXXXX. Puste = bez GTM.', 'placeholder' => 'GTM-XXXXXXX']),
        $b->text('ga4', 'Google Analytics 4: identyfikator pomiaru', ['instructions' => 'np. G-XXXXXXXXXX (tryb bez GTM).', 'placeholder' => 'G-XXXXXXXXXX']),
        $b->text('ads_id', 'Google Ads: identyfikator konwersji', ['instructions' => 'np. AW-123456789 (tryb bez GTM).', 'placeholder' => 'AW-123456789']),
        $b->text('ads_label_lead', 'Google Ads: etykieta konwersji „Lead” (wysłany formularz)', ['instructions' => 'Z Google Ads → Cele → Konwersje → fragment zdarzenia: wartość po ukośniku w send_to.']),
        $b->text('ads_label_contact', 'Google Ads: etykieta konwersji „Kontakt” (telefon, WhatsApp, e-mail)'),
        $b->text('meta_pixel', 'Meta Pixel: identyfikator piksela', ['instructions' => '15–16 cyfr (tryb bez GTM).', 'placeholder' => '1234567890123456']),
        $b->text('meta_capi_token', 'Meta Conversions API: token dostępu', ['instructions' => 'Opcjonalnie: wysyłka zdarzenia Lead także z serwera (odporna na blokery), z deduplikacją po event_id. Token z Menedżera zdarzeń → Ustawienia → Conversions API. Działa tylko, gdy odwiedzający zgodził się na cookies marketingowe. Wymaga pola „Meta Pixel” (nawet w trybie GTM).']),
        $b->text('meta_test_code', 'Meta: kod zdarzeń testowych', ['instructions' => 'Tylko na czas testów (np. TEST12345). Potem usuń.']),
        $b->text('fb_domain_verification', 'Meta: weryfikacja domeny', ['instructions' => 'Wartość z metatagu facebook-domain-verification (Business Manager → Domeny).']),
        $b->text('hotjar_id', 'Hotjar: identyfikator witryny (Site ID)', ['instructions' => 'Same cyfry (tryb bez GTM). Hotjar liczony jako analityczny.']),

        pmbs_tab('form', 'Formularz kontaktowy'),
        $b->rep('form_options', 'Formy treningu (lista w formularzu)', [
            ['label', 'Nazwa w formularzu'],
            ['crm', 'Wartość dla CRM', 'text', ['instructions' => 'Dokładna wartość z listy „interest” w CRM, np. personal, duet, group_men, group_women, group_youth']],
        ]),
        $b->rep('call_times', 'Pory oddzwonienia (pierwsza jest domyślna)', [['label', 'Pora']]),
        $b->text('mail_to', 'Dokąd wysyłać zgłoszenia (e-mail)'),
        $b->text('mail_from', 'Nadawca wiadomości (najlepiej ta sama skrzynka co login SMTP)'),
        $b->yes('smtp_enabled', 'Wysyłaj przez SMTP', ['instructions' => 'Zalecane. Bez SMTP wiadomości idą zwykłą funkcją mail() i często trafiają do spamu.']),
        $b->text('smtp_host', 'SMTP: serwer'),
        $b->num('smtp_port', 'SMTP: port', ['instructions' => '587 (TLS) lub 465 (SSL)']),
        $b->sel('smtp_secure', 'SMTP: szyfrowanie', ['tls' => 'TLS (STARTTLS), port 587', 'ssl' => 'SSL, port 465', '' => 'Bez szyfrowania (niezalecane)'], ['allow_custom' => 0, 'default_value' => 'tls']),
        $b->text('smtp_user', 'SMTP: login'),
        pmbs_f('smtp_pass', 'SMTP: hasło', 'password', ['instructions' => 'Bezpieczniej: wpisz hasło w pliku wp-config.php jako define(\'PMBS_SMTP_PASS\', \'...\'); — wtedy to pole jest ignorowane.']),
        $b->yes('smtp_verify', 'SMTP: sprawdzaj certyfikat serwera'),
        $b->yes('autoreply_enabled', 'Wysyłaj klientowi automatyczne potwierdzenie (gdy poda e-mail)'),
        $b->num('autoreply_max', 'Limit potwierdzeń na dobę', ['instructions' => 'Ochrona przed nadużyciami.']),
        $b->num('min_fill', 'Minimalny czas wypełniania formularza (sekundy)', ['instructions' => 'Szybsze wysłanie uznajemy za bota.']),
        $b->num('rate_max', 'Maks. zgłoszeń z jednego adresu IP na godzinę'),

        pmbs_tab('crm', 'CRM'),
        $b->yes('crm_enabled', 'Przekazuj zgłoszenia do CRM'),
        $b->url('crm_url', 'Adres przyjmowania leadów w CRM', ['instructions' => 'Ten sam adres, który CRM podaje jako „action” formularza.']),
    ];
    return [
        'key' => 'group_pmbs_site', 'title' => 'PM Boxing: ustawienia strony', 'fields' => $fields,
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'pmbs-ustawienia']]],
        'menu_order' => 0, 'position' => 'normal', 'style' => 'seamless', 'label_placement' => 'top', 'instruction_placement' => 'label',
        'hide_on_screen' => '', 'active' => true, 'show_in_rest' => 0,
    ];
}

/* ------------------------------------------------------------ rejestracja */

add_action('acf/init', 'pmbs_register_fields');
function pmbs_register_fields(): void
{
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => 'PM Boxing Studio — ustawienia strony', 'menu_title' => 'PM Boxing', 'menu_slug' => 'pmbs-ustawienia',
            'capability' => 'manage_options', 'icon_url' => 'dashicons-superhero-alt', 'position' => 3, 'redirect' => false,
            'update_button' => 'Zapisz ustawienia', 'updated_message' => 'Ustawienia zapisane.',
        ]);
    }
    // Zapasowo: gdy brak plików JSON, rejestrujemy grupy z kodu.
    if (function_exists('acf_add_local_field_group') && !is_file(PMBS_DIR . '/acf-json/group_pmbs_front.json')) {
        acf_add_local_field_group(pmbs_group_site());
        acf_add_local_field_group(pmbs_group_front());
    }
}

/** Pliki JSON grup pól wczytywane z motywu (widoczne w SCF → Grupy pól). */
add_filter('acf/settings/load_json', static function ($paths) {
    $paths[] = PMBS_DIR . '/acf-json';
    return $paths;
});
add_filter('acf/settings/save_json', static function ($path) {
    return is_dir(PMBS_DIR . '/acf-json') && is_writable(PMBS_DIR . '/acf-json') ? PMBS_DIR . '/acf-json' : $path;
});

/** Podgląd danych strukturalnych w panelu (zakładka „Dane strukturalne”). */
add_filter('acf/load_field/name=schema_preview', static function ($field) {
    if (is_admin() && function_exists('pmbs_schema_preview_html')) {
        $field['message'] = pmbs_schema_preview_html();
    }
    return $field;
});

/** Lista form treningu w polach „Forma treningu w formularzu”. */
function pmbs_form_load($field)
{
    static $busy = false;
    if ($busy || !is_admin()) {
        return $field;
    }
    $busy = true;
    $c = ['' => '(bez wyboru: klient wybiera sam)'];
    foreach (pmbs_form_labels() as $l) {
        $c[$l] = $l;
    }
    $busy = false;
    $field['choices'] = $c;
    return $field;
}
add_filter('acf/load_field/name=form', 'pmbs_form_load');
add_filter('acf/load_field/name=btn_form', 'pmbs_form_load');
add_filter('acf/load_field/name=form_default', 'pmbs_form_load');

/** Lista kotwic (sekcji strony głównej) w polach wyboru celu linku. */
function pmbs_cel_load($field)
{
    static $busy = false;
    if ($busy || !is_admin()) {
        return $field;
    }
    $busy = true;
    $c = [];
    foreach (pmbs_sections() as $s) {
        $a = trim((string) ($s['anchor'] ?? ''));
        if ($a === '') {
            continue;
        }
        $names = pmbs_layout_names();
        $label = trim((string) ($s['menu_label'] ?? ''));
        $c['#' . $a] = '#' . $a . ' — ' . ($label !== '' ? $label : ($names[$s['acf_fc_layout'] ?? ''] ?? $a));
    }
    $busy = false;
    $field['choices'] = $c;
    return $field;
}
foreach (['cel', 'cta_secondary_cel', 'orbit_cel', 'pull_cel', 'btn_cel'] as $__n) {
    add_filter('acf/load_field/name=' . $__n, 'pmbs_cel_load');
}

/** Przyjazne nazwy układów (do list wyboru). */
function pmbs_layout_names(): array
{
    return [
        'hero' => 'Hero', 'oferta' => 'Oferta', 'krok' => 'Pierwszy krok', 'dla' => 'Dla kogo', 'jak' => 'Jak trenujemy', 'proces' => 'Proces',
        'ko' => 'KO Coach', 'about' => 'O trenerze', 'insp' => 'Inspiracje', 'sala' => 'Sala', 'opinie' => 'Opinie', 'grafik' => 'Grafik',
        'cennik' => 'Cennik', 'partnerzy' => 'Partnerzy', 'faq' => 'FAQ', 'final' => 'Sekcja końcowa', 'kontakt' => 'Kontakt',
        'tekst' => 'Tekst + zdjęcie', 'wlasna' => 'Własna sekcja', 'lp_oferta' => 'Oferta',
    ];
}
