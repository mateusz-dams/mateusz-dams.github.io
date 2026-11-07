<?php
/**
 * Narzędzie w panelu: „Uzupełnij treści” (Opcje motywu → Uzupełnij treści).
 *
 * 1. Dopasowuje istniejące usługi (menu „Oferta”) do opisów przygotowanych w projekcie i wpisuje je
 *    WYŁĄCZNIE do pustych pól. Niczego nie nadpisuje i niczego nie usuwa.
 * 2. Kopiuje zespół z dotychczasowej sekcji „Zespół” na stronie głównej do Opcje motywu → COD 2026 → Zespół
 *    (tylko gdy lista jest pusta) – dzięki temu używane są oryginalne zdjęcia z biblioteki mediów.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'cod_importer_menu', 99 );
/**
 * Pozycja w menu.
 */
function cod_importer_menu() {
	$parent = cod_has_acf() && function_exists( 'acf_add_options_page' ) ? 'cod-theme-options' : 'themes.php';
	add_submenu_page( $parent, __( 'Uzupełnij treści', 'cod2026' ), __( 'Uzupełnij treści', 'cod2026' ), 'manage_options', 'cod-importer', 'cod_importer_page' );
}

/**
 * Reguły dopasowania tytułów usług (kolejność ma znaczenie – pierwsza pasująca wygrywa).
 */
function cod_importer_rules() {
	return array(
		'terapia-adhd' => '/neuronowski/',
		'diag-dorosli' => '/diagnoz.*adhd.*doros|adhd.*doros.*diagnoz|diagnoz.*doros.*adhd/',
		'diag-dzieci'  => '/diagnoz.*adhd/',
		'tus'          => '/umiejetnosci spolecznych|\btus\b/',
		'sb5'          => '/stanford|binet/',
		'ie'           => '/instrumental|enrichment/',
		'act'          => '/\bact\b|\btsr\b/',
		'szefowa'      => '/szefowa swojego zycia/',
		'mentoring'    => '/mentoring|lider/',
		'szkolenia'    => '/szkoleni|warsztat/',
		'life'         => '/life ?coaching|mbti/',
		'sport'        => '/sportow|trening mentaln/',
		'konsultacje'  => '/rodzic|konsultacj|poradnictw/',
	);
}

/**
 * Plan dopasowań: post ID => klucz treści.
 */
function cod_importer_plan() {
	$posts = get_posts(
		array(
			'post_type'   => 'offer',
			'post_status' => array( 'publish', 'draft', 'pending', 'private' ),
			'numberposts' => 100,
			'orderby'     => 'title',
			'order'       => 'ASC',
		)
	);
	$used = array();
	$plan = array();
	// Najpierw usługi z ustawionym powiązaniem.
	foreach ( $posts as $p ) {
		$key = (string) get_post_meta( $p->ID, 'cod_key', true );
		if ( '' !== $key ) {
			$plan[ $p->ID ] = $key;
			$used[ $key ]   = true;
		}
	}
	foreach ( $posts as $p ) {
		if ( isset( $plan[ $p->ID ] ) ) {
			continue;
		}
		$title = ' ' . cod_norm( $p->post_title ) . ' ';
		$found = '';
		foreach ( cod_importer_rules() as $key => $re ) {
			if ( empty( $used[ $key ] ) && preg_match( $re, $title ) ) {
				$found = $key;
				break;
			}
		}
		$plan[ $p->ID ] = $found;
		if ( $found ) {
			$used[ $found ] = true;
		}
	}
	return $plan;
}

/**
 * Zapis pola tylko, gdy jest puste.
 *
 * @param string $name    Nazwa pola.
 * @param mixed  $value   Wartość.
 * @param int    $post_id ID wpisu.
 * @param array  $log     Dziennik zmian.
 */
function cod_importer_set( $name, $value, $post_id, &$log ) {
	if ( ! cod_empty( get_post_meta( $post_id, $name, true ) ) || cod_empty( $value ) ) {
		return;
	}
	if ( 'offer_price' === $name || ! function_exists( 'update_field' ) ) {
		update_post_meta( $post_id, $name, $value );
	} else {
		update_field( 'field_cod26o_' . $name, $value, $post_id );
	}
	$log[] = $name;
}

/**
 * Uzupełnienie usług.
 */
function cod_importer_run_offers() {
	$data   = include COD_DIR . '/inc/data/services.php';
	$result = array();
	foreach ( cod_importer_plan() as $post_id => $key ) {
		if ( '' === $key || ! isset( $data[ $key ] ) ) {
			continue;
		}
		$d   = $data[ $key ];
		$log = array();
		cod_importer_set( 'cod_key', $key, $post_id, $log );
		cod_importer_set( 'cod_short_name', $d['short_name'], $post_id, $log );
		cod_importer_set( 'cod_label', $d['label'], $post_id, $log );
		cod_importer_set( 'cod_short', $d['short'], $post_id, $log );
		cod_importer_set( 'cod_bullets', implode( "\n", $d['bullets'] ), $post_id, $log );
		cod_importer_set( 'cod_cta', $d['cta'], $post_id, $log );
		cod_importer_set( 'cod_wizard_q', $d['wizard_q'], $post_id, $log );
		cod_importer_set( 'cod_wizard_a', $d['wizard_a'], $post_id, $log );
		if ( $d['featured'] && '' === (string) get_post_meta( $post_id, 'cod_featured', true ) ) {
			update_field( 'field_cod26o_cod_featured', 1, $post_id );
			$log[] = 'cod_featured';
		}
		if ( ! (int) get_post_meta( $post_id, 'cod_details', true ) && function_exists( 'update_field' ) ) {
			$rows = array();
			foreach ( $d['details'] as $row ) {
				$rows[] = array(
					'field_cod26o_heading' => $row['heading'],
					'field_cod26o_text'    => $row['text'],
				);
			}
			update_field( 'field_cod26o_cod_details', $rows, $post_id );
			$log[] = 'cod_details';
		}
		cod_importer_set( 'offer_price', $d['price'], $post_id, $log );
		$post = get_post( $post_id );
		if ( $post && 0 === (int) $post->menu_order ) {
			wp_update_post( array( 'ID' => $post_id, 'menu_order' => (int) $d['order'] + 1 ) );
			$log[] = 'kolejność';
		}
		$result[ $post_id ] = $log;
	}
	return $result;
}

/**
 * Zespół ze starej sekcji strony głównej.
 */
function cod_importer_team_rows() {
	$front = (int) get_option( 'page_on_front' );
	if ( ! $front || ! cod_has_acf() ) {
		return array();
	}
	$sections = get_field( 'sections', $front );
	$people   = array();
	if ( ! is_array( $sections ) ) {
		return array();
	}
	foreach ( $sections as $s ) {
		if ( empty( $s['acf_fc_layout'] ) || 'our_team_section' !== $s['acf_fc_layout'] || empty( $s['our_team'] ) ) {
			continue;
		}
		foreach ( $s['our_team'] as $row ) {
			if ( empty( $row['our_team_img'] ) || empty( $row['our_team_title'] ) ) {
				continue;
			}
			$img      = $row['our_team_img'];
			$people[] = array(
				'photo' => is_array( $img ) ? ( isset( $img['ID'] ) ? $img['ID'] : ( isset( $img['id'] ) ? $img['id'] : 0 ) ) : (int) $img,
				'name'  => wp_strip_all_tags( $row['our_team_title'] ),
				'role'  => isset( $row['our_team_subtitle'] ) ? wp_strip_all_tags( $row['our_team_subtitle'] ) : '',
				'scope' => isset( $row['our_team_desc'] ) ? cod_snippet( $row['our_team_desc'], 110 ) : '',
				'bio'   => isset( $row['our_team_desc'] ) ? (string) $row['our_team_desc'] : '',
			);
		}
	}
	return $people;
}

/**
 * Uzupełnia puste „Dłuższe opisy” w Opcje motywu → COD 2026 → Zespół opisami ze starej sekcji „Zespół”.
 *
 * @return int Liczba uzupełnionych osób.
 */
function cod_importer_team_bios() {
	$rows = get_field( 'cod_team', 'option' );
	if ( ! is_array( $rows ) || ! $rows ) {
		return 0;
	}
	$old = array();
	foreach ( cod_importer_team_rows() as $r ) {
		$old[ mb_strtolower( trim( $r['name'] ) ) ] = $r['bio'];
	}
	$n = 0;
	foreach ( $rows as $i => $r ) {
		$key = mb_strtolower( trim( isset( $r['name'] ) ? $r['name'] : '' ) );
		if ( '' === trim( wp_strip_all_tags( isset( $r['bio'] ) ? (string) $r['bio'] : '' ) ) && ! empty( $old[ $key ] ) ) {
			update_sub_field( array( 'field_cod26s_cod_team', $i + 1, 'field_cod26s_bio' ), $old[ $key ], 'option' );
			++$n;
		}
	}
	return $n;
}

/**
 * Strona narzędzia.
 */
function cod_importer_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$done_offers = null;
	$done_team   = null;
	$done_bio    = null;
	if ( isset( $_POST['cod_import'] ) && check_admin_referer( 'cod_import' ) ) {
		$what = sanitize_key( wp_unslash( $_POST['cod_import'] ) );
		if ( 'offers' === $what ) {
			$done_offers = cod_importer_run_offers();
		} elseif ( 'team' === $what && function_exists( 'update_field' ) ) {
			$rows = cod_importer_team_rows();
			if ( $rows && ! get_field( 'cod_team', 'option' ) ) {
				$acf_rows = array();
				foreach ( $rows as $r ) {
					$acf_rows[] = array(
						'field_cod26s_photo' => $r['photo'],
						'field_cod26s_name'  => $r['name'],
						'field_cod26s_role'  => $r['role'],
						'field_cod26s_scope' => $r['scope'],
						'field_cod26s_bio'   => $r['bio'],
					);
				}
				update_field( 'field_cod26s_cod_team', $acf_rows, 'option' );
				$done_team = count( $acf_rows );
			} else {
				$done_team = 0;
			}
		} elseif ( 'team_bio' === $what && function_exists( 'update_field' ) ) {
			$done_bio = cod_importer_team_bios();
		}
	}
	$data = include COD_DIR . '/inc/data/services.php';
	$plan = cod_importer_plan();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Uzupełnij treści', 'cod2026' ); ?></h1>
		<p><?php esc_html_e( 'Narzędzie wpisuje treści przygotowane w projekcie strony tylko do PUSTYCH pól. Istniejących treści nie zmienia. Każde pole możesz potem poprawić ręcznie.', 'cod2026' ); ?></p>

		<?php if ( is_array( $done_offers ) ) : ?>
			<div class="notice notice-success"><p>
				<?php
				/* translators: %d: liczba usług. */
				echo esc_html( sprintf( __( 'Gotowe. Uzupełniono usługi: %d.', 'cod2026' ), count( array_filter( $done_offers ) ) ) );
				?>
			</p></div>
		<?php endif; ?>
		<?php if ( null !== $done_team ) : ?>
			<div class="notice notice-<?php echo $done_team ? 'success' : 'warning'; ?>"><p>
				<?php
				echo $done_team
					/* translators: %d: liczba osób. */
					? esc_html( sprintf( __( 'Skopiowano osoby do zespołu: %d.', 'cod2026' ), $done_team ) )
					: esc_html__( 'Nie skopiowano zespołu: lista w Opcje motywu → COD 2026 nie jest pusta albo na stronie głównej nie ma sekcji „Zespół” ze zdjęciami.', 'cod2026' );
				?>
			</p></div>
		<?php endif; ?>

		<?php if ( null !== $done_bio ) : ?>
			<div class="notice notice-<?php echo $done_bio ? 'success' : 'warning'; ?>"><p>
				<?php
				echo $done_bio
					/* translators: %d: liczba osób. */
					? esc_html( sprintf( __( 'Uzupełniono dłuższe opisy osób: %d.', 'cod2026' ), $done_bio ) )
					: esc_html__( 'Nie znaleziono opisów do uzupełnienia (opisy są już wpisane albo w starej sekcji „Zespół” nie było opisów).', 'cod2026' );
				?>
			</p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( '1. Opisy usług', 'cod2026' ); ?></h2>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th><?php esc_html_e( 'Usługa w panelu', 'cod2026' ); ?></th><th><?php esc_html_e( 'Treść z projektu', 'cod2026' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( $plan as $pid => $key ) : ?>
					<tr>
						<td><a href="<?php echo esc_url( get_edit_post_link( $pid ) ); ?>"><?php echo esc_html( get_the_title( $pid ) ); ?></a></td>
						<td><?php echo $key && isset( $data[ $key ] ) ? esc_html( $data[ $key ]['title'] ) : '<em>' . esc_html__( 'brak dopasowania – uzupełnij ręcznie', 'cod2026' ) . '</em>'; ?></td>
					</tr>
				<?php endforeach; ?>
				<?php if ( ! $plan ) : ?>
					<tr><td colspan="2"><?php esc_html_e( 'Brak usług w menu „Oferta”.', 'cod2026' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
		<form method="post" style="margin-top:1em">
			<?php wp_nonce_field( 'cod_import' ); ?>
			<button class="button button-primary" name="cod_import" value="offers"><?php esc_html_e( 'Uzupełnij puste pola usług', 'cod2026' ); ?></button>
		</form>

		<h2 style="margin-top:2em"><?php esc_html_e( '2. Zespół', 'cod2026' ); ?></h2>
		<?php $team_rows = cod_importer_team_rows(); ?>
		<p>
			<?php
			echo $team_rows
				/* translators: %d: liczba osób. */
				? esc_html( sprintf( __( 'Na stronie głównej znaleziono osoby ze zdjęciami: %d.', 'cod2026' ), count( $team_rows ) ) )
				: esc_html__( 'Na stronie głównej nie znaleziono sekcji „Zespół” ze zdjęciami. Uzupełnij zespół ręcznie w Opcje motywu → COD 2026.', 'cod2026' );
			?>
		</p>
		<?php if ( $team_rows ) : ?>
			<form method="post">
				<?php wp_nonce_field( 'cod_import' ); ?>
				<button class="button" name="cod_import" value="team"><?php esc_html_e( 'Skopiuj zespół do ustawień motywu', 'cod2026' ); ?></button>
				<button class="button" name="cod_import" value="team_bio"><?php esc_html_e( 'Uzupełnij puste „Dłuższe opisy” osób', 'cod2026' ); ?></button>
			</form>
			<p class="description"><?php esc_html_e( 'Drugi przycisk przenosi opisy ze starej sekcji „Zespół” do pola „Dłuższy opis” – tylko tam, gdzie jest puste (dopasowanie po imieniu i nazwisku).', 'cod2026' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
