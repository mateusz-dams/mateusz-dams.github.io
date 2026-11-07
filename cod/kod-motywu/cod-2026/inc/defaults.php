<?php
/**
 * Teksty domyślne strony głównej i elementów wspólnych.
 * Każdy z nich można zmienić w panelu (Strony → Strona główna, Opcje motywu → COD 2026).
 * Puste pole w panelu = tekst z tego pliku.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Teksty domyślne strony głównej.
 *
 * @param string|null $key Klucz.
 * @return mixed
 */
function cod_home_default( $key = null ) {
	static $d = null;
	if ( null === $d ) {
		$d = array(
			'cod_hero_kicker'    => 'Psycholog i coach w Radomiu',
			'cod_hero_title'     => 'Pomoc psychologiczna dla dzieci, rodziców i dorosłych',
			'cod_hero_lead'      => 'Diagnoza ADHD, terapia i treningi dla dzieci, wsparcie dla dorosłych oraz coaching i szkolenia dla firm. Pierwszym krokiem jest krótka rozmowa – razem ustalimy, od czego zacząć.',
			'cod_hero_caption'   => 'Ewa Gardyańczyk, psycholog i mindset coach',
			'cod_hero_chips'     => "Diagnoza ADHD\nTerapia i treningi\nCoaching dla liderów",
			'cod_hero_second'    => 'Dobierz formę pomocy',

			'cod_finder_kicker'  => 'Dwa pytania, jedna odpowiedź',
			'cod_finder_title'   => 'Od czego zacząć?',
			'cod_finder_lead'    => 'Odpowiedz na dwa pytania, a podpowiemy, która usługa będzie dobrym pierwszym krokiem. To tylko wskazówka – ostateczną decyzję podejmiemy razem w rozmowie.',

			'cod_offer_kicker'   => 'Oferta',
			'cod_offer_title'    => 'W czym możemy pomóc',
			'cod_offer_lead'     => 'Usługi pogrupowane według tego, kogo dotyczą. Na początku każdej grupy jest to, od czego najczęściej się zaczyna. Kliknij kartę, aby zobaczyć, dla kogo jest usługa, jak wygląda i co dzieje się później.',

			'cod_about_kicker'   => 'Poznaj mnie',
			'cod_about_title'    => 'Ewa Gardyańczyk',
			'cod_about_quote'    => '„Wierzę, że każdy człowiek jest doskonały. Najczęściej jednak tę doskonałość musi w sobie odkryć – a głębokie zrozumienie siebie jest kluczem do pełniejszego życia.”',
			'cod_about_text'     => '<p>Od ponad 18 lat zawodowo zajmuję się rozwojem osobistym i biznesowym ludzi. Doświadczenie zbierałam w Polsce i za granicą, m.in. odpowiadając za procesy szkoleniowo-rozwojowe w międzynarodowych korporacjach. Wspieram dzieci i rodziców w pogłębianiu wzajemnych więzi, kobiety w odzyskiwaniu wpływu na własne życie, a firmy w rozwijaniu liderów i zespołów. Prywatnie jestem mamą Ali i Kostka.</p>',
			'cod_about_quals'    => "Magister psychologii\nDiagnosta\nTerapeuta ACT\nTerapeuta Instrumental Enrichment\nTrener MBTI, Insights Discovery, Belbin, FIRO\nCoach CoachWise™ i Brain Coach Academy\nMentorka Femme Palette\nWykładowca akademicki i MBA\nAutorka e-booków",

			'cod_steps_kicker'   => 'Bez niespodzianek',
			'cod_steps_title'    => 'Jak wygląda pierwszy kontakt',
			'cod_steps_lead'     => 'Nie potrzebujesz gotowych odpowiedzi ani planu. Wystarczy, że opowiesz, co się dzieje – resztą zajmiemy się wspólnie.',
			'cod_prep_title'     => 'Przed telefonem warto pomyśleć',
			'cod_prep_items'     => "kogo dotyczy sprawa – dziecka, Ciebie czy zespołu,\nod kiedy trwa trudność i w jakich sytuacjach ją widać,\nczy masz już jakąś diagnozę lub opinię,\njakie terminy spotkań Ci odpowiadają.",

			'cod_team_kicker'    => 'Zespół',
			'cod_team_title'     => 'Kto będzie z Tobą pracować',
			'cod_team_lead'      => 'Psycholodzy, terapeuci i trenerzy Centrum. W pierwszej rozmowie pomożemy dobrać osobę do Twojej sytuacji.',

			'cod_faq_kicker'     => 'Pytania',
			'cod_faq_title'      => 'Zanim się umówisz',
			'cod_faq_lead'       => 'Nie ma tu Twojego pytania? Zadzwoń – chętnie odpowiemy.',

			'cod_contact_kicker' => 'Kontakt',
			'cod_contact_title'  => 'Umów pierwszy kontakt',
			'cod_contact_lead'   => 'Najszybciej przez telefon. Jeśli wolisz pisać – formularz obok.',

			'cod_shop_kicker'    => 'Sklep i kursy',
			'cod_shop_title'     => 'Do pracy we własnym tempie',
			'cod_shop_lead'      => 'Workbooki, e-booki i systemy autorstwa Ewy Gardyańczyk oraz kursy online.',
			'cod_courses_title'  => 'Kursy online',
			'cod_courses_text'   => 'Po zakupie lekcje i materiały są dostępne na Twoim koncie. Uczysz się we własnym tempie i wracasz do nich, kiedy chcesz.',

			'cod_cta_title'      => 'Zacznijmy od rozmowy.',
			'cod_cta_lead'       => 'Krótka rozmowa pomoże ustalić, od czego zacząć. Nie zobowiązuje do dalszej współpracy.',
		);
	}
	if ( null === $key ) {
		return $d;
	}
	return isset( $d[ $key ] ) ? $d[ $key ] : '';
}

/**
 * Pole strony głównej z tekstem domyślnym.
 *
 * @param string $key Klucz pola.
 */
function cod_home( $key ) {
	$front = (int) get_option( 'page_on_front' );
	return cod_field( $key, $front ? $front : false, cod_home_default( $key ) );
}

/**
 * Liczniki w sekcji „O mnie”.
 */
function cod_default_stats() {
	return array(
		array( 'number' => 18, 'suffix' => '+', 'label' => 'lat doświadczenia w rozwoju ludzi i organizacji' ),
		array( 'number' => 6, 'suffix' => '', 'label' => 'specjalistów w zespole Centrum' ),
		array( 'number' => 3, 'suffix' => '', 'label' => 'ścieżki wsparcia: dzieci i rodzice, dorośli, firmy' ),
	);
}

/**
 * Kroki pierwszego kontaktu.
 */
function cod_default_steps() {
	return array(
		array( 'title' => 'Telefon lub wiadomość', 'text' => 'Zadzwoń od poniedziałku do piątku w godz. 9:00–20:00 albo zostaw wiadomość w formularzu. Wystarczy kilka zdań: kogo dotyczy sprawa i w czym szukasz pomocy.', 'note' => 'Nie musisz opisywać szczegółów – porozmawiamy o nich na spotkaniu.' ),
		array( 'title' => 'Krótka rozmowa', 'text' => 'Dopytamy o Twoją sytuację, podpowiemy, która usługa i która osoba z zespołu będzie odpowiednia, podamy cenę i zaproponujemy termin.', 'note' => '' ),
		array( 'title' => 'Pierwsze spotkanie', 'text' => 'To przestrzeń, żeby spokojnie przyjrzeć się Twojej sytuacji i zobaczyć, czego naprawdę potrzebujesz. Jeśli sprawa dotyczy dziecka, zabierz opinie ze szkoły i wcześniejsze dokumenty, jeśli je masz.', 'note' => '' ),
		array( 'title' => 'Plan dalszej pracy', 'text' => 'Wspólnie ustalamy kierunek i formę wsparcia – diagnozę, terapię, trening albo proces coachingowy. O tym, czy i jak kontynuować, decydujesz Ty.', 'note' => '' ),
	);
}

/**
 * Pytania i odpowiedzi.
 */
function cod_default_faq() {
	return array(
		array( 'q' => 'Jak wygląda pierwsza wizyta i jak się do niej przygotować?', 'a' => '<p>Pierwsze spotkanie to rozmowa o tym, co się dzieje, od kiedy i czego potrzebujesz. Nie musisz mieć gotowego planu ani odpowiedzi – razem określimy kierunek pracy i formę wsparcia.</p><p>Jeśli sprawa dotyczy dziecka, zabierz opinie ze szkoły lub przedszkola oraz wcześniejsze diagnozy, jeśli je masz. Zapytaj przy zapisie, czy pierwsze spotkanie ma się odbyć z dzieckiem, czy z samym rodzicem.</p>' ),
		array( 'q' => 'Czy prowadzicie diagnozę ADHD i spektrum autyzmu?', 'a' => '<p>Prowadzimy diagnozę psychologiczną pod kątem ADHD u dzieci i u dorosłych. Dzieciom wysokofunkcjonującym w spektrum autyzmu oferujemy wsparcie psychologiczne. Jeśli szukasz diagnozy w kierunku spektrum autyzmu, zadzwoń – powiemy, jak możemy pomóc.</p>' ),
		array( 'q' => 'Jak przygotować dziecko do spotkania z psychologiem?', 'a' => '<p>Powiedz dziecku prawdę prostymi słowami: że idziecie porozmawiać i trochę popracować z osobą, która pomaga dzieciom i rodzicom lepiej się rozumieć. Nie strasz wizytą i nie przedstawiaj jej jako kary ani egzaminu. Zadbaj, żeby dziecko było wyspane i najedzone – wtedy łatwiej mu się skupić.</p>' ),
		array( 'q' => 'Czy po diagnozie dziecka otrzymam wskazówki do pracy w domu i w szkole?', 'a' => '<p>Tak. Częścią diagnozy jest spotkanie podsumowujące, na którym omawiamy wyniki i zalecenia – co możesz robić w domu i na co warto zwrócić uwagę w szkole. Jeśli potrzebne jest dalsze wsparcie, podpowiemy, jakie.</p>' ),
		array( 'q' => 'Czy można spotkać się online?', 'a' => '<p>Zapytaj o to przy zapisie. Powiemy, które spotkania w Twojej sprawie mogą odbyć się online, a które wymagają wizyty w gabinecie przy ul. Żeromskiego 51 w Radomiu.</p>' ),
		array( 'q' => 'Czy wizyty są poufne?', 'a' => '<p>Tak. Psychologów obowiązuje tajemnica zawodowa. Informacje ze spotkań nie są przekazywane osobom trzecim bez Twojej zgody, z wyjątkiem sytuacji przewidzianych w przepisach, np. zagrożenia życia lub zdrowia.</p>' ),
		array( 'q' => 'Czy potrzebuję skierowania?', 'a' => '<p>Nie. Wizyty w Centrum są odpłatne i nie wymagają skierowania. Wystarczy zadzwonić lub napisać.</p>' ),
		array( 'q' => 'Czy prowadzicie procesy rozwojowe dla kobiet, które nie potrzebują terapii?', 'a' => '<p>Tak. Sześciomiesięczny program „Szefowa Swojego Życia” jest dla kobiet, które chcą wzmocnić poczucie własnej wartości, lepiej zrozumieć siebie i zaplanować zmiany w życiu osobistym lub zawodowym.</p>' ),
		array( 'q' => 'Czy prowadzicie szkolenia dla firm i grup?', 'a' => '<p>Tak. Prowadzimy indywidualne i grupowe warsztaty z umiejętności miękkich, szkolenia z wykorzystaniem narzędzi MBTI, Insights Discovery, Belbin i FIRO oraz mentoring dla liderów. Napisz do nas, podając temat i wielkość zespołu.</p>' ),
		array( 'q' => 'Czy wizyta u psychologa musi być stresująca?', 'a' => '<p>Nie musi. Pierwsze spotkanie to rozmowa, a nie egzamin – nie ma dobrych ani złych odpowiedzi. Pracujemy w spokojnym tempie i tłumaczymy, co i dlaczego robimy. W każdej chwili możesz zadać pytanie albo powiedzieć, że czegoś nie chcesz.</p>' ),
	);
}

/**
 * Opinie.
 */
function cod_default_reviews() {
	return array(
		array(
			'quote' => 'Rewolucja, wzmocnienie, know-how to trzy sformułowania, które opisują Ewę i jej zawodowe działania! Nikt tak jak Ewa nie potrafi zadać „niewygodnego” pytania, które powoduje rewolucje w głowie i zmusza do działania, aby sięgnąć po więcej. Nikt tak jak Ewa nie wzmocni w chwilach zwątpienia we własne możliwości.',
			'name'  => 'Sylwia Materna',
			'role'  => 'HR Business Partner & Coach',
			'photo' => 'opinia-sylwia-materna.webp',
		),
	);
}

/**
 * Zespół – zapas, gdy w Opcjach motywu nie uzupełniono listy.
 * Zdjęcia: oryginały z poprzedniego motywu (Ewa, Magdalena, Urszula) i kadry z obecnej strony.
 */
function cod_default_team() {
	return array(
		array( 'name' => 'Ewa Gardyańczyk', 'role' => 'Psycholog i mindset coach', 'scope' => 'Diagnoza, terapia ACT i IE, coaching dla kobiet, liderów i zespołów.', 'photo' => 'zespol-ewa-gardyanczyk.webp' ),
		array( 'name' => 'Sylwia Mieszczakowska', 'role' => 'Psycholog dzieci i młodzieży', 'scope' => 'Pracuje z dziećmi, nastolatkami i ich rodzicami.', 'photo' => 'zespol-sylwia-mieszczakowska.webp' ),
		array( 'name' => 'Grzegorz Kwietniewski', 'role' => 'Psycholog dzieci i młodzieży', 'scope' => 'Pracuje z dziećmi, nastolatkami i ich rodzicami.', 'photo' => 'zespol-grzegorz-kwietniewski.webp' ),
		array( 'name' => 'Karol Ociesa', 'role' => 'Psycholog i socjolog', 'scope' => 'Łączy perspektywę psychologii i socjologii – patrzy na człowieka także w kontekście relacji i grupy.', 'photo' => 'zespol-karol-ociesa.webp' ),
		array( 'name' => 'Magdalena Tracz', 'role' => 'Trener Umiejętności Społecznych', 'scope' => 'Prowadzi Treningi Umiejętności Społecznych dla dzieci.', 'photo' => 'zespol-magdalena-tracz.webp' ),
		array( 'name' => 'Urszula Kaczmarska', 'role' => 'Terapeuta i Trener TUS', 'scope' => 'Prowadzi terapię oraz Treningi Umiejętności Społecznych.', 'photo' => 'zespol-urszula-kaczmarska.webp' ),
	);
}

/**
 * Grupy odbiorców (zakładki oferty i kreator).
 */
function cod_audiences() {
	return array(
		'dzieci'  => array(
			'tab'    => __( 'Dzieci i rodzice', 'cod2026' ),
			'who'    => __( 'Mojego dziecka', 'cod2026' ),
			'hint'   => __( 'dziecko lub nastolatek', 'cod2026' ),
			'icon'   => 'child',
			'topic'  => 'Dziecka',
			'match'  => array( 'dzie', 'rodzic', 'młodzież', 'mlodziez' ),
		),
		'dorosli' => array(
			'tab'    => __( 'Dorośli', 'cod2026' ),
			'who'    => __( 'Mnie', 'cod2026' ),
			'hint'   => __( 'jestem osobą dorosłą', 'cod2026' ),
			'icon'   => 'me',
			'topic'  => 'Mnie',
			'match'  => array( 'doros', 'kobiet', 'sport', 'indywidual' ),
		),
		'firmy'   => array(
			'tab'    => __( 'Firmy', 'cod2026' ),
			'who'    => __( 'Firmy lub zespołu', 'cod2026' ),
			'hint'   => __( 'lider, menedżer, HR', 'cod2026' ),
			'icon'   => 'team',
			'topic'  => 'Firmy lub zespołu',
			'match'  => array( 'firm', 'biznes', 'lider', 'organizac' ),
		),
	);
}
