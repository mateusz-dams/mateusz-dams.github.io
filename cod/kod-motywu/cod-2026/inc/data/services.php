<?php
/**
 * Treści usług przygotowane w projekcie strony.
 * Używane przez narzędzie „Uzupełnij opisy usług” (Opcje motywu → COD 2026) – wpisuje je tylko do pustych pól.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
  'diag-dzieci' => array(
    'title' => 'Diagnoza psychologiczna pod kątem ADHD dla dzieci',
    'short_name' => 'Diagnoza ADHD',
    'audience' => 'dzieci',
    'keywords' => array(
      'adhd',
      'dziec',
    ),
    'label' => 'Najczęstszy pierwszy krok',
    'short' => 'Dla rodziców, którzy widzą u dziecka trudności z uwagą, impulsywnością lub nadmierną ruchliwością i chcą rzetelnie sprawdzić, co za nimi stoi.',
    'bullets' => array(
      'rozmowa z rodzicami i spotkania z dzieckiem',
      'badanie wystandaryzowanymi narzędziami',
      'omówienie wyników i zaleceń',
    ),
    'price' => '1 500 zł',
    'featured' => true,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla dzieci i nastolatków, u których rodzice, nauczyciele lub sami zainteresowani zauważają trudności z koncentracją, impulsywnością albo nadmierną ruchliwością.</p>',
      ),
      array(
        'heading' => 'Kiedy warto się zgłosić',
        'text' => '<ul><li>trudności trwają od wielu miesięcy i widać je zarówno w domu, jak i w szkole,</li><li>utrudniają naukę, relacje z rówieśnikami lub codzienne obowiązki,</li><li>szkoła lub przedszkole sugeruje konsultację,</li><li>chcesz wiedzieć, jak najlepiej wspierać dziecko.</li></ul>',
      ),
      array(
        'heading' => 'Jak wygląda diagnoza',
        'text' => '<p>To proces, a nie jedno badanie. Obejmuje rozmowę z rodzicami o rozwoju i codziennym funkcjonowaniu dziecka, spotkania z dzieckiem oraz badanie wystandaryzowanymi narzędziami. Harmonogram spotkań ustalamy przy zapisie.</p>',
      ),
      array(
        'heading' => 'Co dzieje się później',
        'text' => '<p>Na spotkaniu podsumowującym omawiamy wyniki i zalecenia do pracy w domu i w szkole. Jeśli dziecko potrzebuje dalszego wsparcia, zaproponujemy terapię lub trening z naszej oferty.</p>',
      ),
      array(
        'heading' => 'Co warto przygotować',
        'text' => '<p>Opinie ze szkoły lub przedszkola oraz wcześniejszą dokumentację (np. poprzednie diagnozy), jeśli je masz.</p>',
      ),
    ),
    'wizard_q' => 'Trudności z koncentracją, impulsywność, niepokój ruchowy',
    'wizard_a' => 'Pierwszym krokiem jest zwykle diagnoza. Pozwala sprawdzić, czy za trudnościami stoi ADHD, i zaplanować dalsze wsparcie.',
    'order' => 0,
  ),
  'tus' => array(
    'title' => 'Trening Umiejętności Społecznych (TUS)',
    'short_name' => 'Trening Umiejętności Społecznych',
    'audience' => 'dzieci',
    'keywords' => array(
      'umiejętności społecznych',
      'tus',
    ),
    'label' => 'Relacje z rówieśnikami',
    'short' => 'Zajęcia, na których dziecko ćwiczy rozpoznawanie emocji, rozmowę, współpracę i rozwiązywanie konfliktów. Prowadzą Magdalena Tracz i Urszula Kaczmarska.',
    'bullets' => array(),
    'price' => '2 000 zł',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla dzieci, którym trudno nawiązać i utrzymać relacje z rówieśnikami, współpracować w grupie albo radzić sobie z emocjami i konfliktami.</p>',
      ),
      array(
        'heading' => 'Kiedy warto się zgłosić',
        'text' => '<ul><li>dziecko wycofuje się z kontaktów albo często jest w konflikcie z rówieśnikami,</li><li>trudno mu przegrywać, czekać na swoją kolej lub przestrzegać zasad wspólnej zabawy,</li><li>reaguje wybuchami złości w sytuacjach społecznych.</li></ul>',
      ),
      array(
        'heading' => 'Jak to wygląda',
        'text' => '<p>Trening to cykl zajęć, podczas których dziecko w bezpiecznych warunkach ćwiczy konkretne umiejętności: rozpoznawanie i nazywanie emocji, prowadzenie rozmowy, współpracę i szukanie rozwiązań w konfliktach. Formę i terminy zajęć omawiamy przy zapisie.</p>',
      ),
      array(
        'heading' => 'Kto prowadzi',
        'text' => '<p>Magdalena Tracz (Trener Umiejętności Społecznych) i Urszula Kaczmarska (Terapeuta i Trener TUS).</p>',
      ),
    ),
    'wizard_q' => 'Trudności w relacjach z rówieśnikami',
    'wizard_a' => 'Trening Umiejętności Społecznych to zajęcia, na których dziecko ćwiczy rozmowę, współpracę i radzenie sobie z emocjami w kontakcie z innymi.',
    'order' => 1,
  ),
  'terapia-adhd' => array(
    'title' => 'Terapia ADHD dla dzieci metodą dr Neuronowski',
    'short_name' => 'Terapia ADHD',
    'audience' => 'dzieci',
    'keywords' => array(
      'neuronowski',
    ),
    'label' => 'Po diagnozie ADHD',
    'short' => 'Regularna, zaplanowana praca z dzieckiem z ADHD.',
    'bullets' => array(),
    'price' => '4 800 zł',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla dzieci z ADHD i ich rodziców.</p>',
      ),
      array(
        'heading' => 'Kiedy warto się zgłosić',
        'text' => '<p>Gdy po diagnozie chcesz rozpocząć uporządkowaną, regularną pracę nad trudnościami dziecka.</p>',
      ),
      array(
        'heading' => 'Jak to wygląda',
        'text' => '<p>Terapia prowadzona jest metodą dr Neuronowski. Przed rozpoczęciem poznajemy dziecko i razem z rodzicem ustalamy cele oraz plan pracy. Liczbę i częstotliwość spotkań przedstawimy przy zapisie.</p>',
      ),
      array(
        'heading' => 'Co dzieje się później',
        'text' => '<p>W trakcie terapii rozmawiamy z rodzicem o postępach i o tym, jak wspierać dziecko w domu.</p>',
      ),
    ),
    'wizard_q' => 'Dziecko ma już diagnozę ADHD',
    'wizard_a' => 'Po diagnozie możesz rozpocząć regularną terapię. W rozmowie ustalimy, czy lepsza będzie terapia metodą dr Neuronowski, czy np. Instrumental Enrichment.',
    'order' => 2,
  ),
  'sb5' => array(
    'title' => 'Test inteligencji skalą Stanford-Binet',
    'short_name' => 'Test Stanford-Binet',
    'audience' => 'dzieci',
    'keywords' => array(
      'stanford',
      'binet',
      'inteligencji',
    ),
    'label' => 'Zdolności i możliwości',
    'short' => 'Badanie, które pokazuje mocne strony dziecka i obszary do wsparcia.',
    'bullets' => array(),
    'price' => '600 zł',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla dzieci, gdy chcesz rzetelnie poznać ich możliwości intelektualne – na przykład przy podejrzeniu wybitnych zdolności, przy trudnościach w nauce albo przed ważnymi decyzjami edukacyjnymi.</p>',
      ),
      array(
        'heading' => 'O badaniu',
        'text' => '<p>Skala Stanford-Binet to wystandaryzowany test inteligencji. Bada m.in. rozumowanie, wiedzę, rozumowanie ilościowe, przetwarzanie wzrokowo-przestrzenne i pamięć roboczą. Dzięki temu pokazuje nie tylko ogólny wynik, ale też profil mocnych stron i obszarów, które warto wspierać.</p>',
      ),
      array(
        'heading' => 'Jak to wygląda',
        'text' => '<p>Badanie odbywa się indywidualnie, a jego długość zależy od wieku dziecka. Wyniki omawiamy z rodzicem.</p>',
      ),
    ),
    'wizard_q' => 'Chcę poznać zdolności i możliwości dziecka',
    'wizard_a' => 'Test Stanford-Binet pokazuje ogólny poziom inteligencji oraz profil mocnych stron i obszarów, które warto wspierać.',
    'order' => 3,
  ),
  'ie' => array(
    'title' => 'Terapia Instrumental Enrichment (IE)',
    'short_name' => 'Terapia Instrumental Enrichment',
    'audience' => 'dzieci',
    'keywords' => array(
      'instrumental',
      'enrichment',
    ),
    'label' => 'Myślenie i uczenie się',
    'short' => 'Program rozwijający planowanie, uwagę i sposób rozwiązywania zadań.',
    'bullets' => array(),
    'price' => '200 zł',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla dzieci i młodzieży, które potrzebują wsparcia w rozwijaniu uwagi, planowania, porządkowania informacji i samodzielnego rozwiązywania zadań.</p>',
      ),
      array(
        'heading' => 'O metodzie',
        'text' => '<p>Program Instrumental Enrichment opracował prof. Reuven Feuerstein. Praca na kolejnych zestawach zadań uczy, jak planować, porównywać, porządkować informacje i sprawdzać własne rozwiązania. Celem jest przeniesienie tych umiejętności na naukę i codzienne sytuacje.</p>',
      ),
      array(
        'heading' => 'Kto prowadzi',
        'text' => '<p>Ewa Gardyańczyk, terapeuta Instrumental Enrichment.</p>',
      ),
    ),
    'wizard_q' => '',
    'wizard_a' => '',
    'order' => 4,
  ),
  'konsultacje' => array(
    'title' => 'Konsultacje dla rodziców i wsparcie psychologiczne dzieci',
    'short_name' => 'Konsultacje dla rodziców',
    'audience' => 'dzieci',
    'keywords' => array(
      'rodzic i dziecko',
      'konsultacj',
      'poradnictw',
    ),
    'label' => 'Wsparcie dla rodziny',
    'short' => 'Poradnictwo dla rodziców, coaching dla nastolatków oraz wsparcie dzieci w spektrum autyzmu, dzieci wybitnie zdolnych i młodych sportowców.',
    'bullets' => array(),
    'price' => 'Wycena indywidualna',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<ul><li>rodzice, którzy szukają wskazówek, jak reagować na trudne zachowania i wspierać dziecko na co dzień,</li><li>nastolatki, które chcą lepiej poznać siebie i swoje cele (coaching),</li><li>dzieci wysokofunkcjonujące w spektrum autyzmu,</li><li>dzieci wybitnie zdolne,</li><li>młodzi sportowcy.</li></ul>',
      ),
      array(
        'heading' => 'Jak to wygląda',
        'text' => '<p>Zwykle zaczynamy od konsultacji z rodzicem. Na jej podstawie proponujemy dalszą formę pracy – z rodzicem, z dzieckiem albo z obojgiem.</p>',
      ),
      array(
        'heading' => 'W zespole',
        'text' => '<p>Z dziećmi i młodzieżą pracują m.in. Sylwia Mieszczakowska i Grzegorz Kwietniewski, psycholodzy dzieci i młodzieży.</p>',
      ),
    ),
    'wizard_q' => 'Potrzebuję wskazówek jako rodzic',
    'wizard_a' => 'Zacznij od konsultacji dla rodziców. Porozmawiamy o sytuacji i o tym, jak wspierać dziecko na co dzień.',
    'order' => 5,
  ),
  'diag-dorosli' => array(
    'title' => 'Diagnoza psychologiczna pod kątem ADHD dla dorosłych',
    'short_name' => 'Diagnoza ADHD dorosłych',
    'audience' => 'dorosli',
    'keywords' => array(
      'adhd',
      'doros',
    ),
    'label' => 'Chcę sprawdzić, czy to ADHD',
    'short' => 'Dla osób, którym od lat towarzyszą trudności z koncentracją, organizacją czasu czy kończeniem zadań i które chcą wiedzieć, skąd się biorą.',
    'bullets' => array(
      'rozmowa o obecnych trudnościach i historii życia',
      'badanie wystandaryzowanymi narzędziami',
      'omówienie wyników i dalszych możliwości',
    ),
    'price' => '1 500 zł',
    'featured' => true,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla dorosłych, którzy podejrzewają u siebie ADHD.</p>',
      ),
      array(
        'heading' => 'Kiedy warto się zgłosić',
        'text' => '<ul><li>od lat masz trudność ze skupieniem uwagi, organizacją czasu lub kończeniem zadań,</li><li>czujesz, że wkładasz w codzienne obowiązki dużo więcej wysiłku niż inni,</li><li>ADHD zdiagnozowano u Twojego dziecka i rozpoznajesz podobne trudności u siebie.</li></ul>',
      ),
      array(
        'heading' => 'Jak wygląda diagnoza',
        'text' => '<p>Obejmuje rozmowę o obecnym funkcjonowaniu i historii życia – także o dzieciństwie – oraz badanie wystandaryzowanymi narzędziami. Harmonogram spotkań ustalamy przy zapisie.</p>',
      ),
      array(
        'heading' => 'Co dzieje się później',
        'text' => '<p>Omawiamy wyniki i możliwe dalsze kroki. W ofercie jest również terapia ADHD dla dorosłych oraz coaching, jeśli zechcesz kontynuować pracę.</p>',
      ),
    ),
    'wizard_q' => 'Podejrzewam u siebie ADHD',
    'wizard_a' => 'Diagnoza pomoże sprawdzić, czy trudności z koncentracją i organizacją wynikają z ADHD, i zaplanować dalsze kroki.',
    'order' => 6,
  ),
  'act' => array(
    'title' => 'Psychoterapia ACT / TSR',
    'short_name' => 'Psychoterapia ACT / TSR',
    'audience' => 'dorosli',
    'keywords' => array(
      'act',
      'tsr',
      'psychoterapi',
    ),
    'label' => 'Chcę popracować nad sobą',
    'short' => 'Terapia akceptacji i zaangażowania oraz terapia skoncentrowana na rozwiązaniach. Prowadzi m.in. Ewa Gardyańczyk, terapeuta w nurcie ACT.',
    'bullets' => array(),
    'price' => '350 zł',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla dorosłych i młodzieży, którzy czują, że utknęli, są przeciążeni, zmagają się z napięciem i trudnymi emocjami albo stoją przed ważną decyzją.</p>',
      ),
      array(
        'heading' => 'O podejściu',
        'text' => '<p><strong>ACT</strong> (terapia akceptacji i zaangażowania) wywodzi się z nurtu poznawczo-behawioralnego. Pomaga zmienić relację z trudnymi myślami i emocjami, zamiast z nimi walczyć, oraz podejmować działania zgodne z tym, co dla Ciebie ważne.</p><p><strong>TSR</strong> (terapia skoncentrowana na rozwiązaniach) skupia się na Twoich celach i zasobach – na tym, co już działa i co możesz zrobić dalej.</p>',
      ),
      array(
        'heading' => 'Jak to wygląda',
        'text' => '<p>Na pierwszym spotkaniu rozmawiamy o tym, z czym przychodzisz i czego oczekujesz. Wspólnie ustalamy cel i częstotliwość spotkań.</p>',
      ),
    ),
    'wizard_q' => 'Chcę popracować nad emocjami i tym, co mnie blokuje',
    'wizard_a' => 'Psychoterapia ACT / TSR pomaga zmienić relację z trudnymi myślami i działać w zgodzie z tym, co dla Ciebie ważne.',
    'order' => 7,
  ),
  'szefowa' => array(
    'title' => 'Szefowa Swojego Życia',
    'short_name' => 'Szefowa Swojego Życia',
    'audience' => 'dorosli',
    'keywords' => array(
      'szefowa swojego życia',
      'szefowa swojego zycia',
    ),
    'label' => 'Chcę zmiany, nie terapii',
    'short' => 'Sześciomiesięczny program coachingowy dla kobiet: poczucie własnej wartości, zrozumienie siebie i plan działania na własnych zasadach.',
    'bullets' => array(),
    'price' => '12 000 zł',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla kobiet, które nie potrzebują terapii, ale chcą odzyskać wpływ na swoje życie – osobiste i zawodowe – i zacząć działać na własnych zasadach.</p>',
      ),
      array(
        'heading' => 'Nad czym pracujemy',
        'text' => '<ul><li>poczucie własnej wartości i pewność siebie,</li><li>zrozumienie swojej osobowości i sposobów działania,</li><li>przekonania, które zamiast pomagać, blokują,</li><li>cele zgodne z własnymi wartościami i plan działania.</li></ul>',
      ),
      array(
        'heading' => 'Jak to wygląda',
        'text' => '<p>Program trwa sześć miesięcy. Zaczynamy od rozmowy, w której sprawdzamy, czy odpowiada on temu, czego szukasz.</p>',
      ),
      array(
        'heading' => 'Kto prowadzi',
        'text' => '<p>Ewa Gardyańczyk – certyfikowany coach CoachWise™ i Brain Coach Academy, mentorka Femme Palette.</p>',
      ),
    ),
    'wizard_q' => 'Chcę zmiany i planu na życie (program dla kobiet)',
    'wizard_a' => '„Szefowa Swojego Życia” to sześciomiesięczny program coachingowy, w którym budujesz poczucie własnej wartości i plan działania.',
    'order' => 8,
  ),
  'life' => array(
    'title' => 'Life coaching i typologia MBTI',
    'short_name' => 'Life coaching i MBTI',
    'audience' => 'dorosli',
    'keywords' => array(
      'life coaching',
      'mbti',
      'typologi',
    ),
    'label' => 'Chcę lepiej zrozumieć siebie',
    'short' => 'Praca nad celami osobistymi i zawodowymi z wykorzystaniem MBTI.',
    'bullets' => array(),
    'price' => 'Wycena indywidualna',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla dorosłych, którzy chcą lepiej zrozumieć swoje preferencje, sposób działania i to, co daje im energię, a potem przełożyć tę wiedzę na decyzje osobiste i zawodowe.</p>',
      ),
      array(
        'heading' => 'Jak to wygląda',
        'text' => '<p>Proces coachingowy łączymy z pracą z typologią MBTI. Na początku ustalamy cel współpracy i liczbę spotkań.</p>',
      ),
    ),
    'wizard_q' => 'Chcę lepiej zrozumieć siebie i swoje cele',
    'wizard_a' => 'Life coaching z typologią MBTI pomaga poznać swoje preferencje i przełożyć tę wiedzę na decyzje.',
    'order' => 9,
  ),
  'sport' => array(
    'title' => 'Trening mentalny dla sportowców',
    'short_name' => 'Trening mentalny',
    'audience' => 'dorosli',
    'keywords' => array(
      'sportow',
      'mentaln',
    ),
    'label' => 'Uprawiam sport',
    'short' => 'Koncentracja, radzenie sobie z presją i motywacja w sporcie.',
    'bullets' => array(),
    'price' => 'Wycena indywidualna',
    'featured' => false,
    'cta' => 'phone',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla sportowców, którzy chcą lepiej radzić sobie z presją startową, utrzymywać koncentrację i budować motywację.</p>',
      ),
      array(
        'heading' => 'Jak to wygląda',
        'text' => '<p>Zaczynamy od rozmowy o dyscyplinie, celach i tym, co dziś przeszkadza w osiąganiu wyników. Na tej podstawie przygotowujemy plan pracy i wycenę.</p>',
      ),
    ),
    'wizard_q' => 'Jestem sportowcem',
    'wizard_a' => 'Trening mentalny pomaga radzić sobie z presją startową, utrzymać koncentrację i budować motywację.',
    'order' => 10,
  ),
  'mentoring' => array(
    'title' => 'Coaching i mentoring dla liderów',
    'short_name' => 'Mentoring dla liderów',
    'audience' => 'firmy',
    'keywords' => array(
      'mentoring',
      'lider',
    ),
    'label' => 'Dla liderek i liderów',
    'short' => 'Indywidualna praca nad celami biznesowymi, komunikacją z zespołem, radzeniem sobie z presją i równowagą między pracą a życiem prywatnym.',
    'bullets' => array(
      'ponad 18 lat doświadczenia w rozwoju ludzi i organizacji',
      'doświadczenie w międzynarodowych korporacjach',
      'wykładowca akademicki i MBA',
    ),
    'price' => '18 000 zł',
    'featured' => true,
    'cta' => 'form',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla liderek, liderów, menedżerów i właścicieli firm, którzy chcą rozwijać swoje kompetencje przywódcze.</p>',
      ),
      array(
        'heading' => 'Nad czym pracujemy',
        'text' => '<ul><li>efektywna komunikacja i zarządzanie zespołem,</li><li>radzenie sobie ze stresem i presją,</li><li>wyznaczanie celów biznesowych,</li><li>równowaga między życiem zawodowym a prywatnym.</li></ul>',
      ),
      array(
        'heading' => 'Kto prowadzi',
        'text' => '<p>Ewa Gardyańczyk – od ponad 18 lat zajmuje się rozwojem osobistym i biznesowym ludzi. W międzynarodowych korporacjach odpowiadała za wdrażanie i zarządzanie procesami szkoleniowo-rozwojowymi. Wykładowca akademicki i wykładowca MBA.</p>',
      ),
      array(
        'heading' => 'Pierwszy krok',
        'text' => '<p>Napisz lub zadzwoń – umówimy rozmowę o Twoich celach i zaproponujemy przebieg współpracy.</p>',
      ),
    ),
    'wizard_q' => 'Rozwój lidera lub menedżera',
    'wizard_a' => 'Indywidualny coaching i mentoring: cele biznesowe, komunikacja z zespołem, radzenie sobie z presją.',
    'order' => 11,
  ),
  'szkolenia' => array(
    'title' => 'Szkolenia i warsztaty dla zespołów',
    'short_name' => 'Szkolenia dla zespołów',
    'audience' => 'firmy',
    'keywords' => array(
      'szkoleni',
      'warsztat',
      'biznes',
    ),
    'label' => 'Dla zespołów i działów HR',
    'short' => 'Warsztaty z umiejętności miękkich oraz praca z certyfikowanymi narzędziami MBTI, Insights Discovery, Belbin Team Roles i FIRO.',
    'bullets' => array(),
    'price' => 'Wycena indywidualna',
    'featured' => false,
    'cta' => 'form',
    'details' => array(
      array(
        'heading' => 'Dla kogo',
        'text' => '<p>Dla firm i organizacji, które chcą rozwijać komunikację, współpracę i kompetencje przywódcze w zespołach.</p>',
      ),
      array(
        'heading' => 'Co oferujemy',
        'text' => '<ul><li>indywidualne i grupowe warsztaty z umiejętności miękkich,</li><li>szkolenia z wykorzystaniem narzędzi psychometrycznych MBTI, Insights Discovery, Belbin Team Roles i FIRO,</li><li>wsparcie w tworzeniu strategii zarządzania zasobami ludzkimi,</li><li>gotowe narzędzia do dalszej pracy w zespole.</li></ul>',
      ),
      array(
        'heading' => 'Pierwszy krok',
        'text' => '<p>Wyślij zapytanie z informacją o wielkości zespołu i temacie, nad którym chcecie pracować. Przygotujemy propozycję programu i wycenę.</p>',
      ),
    ),
    'wizard_q' => 'Szkolenie lub warsztat dla zespołu',
    'wizard_a' => 'Warsztaty z umiejętności miękkich i szkolenia z narzędziami MBTI, Insights Discovery, Belbin i FIRO – z programem dopasowanym do zespołu.',
    'order' => 12,
  ),
);
