<?php
/**
 * Strona opcji i nowe pola.
 *
 * Dotychczasowe grupy pól (sekcje stron, opcje motywu, cena usługi, obraz kategorii) są zapisane w bazie
 * i zostają bez zmian – motyw czyta te same nazwy pól. Tutaj rejestrujemy tylko NOWE pola, w kodzie,
 * dzięki czemu nie trzeba niczego importować ani ręcznie tworzyć w panelu.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'cod_acf_options_pages' );
/**
 * Strony opcji – „Opcje motywu” z tym samym adresem co dotąd (acf-options) oraz „COD 2026”.
 */
function cod_acf_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	$parent = acf_add_options_page(
		array(
			'page_title' => 'Opcje motywu',
			'menu_title' => 'Opcje motywu',
			'menu_slug'  => 'cod-theme-options',
			'redirect'   => true,
			'icon_url'   => 'dashicons-admin-customizer',
			'position'   => 59,
		)
	);
	acf_add_options_sub_page(
		array(
			'page_title'  => 'Opcje motywu',
			'menu_title'  => 'Dane firmy i logo',
			'menu_slug'   => 'acf-options',
			'parent_slug' => $parent['menu_slug'],
		)
	);
	acf_add_options_sub_page(
		array(
			'page_title'  => 'COD 2026 – ustawienia',
			'menu_title'  => 'COD 2026',
			'menu_slug'   => 'cod-2026',
			'parent_slug' => $parent['menu_slug'],
		)
	);
}

/**
 * Buduje definicję pola ACF.
 *
 * @param string $group Prefiks klucza grupy.
 * @param string $type  Typ pola.
 * @param string $name  Nazwa pola.
 * @param string $label Etykieta.
 * @param array  $extra Dodatkowe ustawienia.
 */
function cod_f( $group, $type, $name, $label, $extra = array() ) {
	return array_merge(
		array(
			'key'   => 'field_' . $group . '_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
}

/**
 * Zakładka w grupie pól.
 */
function cod_tab( $group, $name, $label ) {
	return array(
		'key'       => 'field_' . $group . '_tab_' . $name,
		'name'      => '',
		'label'     => $label,
		'type'      => 'tab',
		'placement' => 'top',
	);
}

/**
 * Pole tekstowe z tekstem domyślnym strony głównej.
 */
function cod_fh( $type, $name, $label, $extra = array() ) {
	$default = cod_home_default( $name );
	$base    = array(
		'default_value' => $default,
		'instructions'  => __( 'Puste pole = tekst domyślny motywu.', 'cod2026' ),
	);
	if ( 'textarea' === $type ) {
		$base['rows']      = 3;
		$base['new_lines'] = '';
	}
	return cod_f( 'cod26h', $type, $name, $label, array_merge( $base, $extra ) );
}

add_action( 'acf/init', 'cod_acf_fields' );
/**
 * Rejestracja nowych grup pól.
 */
function cod_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$services = array( '' => '— brak —' );
	$data     = include COD_DIR . '/inc/data/services.php';
	foreach ( $data as $key => $svc ) {
		$services[ $key ] = $svc['title'];
	}

	// --- Usługa (typ wpisu „Oferta”) ---
	$g = 'cod26o';
	acf_add_local_field_group(
		array(
			'key'                   => 'group_cod26_offer',
			'title'                 => 'Usługa – opis na stronie (COD 2026)',
			'menu_order'            => 5,
			'position'              => 'normal',
			'location'              => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'offer' ) ) ),
			'fields'                => array(
				cod_tab( $g, 'card', 'Karta usługi' ),
				cod_f( $g, 'select', 'cod_audience', 'Dla kogo (zakładka na stronie głównej)', array( 'choices' => array( '' => 'Automatycznie z kategorii', 'dzieci' => 'Dzieci i rodzice', 'dorosli' => 'Dorośli', 'firmy' => 'Firmy' ), 'default_value' => '' ) ),
				cod_f( $g, 'text', 'cod_short_name', 'Krótka nazwa', array( 'instructions' => 'Używana w pasku usług i w krótkich miejscach, np. „Diagnoza ADHD”.' ) ),
				cod_f( $g, 'text', 'cod_label', 'Etykieta nad tytułem', array( 'instructions' => 'Np. „Najczęstszy pierwszy krok”, „Relacje z rówieśnikami”.' ) ),
				cod_f( $g, 'textarea', 'cod_short', 'Krótki opis na karcie', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Jedno–dwa zdania: dla kogo i po co. Gdy puste – używana jest zajawka wpisu.' ) ),
				cod_f( $g, 'true_false', 'cod_featured', 'Wyróżnij jako pierwszy krok', array( 'ui' => 1, 'instructions' => 'Duża ciemna karta na początku zakładki. Zaznacz jedną usługę w każdej grupie.' ) ),
				cod_f( $g, 'textarea', 'cod_bullets', 'Punkty na wyróżnionej karcie', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Jeden punkt w linii.' ) ),
				cod_f( $g, 'select', 'cod_cta', 'Główny przycisk', array( 'choices' => array( 'phone' => 'Telefon', 'form' => 'Formularz' ), 'default_value' => 'phone' ) ),
				cod_tab( $g, 'details', 'Szczegóły' ),
				cod_f(
					$g,
					'repeater',
					'cod_details',
					'Szczegóły usługi (panel „Szczegóły” i strona usługi)',
					array(
						'layout'       => 'block',
						'button_label' => 'Dodaj sekcję',
						'instructions' => 'Np. Dla kogo · Kiedy warto się zgłosić · Jak to wygląda · Co dzieje się później.',
						'sub_fields'   => array(
							cod_f( $g, 'text', 'heading', 'Nagłówek' ),
							cod_f( $g, 'wysiwyg', 'text', 'Treść', array( 'tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0 ) ),
						),
					)
				),
				cod_tab( $g, 'wizard', 'Kreator' ),
				cod_f( $g, 'text', 'cod_wizard_q', 'Odpowiedź w kreatorze „Od czego zacząć?”', array( 'instructions' => 'Np. „Dziecko ma już diagnozę ADHD”. Puste = usługa nie pojawia się w kreatorze.' ) ),
				cod_f( $g, 'textarea', 'cod_wizard_a', 'Wyjaśnienie w kreatorze', array( 'rows' => 2, 'new_lines' => '' ) ),
				cod_f( $g, 'select', 'cod_key', 'Powiązanie z treściami projektu', array( 'choices' => $services, 'default_value' => '', 'instructions' => 'Ustawiane automatycznie przez narzędzie „Uzupełnij opisy usług”.' ) ),
			),
		)
	);

	// --- Strona główna ---
	$g      = 'cod26h';
	$fields = array(
		cod_tab( $g, 'hero', 'Pierwszy ekran' ),
		cod_fh( 'text', 'cod_hero_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_hero_title', 'Tytuł (H1)' ),
		cod_fh( 'textarea', 'cod_hero_lead', 'Opis' ),
		cod_fh( 'text', 'cod_hero_second', 'Drugi przycisk (prowadzi do kreatora)' ),
		cod_f( $g, 'image', 'cod_hero_image', 'Zdjęcie', array( 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Pionowe, min. 900×1100 px. Puste = zdjęcie z motywu.' ) ),
		cod_fh( 'text', 'cod_hero_caption', 'Podpis zdjęcia' ),
		cod_fh( 'textarea', 'cod_hero_chips', 'Etykiety przy zdjęciu (jedna w linii, maks. 3)' ),
		cod_tab( $g, 'finder', 'Od czego zacząć?' ),
		cod_fh( 'text', 'cod_finder_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_finder_title', 'Tytuł' ),
		cod_fh( 'textarea', 'cod_finder_lead', 'Opis' ),
		cod_tab( $g, 'offer', 'Oferta' ),
		cod_fh( 'text', 'cod_offer_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_offer_title', 'Tytuł' ),
		cod_fh( 'textarea', 'cod_offer_lead', 'Opis', array( 'instructions' => 'Usługi pobierają się z menu „Oferta”.' ) ),
		cod_tab( $g, 'about', 'O mnie' ),
		cod_fh( 'text', 'cod_about_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_about_title', 'Tytuł' ),
		cod_f( $g, 'image', 'cod_about_image', 'Zdjęcie', array( 'return_format' => 'array', 'preview_size' => 'medium' ) ),
		cod_fh( 'textarea', 'cod_about_quote', 'Motto' ),
		cod_fh( 'wysiwyg', 'cod_about_text', 'Tekst', array( 'tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0 ) ),
		cod_fh( 'textarea', 'cod_about_quals', 'Kwalifikacje (jedna w linii)', array( 'rows' => 6 ) ),
		cod_f(
			$g,
			'repeater',
			'cod_stats',
			'Liczniki',
			array(
				'layout'       => 'table',
				'max'          => 3,
				'button_label' => 'Dodaj licznik',
				'instructions' => 'Puste = 18+ lat, 6 specjalistów, 3 ścieżki.',
				'sub_fields'   => array(
					cod_f( $g, 'number', 'number', 'Liczba' ),
					cod_f( $g, 'text', 'suffix', 'Znak po liczbie', array( 'placeholder' => '+' ) ),
					cod_f( $g, 'text', 'label', 'Opis' ),
				),
			)
		),
		cod_tab( $g, 'steps', 'Pierwszy kontakt' ),
		cod_fh( 'text', 'cod_steps_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_steps_title', 'Tytuł' ),
		cod_fh( 'textarea', 'cod_steps_lead', 'Opis' ),
		cod_f(
			$g,
			'repeater',
			'cod_steps',
			'Kroki',
			array(
				'layout'       => 'block',
				'button_label' => 'Dodaj krok',
				'instructions' => 'Puste = 4 kroki z projektu.',
				'sub_fields'   => array(
					cod_f( $g, 'text', 'title', 'Tytuł' ),
					cod_f( $g, 'textarea', 'text', 'Opis', array( 'rows' => 2, 'new_lines' => '' ) ),
					cod_f( $g, 'text', 'note', 'Dopisek (opcjonalnie)' ),
				),
			)
		),
		cod_fh( 'text', 'cod_prep_title', 'Karta obok – tytuł' ),
		cod_fh( 'textarea', 'cod_prep_items', 'Karta obok – punkty (jeden w linii)', array( 'rows' => 4 ) ),
		cod_tab( $g, 'team', 'Zespół i opinie' ),
		cod_fh( 'text', 'cod_team_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_team_title', 'Tytuł' ),
		cod_fh( 'textarea', 'cod_team_lead', 'Opis', array( 'instructions' => 'Osoby edytujesz w Opcje motywu → COD 2026 → Zespół.' ) ),
		cod_f(
			$g,
			'repeater',
			'cod_reviews',
			'Opinie',
			array(
				'layout'       => 'block',
				'button_label' => 'Dodaj opinię',
				'instructions' => 'Tylko prawdziwe opinie, za zgodą autora. Puste = wszystkie opinie z sekcji „Opinie” na stronach (i opinia Sylwii Materny), pokazane w sliderze.',
				'sub_fields'   => array(
					cod_f( $g, 'textarea', 'quote', 'Treść', array( 'rows' => 4, 'new_lines' => '' ) ),
					cod_f( $g, 'text', 'name', 'Imię i nazwisko' ),
					cod_f( $g, 'text', 'role', 'Funkcja / opis' ),
					cod_f( $g, 'image', 'photo', 'Zdjęcie', array( 'return_format' => 'array', 'preview_size' => 'thumbnail' ) ),
				),
			)
		),
		cod_tab( $g, 'faq', 'Pytania' ),
		cod_fh( 'text', 'cod_faq_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_faq_title', 'Tytuł' ),
		cod_fh( 'textarea', 'cod_faq_lead', 'Opis' ),
		cod_f(
			$g,
			'repeater',
			'cod_faq',
			'Pytania i odpowiedzi',
			array(
				'layout'       => 'block',
				'button_label' => 'Dodaj pytanie',
				'instructions' => 'Puste = 10 pytań z projektu.',
				'sub_fields'   => array(
					cod_f( $g, 'text', 'q', 'Pytanie' ),
					cod_f( $g, 'wysiwyg', 'a', 'Odpowiedź', array( 'tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0 ) ),
				),
			)
		),
		cod_tab( $g, 'contact', 'Kontakt' ),
		cod_fh( 'text', 'cod_contact_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_contact_title', 'Tytuł' ),
		cod_fh( 'textarea', 'cod_contact_lead', 'Opis' ),
		cod_tab( $g, 'shop', 'Sklep i kursy' ),
		cod_fh( 'text', 'cod_shop_kicker', 'Nadtytuł' ),
		cod_fh( 'text', 'cod_shop_title', 'Tytuł' ),
		cod_fh( 'textarea', 'cod_shop_lead', 'Opis' ),
		cod_f( $g, 'relationship', 'cod_shop_products', 'Produkty', array( 'post_type' => array( 'product' ), 'filters' => array( 'search' ), 'return_format' => 'id', 'max' => 8, 'instructions' => 'Puste = wszystkie opublikowane produkty (maks. 8).' ) ),
		cod_fh( 'text', 'cod_courses_title', 'Kursy – tytuł' ),
		cod_fh( 'textarea', 'cod_courses_text', 'Kursy – opis' ),
		cod_tab( $g, 'cta', 'Pasek końcowy' ),
		cod_fh( 'text', 'cod_cta_title', 'Hasło' ),
		cod_fh( 'textarea', 'cod_cta_lead', 'Opis' ),
	);
	acf_add_local_field_group(
		array(
			'key'            => 'group_cod26_home',
			'title'          => 'Strona główna (COD 2026)',
			'menu_order'     => 0,
			'position'       => 'acf_after_title',
			'hide_on_screen' => array( 'the_content' ),
			'location'       => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
			'fields'         => $fields,
		)
	);

	// --- Ustawienia motywu ---
	$g = 'cod26s';
	acf_add_local_field_group(
		array(
			'key'      => 'group_cod26_settings',
			'title'    => 'Ustawienia COD 2026',
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cod-2026' ) ) ),
			'fields'   => array(
				cod_tab( $g, 'contact', 'Kontakt' ),
				cod_f( $g, 'message', 'cod_contact_info', 'Dane firmy', array( 'message' => 'Telefon, e-mail, adres, NIP, REGON i godziny pobierane są z zakładki „Dane firmy i logo”.' ) ),
				cod_f( $g, 'checkbox', 'cod_hours_days', 'Dni kontaktu (status „Teraz w godzinach kontaktu”)', array( 'choices' => array( '1' => 'pon.', '2' => 'wt.', '3' => 'śr.', '4' => 'czw.', '5' => 'pt.', '6' => 'sob.', '0' => 'niedz.' ), 'default_value' => array( '1', '2', '3', '4', '5' ), 'layout' => 'horizontal', 'return_format' => 'value' ) ),
				cod_f( $g, 'text', 'cod_hours_from', 'Od godziny', array( 'default_value' => '9:00', 'wrapper' => array( 'width' => 50 ) ) ),
				cod_f( $g, 'text', 'cod_hours_to', 'Do godziny', array( 'default_value' => '20:00', 'wrapper' => array( 'width' => 50 ) ) ),
				cod_f( $g, 'url', 'cod_maps_url', 'Link do mapy / dojazdu', array( 'default_value' => 'https://maps.app.goo.gl/1v2PFsN68EbBr6Nc7' ) ),
				cod_f( $g, 'url', 'cod_booking_url', 'System rezerwacji online (opcjonalnie)', array( 'instructions' => 'Gdy uzupełnisz, przy telefonie pojawi się przycisk „Zarezerwuj termin online”.' ) ),
				cod_tab( $g, 'look', 'Wygląd' ),
				cod_f( $g, 'select', 'cod_color_mode', 'Tryb ciemny', array( 'choices' => array( 'auto' => 'Automatycznie – według ustawień telefonu lub komputera odwiedzającego', 'light' => 'Wyłączony – strona zawsze jasna' ), 'default_value' => 'auto' ) ),
				cod_f( $g, 'image', 'cod_logo_inverse', 'Logo na ciemne tło (opcjonalnie)', array( 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Używane w nagłówku nad ciemnym pierwszym ekranem i w trybie ciemnym. Puste = wersja logo z jasnym napisem dołączona do motywu.' ) ),
				cod_tab( $g, 'form', 'Formularz' ),
				cod_f( $g, 'select', 'cod_form_mode', 'Formularz na stronie głównej', array( 'choices' => array( 'theme' => 'Formularz motywu (wysyła e-mail przez WordPressa / wtyczkę SMTP)', 'cf7' => 'Contact Form 7 (shortcode poniżej)' ), 'default_value' => 'theme' ) ),
				cod_f( $g, 'text', 'cod_form_shortcode', 'Shortcode Contact Form 7', array( 'placeholder' => '[contact-form-7 id="…" title="…"]' ) ),
				cod_f( $g, 'email', 'cod_form_to', 'Adres, na który przychodzą wiadomości', array( 'instructions' => 'Puste = e-mail z danych firmy.' ) ),
				cod_tab( $g, 'team', 'Zespół' ),
				cod_f(
					$g,
					'repeater',
					'cod_team',
					'Zespół',
					array(
						'layout'       => 'block',
						'button_label' => 'Dodaj osobę',
						'instructions' => 'Tylko prawdziwe osoby i ich zdjęcia. Puste = 6 osób z obecnej strony.',
						'sub_fields'   => array(
							cod_f( $g, 'image', 'photo', 'Zdjęcie', array( 'return_format' => 'array', 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => 25 ) ) ),
							cod_f( $g, 'text', 'name', 'Imię i nazwisko', array( 'wrapper' => array( 'width' => 25 ) ) ),
							cod_f( $g, 'text', 'role', 'Rola', array( 'wrapper' => array( 'width' => 25 ) ) ),
							cod_f( $g, 'textarea', 'scope', 'Krótko (1 zdanie na kafelku)', array( 'rows' => 2, 'new_lines' => '', 'wrapper' => array( 'width' => 25 ) ) ),
							cod_f( $g, 'wysiwyg', 'bio', 'Dłuższy opis (w panelu po kliknięciu)', array( 'tabs' => 'visual', 'toolbar' => 'basic', 'media_upload' => 0, 'instructions' => 'Czym się zajmuje, z kim pracuje, kwalifikacje. Puste = w panelu pokaże się krótki opis.' ) ),
						),
					)
				),
				cod_tab( $g, 'tracking', 'Pomiar i cookies' ),
				cod_f( $g, 'message', 'cod_tracking_info', 'Jak to działa', array( 'message' => 'Zalecane: GA4, Google Ads i Hotjar skonfigurowane w Google Tag Manager i uruchamiane po zgodzie na cookies. Pola „wczytuj bezpośrednio” to tryb awaryjny – omijają zgodę i dublują pomiar, jeśli te same tagi są w GTM.' ) ),
				cod_f( $g, 'text', 'cod_gtm_id', 'Identyfikator Google Tag Manager', array( 'default_value' => '' ) ),
				cod_f( $g, 'true_false', 'cod_consent_mode', 'Google Consent Mode v2 – domyślnie brak zgody', array( 'ui' => 1, 'default_value' => 1, 'instructions' => 'Włączone: przed zgodą narzędzia Google nie zapisują cookies. Wtyczka cookies musi wysyłać aktualizację zgody (Compliance by Hu-manity.co: Ustawienia → Integracje → Google Consent Mode). Wyłącz, jeśli wtyczka sama ustawia stan domyślny.' ) ),
				cod_f( $g, 'text', 'cod_ga4_id', 'Google Analytics 4 – identyfikator', array( 'default_value' => '', 'wrapper' => array( 'width' => 50 ) ) ),
				cod_f( $g, 'true_false', 'cod_ga4_direct', 'GA4 – wczytuj bezpośrednio (tryb awaryjny)', array( 'ui' => 1, 'default_value' => 0, 'wrapper' => array( 'width' => 50 ) ) ),
				cod_f( $g, 'text', 'cod_ads_id', 'Google Ads – identyfikator', array( 'default_value' => '', 'wrapper' => array( 'width' => 50 ) ) ),
				cod_f( $g, 'true_false', 'cod_ads_direct', 'Google Ads – wczytuj bezpośrednio (tryb awaryjny)', array( 'ui' => 1, 'default_value' => 0, 'wrapper' => array( 'width' => 50 ) ) ),
				cod_f( $g, 'text', 'cod_hotjar_id', 'Hotjar – identyfikator', array( 'default_value' => '', 'wrapper' => array( 'width' => 50 ) ) ),
				cod_f( $g, 'true_false', 'cod_hotjar_direct', 'Hotjar – wczytuj bezpośrednio (tryb awaryjny)', array( 'ui' => 1, 'default_value' => 0, 'wrapper' => array( 'width' => 50 ) ) ),
				cod_tab( $g, 'shop', 'Sklep i kursy' ),
				cod_f( $g, 'true_false', 'cod_redirect_enabled', 'Po opłaceniu zamówienia z kursem przekieruj do szkoleń', array( 'ui' => 1, 'default_value' => 1, 'instructions' => 'Dotyczy tylko zamówień z produktem powiązanym z kursem LearnDash. Zakup e-booka kończy się zwykłym potwierdzeniem.' ) ),
				cod_f( $g, 'url', 'cod_redirect_url', 'Adres przekierowania', array( 'instructions' => 'Puste = strona „Moje szkolenia”, a gdy jej brak – /szkolenia/.' ) ),
			),
		)
	);
}
