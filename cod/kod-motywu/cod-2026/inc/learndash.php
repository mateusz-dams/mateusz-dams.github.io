<?php
/**
 * Integracja z LearnDash.
 *
 * Przekierowanie po zakupie: tylko gdy zamówienie jest opłacone i zawiera produkt powiązany z kursem
 * (LearnDash – WooCommerce Integration zapisuje powiązanie w polach _related_course / _related_group produktu).
 * Zakup e-booka lub workbooka kończy się standardową stroną „Zamówienie otrzymane”.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Czy zamówienie zawiera produkt dający dostęp do kursu lub grupy LearnDash.
 *
 * @param WC_Order $order Zamówienie.
 */
function cod_order_has_course( $order ) {
	foreach ( $order->get_items() as $item ) {
		if ( ! is_a( $item, 'WC_Order_Item_Product' ) ) {
			continue;
		}
		$ids = array_filter( array( $item->get_product_id(), $item->get_variation_id() ) );
		foreach ( $ids as $pid ) {
			foreach ( array( '_related_course', '_related_group' ) as $key ) {
				$rel = get_post_meta( $pid, $key, true );
				if ( ! empty( $rel ) && array() !== array_filter( (array) $rel ) ) {
					return true;
				}
			}
		}
	}
	return (bool) apply_filters( 'cod_order_has_course', false, $order );
}

/**
 * Adres strony ze szkoleniami.
 */
function cod_courses_redirect_url() {
	$url = (string) cod_option( 'cod_redirect_url', '' );
	if ( '' !== $url ) {
		return $url;
	}
	$page = get_page_by_path( 'moje-szkolenia' );
	if ( $page && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}
	return home_url( '/szkolenia/' );
}

add_action( 'template_redirect', 'cod_redirect_after_course_purchase' );
/**
 * Przekierowanie po opłaceniu zamówienia z kursem.
 */
function cod_redirect_after_course_purchase() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
		return;
	}
	if ( ! cod_option_bool( 'cod_redirect_enabled', true ) ) {
		return;
	}
	global $wp;
	$order_id = isset( $wp->query_vars['order-received'] ) ? absint( $wp->query_vars['order-received'] ) : 0;
	$key      = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$order    = $order_id ? wc_get_order( $order_id ) : null;
	if ( ! $order || ! hash_equals( $order->get_order_key(), $key ) ) {
		return;
	}
	if ( ! $order->is_paid() || ! cod_order_has_course( $order ) ) {
		return;
	}
	$target = apply_filters( 'cod_course_redirect_url', cod_courses_redirect_url(), $order );
	wp_safe_redirect( add_query_arg( 'zakup', 'ok', $target ) );
	exit;
}

add_filter( 'allowed_redirect_hosts', 'cod_allowed_redirect_hosts' );
/**
 * Własna domena zawsze dozwolona w przekierowaniu.
 *
 * @param array $hosts Hosty.
 */
function cod_allowed_redirect_hosts( $hosts ) {
	$hosts[] = wp_parse_url( home_url(), PHP_URL_HOST );
	return array_unique( $hosts );
}

add_action( 'cod_before_learning_content', 'cod_purchase_notice' );
/**
 * Komunikat po zakupie na stronie szkoleń.
 */
function cod_purchase_notice() {
	if ( isset( $_GET['zakup'] ) && 'ok' === $_GET['zakup'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice-ok" role="status">' . esc_html__( 'Dziękujemy za zakup! Twoje szkolenie jest już dostępne poniżej. Potwierdzenie zamówienia wysłaliśmy e-mailem.', 'cod2026' ) . '</div>';
	}
}

add_filter( 'the_content', 'cod_purchase_notice_on_page', 5 );
/**
 * Komunikat po zakupie także na zwykłej stronie (np. „Moje szkolenia” z shortcodem [ld_profile]).
 *
 * @param string $content Treść.
 */
function cod_purchase_notice_on_page( $content ) {
	if ( is_main_query() && in_the_loop() && is_page() && isset( $_GET['zakup'] ) && 'ok' === $_GET['zakup'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		ob_start();
		cod_purchase_notice();
		return ob_get_clean() . $content;
	}
	return $content;
}

add_action( 'woocommerce_thankyou', 'cod_thankyou_course_box', 5 );
/**
 * Gdy płatność nie jest jeszcze potwierdzona (np. Przelewy24 jeszcze nie odpowiedziały),
 * na stronie potwierdzenia pokazujemy, gdzie znaleźć kurs.
 *
 * @param int $order_id ID zamówienia.
 */
function cod_thankyou_course_box( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! cod_order_has_course( $order ) ) {
		return;
	}
	echo '<div class="course-box on-dark"><h2>' . esc_html__( 'Twoje szkolenie', 'cod2026' ) . '</h2><p>' . esc_html__( 'Dostęp do szkolenia włączy się automatycznie po zaksięgowaniu płatności. Zwykle trwa to kilka minut.', 'cod2026' ) . '</p>';
	echo cod_button( cod_courses_redirect_url(), 'primary', array( 'label' => __( 'Przejdź do moich szkoleń', 'cod2026' ), 'location' => 'potwierdzenie-zamowienia' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}

/**
 * Postęp użytkownika w kursie i adres kolejnego kroku (do paska „Twój postęp” na stronie kursu).
 * Wszystkie wywołania LearnDash są warunkowe – bez wtyczki funkcja zwraca null.
 *
 * @param int $course_id Kurs.
 * @return array|null percentage, completed, total, next_url, started.
 */
function cod_course_status( $course_id ) {
	$user = get_current_user_id();
	if ( ! $user || ! function_exists( 'sfwd_lms_has_access' ) || ! sfwd_lms_has_access( $course_id, $user ) ) {
		return null;
	}
	$prog = function_exists( 'learndash_course_progress' ) ? learndash_course_progress( array( 'user_id' => $user, 'course_id' => $course_id, 'array' => true ) ) : array();
	$pct  = isset( $prog['percentage'] ) ? (int) $prog['percentage'] : 0;
	$done = isset( $prog['completed'] ) ? (int) $prog['completed'] : 0;
	$all  = isset( $prog['total'] ) ? (int) $prog['total'] : 0;

	$next = 0;
	if ( $pct < 100 && function_exists( 'learndash_user_progress_get_first_incomplete_step' ) ) {
		$next = (int) learndash_user_progress_get_first_incomplete_step( $user, $course_id );
	}
	if ( ! $next && function_exists( 'learndash_get_course_steps' ) ) {
		$steps = learndash_get_course_steps( $course_id );
		if ( is_array( $steps ) && $steps ) {
			$next = (int) reset( $steps );
		}
	}
	$url = $next && $next !== (int) $course_id ? get_permalink( $next ) : '';
	if ( $url && function_exists( 'learndash_get_step_permalink' ) ) {
		$step_url = learndash_get_step_permalink( $next, $course_id );
		$url      = $step_url ? $step_url : $url;
	}
	return array(
		'percentage' => max( 0, min( 100, $pct ) ),
		'completed'  => $done,
		'total'      => $all,
		'next_url'   => $url,
		'started'    => $done > 0,
	);
}

add_filter( 'gettext_with_context_learndash', 'cod_learndash_polish', 10, 4 );
add_filter( 'gettext_learndash', 'cod_learndash_polish_plain', 10, 3 );
/**
 * Poprawne polskie napisy w miejscach, gdzie LearnDash skleja etykiety („Szkolenie zawartość”, „Postęp Zagadnienie”).
 * Rodzaj (kurs / lekcja / temat / test) rozpoznajemy po kontekście tłumaczenia.
 *
 * @param string $translation Tłumaczenie.
 * @param string $text        Tekst oryginalny.
 * @param string $context     Kontekst.
 * @param string $domain      Domena.
 */
function cod_learndash_polish( $translation, $text, $context, $domain ) {
	$fixed = cod_learndash_label_fix( $text, (string) $context );
	return null === $fixed ? $translation : $fixed;
}

/**
 * To samo dla tekstów bez kontekstu.
 *
 * @param string $translation Tłumaczenie.
 * @param string $text        Tekst oryginalny.
 * @param string $domain      Domena.
 */
function cod_learndash_polish_plain( $translation, $text, $domain ) {
	$fixed = cod_learndash_label_fix( $text, '' );
	return null === $fixed ? $translation : $fixed;
}

/**
 * Wspólna mapa poprawek.
 *
 * @param string $text    Tekst oryginalny (angielski).
 * @param string $context Kontekst.
 * @return string|null
 */
function cod_learndash_label_fix( $text, $context ) {
	$t      = strtolower( trim( $text ) );
	$simple = array(
		'expand'       => 'Rozwiń',
		'collapse'     => 'Zwiń',
		'expand all'   => 'Rozwiń wszystko',
		'collapse all' => 'Zwiń wszystko',
	);
	if ( isset( $simple[ $t ] ) ) {
		return $simple[ $t ];
	}
	if ( in_array( $t, array( 'back to %s', 'back to %1$s' ), true ) ) {
		return 'Wróć do szkolenia';
	}
	if ( ! in_array( $t, array( '%s content', '%s progress', '%1$s content', '%1$s progress' ), true ) ) {
		return null;
	}
	$c    = strtolower( $context );
	$kind = '';
	foreach ( array( 'quiz', 'topic', 'lesson', 'course' ) as $k ) {
		if ( false !== strpos( $c, $k ) ) {
			$kind = $k;
			break;
		}
	}
	$content = array(
		'course' => 'Zawartość szkolenia',
		'lesson' => 'Zawartość lekcji',
		'topic'  => 'Zawartość tematu',
		'quiz'   => 'Zawartość testu',
		''       => 'Zawartość',
	);
	$progress = array(
		'course' => 'Postęp szkolenia',
		'lesson' => 'Postęp lekcji',
		'topic'  => 'Postęp tematu',
		'quiz'   => 'Postęp testu',
		''       => 'Postęp',
	);
	return false !== strpos( $t, 'content' ) ? $content[ $kind ] : $progress[ $kind ];
}
