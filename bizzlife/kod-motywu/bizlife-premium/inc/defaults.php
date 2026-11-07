<?php
/**
 * Wartości domyślne ustawień motywu (Customizer).
 *
 * Treści bazują na aktualnej stronie bizzlife.pl. Pozycje oznaczone w docs/DO-WERYFIKACJI.md
 * wymagają potwierdzenia przez właściciela firmy.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Definicje grup powtarzalnych (sloty).
 *
 * Każda grupa: liczba slotów, lista pól, domyślne wartości kolejnych slotów.
 *
 * @return array
 */
function bzl_slot_groups() {
	return array(
		'hero_point'   => array(
			'count'    => 3,
			'fields'   => array( 'text' ),
			'defaults' => array(
				array( 'text' => __( 'Pierwsza konsultacja bezpłatna', 'bizlife' ) ),
				array( 'text' => __( 'JDG i spółki', 'bizlife' ) ),
				array( 'text' => __( 'Księgowość, kadry i płace', 'bizlife' ) ),
			),
		),
		'intro'        => array(
			'count'    => 4,
			'fields'   => array( 'icon', 'title', 'text' ),
			'defaults' => array(
				array(
					'icon'  => 'ledger',
					'title' => __( 'Przejrzysta księgowość', 'bizlife' ),
					'text'  => __( 'KPiR, ryczałt lub pełne księgi handlowe prowadzone zgodnie z przepisami i wyjaśniane zrozumiałym językiem.', 'bizlife' ),
				),
				array(
					'icon'  => 'percent',
					'title' => __( 'Wsparcie podatkowe', 'bizlife' ),
					'text'  => __( 'Pomagamy porównać formy opodatkowania i odpowiadamy na bieżące pytania podatkowe związane z Twoją działalnością.', 'bizlife' ),
				),
				array(
					'icon'  => 'chat',
					'title' => __( 'Sprawna komunikacja', 'bizlife' ),
					'text'  => __( 'Stały kontakt telefoniczny i mailowy z opiekunem oraz możliwość spotkania w biurze w Radomiu.', 'bizlife' ),
				),
				array(
					'icon'  => 'person',
					'title' => __( 'Indywidualne podejście', 'bizlife' ),
					'text'  => __( 'Zakres i cenę obsługi ustalamy na podstawie liczby dokumentów i specyfiki Twojej firmy.', 'bizlife' ),
				),
			),
		),
		'why'          => array(
			'count'    => 6,
			'fields'   => array( 'icon', 'title', 'text' ),
			'defaults' => array(
				array(
					'icon'  => 'folder',
					'title' => __( 'Organizacja dokumentów', 'bizlife' ),
					'text'  => __( 'Na starcie ustalamy, jakie dokumenty przekazujesz i w jakim rytmie, żeby comiesięczne rozliczenia przebiegały bez szukania faktur na ostatnią chwilę.', 'bizlife' ),
				),
				array(
					'icon'  => 'chat',
					'title' => __( 'Komunikacja z biurem', 'bizlife' ),
					'text'  => __( 'Masz stały kontakt telefoniczny i mailowy ze swoim opiekunem, a w razie potrzeby — spotkanie w naszym biurze w Radomiu.', 'bizlife' ),
				),
				array(
					'icon'  => 'layers',
					'title' => __( 'Zakres obsługi w jednym miejscu', 'bizlife' ),
					'text'  => __( 'Księgowość, kadry i płace, rejestracja spółek, dotacje i wsparcie podatkowe — bez koordynowania kilku różnych biur.', 'bizlife' ),
				),
				array(
					'icon'  => 'shield',
					'title' => __( 'Reprezentacja przed urzędami', 'bizlife' ),
					'text'  => __( 'Na podstawie pełnomocnictwa reprezentujemy klientów przed US, ZUS, PIP i GUS oraz składamy wyjaśnienia w ich imieniu.', 'bizlife' ),
				),
				array(
					'icon'  => 'calendar',
					'title' => __( 'Terminowość procesów', 'bizlife' ),
					'text'  => __( 'Pilnujemy terminów podatkowych i składek ZUS, abyś nie musiał samodzielnie śledzić kalendarza księgowego.', 'bizlife' ),
				),
				array(
					'icon'  => 'doc-check',
					'title' => __( 'Przejrzyste zasady współpracy', 'bizlife' ),
					'text'  => __( 'Zakres obsługi i wynagrodzenie ustalamy przed rozpoczęciem współpracy. Bez niespodzianek na fakturze.', 'bizlife' ),
				),
			),
		),
		'process'      => array(
			'count'    => 4,
			'fields'   => array( 'title', 'text' ),
			'defaults' => array(
				array(
					'title' => __( 'Pierwszy kontakt', 'bizlife' ),
					'text'  => __( 'Dzwonisz, piszesz albo zostawiasz wiadomość w formularzu. Pierwsza konsultacja jest bezpłatna.', 'bizlife' ),
				),
				array(
					'title' => __( 'Rozpoznanie potrzeb firmy', 'bizlife' ),
					'text'  => __( 'Rozmawiamy o formie działalności, branży, liczbie dokumentów i zatrudnieniu.', 'bizlife' ),
				),
				array(
					'title' => __( 'Ustalenie zakresu współpracy', 'bizlife' ),
					'text'  => __( 'Przedstawiamy zakres obsługi i wycenę. Jeśli zmieniasz biuro, ustalamy sposób przekazania dokumentacji.', 'bizlife' ),
				),
				array(
					'title' => __( 'Rozpoczęcie obsługi księgowej', 'bizlife' ),
					'text'  => __( 'Aktualizujemy dane w urzędzie skarbowym i ZUS, przejmujemy dokumenty i prowadzimy księgowość od ustalonego miesiąca.', 'bizlife' ),
				),
			),
		),
		'audience'     => array(
			'count'    => 6,
			'fields'   => array( 'icon', 'title', 'text', 'url' ),
			'defaults' => array(
				array(
					'icon'  => 'person',
					'title' => __( 'Jednoosobowe działalności', 'bizlife' ),
					'text'  => __( 'KPiR, ryczałt, rozliczenia ZUS i bieżące deklaracje dla osób samozatrudnionych.', 'bizlife' ),
					'url'   => '{usluga:ksiegowosc}',
				),
				array(
					'icon'  => 'building',
					'title' => __( 'Spółki', 'bizlife' ),
					'text'  => __( 'Pełna księgowość i obsługa spółek — także na etapie rejestracji w systemie S24.', 'bizlife' ),
					'url'   => '{usluga:rejestracja-i-obsluga-spolek}',
				),
				array(
					'icon'  => 'truck',
					'title' => __( 'Firmy transportowe', 'bizlife' ),
					'text'  => __( 'Księgowość uwzględniająca specyfikę branży transportowej, w tym rozliczanie czasu pracy kierowców.', 'bizlife' ),
					'url'   => '{usluga:ksiegowosc-dla-transportu}',
				),
				array(
					'icon'  => 'sprout',
					'title' => __( 'Nowe firmy', 'bizlife' ),
					'text'  => __( 'Wybór formy działalności, rejestracja w CEIDG lub S24 i wsparcie przy wnioskach o dotacje.', 'bizlife' ),
					'url'   => '{usluga:dotacje-i-zakladanie-firm}',
				),
				array(
					'icon'  => 'cart',
					'title' => '',
					'text'  => '',
					'url'   => '',
				),
				array(
					'icon'  => 'briefcase',
					'title' => '',
					'text'  => '',
					'url'   => '',
				),
			),
		),
		'contact_step' => array(
			'count'    => 3,
			'fields'   => array( 'title', 'text' ),
			'defaults' => array(
				array(
					'title' => __( 'Odpowiadamy na wiadomość', 'bizlife' ),
					'text'  => __( 'Oddzwaniamy lub odpisujemy w godzinach pracy biura.', 'bizlife' ),
				),
				array(
					'title' => __( 'Umawiamy konsultację', 'bizlife' ),
					'text'  => __( 'Telefonicznie lub w biurze przy ul. Niedziałkowskiego.', 'bizlife' ),
				),
				array(
					'title' => __( 'Przedstawiamy zakres i wycenę', 'bizlife' ),
					'text'  => __( 'Dopasowane do liczby dokumentów i formy działalności.', 'bizlife' ),
				),
			),
		),
	);
}

/**
 * Wszystkie domyślne wartości (płaska tablica klucz => wartość).
 *
 * @return array
 */
function bzl_defaults() {
	static $defaults = null;
	if ( null !== $defaults ) {
		return $defaults;
	}

	$defaults = array(
		// Marka.
		'brand_name'           => 'Bizzlife',
		'brand_descriptor'     => __( 'Biuro rachunkowe · Radom', 'bizlife' ),
		'logo_url'             => 'https://bizzlife.pl/wp-content/uploads/2025/11/bizzlife_logo.svg',
		'logo_height'          => 44,
		'header_cta_label'     => __( 'Bezpłatna konsultacja', 'bizlife' ),
		'header_cta_url'       => '{kontakt}',
		'header_show_phone'    => true,

		// Dane firmy.
		'company_name'         => 'Bizzlife Sp. z o.o.',
		'phone'                => '+48 605 666 610',
		'email'                => 'biuro@bizzlife.pl',
		'street'               => 'ul. Niedziałkowskiego 40/2',
		'postal_code'          => '26-610',
		'city'                 => 'Radom',
		'hours'                => 'pn–pt: 8:00–16:00',
		'opening_hours_schema' => 'Mo-Fr 08:00-16:00',
		'krs'                  => '0000776299',
		'nip'                  => '7962989526',
		'regon'                => '382828941',
		'geo_lat'              => '51.4065945',
		'geo_lng'              => '21.1616705',
		'map_embed_url'        => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2488.8073452912977!2d21.161670499999996!3d51.40659449999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47185f1248915d37%3A0x1437ed040f0ada1d!2sBiuro%20Rachunkowe%20-%20BizzLife%20%7C%20Ksi%C4%99gowo%C5%9B%C4%87%20%7C%20Radom!5e0!3m2!1spl!2spl!4v1762343950599!5m2!1spl!2spl',
		'map_place_url'        => 'https://maps.app.goo.gl/zV6sB3nMmWi9FRF27',

		// Social media.
		'social_facebook'      => 'https://www.facebook.com/bizzliferadom/',
		'social_instagram'     => 'https://www.instagram.com/bizzlifeksiegowosc/',
		'social_whatsapp'      => 'https://wa.me/48605666610',
		'social_google'        => 'https://share.google/4sG2QmSr5GfVZAAHq',
		'social_linkedin'      => '',

		// Strona główna – kolejność i widoczność.
		'home_order'           => 'hero,intro,services,why,about,process,audience,content,news,faq,cta,contact',

		// Hero.
		'hero_show'            => true,
		'hero_eyebrow'         => __( 'Biuro rachunkowe w Radomiu', 'bizlife' ),
		'hero_title'           => __( 'Księgowość, która daje Ci *przestrzeń* do prowadzenia biznesu.', 'bizlife' ),
		'hero_lead'            => __( 'Prowadzimy księgowość, kadry i płace oraz wspieramy w sprawach podatkowych jednoosobowe działalności i spółki. Ty rozwijasz firmę — my dbamy o dokumenty, terminy i rozliczenia z urzędami.', 'bizlife' ),
		'hero_cta1_label'      => __( 'Umów konsultację', 'bizlife' ),
		'hero_cta1_url'        => '{kontakt}',
		'hero_cta2_label'      => __( 'Poznaj nasze usługi', 'bizlife' ),
		'hero_cta2_url'        => '{uslugi}',
		'hero_image'           => 0,
		'hero_image_caption'   => '',

		// Wprowadzenie.
		'intro_show'           => true,
		'intro_tone'           => 'light',
		'intro_eyebrow'        => __( 'Komu pomagamy', 'bizlife' ),
		'intro_title'          => __( 'Porządek w finansach firmy bez formalności po Twojej stronie', 'bizlife' ),
		'intro_lead'           => __( 'Pomagamy przedsiębiorcom, którzy chcą mieć pewność, że ich księgowość jest prowadzona rzetelnie, a sprawy z urzędami pozostają pod kontrolą.', 'bizlife' ),

		// Usługi.
		'services_show'        => true,
		'services_tone'        => 'dark',
		'services_eyebrow'     => __( 'Oferta', 'bizlife' ),
		'services_title'       => __( 'Usługi księgowe i podatkowe', 'bizlife' ),
		'services_lead'        => __( 'Obsługa dopasowana do formy prawnej, branży i skali Twojej firmy — od pierwszej faktury po pełną obsługę spółki.', 'bizlife' ),
		'services_image'       => 0,
		'services_cta_show'    => true,
		'services_cta_title'   => __( 'Nie wiesz, czego potrzebujesz?', 'bizlife' ),
		'services_cta_text'    => __( 'Opisz krótko swoją firmę — podpowiemy, od czego zacząć.', 'bizlife' ),
		'services_cta_label'   => __( 'Zapytaj o ofertę', 'bizlife' ),
		'services_cta_url'     => '{kontakt}',

		// Dlaczego.
		'why_show'             => true,
		'why_tone'             => 'darker',
		'why_eyebrow'          => __( 'Dlaczego Bizzlife', 'bizlife' ),
		'why_title'            => __( 'Jak pracujemy na co dzień', 'bizlife' ),
		'why_lead'             => __( 'Pracujemy tak, jak sami chcielibyśmy być obsługiwani — z szacunkiem do Twojego czasu i pieniędzy.', 'bizlife' ),

		// O nas.
		'about_show'           => true,
		'about_tone'           => 'light',
		'about_eyebrow'        => __( 'O nas', 'bizlife' ),
		'about_title'          => __( 'Poznaj Bizzlife — biuro rachunkowe w Radomiu', 'bizlife' ),
		'about_text'           => __( "Nazywam się Ewelina Oleksiewicz. Bizzlife to biuro podatkowo-księgowe, które stworzyłam z pasji do finansów i chęci wspierania ludzi w prowadzeniu własnej firmy.\n\nWykształcenie finansowo-księgowe i praktyka pozwalają mi patrzeć na biznes szerzej niż przez pryzmat samego „księgowania faktur”. Towarzyszymy klientom na każdym etapie — od założenia firmy, przez bieżące sprawy kadrowo-płacowe, po wsparcie w kwestiach podatkowych.", 'bizlife' ),
		'about_person_name'    => 'Ewelina Oleksiewicz',
		'about_person_role'    => __( 'Założycielka Bizzlife', 'bizlife' ),
		'about_image'          => 0,
		'about_cta_label'      => __( 'Poznaj nas', 'bizlife' ),
		'about_cta_url'        => '{o-nas}',

		// Proces.
		'process_show'         => true,
		'process_tone'         => 'dark',
		'process_eyebrow'      => __( 'Współpraca', 'bizlife' ),
		'process_title'        => __( 'Jak wygląda współpraca?', 'bizlife' ),
		'process_lead'         => __( 'Cztery kroki od pierwszej rozmowy do przejęcia Twojej księgowości.', 'bizlife' ),

		// Dla kogo.
		'audience_show'        => true,
		'audience_tone'        => 'light',
		'audience_eyebrow'     => __( 'Dla kogo', 'bizlife' ),
		'audience_title'       => __( 'Dla kogo pracujemy?', 'bizlife' ),
		'audience_lead'        => __( 'Obsługujemy zarówno osoby samozatrudnione, jak i spółki kapitałowe oraz firmy z sektora transportowego.', 'bizlife' ),

		// Aktualności.
		'news_show'            => true,
		'news_tone'            => 'dark',
		'news_eyebrow'         => __( 'Wiedza', 'bizlife' ),
		'news_title'           => __( 'Aktualności i poradniki', 'bizlife' ),
		'news_lead'            => __( 'Praktyczne informacje o podatkach, ZUS i prowadzeniu firmy.', 'bizlife' ),
		'news_count'           => 3,
		'news_cta_label'       => __( 'Wszystkie artykuły', 'bizlife' ),

		// FAQ.
		'faq_show'             => true,
		'faq_tone'             => 'darker',
		'faq_eyebrow'          => __( 'FAQ', 'bizlife' ),
		'faq_title'            => __( 'Najczęstsze pytania', 'bizlife' ),
		'faq_lead'             => __( 'Jeśli nie znajdziesz tu odpowiedzi, zadzwoń lub napisz — chętnie omówimy sytuację Twojej firmy.', 'bizlife' ),
		'faq_count'            => 8,
		'faq_cta_label'        => __( 'Wszystkie pytania', 'bizlife' ),

		// CTA.
		'cta_show'             => true,
		'cta_title'            => __( 'Zadbaj o księgowość swojej firmy.', 'bizlife' ),
		'cta_text'             => __( 'Opowiedz nam o swojej działalności. Pierwsza konsultacja jest bezpłatna i do niczego nie zobowiązuje.', 'bizlife' ),
		'cta_btn1_label'       => __( 'Umów konsultację', 'bizlife' ),
		'cta_btn1_url'         => '{kontakt}',
		'cta_btn2_label'       => __( 'Zadzwoń do nas', 'bizlife' ),
		'cta_btn2_url'         => '{tel}',

		// Kontakt.
		'contact_show'         => true,
		'contact_eyebrow'      => __( 'Kontakt', 'bizlife' ),
		'contact_title'        => __( 'Skontaktuj się z nami', 'bizlife' ),
		'contact_lead'         => __( 'Masz pytania albo chcesz omówić zakres usług? Zostaw wiadomość — odezwiemy się najszybciej, jak to możliwe.', 'bizlife' ),
		'contact_steps_title'  => __( 'Co dalej?', 'bizlife' ),

		// Formularz.
		'form_title'           => __( 'Napisz do nas', 'bizlife' ),
		'form_intro'           => __( 'Pola oznaczone gwiazdką są wymagane. Podaj telefon lub e-mail — oddzwonimy albo odpiszemy.', 'bizlife' ),
		'form_recipient'       => '',
		'form_submit_label'    => __( 'Wyślij wiadomość', 'bizlife' ),
		'form_success'         => __( 'Dziękujemy! Wiadomość została wysłana. Odezwiemy się najszybciej, jak to możliwe.', 'bizlife' ),
		'form_consent'         => __( 'Wyrażam zgodę na przetwarzanie moich danych osobowych przez Bizzlife Sp. z o.o. w celu udzielenia odpowiedzi na moje zapytanie.', 'bizlife' ),
		'form_gdpr_note'       => __( 'Administratorem danych osobowych jest Bizzlife Sp. z o.o., ul. Niedziałkowskiego 40/2, 26-610 Radom. Dane przetwarzamy wyłącznie w celu odpowiedzi na zapytanie. Przysługuje Ci prawo dostępu do danych, ich sprostowania, usunięcia oraz wniesienia sprzeciwu.', 'bizlife' ),
		'form_shortcode'       => '',
		'form_store_leads'     => true,
		'form_retention_days'  => 180,

		// Stopka.
		'footer_about'         => __( 'Biuro rachunkowe w Radomiu. Księgowość, kadry i płace oraz wsparcie podatkowe dla przedsiębiorców i spółek.', 'bizlife' ),
		'footer_credits'       => '',
		'cookie_info_url'      => '',

		// Wygląd.
		'accent_color'         => '#B9945A',
		'serif_headings'       => true,
		'animations'           => true,
		'ghost_words'          => true,
		'schema_enable'        => true,
		'webp_subsizes'        => true,
	);

	foreach ( bzl_slot_groups() as $group => $def ) {
		for ( $i = 1; $i <= $def['count']; $i++ ) {
			foreach ( $def['fields'] as $field ) {
				$defaults[ "{$group}_{$i}_{$field}" ] = $def['defaults'][ $i - 1 ][ $field ] ?? '';
			}
		}
	}

	$defaults = apply_filters( 'bzl_defaults', $defaults );
	return $defaults;
}
