<?php
/**
 * Automatyczna konfiguracja startowa (jednorazowo, po aktywacji motywu).
 *
 * Dzięki niej każda podstrona działa od razu – bez ręcznego uruchamiania kreatora:
 * usługi, FAQ, strony (O nas, Kontakt, FAQ, Aktualności), menu, zdjęcia i przykładowe artykuły.
 * Niczego nie nadpisuje: tworzy tylko to, czego brakuje.
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Zdjęcia dołączone do motywu (assets/img/starter).
 *
 * @return array klucz => [plik, tekst alternatywny, tytuł]
 */
function bzl_starter_images() {
	return array(
		'hero'       => array( 'ewelina-oleksiewicz-bizzlife.jpg', 'Ewelina Oleksiewicz, założycielka biura rachunkowego Bizzlife w Radomiu', 'Ewelina Oleksiewicz – Bizzlife' ),
		'portrait'   => array( 'ewelina-oleksiewicz-portret.jpg', 'Ewelina Oleksiewicz – Bizzlife, biuro rachunkowe Radom', 'Ewelina Oleksiewicz – portret' ),
		'office'     => array( 'biuro-rachunkowe-radom-bizzlife.jpg', 'Konsultacja telefoniczna w biurze rachunkowym Bizzlife w Radomiu', 'Biuro rachunkowe Bizzlife' ),
		'koszty'     => array( 'artykul-koszty.jpg', '', 'Koszty uzyskania przychodu – grafika' ),
		'transport'  => array( 'artykul-transport.jpg', '', 'Czas pracy kierowców – grafika' ),
		'zakladanie' => array( 'artykul-zakladanie.jpg', '', 'Zakładanie firmy – grafika' ),
	);
}

/**
 * Importuje zdjęcie z motywu do biblioteki mediów (raz) i zwraca ID załącznika.
 *
 * @param string $key Klucz z bzl_starter_images().
 * @return int
 */
function bzl_starter_image( $key ) {
	$images = bzl_starter_images();
	if ( empty( $images[ $key ] ) ) {
		return 0;
	}
	list( $file, $alt, $title ) = $images[ $key ];

	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_bzl_starter_image', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = BZL_DIR . '/assets/img/starter/' . $file;
	if ( ! file_exists( $source ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $file );
	if ( ! $tmp || ! copy( $source, $tmp ) ) {
		return 0;
	}
	$id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $tmp,
		),
		0,
		$title
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	update_post_meta( $id, '_bzl_starter_image', $file );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return (int) $id;
}

/**
 * Bloki pomocnicze.
 *
 * @param string $type  Typ bloku: p, lead, h2, h3, list, panel.
 * @param mixed  $value Treść.
 * @return string
 */
function bzl_block( $type, $value ) {
	switch ( $type ) {
		case 'lead':
			return '<!-- wp:paragraph {"className":"is-style-lead"} --><p class="is-style-lead">' . esc_html( $value ) . '</p><!-- /wp:paragraph -->';
		case 'h2':
			return '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html( $value ) . '</h2><!-- /wp:heading -->';
		case 'h3':
			return '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html( $value ) . '</h3><!-- /wp:heading -->';
		case 'list':
			$out = '<!-- wp:list {"className":"is-style-checks"} --><ul class="wp-block-list is-style-checks">';
			foreach ( (array) $value as $item ) {
				$out .= '<!-- wp:list-item --><li>' . esc_html( $item ) . '</li><!-- /wp:list-item -->';
			}
			return $out . '</ul><!-- /wp:list -->';
		case 'panel':
			return '<!-- wp:group {"className":"is-style-panel","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-panel">'
				. '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html( $value[0] ) . '</h3><!-- /wp:heading -->'
				. '<!-- wp:paragraph --><p>' . esc_html( $value[1] ) . '</p><!-- /wp:paragraph --></div><!-- /wp:group -->';
		case 'image':
			$url = wp_get_attachment_image_url( (int) $value, 'large' );
			if ( ! $url ) {
				return '';
			}
			$alt = (string) get_post_meta( (int) $value, '_wp_attachment_image_alt', true );
			return '<!-- wp:image {"id":' . (int) $value . ',"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="wp-image-' . (int) $value . '"/></figure><!-- /wp:image -->';
		default:
			return '<!-- wp:paragraph --><p>' . esc_html( $value ) . '</p><!-- /wp:paragraph -->';
	}
}

/**
 * Przykładowe artykuły (tworzone tylko, gdy w serwisie nie ma żadnego wpisu).
 *
 * @return array
 */
function bzl_starter_posts() {
	$b = 'bzl_block';
	return array(
		array(
			'slug'     => 'koszty-uzyskania-przychodu-w-firmie',
			'title'    => 'Koszty uzyskania przychodu w firmie – co przedsiębiorca może bezpiecznie wrzucić w koszty?',
			'date'     => '2026-05-18 09:00:00',
			'category' => 'Podatki',
			'image'    => 'koszty',
			'excerpt'  => 'Jakie wydatki można zaliczyć do kosztów firmy, jak je dokumentować i na co uważać, żeby nie narazić się na zakwestionowanie kosztu przez urząd skarbowy.',
			'content'  => $b( 'lead', 'Prawidłowo rozliczone koszty obniżają podstawę opodatkowania. Żeby jednak wydatek mógł bezpiecznie trafić do kosztów, musi spełnić kilka warunków. Wyjaśniamy je w prostych słowach.' )
				. $b( 'h2', 'Kiedy wydatek jest kosztem uzyskania przychodu?' )
				. $b( 'p', 'Kosztem jest wydatek poniesiony w celu osiągnięcia przychodu albo zachowania lub zabezpieczenia jego źródła. W praktyce oznacza to, że musisz umieć wykazać związek wydatku z prowadzoną działalnością – nawet jeśli konkretny zakup nie przyniósł od razu przychodu.' )
				. $b( 'list', array( 'wydatek został faktycznie poniesiony przez firmę,', 'ma związek z działalnością i jej przychodami,', 'jest prawidłowo udokumentowany (np. fakturą),', 'nie znajduje się w katalogu wydatków, których przepisy nie pozwalają zaliczyć do kosztów.' ) )
				. $b( 'h2', 'Typowe koszty w małej firmie' )
				. $b( 'list', array( 'zakup towarów i materiałów, usługi podwykonawców,', 'wynajem biura lub lokalu, media, internet i telefon,', 'oprogramowanie, sprzęt komputerowy, wyposażenie,', 'szkolenia i literatura fachowa związane z branżą,', 'usługi księgowe, prawne i marketingowe,', 'wynagrodzenia i składki pracowników.' ) )
				. $b( 'h2', 'Na co szczególnie uważać?' )
				. $b( 'p', 'Najwięcej wątpliwości budzą wydatki, które mogą służyć jednocześnie celom firmowym i prywatnym – np. samochód, telefon czy remont w mieszkaniu, w którym prowadzisz działalność. Przepisy przewidują dla nich szczególne zasady i limity, a urząd może zakwestionować koszt, jeśli związek z firmą nie jest oczywisty.' )
				. $b( 'panel', array( 'Warto wiedzieć', 'Przy wydatkach „mieszanych” warto zachować dodatkowe dowody związku z firmą: opis faktury, korespondencję z klientem, ewidencję przebiegu pojazdu. Ułatwia to obronę kosztu podczas kontroli.' ) )
				. $b( 'h2', 'Dokumentowanie kosztów' )
				. $b( 'p', 'Podstawowym dowodem jest faktura. Od czasu wprowadzenia Krajowego Systemu e-Faktur coraz więcej faktur trafia do firmy elektronicznie – dlatego warto ustalić z biurem rachunkowym prosty sposób ich przekazywania i opisywania.' )
				. $b( 'p', 'Masz wątpliwość, czy konkretny wydatek możesz zaliczyć do kosztów? Zapytaj przed zakupem – razem ocenimy ryzyko i dobierzemy właściwy sposób rozliczenia.' ),
		),
		array(
			'slug'     => 'rozliczanie-czasu-pracy-kierowcow-i-pakiet-mobilnosci',
			'title'    => 'Rozliczanie czasu pracy kierowców i Pakiet Mobilności – największe wyzwania w księgowości transportowej',
			'date'     => '2026-04-22 09:00:00',
			'category' => 'Transport',
			'image'    => 'transport',
			'excerpt'  => 'Pakiet Mobilności zmienił zasady pracy kierowców w transporcie międzynarodowym. Podpowiadamy, na co zwrócić uwagę w kadrach, płacach i dokumentacji firmy transportowej.',
			'content'  => $b( 'lead', 'Firmy transportowe łączą klasyczną księgowość z wyjątkowo rozbudowanymi obowiązkami kadrowymi. Pakiet Mobilności – unijne przepisy dotyczące przewozów drogowych – dodatkowo podniósł poprzeczkę.' )
				. $b( 'h2', 'Co zmienił Pakiet Mobilności?' )
				. $b( 'p', 'Przepisy wprowadziły m.in. nowe zasady organizacji odpoczynków kierowców, obowiązek regularnego powrotu kierowcy i pojazdu do kraju siedziby firmy, zasady delegowania kierowców w przewozach międzynarodowych oraz kolejne generacje tachografów.' )
				. $b( 'list', array( 'planowanie tras z uwzględnieniem obowiązkowych powrotów,', 'ewidencja czasu pracy oparta na danych z tachografu,', 'wynagrodzenie kierowców delegowanych zgodne z przepisami kraju, w którym wykonują przewóz,', 'zgłoszenia delegowania w unijnym systemie IMI,', 'przechowywanie dokumentacji na potrzeby kontroli.' ) )
				. $b( 'h2', 'Czas pracy kierowcy a wynagrodzenie' )
				. $b( 'p', 'Czas pracy kierowcy to nie tylko jazda. Obejmuje także m.in. załadunek, rozładunek, obsługę pojazdu czy czas gotowości. Prawidłowe rozliczenie wymaga połączenia danych z tachografu z ewidencją kadrową – błędy w tym obszarze najczęściej wychodzą podczas kontroli.' )
				. $b( 'panel', array( 'Dobra praktyka', 'Regularnie pobieraj dane z tachografów i kart kierowców i przekazuj je razem z dokumentami do rozliczenia płac. Dzięki temu nieprawidłowości wychodzą od razu, a nie po kilku miesiącach.' ) )
				. $b( 'h2', 'Jak możemy pomóc?' )
				. $b( 'p', 'Prowadzimy księgowość oraz kadry i płace firm transportowych, uwzględniając specyfikę branży. Jeśli dopiero porządkujesz dokumentację po zmianach przepisów, zacznij od bezpłatnej konsultacji.' ),
		),
		array(
			'slug'     => 'zakladanie-firmy-bez-tajemnic',
			'title'    => 'Zakładanie firmy bez tajemnic. Jak krok po kroku uruchomić własny biznes?',
			'date'     => '2026-01-04 09:00:00',
			'category' => 'Zakładanie firmy',
			'image'    => 'zakladanie',
			'excerpt'  => 'Od wyboru formy działalności, przez rejestrację w CEIDG, po pierwsze rozliczenia z ZUS i urzędem skarbowym – praktyczna lista kroków dla osób zakładających firmę.',
			'content'  => $b( 'lead', 'Założenie jednoosobowej działalności gospodarczej zajmuje dziś niewiele czasu. Znacznie ważniejsze są decyzje, które podejmujesz na starcie – bo to one wpływają na wysokość podatków i składek przez kolejne miesiące.' )
				. $b( 'h2', '1. Wybierz formę działalności' )
				. $b( 'p', 'Najczęściej wybór pada na jednoosobową działalność gospodarczą (JDG). Jeśli planujesz wspólny biznes, większą skalę lub chcesz ograniczyć osobistą odpowiedzialność, warto rozważyć spółkę – np. spółkę z o.o. zakładaną przez system S24.' )
				. $b( 'h2', '2. Zarejestruj firmę w CEIDG' )
				. $b( 'p', 'Wniosek o wpis do Centralnej Ewidencji i Informacji o Działalności Gospodarczej złożysz online. We wniosku podajesz m.in. nazwę firmy, adres, kody PKD opisujące rodzaj działalności oraz datę rozpoczęcia działalności.' )
				. $b( 'h2', '3. Zdecyduj o formie opodatkowania' )
				. $b( 'p', 'Do wyboru są: zasady ogólne (skala podatkowa), podatek liniowy oraz ryczałt od przychodów ewidencjonowanych. Każda forma inaczej wpływa na wysokość podatku i składki zdrowotnej – dlatego przed decyzją warto przygotować symulację.' )
				. $b( 'h2', '4. Sprawdź, z jakich ulg w ZUS możesz skorzystać' )
				. $b( 'list', array( 'ulga na start – przez pierwsze miesiące działalności bez składek społecznych,', 'preferencyjne składki ZUS w kolejnym okresie,', 'Mały ZUS Plus – dla firm o niewielkich przychodach.' ) )
				. $b( 'p', 'Warunki skorzystania z ulg zależą m.in. od Twojej wcześniejszej aktywności zawodowej – sprawdzimy je indywidualnie.' )
				. $b( 'h2', '5. VAT, konto firmowe i księgowość' )
				. $b( 'p', 'Zdecyduj, czy rejestrujesz się jako czynny podatnik VAT, czy korzystasz ze zwolnienia. Załóż rachunek firmowy, zgłoś go do urzędu i ustal sposób prowadzenia księgowości – samodzielnie lub z biurem rachunkowym.' )
				. $b( 'panel', array( 'Dotacja na start', 'Jeśli jesteś zarejestrowany jako bezrobotny, możesz ubiegać się o dofinansowanie na rozpoczęcie działalności z urzędu pracy. Wniosek trzeba złożyć przed rejestracją firmy – pomagamy go przygotować.' ) )
				. $b( 'p', 'Chcesz przejść przez te kroki bez stresu? Umów bezpłatną konsultację – pomożemy wybrać formę działalności i opodatkowania oraz dopełnić formalności.' ),
		),
	);
}

/**
 * Klucz opcji konfiguracji startowej – osobny dla każdej wersji motywu
 * (ustawienia Customizera są zapisywane osobno dla każdego motywu).
 *
 * @return string
 */
function bzl_setup_option_key() {
	$slug = get_stylesheet();
	return 'bizlife-premium' === $slug ? 'bzl_setup_v2' : 'bzl_setup_v2_' . sanitize_key( $slug );
}

/**
 * Po przełączeniu między wersją ciemną i jasną kopiuje ustawienia z drugiej wersji
 * (teksty, zdjęcia, dane firmy, menu), aby nic nie trzeba było konfigurować od nowa.
 * Logo nie jest kopiowane – każda wersja ma własny wariant kolorystyczny.
 */
function bzl_copy_sibling_mods() {
	$current  = get_stylesheet();
	$siblings = array( 'bizlife-premium', 'bizlife-premium-light' );
	if ( ! in_array( $current, $siblings, true ) ) {
		return;
	}
	$mods = get_theme_mods();
	if ( ! empty( $mods['bzl_mods_copied'] ) ) {
		return;
	}
	$other      = 'bizlife-premium' === $current ? 'bizlife-premium-light' : 'bizlife-premium';
	$other_mods = get_option( 'theme_mods_' . $other );
	if ( is_array( $other_mods ) && $other_mods ) {
		$skip = array( 'custom_logo', 'logo_url', 'accent_color', 'bzl_mods_copied', 'custom_css_post_id' );
		foreach ( $other_mods as $key => $value ) {
			if ( in_array( $key, $skip, true ) || ( isset( $mods[ $key ] ) && 'nav_menu_locations' !== $key ) ) {
				continue;
			}
			if ( 'nav_menu_locations' === $key && ! empty( $mods['nav_menu_locations'] ) ) {
				continue;
			}
			set_theme_mod( $key, $value );
		}
	}
	set_theme_mod( 'bzl_mods_copied', 1 );
}
add_action( 'after_switch_theme', 'bzl_copy_sibling_mods', 5 );

/**
 * Jednorazowa konfiguracja startowa.
 */
function bzl_auto_setup() {
	$bzl_key = bzl_setup_option_key();
	if ( get_option( $bzl_key ) ) {
		return;
	}
	// Blokada przed równoległym uruchomieniem.
	if ( ! add_option( $bzl_key, 'running', '', false ) ) {
		return;
	}
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}

	// 0. Przyjazne adresy URL (gdy ustawione są „proste” adresy ?p=123) – struktura jak na bizzlife.pl.
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/aktualnosci/%postname%/' );
	}

	// 1. Usługi, FAQ, strony, ustawienia czytania, menu (tylko brakujące elementy).
	bzl_do_import( array( 'services', 'faq', 'pages', 'reading', 'menus' ) );

	// 2. Zdjęcia w ustawieniach motywu.
	$map = array(
		'hero_image'     => 'hero',
		'about_image'    => 'portrait',
		'services_image' => 'office',
	);
	foreach ( $map as $mod => $key ) {
		$current = (int) get_theme_mod( $mod );
		if ( ! $current || ! wp_attachment_is_image( $current ) ) {
			$id = bzl_starter_image( $key );
			if ( $id ) {
				set_theme_mod( $mod, $id );
			}
		}
	}

	// 3. Zdjęcia wyróżniające stron.
	$pages = array(
		'o-nas'   => 'portrait',
		'kontakt' => 'office',
	);
	foreach ( $pages as $slug => $key ) {
		$page = get_page_by_path( $slug );
		if ( $page && ! has_post_thumbnail( $page ) ) {
			$id = bzl_starter_image( $key );
			if ( $id ) {
				set_post_thumbnail( $page, $id );
			}
		}
	}

	// 4. Zdjęcie biura w treści strony „O nas” (tylko w treści utworzonej przez motyw).
	$about = get_page_by_path( 'o-nas' );
	if ( $about && get_post_meta( $about->ID, '_bzl_starter_page', true ) && false === strpos( $about->post_content, 'wp-block-image' ) ) {
		$office = bzl_starter_image( 'office' );
		if ( $office ) {
			$parts = explode( '<!-- wp:heading -->', $about->post_content, 2 );
			if ( 2 === count( $parts ) ) {
				wp_update_post(
					array(
						'ID'           => $about->ID,
						'post_content' => $parts[0] . bzl_block( 'image', $office ) . '<!-- wp:heading -->' . $parts[1],
					)
				);
			}
		}
	}

	// 5. Przykładowe artykuły – tylko w pustym serwisie.
	$has_posts = get_posts(
		array(
			'post_type'   => 'post',
			'post_status' => array( 'publish', 'draft', 'future', 'private' ),
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	$hello     = get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( ! $has_posts || ( 1 === count( $has_posts ) && $hello && (int) $hello->ID === (int) $has_posts[0] ) ) {
		if ( $hello ) {
			wp_trash_post( $hello->ID );
		}
		foreach ( bzl_starter_posts() as $p ) {
			if ( get_page_by_path( $p['slug'], OBJECT, 'post' ) ) {
				continue;
			}
			$cat = term_exists( $p['category'], 'category' );
			if ( ! $cat ) {
				$cat = wp_insert_term( $p['category'], 'category' );
			}
			$cat_id = is_array( $cat ) ? (int) $cat['term_id'] : (int) $cat;
			$id     = wp_insert_post(
				array(
					'post_type'     => 'post',
					'post_status'   => 'publish',
					'post_title'    => $p['title'],
					'post_name'     => $p['slug'],
					'post_excerpt'  => $p['excerpt'],
					'post_content'  => $p['content'],
					'post_date'     => $p['date'],
					'post_category' => $cat_id ? array( $cat_id ) : array(),
				),
				true
			);
			if ( ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_bzl_starter_post', 1 );
				$img = bzl_starter_image( $p['image'] );
				if ( $img ) {
					set_post_thumbnail( $id, $img );
				}
			}
		}
	}

	update_option( $bzl_key, 1, false );
	update_option( 'bzl_imported', 1, false );
	flush_rewrite_rules( false );
}
add_action( 'init', 'bzl_auto_setup', 30 );

/**
 * Po zmianie wersji motywu (ciemna ↔ jasna) podmienia okładki przykładowych artykułów
 * na grafiki w kolorystyce aktywnej wersji. Dotyczy tylko okładek dodanych przez motyw.
 */
function bzl_swap_starter_covers() {
	$map    = array(
		'koszty-uzyskania-przychodu-w-firmie' => 'koszty',
		'rozliczanie-czasu-pracy-kierowcow-i-pakiet-mobilnosci' => 'transport',
		'zakladanie-firmy-bez-tajemnic'       => 'zakladanie',
	);
	$images = bzl_starter_images();
	foreach ( $map as $slug => $key ) {
		$post = get_page_by_path( $slug, OBJECT, 'post' );
		if ( ! $post || ! get_post_meta( $post->ID, '_bzl_starter_post', true ) ) {
			continue;
		}
		$thumb   = (int) get_post_thumbnail_id( $post );
		$current = $thumb ? (string) get_post_meta( $thumb, '_bzl_starter_image', true ) : '';
		if ( $thumb && '' === $current ) {
			continue; // Okładka zmieniona ręcznie – nie ruszamy.
		}
		if ( $current !== $images[ $key ][0] ) {
			$id = bzl_starter_image( $key );
			if ( $id ) {
				set_post_thumbnail( $post, $id );
			}
		}
	}
}
add_action( 'after_switch_theme', 'bzl_swap_starter_covers', 20 );
