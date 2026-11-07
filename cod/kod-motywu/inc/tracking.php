<?php
/**
 * Pomiar: Google Consent Mode v2, Google Tag Manager i kody awaryjne.
 *
 * Domyślnie działa tylko GTM (z Consent Mode), a GA4 / Ads / Hotjar są tagami w GTM.
 * Gdy w GTM jeszcze ich nie ma, można je tymczasowo włączyć „bezpośrednio” w Opcje motywu → COD 2026.
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Identyfikator w bezpiecznym formacie.
 *
 * @param string $value Wartość.
 * @param string $regex Wzorzec.
 */
function cod_tracking_id( $value, $regex ) {
	$value = strtoupper( trim( (string) $value ) );
	return preg_match( $regex, $value ) ? $value : '';
}

add_action( 'wp_head', 'cod_tracking_head', 1 );
/**
 * Kody w <head> – jak najwcześniej.
 */
function cod_tracking_head() {
	if ( is_admin() || is_customize_preview() ) {
		return;
	}
	$consent = cod_option_bool( 'cod_consent_mode', true );
	$gtm     = cod_tracking_id( cod_option_raw( 'cod_gtm_id', '' ), '/^GTM-[A-Z0-9]+$/' );
	$ga4     = cod_option_bool( 'cod_ga4_direct', false ) ? cod_tracking_id( cod_option( 'cod_ga4_id', '' ), '/^G-[A-Z0-9]+$/' ) : '';
	$ads     = cod_option_bool( 'cod_ads_direct', false ) ? cod_tracking_id( cod_option( 'cod_ads_id', '' ), '/^AW-[0-9]+$/' ) : '';
	$hotjar  = cod_option_bool( 'cod_hotjar_direct', false ) ? preg_replace( '/\D/', '', (string) cod_option( 'cod_hotjar_id', '' ) ) : '';

	echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}</script>\n";

	if ( $consent ) {
		echo "<script>gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',functionality_storage:'granted',security_storage:'granted',wait_for_update:500});gtag('set','ads_data_redaction',true);</script>\n";
	}

	if ( $gtm ) {
		printf(
			"<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','%s');</script>\n",
			esc_js( $gtm )
		);
	}

	if ( $ga4 || $ads ) {
		printf( '<script async src="https://www.googletagmanager.com/gtag/js?id=%s"></script>' . "\n", esc_attr( $ga4 ? $ga4 : $ads ) );
		echo "<script>gtag('js',new Date());";
		if ( $ga4 ) {
			printf( "gtag('config','%s');", esc_js( $ga4 ) );
		}
		if ( $ads ) {
			printf( "gtag('config','%s');", esc_js( $ads ) );
		}
		echo "</script>\n";
	}

	if ( $hotjar ) {
		printf(
			"<script>(function(h,o,t,j,a,r){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};h._hjSettings={hjid:%d,hjsv:6};a=o.getElementsByTagName('head')[0];r=o.createElement('script');r.async=1;r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;a.appendChild(r);})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');</script>\n",
			(int) $hotjar
		);
	}
}

add_action( 'wp_body_open', 'cod_tracking_body', 1 );
/**
 * GTM bez JavaScriptu.
 */
function cod_tracking_body() {
	$gtm = cod_tracking_id( cod_option_raw( 'cod_gtm_id', '' ), '/^GTM-[A-Z0-9]+$/' );
	if ( $gtm && ! is_admin() ) {
		printf(
			'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>' . "\n",
			esc_attr( $gtm )
		);
	}
}
