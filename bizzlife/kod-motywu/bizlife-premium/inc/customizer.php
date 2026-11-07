<?php
/**
 * Ustawienia motywu w Customizerze (Wygląd → Dostosuj → Bizzlife).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitizacja checkboxa.
 *
 * @param mixed $v Wartość.
 * @return bool
 */
function bzl_sanitize_checkbox( $v ) {
	return ( true === $v || '1' === $v || 1 === $v || 'true' === $v );
}

/**
 * Sanitizacja tonu sekcji.
 *
 * @param string $v Wartość.
 * @return string
 */
function bzl_sanitize_tone( $v ) {
	return in_array( $v, array( 'dark', 'darker', 'light' ), true ) ? $v : 'dark';
}

/**
 * Sanitizacja ikony.
 *
 * @param string $v Wartość.
 * @return string
 */
function bzl_sanitize_icon( $v ) {
	return array_key_exists( $v, bzl_icon_choices() ) ? $v : '';
}

/**
 * Sanitizacja linku lub tokenu ({kontakt}, #kotwica, URL).
 *
 * @param string $v Wartość.
 * @return string
 */
function bzl_sanitize_link( $v ) {
	$v = trim( (string) $v );
	if ( '' === $v ) {
		return '';
	}
	if ( preg_match( '/^\{(kontakt|uslugi|o-nas|faq|aktualnosci|tel|email|usluga:[a-z0-9\-]+)\}$/', $v ) ) {
		return $v;
	}
	if ( preg_match( '/^#[A-Za-z0-9\-_]+$/', $v ) ) {
		return $v;
	}
	return esc_url_raw( $v, array( 'http', 'https', 'tel', 'mailto' ) );
}

/**
 * Sanitizacja kolejności sekcji.
 *
 * @param string $v Wartość.
 * @return string
 */
function bzl_sanitize_order( $v ) {
	$allowed = array_keys( bzl_home_sections() );
	$parts   = array_filter( array_map( 'trim', explode( ',', strtolower( (string) $v ) ) ) );
	$parts   = array_values( array_unique( array_intersect( $parts, $allowed ) ) );
	return implode( ',', $parts );
}

/**
 * Sanitizacja liczby całkowitej.
 *
 * @param mixed $v Wartość.
 * @return int
 */
function bzl_sanitize_int( $v ) {
	return absint( $v );
}

/**
 * Sanitizacja tekstu z dozwolonymi podstawowymi tagami (np. kod mapy niepotrzebny – tylko URL).
 *
 * @param string $v Wartość.
 * @return string
 */
function bzl_sanitize_shortcode( $v ) {
	$v = trim( wp_strip_all_tags( (string) $v ) );
	return preg_match( '/^\[[^\[\]]+\]$/', $v ) ? $v : '';
}

/**
 * Sekcje strony głównej (klucz => etykieta).
 *
 * @return array
 */
function bzl_home_sections() {
	return array(
		'hero'     => __( 'Hero (otwarcie strony)', 'bizlife' ),
		'intro'    => __( 'Wprowadzenie', 'bizlife' ),
		'services' => __( 'Usługi', 'bizlife' ),
		'why'      => __( 'Dlaczego Bizzlife', 'bizlife' ),
		'about'    => __( 'O nas', 'bizlife' ),
		'process'  => __( 'Jak wygląda współpraca', 'bizlife' ),
		'audience' => __( 'Dla kogo pracujemy', 'bizlife' ),
		'content'  => __( 'Treść strony z edytora bloków', 'bizlife' ),
		'news'     => __( 'Aktualności', 'bizlife' ),
		'faq'      => __( 'FAQ', 'bizlife' ),
		'cta'      => __( 'Wezwanie do działania (złoty blok)', 'bizlife' ),
		'contact'  => __( 'Kontakt', 'bizlife' ),
	);
}

/**
 * Rejestracja ustawień.
 *
 * @param WP_Customize_Manager $wp Manager.
 */
function bzl_customize_register( $wp ) {
	$d = bzl_defaults();

	$add = static function ( $section, $key, $label, $type = 'text', $extra = array() ) use ( $wp, $d ) {
		$sanitize = array(
			'text'     => 'sanitize_text_field',
			'textarea' => 'sanitize_textarea_field',
			'url'      => 'esc_url_raw',
			'link'     => 'bzl_sanitize_link',
			'email'    => 'sanitize_email',
			'checkbox' => 'bzl_sanitize_checkbox',
			'number'   => 'bzl_sanitize_int',
			'tone'     => 'bzl_sanitize_tone',
			'icon'     => 'bzl_sanitize_icon',
			'media'    => 'absint',
			'color'    => 'sanitize_hex_color',
		);
		$wp->add_setting(
			$key,
			array(
				'default'           => $d[ $key ] ?? '',
				'sanitize_callback' => $extra['sanitize'] ?? $sanitize[ $type ],
				'transport'         => 'refresh',
			)
		);
		$args = array(
			'label'       => $label,
			'section'     => $section,
			'description' => $extra['description'] ?? '',
		);
		if ( 'media' === $type ) {
			$args['mime_type'] = 'image';
			$wp->add_control( new WP_Customize_Media_Control( $wp, $key, $args ) );
			return;
		}
		if ( 'color' === $type ) {
			$wp->add_control( new WP_Customize_Color_Control( $wp, $key, $args ) );
			return;
		}
		if ( 'tone' === $type ) {
			$args['type']    = 'select';
			$args['choices'] = array(
				'dark'   => __( 'Grafitowe', 'bizlife' ),
				'darker' => __( 'Czarne', 'bizlife' ),
				'light'  => __( 'Jasne (ciepła biel)', 'bizlife' ),
			);
		} elseif ( 'icon' === $type ) {
			$args['type']    = 'select';
			$args['choices'] = bzl_icon_choices();
		} elseif ( 'link' === $type ) {
			$args['type'] = 'text';
			if ( empty( $args['description'] ) ) {
				$args['description'] = __( 'Adres URL, #kotwica lub token: {kontakt}, {uslugi}, {o-nas}, {faq}, {aktualnosci}, {tel}, {email}, {usluga:slug}.', 'bizlife' );
			}
		} elseif ( 'number' === $type ) {
			$args['type']        = 'number';
			$args['input_attrs'] = $extra['input_attrs'] ?? array( 'min' => 0 );
		} elseif ( 'select' === $type ) {
			$args['type']    = 'select';
			$args['choices'] = $extra['choices'];
		} else {
			$args['type'] = $type;
		}
		$wp->add_control( $key, $args );
	};

	$add_slots = static function ( $section, $group, $labels ) use ( $add ) {
		$def = bzl_slot_groups()[ $group ];
		for ( $i = 1; $i <= $def['count']; $i++ ) {
			foreach ( $def['fields'] as $field ) {
				$type = array(
					'icon'  => 'icon',
					'title' => 'text',
					'text'  => 'title' === $def['fields'][0] || in_array( 'title', $def['fields'], true ) ? 'textarea' : 'text',
					'url'   => 'link',
				)[ $field ];
				/* translators: 1: etykieta pola, 2: numer pozycji. */
				$add( $section, "{$group}_{$i}_{$field}", sprintf( __( '%1$s %2$d', 'bizlife' ), $labels[ $field ], $i ), $type );
			}
		}
	};

	$field_labels = array(
		'icon'  => __( 'Ikona', 'bizlife' ),
		'title' => __( 'Tytuł', 'bizlife' ),
		'text'  => __( 'Opis', 'bizlife' ),
		'url'   => __( 'Link', 'bizlife' ),
	);

	$wp->add_panel(
		'bzl_panel',
		array(
			'title'       => __( 'Bizzlife – ustawienia motywu', 'bizlife' ),
			'priority'    => 25,
			'description' => __( 'Teksty, zdjęcia, dane kontaktowe i sekcje strony głównej. Puste pole tytułu ukrywa daną kartę.', 'bizlife' ),
		)
	);

	$section = static function ( $id, $title, $desc = '' ) use ( $wp ) {
		$wp->add_section(
			$id,
			array(
				'title'       => $title,
				'panel'       => 'bzl_panel',
				'description' => $desc,
			)
		);
	};

	// Marka i nagłówek.
	$section( 'bzl_brand', __( 'Marka i nagłówek', 'bizlife' ) );
	$logo = $wp->get_control( 'custom_logo' );
	if ( $logo ) {
		$logo->section     = 'bzl_brand';
		$logo->priority    = 1;
		$logo->description = __( 'Wgraj oryginalne logo w jasnej wersji (na ciemne tło). Pliki SVG wymagają wtyczki np. Safe SVG.', 'bizlife' );
	}
	$add( 'bzl_brand', 'logo_url', __( 'Logo – adres URL (zamiast pliku powyżej)', 'bizlife' ), 'url', array( 'description' => __( 'Opcjonalnie: adres istniejącego pliku logo, np. z biblioteki mediów.', 'bizlife' ) ) );
	$add(
		'bzl_brand',
		'logo_height',
		__( 'Wysokość logo (px)', 'bizlife' ),
		'number',
		array(
			'input_attrs' => array(
				'min' => 24,
				'max' => 120,
			),
		)
	);
	$add( 'bzl_brand', 'brand_name', __( 'Nazwa marki', 'bizlife' ) );
	$add( 'bzl_brand', 'brand_descriptor', __( 'Dopisek przy nazwie (gdy brak logo)', 'bizlife' ) );
	$add( 'bzl_brand', 'header_cta_label', __( 'Przycisk w nagłówku – tekst', 'bizlife' ) );
	$add( 'bzl_brand', 'header_cta_url', __( 'Przycisk w nagłówku – link', 'bizlife' ), 'link' );
	$add( 'bzl_brand', 'header_show_phone', __( 'Pokaż numer telefonu w nagłówku', 'bizlife' ), 'checkbox' );

	// Dane firmy.
	$section( 'bzl_company', __( 'Dane firmy i kontakt', 'bizlife' ), __( 'Dane używane w nagłówku, stopce, sekcji kontaktu i danych strukturalnych.', 'bizlife' ) );
	$add( 'bzl_company', 'company_name', __( 'Pełna nazwa firmy', 'bizlife' ) );
	$add( 'bzl_company', 'phone', __( 'Telefon', 'bizlife' ) );
	$add( 'bzl_company', 'email', __( 'E-mail', 'bizlife' ), 'email' );
	$add( 'bzl_company', 'street', __( 'Ulica i numer', 'bizlife' ) );
	$add( 'bzl_company', 'postal_code', __( 'Kod pocztowy', 'bizlife' ) );
	$add( 'bzl_company', 'city', __( 'Miejscowość', 'bizlife' ) );
	$add( 'bzl_company', 'hours', __( 'Godziny pracy (widoczne)', 'bizlife' ), 'textarea', array( 'description' => __( 'Każda linia osobno.', 'bizlife' ) ) );
	$add( 'bzl_company', 'opening_hours_schema', __( 'Godziny dla Google (format schema)', 'bizlife' ), 'text', array( 'description' => __( 'Np. Mo-Fr 08:00-16:00. Puste = nie podawaj.', 'bizlife' ) ) );
	$add( 'bzl_company', 'krs', 'KRS' );
	$add( 'bzl_company', 'nip', 'NIP' );
	$add( 'bzl_company', 'regon', 'REGON' );
	$add( 'bzl_company', 'geo_lat', __( 'Szerokość geograficzna', 'bizlife' ) );
	$add( 'bzl_company', 'geo_lng', __( 'Długość geograficzna', 'bizlife' ) );
	$add( 'bzl_company', 'map_embed_url', __( 'Mapa – adres osadzenia (src z Google Maps „Udostępnij → Umieść mapę”)', 'bizlife' ), 'url' );
	$add( 'bzl_company', 'map_place_url', __( 'Mapa – link do wizytówki', 'bizlife' ), 'url' );

	// Social.
	$section( 'bzl_social', __( 'Media społecznościowe', 'bizlife' ), __( 'Puste pole ukrywa ikonę.', 'bizlife' ) );
	$add( 'bzl_social', 'social_facebook', 'Facebook', 'url' );
	$add( 'bzl_social', 'social_instagram', 'Instagram', 'url' );
	$add( 'bzl_social', 'social_whatsapp', 'WhatsApp', 'url' );
	$add( 'bzl_social', 'social_google', __( 'Profil Google', 'bizlife' ), 'url' );
	$add( 'bzl_social', 'social_linkedin', 'LinkedIn', 'url' );

	// Układ strony głównej.
	$section( 'bzl_home_layout', __( 'Strona główna – kolejność sekcji', 'bizlife' ) );
	$add(
		'bzl_home_layout',
		'home_order',
		__( 'Kolejność sekcji', 'bizlife' ),
		'text',
		array(
			'sanitize'    => 'bzl_sanitize_order',
			'description' => sprintf(
				/* translators: %s: lista kluczy. */
				__( 'Klucze oddzielone przecinkami. Usunięcie klucza ukrywa sekcję. Dostępne: %s. „content” wyświetla bloki dodane w edytorze strony głównej.', 'bizlife' ),
				implode( ', ', array_keys( bzl_home_sections() ) )
			),
		)
	);

	// Hero.
	$section( 'bzl_home_hero', __( 'Strona główna – Hero', 'bizlife' ), __( 'Fragment tytułu w *gwiazdkach* zostanie wyróżniony kursywą w kolorze akcentu.', 'bizlife' ) );
	$add( 'bzl_home_hero', 'hero_show', __( 'Pokaż sekcję', 'bizlife' ), 'checkbox' );
	$add( 'bzl_home_hero', 'hero_eyebrow', __( 'Nadtytuł', 'bizlife' ) );
	$add( 'bzl_home_hero', 'hero_title', __( 'Nagłówek H1', 'bizlife' ), 'textarea' );
	$add( 'bzl_home_hero', 'hero_lead', __( 'Opis', 'bizlife' ), 'textarea' );
	$add( 'bzl_home_hero', 'hero_cta1_label', __( 'Przycisk główny – tekst', 'bizlife' ) );
	$add( 'bzl_home_hero', 'hero_cta1_url', __( 'Przycisk główny – link', 'bizlife' ), 'link' );
	$add( 'bzl_home_hero', 'hero_cta2_label', __( 'Przycisk drugi – tekst', 'bizlife' ) );
	$add( 'bzl_home_hero', 'hero_cta2_url', __( 'Przycisk drugi – link', 'bizlife' ), 'link' );
	$add( 'bzl_home_hero', 'hero_image', __( 'Zdjęcie (pionowe, min. 1200×1400 px)', 'bizlife' ), 'media' );
	$add( 'bzl_home_hero', 'hero_image_caption', __( 'Podpis na zdjęciu (opcjonalnie)', 'bizlife' ) );
	$add_slots( 'bzl_home_hero', 'hero_point', array( 'text' => __( 'Wyróżnik', 'bizlife' ) ) );

	// Sekcje standardowe.
	$standard = array(
		'intro'    => __( 'Strona główna – Wprowadzenie', 'bizlife' ),
		'services' => __( 'Strona główna – Usługi', 'bizlife' ),
		'why'      => __( 'Strona główna – Dlaczego Bizzlife', 'bizlife' ),
		'about'    => __( 'Strona główna – O nas', 'bizlife' ),
		'process'  => __( 'Strona główna – Współpraca', 'bizlife' ),
		'audience' => __( 'Strona główna – Dla kogo', 'bizlife' ),
		'news'     => __( 'Strona główna – Aktualności', 'bizlife' ),
		'faq'      => __( 'Strona główna – FAQ', 'bizlife' ),
	);
	foreach ( $standard as $key => $title ) {
		$sid = 'bzl_home_' . $key;
		$section( $sid, $title );
		$add( $sid, $key . '_show', __( 'Pokaż sekcję', 'bizlife' ), 'checkbox' );
		$add( $sid, $key . '_tone', __( 'Tło sekcji', 'bizlife' ), 'tone' );
		$add( $sid, $key . '_eyebrow', __( 'Nadtytuł', 'bizlife' ) );
		$add( $sid, $key . '_title', __( 'Nagłówek', 'bizlife' ), 'textarea' );
		if ( 'about' !== $key ) {
			$add( $sid, $key . '_lead', __( 'Opis pod nagłówkiem', 'bizlife' ), 'textarea' );
		}
	}

	$add_slots( 'bzl_home_intro', 'intro', $field_labels );

	$wp->get_section( 'bzl_home_services' )->description = __( 'Karty usług pobierane są automatycznie z menu „Usługi” w kokpicie.', 'bizlife' );
	$add( 'bzl_home_services', 'services_image', __( 'Zdjęcie w nagłówku podstrony „Usługi”', 'bizlife' ), 'media' );
	$add( 'bzl_home_services', 'services_cta_show', __( 'Pokaż kartę z zachętą do kontaktu', 'bizlife' ), 'checkbox' );
	$add( 'bzl_home_services', 'services_cta_title', __( 'Karta – tytuł', 'bizlife' ) );
	$add( 'bzl_home_services', 'services_cta_text', __( 'Karta – opis', 'bizlife' ), 'textarea' );
	$add( 'bzl_home_services', 'services_cta_label', __( 'Karta – tekst linku', 'bizlife' ) );
	$add( 'bzl_home_services', 'services_cta_url', __( 'Karta – link', 'bizlife' ), 'link' );

	$add_slots( 'bzl_home_why', 'why', $field_labels );

	$add( 'bzl_home_about', 'about_text', __( 'Treść (pusta linia = nowy akapit)', 'bizlife' ), 'textarea' );
	$add( 'bzl_home_about', 'about_person_name', __( 'Imię i nazwisko', 'bizlife' ) );
	$add( 'bzl_home_about', 'about_person_role', __( 'Funkcja', 'bizlife' ) );
	$add( 'bzl_home_about', 'about_image', __( 'Zdjęcie (portret 4:5)', 'bizlife' ), 'media' );
	$add( 'bzl_home_about', 'about_cta_label', __( 'Przycisk – tekst', 'bizlife' ) );
	$add( 'bzl_home_about', 'about_cta_url', __( 'Przycisk – link', 'bizlife' ), 'link' );

	$add_slots( 'bzl_home_process', 'process', $field_labels );
	$add_slots( 'bzl_home_audience', 'audience', $field_labels );

	$add(
		'bzl_home_news',
		'news_count',
		__( 'Liczba wpisów', 'bizlife' ),
		'number',
		array(
			'input_attrs' => array(
				'min' => 1,
				'max' => 6,
			),
		)
	);
	$add( 'bzl_home_news', 'news_cta_label', __( 'Przycisk do archiwum – tekst', 'bizlife' ) );

	$wp->get_section( 'bzl_home_faq' )->description = __( 'Pytania dodajesz w menu „FAQ” w kokpicie. Zaznacz „Pokaż na stronie głównej” przy wybranych pytaniach.', 'bizlife' );
	$add(
		'bzl_home_faq',
		'faq_count',
		__( 'Maksymalna liczba pytań', 'bizlife' ),
		'number',
		array(
			'input_attrs' => array(
				'min' => 1,
				'max' => 20,
			),
		)
	);
	$add( 'bzl_home_faq', 'faq_cta_label', __( 'Przycisk do strony FAQ – tekst', 'bizlife' ) );

	// CTA.
	$section( 'bzl_cta', __( 'Złoty blok CTA', 'bizlife' ), __( 'Wyświetlany na stronie głównej oraz na podstronach usług i wpisów.', 'bizlife' ) );
	$add( 'bzl_cta', 'cta_show', __( 'Pokaż na stronie głównej', 'bizlife' ), 'checkbox' );
	$add( 'bzl_cta', 'cta_title', __( 'Nagłówek', 'bizlife' ), 'textarea' );
	$add( 'bzl_cta', 'cta_text', __( 'Opis', 'bizlife' ), 'textarea' );
	$add( 'bzl_cta', 'cta_btn1_label', __( 'Przycisk 1 – tekst', 'bizlife' ) );
	$add( 'bzl_cta', 'cta_btn1_url', __( 'Przycisk 1 – link', 'bizlife' ), 'link' );
	$add( 'bzl_cta', 'cta_btn2_label', __( 'Przycisk 2 – tekst', 'bizlife' ) );
	$add( 'bzl_cta', 'cta_btn2_url', __( 'Przycisk 2 – link', 'bizlife' ), 'link' );

	// Kontakt i formularz.
	$section( 'bzl_contact', __( 'Kontakt i formularz', 'bizlife' ) );
	$add( 'bzl_contact', 'contact_show', __( 'Pokaż sekcję na stronie głównej', 'bizlife' ), 'checkbox' );
	$add( 'bzl_contact', 'contact_eyebrow', __( 'Nadtytuł', 'bizlife' ) );
	$add( 'bzl_contact', 'contact_title', __( 'Nagłówek', 'bizlife' ) );
	$add( 'bzl_contact', 'contact_lead', __( 'Opis', 'bizlife' ), 'textarea' );
	$add( 'bzl_contact', 'contact_steps_title', __( 'Nagłówek kroków', 'bizlife' ) );
	$add_slots( 'bzl_contact', 'contact_step', $field_labels );
	$add( 'bzl_contact', 'form_title', __( 'Formularz – nagłówek', 'bizlife' ) );
	$add( 'bzl_contact', 'form_intro', __( 'Formularz – opis', 'bizlife' ), 'textarea' );
	$add( 'bzl_contact', 'form_recipient', __( 'Odbiorca wiadomości (e-mail)', 'bizlife' ), 'email', array( 'description' => __( 'Puste = adres z „Dane firmy”.', 'bizlife' ) ) );
	$add( 'bzl_contact', 'form_submit_label', __( 'Tekst przycisku', 'bizlife' ) );
	$add( 'bzl_contact', 'form_success', __( 'Komunikat po wysłaniu', 'bizlife' ), 'textarea' );
	$add( 'bzl_contact', 'form_consent', __( 'Treść zgody (checkbox)', 'bizlife' ), 'textarea', array( 'description' => __( 'Link do polityki prywatności (Ustawienia → Prywatność) dodawany jest automatycznie.', 'bizlife' ) ) );
	$add( 'bzl_contact', 'form_gdpr_note', __( 'Klauzula informacyjna RODO', 'bizlife' ), 'textarea' );
	$add( 'bzl_contact', 'form_store_leads', __( 'Zapisuj kopię zgłoszeń w kokpicie (Zgłoszenia)', 'bizlife' ), 'checkbox' );
	$add( 'bzl_contact', 'form_retention_days', __( 'Usuwaj zgłoszenia starsze niż (dni, 0 = nie usuwaj)', 'bizlife' ), 'number' );
	$add(
		'bzl_contact',
		'form_shortcode',
		__( 'Zamiast wbudowanego formularza użyj shortcode', 'bizlife' ),
		'text',
		array(
			'sanitize'    => 'bzl_sanitize_shortcode',
			'description' => __( 'Np. [contact-form-7 id="123"]. Puste = formularz motywu.', 'bizlife' ),
		)
	);

	// Stopka.
	$section( 'bzl_footer', __( 'Stopka', 'bizlife' ) );
	$add( 'bzl_footer', 'footer_about', __( 'Krótki opis firmy', 'bizlife' ), 'textarea' );
	$add( 'bzl_footer', 'cookie_info_url', __( 'Link „Informacja o cookies”', 'bizlife' ), 'url', array( 'description' => __( 'Puste = link prowadzi do polityki prywatności.', 'bizlife' ) ) );
	$add( 'bzl_footer', 'footer_credits', __( 'Dopisek „Projekt i realizacja” (opcjonalnie)', 'bizlife' ) );

	// Wygląd.
	$section( 'bzl_visual', __( 'Wygląd i technika', 'bizlife' ) );
	$add( 'bzl_visual', 'accent_color', __( 'Kolor akcentu (złoto)', 'bizlife' ), 'color' );
	$add( 'bzl_visual', 'serif_headings', __( 'Szeryfowe duże nagłówki', 'bizlife' ), 'checkbox' );
	$add( 'bzl_visual', 'animations', __( 'Subtelne animacje przy przewijaniu', 'bizlife' ), 'checkbox' );
	$add( 'bzl_visual', 'ghost_words', __( 'Duże tło-napisy w sekcjach', 'bizlife' ), 'checkbox' );
	$add( 'bzl_visual', 'schema_enable', __( 'Dane strukturalne firmy (AccountingService)', 'bizlife' ), 'checkbox', array( 'description' => __( 'Wyłącz, jeśli wtyczka SEO już generuje dane LocalBusiness.', 'bizlife' ) ) );
	$add( 'bzl_visual', 'webp_subsizes', __( 'Generuj miniatury JPEG/PNG jako WebP (nowe zdjęcia)', 'bizlife' ), 'checkbox' );
}
add_action( 'customize_register', 'bzl_customize_register' );
