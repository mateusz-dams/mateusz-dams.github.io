<?php
/**
 * Kreator startowy: Wygląd → Bizzlife: start.
 *
 * Tworzy usługi, FAQ, strony i menu na podstawie treści obecnej strony bizzlife.pl.
 * Każdy krok jest bezpieczny do ponownego uruchomienia (nie tworzy duplikatów).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Treści startowe usług.
 *
 * @return array
 */
function bzl_seed_services() {
	$p  = static function ( $t ) {
		return '<!-- wp:paragraph --><p>' . esc_html( $t ) . '</p><!-- /wp:paragraph -->';
	};
	$h  = static function ( $t ) {
		return '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html( $t ) . '</h2><!-- /wp:heading -->';
	};
	$ld = static function ( $t ) {
		return '<!-- wp:paragraph {"className":"is-style-lead"} --><p class="is-style-lead">' . esc_html( $t ) . '</p><!-- /wp:paragraph -->';
	};
	$ul = static function ( $items ) {
		$out = '<!-- wp:list {"className":"is-style-checks"} --><ul class="wp-block-list is-style-checks">';
		foreach ( $items as $i ) {
			$out .= '<!-- wp:list-item --><li>' . esc_html( $i ) . '</li><!-- /wp:list-item -->';
		}
		return $out . '</ul><!-- /wp:list -->';
	};

	return array(
		array(
			'slug'     => 'ksiegowosc',
			'title'    => 'Księgowość',
			'icon'     => 'ledger',
			'topic'    => 'wspolpraca',
			'excerpt'  => 'Prowadzenie KPiR, ewidencji ryczałtu i pełnych ksiąg handlowych dla JDG i spółek, z bieżącymi rozliczeniami z urzędem skarbowym i ZUS.',
			'scope'    => "Książka przychodów i rozchodów (KPiR)\nEwidencja przychodów dla ryczałtu\nPełne księgi handlowe dla spółek\nDeklaracje i rozliczenia podatkowe\nRozliczenia ZUS i składki zdrowotnej\nObsługa e-faktur w systemie KSeF",
			'audience' => "Jednoosobowe działalności gospodarcze\nSpółki osobowe i kapitałowe\nFirmy z branży transportowej",
			'content'  => $ld( 'Prowadzimy księgowość firm z Radomia — od jednoosobowych działalności po spółki kapitałowe. Dbamy o poprawne ewidencje, deklaracje i terminowe rozliczenia z urzędem skarbowym oraz ZUS.' )
				. $h( 'Co obejmuje obsługa księgowa' )
				. $ul( array( 'Prowadzenie KPiR lub ewidencji przychodów dla ryczałtu', 'Pełne księgi handlowe dla spółek', 'Sporządzanie i wysyłka deklaracji podatkowych', 'Rozliczenia ZUS i składki zdrowotnej', 'Obsługa e-faktur w Krajowym Systemie e-Faktur (KSeF)' ) )
				. $h( 'Tradycyjna rzetelność, nowoczesne narzędzia' )
				. $p( 'Łączymy staranność w prowadzeniu dokumentacji z nowoczesnymi rozwiązaniami, takimi jak e-księgowość i obsługa KSeF. Zakres i cenę obsługi ustalamy indywidualnie — na podstawie liczby dokumentów i stopnia złożoności rozliczeń.' ),
		),
		array(
			'slug'     => 'doradztwo-podatkowe',
			'title'    => 'Doradztwo podatkowe',
			'icon'     => 'percent',
			'topic'    => 'uslugi',
			'excerpt'  => 'Pomoc w wyborze formy opodatkowania, analiza kosztów i wyjaśnianie przepisów podatkowych w sposób zrozumiały dla przedsiębiorcy.',
			'scope'    => "Porównanie skali podatkowej, podatku liniowego i ryczałtu\nUwzględnienie składki zdrowotnej w symulacjach\nKonsultacje bieżących zagadnień podatkowych\nAnaliza kosztów działalności",
			'audience' => "Osoby zakładające działalność\nPrzedsiębiorcy rozważający zmianę formy opodatkowania\nSpółki",
			'content'  => $ld( 'Wykraczamy poza standardową księgowość. Pomagamy zrozumieć skutki podatkowe decyzji biznesowych i wybrać formę opodatkowania dopasowaną do Twojej sytuacji.' )
				. $h( 'Wybór formy opodatkowania' )
				. $p( 'Przygotowujemy symulację kosztów dla skali podatkowej, podatku liniowego i ryczałtu, uwzględniając aktualne zasady naliczania składki zdrowotnej. Dzięki temu decyzję podejmujesz na podstawie liczb, a nie przypuszczeń.' )
				. $h( 'Bieżące konsultacje' )
				. $p( 'Omawiamy trudniejsze przypadki podatkowe i wyjaśniamy przepisy językiem zrozumiałym dla przedsiębiorcy.' ),
		),
		array(
			'slug'     => 'kadry-i-place',
			'title'    => 'Kadry i płace',
			'icon'     => 'payroll',
			'topic'    => 'uslugi',
			'excerpt'  => 'Akta osobowe, listy płac i rozliczenia z ZUS — zgodnie z prawem pracy i w wymaganych terminach.',
			'scope'    => "Prowadzenie akt osobowych\nNaliczanie wynagrodzeń i listy płac\nRozliczenia z ZUS\nDeklaracje i rozliczenia pracownicze",
			'audience' => "Firmy zatrudniające pracowników\nSpółki\nFirmy transportowe zatrudniające kierowców",
			'content'  => $ld( 'Zapewniamy pełną obsługę kadrowo-płacową: od dokumentacji pracowniczej po comiesięczne naliczanie wynagrodzeń.' )
				. $h( 'Zakres obsługi' )
				. $ul( array( 'Prowadzenie akt osobowych pracowników', 'Naliczanie wynagrodzeń i sporządzanie list płac', 'Terminowe rozliczenia z ZUS', 'Deklaracje i rozliczenia pracownicze' ) )
				. $p( 'Dbamy o zgodność dokumentacji z prawem pracy i pilnujemy terminów rozliczeń.' ),
		),
		array(
			'slug'     => 'rejestracja-i-obsluga-spolek',
			'title'    => 'Rejestracja i obsługa spółek',
			'icon'     => 'building',
			'topic'    => 'zakladanie-firmy',
			'excerpt'  => 'Rejestracja spółki w systemie S24, dopełnienie formalności startowych i pełna księgowość spółki.',
			'scope'    => "Rejestracja spółki w systemie S24\nFormalności startowe po rejestracji\nPełne księgi handlowe\nBieżąca obsługa księgowa spółki",
			'audience' => "Przedsiębiorcy zakładający spółkę\nIstniejące spółki szukające biura rachunkowego",
			'content'  => $ld( 'Pomagamy przejść przez rejestrację spółki i przejmujemy jej bieżącą obsługę księgową.' )
				. $h( 'Rejestracja spółki' )
				. $p( 'Wspieramy przy rejestracji spółki w systemie S24 i dbamy o dopełnienie wszystkich formalności startowych.' )
				. $h( 'Księgowość spółki' )
				. $p( 'Prowadzimy pełne księgi handlowe oraz bieżące rozliczenia spółki z urzędem skarbowym i ZUS.' ),
		),
		array(
			'slug'     => 'obsluga-prawna-firm',
			'title'    => 'Obsługa prawna firm',
			'icon'     => 'scales',
			'topic'    => '',
			'excerpt'  => 'Wsparcie w sprawach prawnych związanych z prowadzeniem firmy. Zakres i formę współpracy ustalamy indywidualnie.',
			'scope'    => '',
			'audience' => '',
			'content'  => $ld( 'Wsparcie w sprawach prawnych związanych z prowadzeniem działalności gospodarczej.' )
				. $p( 'Zakres obsługi prawnej oraz sposób jej realizacji omawiamy podczas bezpłatnej konsultacji.' ),
		),
		array(
			'slug'     => 'ksiegowosc-dla-transportu',
			'title'    => 'Księgowość dla transportu',
			'icon'     => 'truck',
			'topic'    => '',
			'excerpt'  => 'Księgowość firm transportowych z uwzględnieniem specyfiki branży, w tym rozliczania czasu pracy kierowców i Pakietu Mobilności.',
			'scope'    => "Księgowość firm transportowych\nRozliczanie czasu pracy kierowców\nKadry i płace kierowców\nRozliczenia z uwzględnieniem Pakietu Mobilności",
			'audience' => "Przewoźnicy i firmy transportowe\nJednoosobowe działalności w branży TSL",
			'content'  => $ld( 'Branża transportowa ma swoją specyfikę — od rozliczania czasu pracy kierowców po wymogi Pakietu Mobilności. Uwzględniamy ją w codziennej obsłudze księgowej i kadrowej.' )
				. $h( 'Co obejmuje obsługa' )
				. $ul( array( 'Księgowość firm transportowych', 'Rozliczanie czasu pracy kierowców', 'Kadry i płace kierowców', 'Rozliczenia z uwzględnieniem Pakietu Mobilności' ) ),
		),
		array(
			'slug'     => 'dotacje-i-zakladanie-firm',
			'title'    => 'Dotacje i zakładanie firm',
			'icon'     => 'sprout',
			'topic'    => 'zakladanie-firmy',
			'excerpt'  => 'Rejestracja działalności w CEIDG, wybór formy opodatkowania oraz pomoc przy wnioskach o dofinansowanie z urzędu pracy i funduszy UE.',
			'scope'    => "Rejestracja działalności w CEIDG\nWybór formy działalności i opodatkowania\nWnioski o dotację na rozpoczęcie działalności\nWnioski o refundację doposażenia stanowiska pracy\nRozliczenie otrzymanej dotacji",
			'audience' => "Osoby zakładające pierwszą firmę\nPrzedsiębiorcy planujący zatrudnienie",
			'content'  => $ld( 'Wspieramy przyszłych przedsiębiorców od pierwszego kroku: pomagamy założyć firmę i przygotować wniosek o dofinansowanie.' )
				. $h( 'Zakładanie działalności' )
				. $p( 'Pomagamy założyć firmę w CEIDG lub zarejestrować spółkę w systemie S24 i wybrać formę opodatkowania.' )
				. $h( 'Dotacje' )
				. $p( 'Przygotowujemy wnioski o dofinansowanie na rozpoczęcie działalności oraz doposażenie stanowisk pracy — m.in. z Powiatowego Urzędu Pracy w Radomiu i funduszy UE. Pomagamy również w prawidłowym rozliczeniu otrzymanej dotacji.' ),
		),
	);
}

/**
 * Treści startowe FAQ.
 *
 * @return array [pytanie, odpowiedź, temat, na stronie głównej]
 */
function bzl_seed_faqs() {
	return array(
		array( 'Jak rozpocząć współpracę z biurem rachunkowym?', 'Wystarczy zadzwonić, napisać e-mail lub wypełnić formularz kontaktowy. Podczas bezpłatnej konsultacji poznajemy Twoją firmę, ustalamy zakres obsługi i przedstawiamy wycenę.', 'wspolpraca', true ),
		array( 'Jak zmienić dotychczasowe biuro rachunkowe na Bizzlife?', 'Zmiana biura jest prosta i możliwa w każdym miesiącu. Przejmujemy kontakt z poprzednim księgowym, pomagamy w bezpiecznym przeniesieniu dokumentacji i aktualizujemy dane w urzędzie skarbowym oraz ZUS.', 'wspolpraca', true ),
		array( 'Jakie dokumenty są potrzebne na start współpracy?', 'To zależy od formy działalności i tego, czy przechodzisz z innego biura. Zwykle są to dane rejestrowe firmy, dokumentacja z dotychczasowej księgowości oraz bieżące dokumenty sprzedaży i kosztów. Dokładną listę przekażemy po konsultacji.', 'wspolpraca', true ),
		array( 'Czy obsługujecie firmy spoza Radomia?', 'Nasze biuro znajduje się w Radomiu przy ul. Niedziałkowskiego 40/2. Jeśli prowadzisz firmę w innej miejscowości, skontaktuj się z nami — omówimy możliwość współpracy.', 'wspolpraca', true ),
		array( 'Jak wygląda przekazywanie dokumentów?', 'Sposób i terminy przekazywania dokumentów ustalamy na początku współpracy. Dokumenty możesz też dostarczyć osobiście do naszego biura w Radomiu.', 'wspolpraca', true ),
		array( 'Od czego zależy cena obsługi księgowej?', 'Cenę ustalamy indywidualnie — na podstawie liczby dokumentów i stopnia złożoności rozliczeń. Oferujemy elastyczne pakiety, które można dopasować do rozwoju firmy.', 'wspolpraca', true ),
		array( 'Czy obsługujecie spółki?', 'Tak. Prowadzimy pełne księgi handlowe spółek, a także pomagamy przy rejestracji spółki w systemie S24.', 'uslugi', true ),
		array( 'Czy pomagacie przy zakładaniu działalności?', 'Tak, wspieramy przyszłych przedsiębiorców na każdym kroku. Pomagamy założyć firmę w CEIDG lub zarejestrować spółkę w systemie S24 i dbamy o dopełnienie formalności startowych.', 'zakladanie-firmy', true ),
		array( 'Jakie usługi oferuje biuro rachunkowe Bizzlife?', 'Prowadzimy KPiR i pełne księgi handlowe, obsługę kadrowo-płacową i rozliczenia ZUS, wspieramy w sprawach podatkowych JDG i spółki, a także pomagamy przy zakładaniu firm i wnioskach o dotacje.', 'uslugi', false ),
		array( 'Na jaką pomoc mogę liczyć przy wyborze formy opodatkowania?', 'Przygotowujemy symulację kosztów: porównujemy ryczałt, podatek liniowy i skalę podatkową, uwzględniając aktualne zasady naliczania składki zdrowotnej.', 'zakladanie-firmy', false ),
		array( 'Czy prowadzicie kadry i płace?', 'Tak. Prowadzimy akta osobowe, naliczamy wynagrodzenia i sporządzamy listy płac, dbając o zgodność z prawem pracy i terminowe rozliczenia z ZUS.', 'uslugi', false ),
		array( 'Czy biuro pomaga uzyskać dotacje z urzędu pracy lub funduszy UE?', 'Tak. Pomagamy przygotować wnioski o dofinansowanie na rozpoczęcie działalności oraz doposażenie stanowisk pracy, a także prawidłowo rozliczyć otrzymaną dotację.', 'zakladanie-firmy', false ),
		array( 'Czy reprezentujecie klienta przed urzędami?', 'Tak, na podstawie pełnomocnictwa reprezentujemy klientów przed urzędem skarbowym, ZUS, PIP oraz GUS. Składamy wyjaśnienia w Twoim imieniu i towarzyszymy Ci podczas kontroli skarbowych.', 'uslugi', false ),
		array( 'Jak wygląda bieżący kontakt z księgową?', 'Masz stały kontakt telefoniczny i mailowy ze swoim opiekunem oraz możliwość osobistych konsultacji w naszym biurze w Radomiu.', 'wspolpraca', false ),
	);
}

/**
 * Tematy FAQ.
 *
 * @return array slug => nazwa
 */
function bzl_seed_topics() {
	return array(
		'wspolpraca'       => 'Współpraca z biurem',
		'uslugi'           => 'Usługi',
		'zakladanie-firmy' => 'Zakładanie firmy i podatki',
	);
}

/**
 * Strona kreatora w menu Wygląd.
 */
function bzl_importer_menu() {
	add_theme_page( __( 'Bizzlife: start', 'bizlife' ), __( 'Bizzlife: start', 'bizlife' ), 'manage_options', 'bzl-start', 'bzl_importer_page' );
}
add_action( 'admin_menu', 'bzl_importer_menu' );

/**
 * Powiadomienie po aktywacji.
 */
function bzl_importer_notice() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'bzl_imported' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_bzl-start' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%1$s <a class="button button-primary" href="%2$s">%3$s</a></p></div>',
		esc_html__( 'Motyw Bizzlife Premium jest aktywny. Uruchom kreator, aby utworzyć usługi, FAQ, strony i menu.', 'bizlife' ),
		esc_url( admin_url( 'themes.php?page=bzl-start' ) ),
		esc_html__( 'Otwórz kreator', 'bizlife' )
	);
}
add_action( 'admin_notices', 'bzl_importer_notice' );

/**
 * Widok kreatora.
 */
function bzl_importer_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$report = get_transient( 'bzl_import_report' );
	delete_transient( 'bzl_import_report' );
	$steps = array(
		'services' => array( __( 'Utwórz usługi (7 usług z obecnej oferty)', 'bizlife' ), true ),
		'migrate'  => array( __( 'Przenieś treść ze starych stron usług o tym samym adresie (np. /ksiegowosc/) i ustaw te strony jako szkic – stare adresy zaczną przekierowywać 301 na /uslugi/…', 'bizlife' ), false ),
		'faq'      => array( __( 'Utwórz pytania FAQ i tematy', 'bizlife' ), true ),
		'pages'    => array( __( 'Utwórz strony: O nas, Kontakt, FAQ, Aktualności (jeśli nie istnieją)', 'bizlife' ), true ),
		'reading'  => array( __( 'Ustaw stronę główną i stronę wpisów (Ustawienia → Czytanie)', 'bizlife' ), true ),
		'menus'    => array( __( 'Utwórz menu główne, menu stopki i linki prawne', 'bizlife' ), true ),
		'media'    => array( __( 'Podepnij logo i zdjęcia z biblioteki mediów (wyszukiwanie po nazwach plików z obecnej strony)', 'bizlife' ), true ),
		'download' => array( __( 'Jeśli zdjęć nie ma w bibliotece – pobierz je z bizzlife.pl', 'bizlife' ), false ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Bizzlife Premium – kreator startowy', 'bizlife' ); ?></h1>
		<p><?php esc_html_e( 'Kreator można uruchamiać wielokrotnie – istniejące elementy nie zostaną zduplikowane ani nadpisane.', 'bizlife' ); ?></p>
		<?php if ( is_array( $report ) ) : ?>
			<div class="notice notice-success"><ul style="list-style:disc;padding-left:20px">
				<?php foreach ( $report as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="bzl_import">
			<?php wp_nonce_field( 'bzl_import', 'bzl_import_nonce' ); ?>
			<table class="form-table" role="presentation"><tbody>
				<?php foreach ( $steps as $key => $step ) : ?>
					<tr><td><label><input type="checkbox" name="bzl_steps[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $step[1] ); ?>> <?php echo esc_html( $step[0] ); ?></label></td></tr>
				<?php endforeach; ?>
			</tbody></table>
			<?php submit_button( __( 'Uruchom kreator', 'bizlife' ) ); ?>
		</form>
		<h2><?php esc_html_e( 'Gdzie edytować treści', 'bizlife' ); ?></h2>
		<ul style="list-style:disc;padding-left:20px">
			<li><a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=bzl_panel' ) ); ?>"><?php esc_html_e( 'Wygląd → Dostosuj → Bizzlife – ustawienia motywu', 'bizlife' ); ?></a> – <?php esc_html_e( 'teksty sekcji, zdjęcia, dane firmy, CTA, formularz', 'bizlife' ); ?></li>
			<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=bzl_service' ) ); ?>"><?php esc_html_e( 'Usługi', 'bizlife' ); ?></a>, <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=bzl_faq' ) ); ?>"><?php esc_html_e( 'FAQ', 'bizlife' ); ?></a>, <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=bzl_lead' ) ); ?>"><?php esc_html_e( 'Zgłoszenia', 'bizlife' ); ?></a></li>
			<li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Wygląd → Menu', 'bizlife' ); ?></a></li>
		</ul>
	</div>
	<?php
}

/**
 * Znajduje załącznik po fragmencie nazwy pliku.
 *
 * @param string $needle Fragment nazwy.
 * @return int
 */
function bzl_find_attachment( $needle ) {
	global $wpdb;
	$like = '%' . $wpdb->esc_like( $needle ) . '%';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id ASC LIMIT 1", $like ) );
	return (int) $id;
}

/**
 * Zwraca istniejące menu o tej nazwie albo tworzy nowe (bez duplikatów przy zmianie wersji motywu).
 *
 * @param string $name    Nazwa menu.
 * @param bool   $created Ustawiane na true, gdy menu zostało utworzone.
 * @return int|WP_Error
 */
function bzl_get_or_create_menu( $name, &$created ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		$created = false;
		return (int) $menu->term_id;
	}
	$created = true;
	return wp_create_nav_menu( $name );
}

/**
 * Obsługa kreatora.
 */
function bzl_run_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Brak uprawnień.', 'bizlife' ), 403 );
	}
	check_admin_referer( 'bzl_import', 'bzl_import_nonce' );
	$steps  = isset( $_POST['bzl_steps'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['bzl_steps'] ) ) : array();
	$report = bzl_do_import( $steps );
	update_option( 'bzl_imported', 1, false );
	flush_rewrite_rules();

	set_transient( 'bzl_import_report', $report ? $report : array( __( 'Gotowe – nie było nic do zrobienia.', 'bizlife' ) ), 5 * MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'themes.php?page=bzl-start' ) );
	exit;
}
add_action( 'admin_post_bzl_import', 'bzl_run_import' );

/**
 * Wykonuje wybrane kroki kreatora.
 *
 * @param string[] $steps Kroki.
 * @return string[] Raport.
 */
function bzl_do_import( $steps ) {
	$report = array();

	// Tematy FAQ.
	$topic_ids = array();
	if ( in_array( 'faq', $steps, true ) || in_array( 'services', $steps, true ) ) {
		foreach ( bzl_seed_topics() as $slug => $name ) {
			$term = get_term_by( 'slug', $slug, 'bzl_faq_topic' );
			if ( ! $term ) {
				$res = wp_insert_term( $name, 'bzl_faq_topic', array( 'slug' => $slug ) );
				if ( ! is_wp_error( $res ) ) {
					$topic_ids[ $slug ] = (int) $res['term_id'];
				}
			} else {
				$topic_ids[ $slug ] = (int) $term->term_id;
			}
		}
	}

	// Usługi.
	if ( in_array( 'services', $steps, true ) ) {
		$created = 0;
		foreach ( bzl_seed_services() as $order => $s ) {
			$existing = get_page_by_path( $s['slug'], OBJECT, 'bzl_service' );
			if ( $existing ) {
				continue;
			}
			$content = $s['content'];
			$legacy  = get_page_by_path( $s['slug'], OBJECT, 'page' );
			if ( $legacy && in_array( 'migrate', $steps, true ) && trim( $legacy->post_content ) ) {
				$content = $legacy->post_content;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => 'bzl_service',
					'post_status'  => 'publish',
					'post_title'   => $s['title'],
					'post_name'    => $s['slug'],
					'post_excerpt' => $s['excerpt'],
					'post_content' => $content,
					'menu_order'   => $order + 1,
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, 'bzl_icon', $s['icon'] );
			update_post_meta( $id, 'bzl_scope', $s['scope'] );
			update_post_meta( $id, 'bzl_audience', $s['audience'] );
			if ( $s['topic'] && isset( $topic_ids[ $s['topic'] ] ) ) {
				update_post_meta( $id, 'bzl_faq_topic', $topic_ids[ $s['topic'] ] );
			}
			if ( $legacy && in_array( 'migrate', $steps, true ) ) {
				$thumb = get_post_thumbnail_id( $legacy );
				if ( $thumb ) {
					set_post_thumbnail( $id, $thumb );
				}
				wp_update_post(
					array(
						'ID'          => $legacy->ID,
						'post_status' => 'draft',
					)
				);
				/* translators: %s: tytuł strony. */
				$report[] = sprintf( __( 'Przeniesiono treść strony „%s” i ustawiono ją jako szkic.', 'bizlife' ), $legacy->post_title );
			}
			++$created;
		}
		/* translators: %d: liczba. */
		$report[] = sprintf( __( 'Usługi: utworzono %d.', 'bizlife' ), $created );
	}

	// FAQ.
	if ( in_array( 'faq', $steps, true ) ) {
		$created = 0;
		foreach ( bzl_seed_faqs() as $i => $f ) {
			$exists = get_posts(
				array(
					'post_type'   => 'bzl_faq',
					'post_status' => 'any',
					'title'       => $f[0],
					'numberposts' => 1,
					'fields'      => 'ids',
				)
			);
			if ( $exists ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => 'bzl_faq',
					'post_status'  => 'publish',
					'post_title'   => $f[0],
					'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $f[1] ) . '</p><!-- /wp:paragraph -->',
					'menu_order'   => $i + 1,
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			if ( isset( $topic_ids[ $f[2] ] ) ) {
				wp_set_object_terms( $id, array( $topic_ids[ $f[2] ] ), 'bzl_faq_topic' );
			}
			update_post_meta( $id, 'bzl_on_home', (bool) $f[3] );
			++$created;
		}
		/* translators: %d: liczba. */
		$report[] = sprintf( __( 'FAQ: utworzono %d pytań.', 'bizlife' ), $created );
	}

	// Strony.
	$page_ids = array();
	if ( in_array( 'pages', $steps, true ) || in_array( 'reading', $steps, true ) || in_array( 'menus', $steps, true ) ) {
		$about_content = '<!-- wp:paragraph {"className":"is-style-lead"} --><p class="is-style-lead">Nazywam się Ewelina Oleksiewicz i wierzę, że za każdym sukcesem biznesowym stoi rzetelne wsparcie i zaufany partner. Bizzlife Radom to biuro podatkowo-księgowe, które stworzyłam z pasji do finansów i chęci pomagania ludziom w realizacji ich marzeń o własnej firmie.</p><!-- /wp:paragraph -->'
			. '<!-- wp:paragraph --><p>Moje wykształcenie finansowo-księgowe oraz wieloletnie doświadczenie praktyczne pozwalają mi patrzeć na biznes wielowymiarowo. W Bizzlife nie ograniczamy się do „księgowania faktur” – oferujemy zaangażowanie na każdym etapie: od bezpiecznego założenia firmy, przez codzienne wyzwania kadrowo-płacowe, aż po wsparcie podatkowe.</p><!-- /wp:paragraph -->'
			. '<!-- wp:paragraph --><p>Prywatnie jestem mamą dwóch córek. Uwielbiam odpoczywać w otoczeniu natury, a moje pasje to jazda konna, nurkowanie, surfing oraz psychologia. Ta wszechstronność uczy mnie spokoju i precyzji, które przenoszę do pracy z klientami.</p><!-- /wp:paragraph -->'
			. '<!-- wp:heading --><h2 class="wp-block-heading">Twój partner w finansach i podatkach</h2><!-- /wp:heading -->'
			. '<!-- wp:paragraph --><p>Jako biuro rachunkowe w Radomiu rozumiemy specyfikę lokalnego rynku oraz zmiany w polskim systemie podatkowym. Nasza oferta jest skierowana zarówno do mikroprzedsiębiorców na samozatrudnieniu (JDG), jak i do spółek kapitałowych oraz firm z sektora transportowego.</p><!-- /wp:paragraph -->'
			. '<!-- wp:paragraph --><p>Łączymy tradycyjną rzetelność z nowoczesnymi rozwiązaniami, takimi jak e-księgowość czy obsługa systemu KSeF.</p><!-- /wp:paragraph -->';

		$pages = array(
			'o-nas'       => array( 'O nas', 'page-templates/template-about.php', $about_content ),
			'kontakt'     => array( 'Kontakt', 'page-templates/template-contact.php', '' ),
			'faq'         => array( 'FAQ – najczęstsze pytania', 'page-templates/template-faq.php', '<!-- wp:paragraph --><p>Zebraliśmy odpowiedzi na pytania, które przedsiębiorcy zadają nam najczęściej. Jeśli nie znajdziesz tu swojej sytuacji, skontaktuj się z nami.</p><!-- /wp:paragraph -->' ),
			'aktualnosci' => array( 'Aktualności', '', '' ),
		);
		if ( in_array( 'reading', $steps, true ) && 'page' !== get_option( 'show_on_front' ) ) {
			$pages['strona-glowna'] = array( 'Strona główna', '', '' );
		}
		foreach ( $pages as $slug => $def ) {
			$existing = get_page_by_path( $slug );
			if ( $existing ) {
				$page_ids[ $slug ] = (int) $existing->ID;
				if ( $def[1] && ! get_post_meta( $existing->ID, '_wp_page_template', true ) ) {
					update_post_meta( $existing->ID, '_wp_page_template', $def[1] );
				}
				continue;
			}
			if ( ! in_array( 'pages', $steps, true ) && 'strona-glowna' !== $slug ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $def[0],
					'post_name'    => $slug,
					'post_content' => $def[2],
				),
				true
			);
			if ( ! is_wp_error( $id ) ) {
				$page_ids[ $slug ] = (int) $id;
				update_post_meta( $id, '_bzl_starter_page', 1 );
				if ( $def[1] ) {
					update_post_meta( $id, '_wp_page_template', $def[1] );
				}
				/* translators: %s: tytuł. */
				$report[] = sprintf( __( 'Utworzono stronę „%s”.', 'bizlife' ), $def[0] );
			}
		}
	}

	if ( in_array( 'reading', $steps, true ) ) {
		if ( 'page' !== get_option( 'show_on_front' ) && ! empty( $page_ids['strona-glowna'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $page_ids['strona-glowna'] );
		}
		if ( ! get_option( 'page_for_posts' ) && ! empty( $page_ids['aktualnosci'] ) ) {
			update_option( 'page_for_posts', $page_ids['aktualnosci'] );
		}
		$report[] = __( 'Ustawienia czytania zaktualizowane (bez nadpisywania istniejących).', 'bizlife' );
	}

	// Menu.
	if ( in_array( 'menus', $steps, true ) ) {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		if ( empty( $locations['primary'] ) ) {
			$created = false;
			$menu_id = bzl_get_or_create_menu( 'Bizzlife – menu główne', $created );
			if ( ! $created && ! is_wp_error( $menu_id ) ) {
				$locations['primary'] = $menu_id;
			} elseif ( ! is_wp_error( $menu_id ) ) {
				$parent = wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => 'Usługi',
						'menu-item-type'   => 'post_type_archive',
						'menu-item-object' => 'bzl_service',
						'menu-item-status' => 'publish',
					)
				);
				foreach ( bzl_services_query()->posts as $s ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-object-id' => $s->ID,
							'menu-item-object'    => 'bzl_service',
							'menu-item-type'      => 'post_type',
							'menu-item-parent-id' => $parent,
							'menu-item-status'    => 'publish',
						)
					);
				}
				foreach ( array( 'o-nas', 'aktualnosci', 'faq', 'kontakt' ) as $slug ) {
					if ( ! empty( $page_ids[ $slug ] ) ) {
						wp_update_nav_menu_item(
							$menu_id,
							0,
							array(
								'menu-item-title'     => 'faq' === $slug ? 'FAQ' : get_the_title( $page_ids[ $slug ] ),
								'menu-item-object-id' => $page_ids[ $slug ],
								'menu-item-object'    => 'page',
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
							)
						);
					}
				}
				$locations['primary'] = $menu_id;
				$report[]             = __( 'Utworzono menu główne.', 'bizlife' );
			}
		}
		if ( empty( $locations['footer'] ) ) {
			$created = false;
			$menu_id = bzl_get_or_create_menu( 'Bizzlife – stopka', $created );
			if ( ! $created && ! is_wp_error( $menu_id ) ) {
				$locations['footer'] = $menu_id;
			} elseif ( ! is_wp_error( $menu_id ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => 'Strona główna',
						'menu-item-url'    => home_url( '/' ),
						'menu-item-type'   => 'custom',
						'menu-item-status' => 'publish',
					)
				);
				foreach ( array( 'o-nas', 'aktualnosci', 'faq', 'kontakt' ) as $slug ) {
					if ( ! empty( $page_ids[ $slug ] ) ) {
						wp_update_nav_menu_item(
							$menu_id,
							0,
							array(
								'menu-item-title'     => 'faq' === $slug ? 'FAQ' : get_the_title( $page_ids[ $slug ] ),
								'menu-item-object-id' => $page_ids[ $slug ],
								'menu-item-object'    => 'page',
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
							)
						);
					}
				}
				$locations['footer'] = $menu_id;
			}
		}
		if ( false && empty( $locations['legal'] ) ) { // Linki prawne: domyślnie automatycznie (polityka prywatności + cookies).
			$privacy = (int) get_option( 'wp_page_for_privacy_policy' );
			if ( $privacy && 'publish' === get_post_status( $privacy ) ) {
				$menu_id = wp_create_nav_menu( 'Bizzlife – linki prawne' );
				if ( ! is_wp_error( $menu_id ) ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-object-id' => $privacy,
							'menu-item-object'    => 'page',
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						)
					);
					$locations['legal'] = $menu_id;
				}
			}
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// Media.
	if ( in_array( 'media', $steps, true ) ) {
		$map = array(
			'custom_logo' => array( 'bizzlife_logo.svg', 'https://bizzlife.pl/wp-content/uploads/2025/11/bizzlife_logo.svg' ),
			'hero_image'  => array( 'PHOTO-2025-11-13-08-16-41-2', 'https://bizzlife.pl/wp-content/uploads/2025/11/PHOTO-2025-11-13-08-16-41-2.jpg' ),
			'about_image' => array( '490939324_1189606032962617_309151569226770616_n', 'https://bizzlife.pl/wp-content/uploads/2025/11/490939324_1189606032962617_309151569226770616_n.jpg' ),
		);
		foreach ( $map as $mod => $src ) {
			if ( get_theme_mod( $mod ) ) {
				continue;
			}
			$att = bzl_find_attachment( $src[0] );
			if ( ! $att && 'custom_logo' === $mod ) {
				// Logo SVG: jeśli nie ma w bibliotece, używamy adresu URL (WordPress domyślnie blokuje wgrywanie SVG).
				if ( ! get_theme_mod( 'logo_url' ) && in_array( 'download', $steps, true ) ) {
					set_theme_mod( 'logo_url', esc_url_raw( $src[1] ) );
					$report[] = __( 'Logo: ustawiono adres pliku z bizzlife.pl.', 'bizlife' );
				}
				continue;
			}
			if ( ! $att && in_array( 'download', $steps, true ) ) {
				require_once ABSPATH . 'wp-admin/includes/media.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/image.php';
				$res = media_sideload_image( $src[1], 0, null, 'id' );
				if ( ! is_wp_error( $res ) ) {
					$att = (int) $res;
				} else {
					/* translators: %s: błąd. */
					$report[] = sprintf( __( 'Nie udało się pobrać zdjęcia: %s', 'bizlife' ), $res->get_error_message() );
				}
			}
			if ( ! $att && 'hero_image' === $mod ) {
				$att = bzl_starter_image( 'hero' );
			} elseif ( ! $att && 'about_image' === $mod ) {
				$att = bzl_starter_image( 'portrait' );
			}
			if ( $att ) {
				set_theme_mod( $mod, $att );
				/* translators: %s: nazwa ustawienia. */
				$report[] = sprintf( __( 'Podpięto plik: %s.', 'bizlife' ), $src[0] );
			}
		}
	}

	return $report;
}

/**
 * Automatyczne treści startowe: jeżeli w bazie nie ma żadnej usługi ani pytania FAQ,
 * motyw sam je tworzy (jednorazowo), aby menu „Usługi”, sekcja usług, FAQ i stopka
 * działały od razu po aktywacji – także bez uruchamiania kreatora.
 * Nie zmienia stron, menu ani ustawień czytania.
 */
function bzl_auto_seed() {
	if ( get_option( 'bzl_auto_seeded' ) ) {
		return;
	}
	// Blokada przed równoległym uruchomieniem (add_option zwraca false, gdy opcja istnieje).
	if ( ! add_option( 'bzl_auto_seeded', 'running', '', false ) ) {
		return;
	}
	$steps = array();
	if ( ! get_posts(
		array(
			'post_type'   => 'bzl_service',
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	) ) {
		$steps[] = 'services';
	}
	if ( ! get_posts(
		array(
			'post_type'   => 'bzl_faq',
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	) ) {
		$steps[] = 'faq';
	}
	if ( $steps ) {
		bzl_do_import( $steps );
		update_option( 'bzl_auto_seeded_report', $steps, false );
		flush_rewrite_rules( false );
	}
	update_option( 'bzl_auto_seeded', 1, false );
}
// Zastąpione przez bzl_auto_setup() w inc/starter.php.
