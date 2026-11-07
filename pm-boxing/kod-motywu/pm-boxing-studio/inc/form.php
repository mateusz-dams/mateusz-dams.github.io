<?php
/**
 * Formularz kontaktowy: wysyłka e-mail (SMTP przez wp_mail), lead do CRM, potwierdzenie dla klienta.
 * Odpowiednik dawnego pliku kontakt.php. Adres obsługi: admin-ajax.php, akcja pmbs_contact.
 */
if (!defined('ABSPATH')) {
    exit;
}

/* ------------------------------------------------------------ SMTP */

function pmbs_smtp_pass(): string
{
    return defined('PMBS_SMTP_PASS') ? (string) PMBS_SMTP_PASS : (string) pmo('smtp_pass');
}

/** Konfiguruje PHPMailera (wp_mail) na SMTP skrzynki studia. */
add_action('phpmailer_init', static function ($m) {
    if (!pmo('smtp_enabled') || !empty($GLOBALS['pmbs_force_mail'])) {
        return;
    }
    $host = trim((string) pmo('smtp_host'));
    if ($host === '') {
        return;
    }
    $sec = (string) pmo('smtp_secure');
    $m->isSMTP();
    $m->Host = $host;
    $m->Port = (int) pmo('smtp_port') ?: 587;
    $m->SMTPSecure = in_array($sec, ['tls', 'ssl'], true) ? $sec : '';
    $m->SMTPAutoTLS = ($sec === 'tls');
    $user = trim((string) pmo('smtp_user'));
    $m->SMTPAuth = ($user !== '');
    if ($user !== '') {
        $m->Username = $user;
        $m->Password = pmbs_smtp_pass();
    }
    $m->Timeout = 15;
    $m->CharSet = 'UTF-8';
    if (!pmo('smtp_verify')) {
        $m->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
    }
});

/**
 * Wysyła wiadomość tekstową. Najpierw SMTP; gdy się nie uda, próbuje zwykłego mail().
 * Zwraca [sukces, komunikat błędu].
 */
function pmbs_mail(string $to, string $subject, string $body, string $replyTo = ''): array
{
    $from = is_email((string) pmo('mail_from')) ? (string) pmo('mail_from') : (string) get_option('admin_email');
    $headers = ['Content-Type: text/plain; charset=UTF-8', 'From: ' . wp_specialchars_decode((string) pmo('brand'), ENT_QUOTES) . ' <' . $from . '>'];
    if ($replyTo !== '' && is_email($replyTo)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }
    $err = '';
    $cb = static function ($e) use (&$err) {
        $err = $e->get_error_message();
    };
    add_action('wp_mail_failed', $cb);
    $ok = wp_mail($to, $subject, $body, $headers);
    if (!$ok && pmo('smtp_enabled')) {
        error_log('[PM Boxing] SMTP nie zadziałał: ' . $err);
        $GLOBALS['pmbs_force_mail'] = true;
        $ok = wp_mail($to, $subject, $body, $headers);
        unset($GLOBALS['pmbs_force_mail']);
    }
    remove_action('wp_mail_failed', $cb);
    return [(bool) $ok, $err];
}

/* ------------------------------------------------------------ CRM */

/** Przesyła lead do CRM (POST jak ze zwykłego formularza). Zwraca [sukces, opis]. */
function pmbs_crm_forward(array $fields): array
{
    $url = trim((string) pmo('crm_url'));
    if ($url === '') {
        return [false, 'Brak adresu CRM.'];
    }
    $r = wp_remote_post($url, ['timeout' => 5, 'redirection' => 0, 'body' => $fields]);
    if (is_wp_error($r)) {
        return [false, 'CRM: błąd połączenia — ' . $r->get_error_message()];
    }
    $code = (int) wp_remote_retrieve_response_code($r);
    if ($code >= 200 && $code < 400) {
        return [true, 'CRM: OK (HTTP ' . $code . ')'];
    }
    return [false, 'CRM: błąd (HTTP ' . $code . ')'];
}

function pmbs_crm_value(string $formLabel): string
{
    foreach ((array) pmo('form_options') as $r) {
        if (($r['label'] ?? '') === $formLabel && !empty($r['crm'])) {
            return (string) $r['crm'];
        }
    }
    return $formLabel;
}

/* ------------------------------------------------------------ pomocnicze */

function pmbs_post(string $key, int $max, bool $multiline = false): string
{
    $v = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
    if (!is_string($v)) {
        return '';
    }
    $v = wp_check_invalid_utf8($v); // zwraca pusty tekst, gdy kodowanie jest błędne
    $pattern = $multiline ? '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u' : '/[\x00-\x1F\x7F]/u';
    $v = trim((string) preg_replace($pattern, $multiline ? '' : ' ', $v));
    return mb_strlen($v) > $max ? mb_substr($v, 0, $max) : $v;
}

function pmbs_rate_limited(): bool
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
    $key = 'pmbs_rl_' . md5($ip);
    $now = time();
    $t = get_transient($key);
    $arr = [];
    foreach (is_array($t) ? $t : [] as $x) {
        if ((int) $x > $now - HOUR_IN_SECONDS) {
            $arr[] = (int) $x;
        }
    }
    if (count($arr) >= max(1, (int) pmo('rate_max'))) {
        return true;
    }
    $arr[] = $now;
    set_transient($key, $arr, HOUR_IN_SECONDS);
    return false;
}

function pmbs_autoreply_allowed(): bool
{
    $now = time();
    $t = get_transient('pmbs_autoreply');
    $arr = [];
    foreach (is_array($t) ? $t : [] as $x) {
        if ((int) $x > $now - DAY_IN_SECONDS) {
            $arr[] = (int) $x;
        }
    }
    if (count($arr) >= max(1, (int) pmo('autoreply_max'))) {
        return false;
    }
    $arr[] = $now;
    set_transient('pmbs_autoreply', $arr, DAY_IN_SECONDS);
    return true;
}

function pmbs_json(int $code, array $data): void
{
    nocache_headers();
    wp_send_json($data, $code);
}

/* ------------------------------------------------------------ obsługa AJAX */

add_action('wp_ajax_nopriv_pmbs_contact', 'pmbs_ajax_contact');
add_action('wp_ajax_pmbs_contact', 'pmbs_ajax_contact');

function pmbs_ajax_contact(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        pmbs_json(405, ['ok' => false, 'error' => 'Metoda niedozwolona.']);
    }

    // Pochodzenie żądania
    $origin = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
    if ($origin !== '') {
        $host = strtolower((string) wp_parse_url((string) $origin, PHP_URL_HOST));
        $own = strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST));
        $allowed = array_unique([$own, preg_replace('/^www\./', '', $own), 'www.' . preg_replace('/^www\./', '', $own)]);
        if ($host === '' || !in_array($host, $allowed, true)) {
            pmbs_json(403, ['ok' => false, 'error' => 'Żądanie odrzucone.']);
        }
    }

    // Pułapka na boty
    if (pmbs_post('pole_kontrolne', 200) !== '') {
        pmbs_json(200, ['ok' => true]);
    }
    if ((int) ($_POST['t'] ?? 0) < max(0, (int) pmo('min_fill'))) {
        pmbs_json(400, ['ok' => false, 'error' => 'Odczekaj chwilę i spróbuj ponownie.']);
    }

    $name    = pmbs_post('imie', 80);
    $phone   = pmbs_post('telefon', 30);
    $email   = pmbs_post('email', 120);
    $form    = pmbs_post('forma', 60);
    $when    = pmbs_post('kiedy', 60);
    $message = pmbs_post('wiadomosc', 2000, true);
    $consent = in_array(strtolower(pmbs_post('zgoda', 10)), ['1', 'on', 'true', 'tak'], true);

    $source  = pmbs_post('zrodlo', 500);
    $pageUrl = esc_url_raw(pmbs_post('url', 300));

    $callTimes = pmbs_lines(pmo('call_times'));
    $defaultWhen = $callTimes[0] ?? 'Dowolna pora';
    if (!in_array($when, $callTimes, true)) {
        $when = $defaultWhen;
    }
    $digits = preg_replace('/[\s\-().]/', '', $phone);

    if (mb_strlen($name) < 2) {
        pmbs_json(422, ['ok' => false, 'error' => 'Podaj imię.']);
    }
    if (!preg_match('/^\+?\d{9,13}$/', (string) $digits)) {
        pmbs_json(422, ['ok' => false, 'error' => 'Podaj poprawny numer telefonu.']);
    }
    if ($email !== '' && !is_email($email)) {
        pmbs_json(422, ['ok' => false, 'error' => 'Podaj poprawny adres e-mail albo zostaw pole puste.']);
    }
    if (!in_array($form, pmbs_form_labels(), true)) {
        pmbs_json(422, ['ok' => false, 'error' => 'Wybierz formę treningu.']);
    }
    if (!$consent) {
        pmbs_json(422, ['ok' => false, 'error' => 'Zgoda na kontakt jest wymagana.']);
    }
    if (pmbs_rate_limited()) {
        pmbs_json(429, ['ok' => false, 'error' => 'Zbyt wiele zgłoszeń. Zadzwoń: ' . pmo('phone_display') . '.']);
    }

    $brand = (string) pmo('brand');
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
    $subject = 'Nowe zgłoszenie: ' . $form . ' — ' . $name;
    $body = implode("\n", [
        'Nowe zgłoszenie z formularza na stronie ' . $brand,
        '',
        'Imię: ' . $name,
        'Telefon: ' . $phone,
        'E-mail: ' . ($email !== '' ? $email : '(nie podano)'),
        'Forma treningu: ' . $form,
        'Najlepsza pora na telefon: ' . $when,
        '',
        'Wiadomość:',
        $message !== '' ? $message : '(brak)',
        '',
        'Zgoda na kontakt i przetwarzanie danych: TAK',
        'Źródło wejścia: ' . ($source !== '' ? $source : '(bezpośrednio / brak danych)'),
        'Strona: ' . ($pageUrl !== '' ? $pageUrl : '(brak)'),
        'Data: ' . wp_date('Y-m-d H:i:s'),
        'IP: ' . $ip,
    ]);

    $to = is_email((string) pmo('mail_to')) ? (string) pmo('mail_to') : (string) get_option('admin_email');
    [$sent, $mailErr] = pmbs_mail($to, $subject, $body, $email);

    // Lead do CRM (niezależnie od tego, czy poszedł e-mail)
    if (pmo('crm_enabled')) {
        $crmMessage = $message;
        if ($when !== $defaultWhen) {
            $crmMessage = trim($crmMessage . "\n\nPreferowana pora kontaktu: " . $when);
        }
        if ($source !== '') {
            $crmMessage = trim($crmMessage . "\n\nŹródło wejścia: " . $source);
        }
        [$crmOk, $crmInfo] = pmbs_crm_forward([
            'name' => $name, 'phone' => $phone, 'email' => $email, 'message' => $crmMessage,
            'interest' => pmbs_crm_value($form), 'source' => 'strona', 'consent' => '1', 'website' => '',
        ]);
        if (!$crmOk) {
            error_log('[PM Boxing] ' . $crmInfo . ' — lead od: ' . $name);
        }
    }

    // Meta Conversions API (zdarzenie Lead z serwera, deduplikowane z pikselem po event_id)
    pmbs_capi_lead($name, $phone, $email, $form, $pageUrl);

    if (!$sent) {
        error_log('[PM Boxing] Nie udało się wysłać zgłoszenia od: ' . $name . ' — ' . $mailErr);
        pmbs_json(500, ['ok' => false, 'error' => 'Nie udało się wysłać zgłoszenia. Zadzwoń: ' . pmo('phone_display') . '.']);
    }

    // Automatyczne potwierdzenie dla klienta
    $confirmed = false;
    if ($email !== '' && pmo('autoreply_enabled') && pmbs_autoreply_allowed()) {
        $callInfo = $when !== $defaultWhen ? ' w wybranej porze (' . $when . ')' : '';
        $reply = implode("\n", [
            'Dzień dobry, ' . $name . '!',
            '',
            'Dziękujemy za zgłoszenie do ' . $brand . '.',
            '',
            'Oddzwonimy na numer ' . $phone . $callInfo . ', aby ustalić szczegóły. Wybrana forma treningu: ' . $form . '.',
            '',
            'Na pierwszy trening zabierz strój i buty na zmianę. Po treningu podpowiemy, co warto kupić na początek.',
            '',
            'Jeśli wolisz napisać, jesteśmy na WhatsAppie: ' . pmbs_wa_url(),
            'Możesz też odpowiedzieć na tę wiadomość albo zadzwonić: ' . pmo('phone_display'),
            '',
            $brand,
            pmo('address') . ', ' . pmo('city'),
            '',
            'Ta wiadomość została wysłana automatycznie po wypełnieniu formularza na stronie z Twoim adresem e-mail. Jeśli to nie Ty, po prostu ją zignoruj.',
        ]);
        [$confirmed] = pmbs_mail($email, 'Dziękujemy za zgłoszenie — ' . $brand, $reply, $to);
    }

    pmbs_json(200, ['ok' => true, 'confirmation' => (bool) $confirmed]);
}


/**
 * Wysyła zdarzenie Lead do Meta Conversions API. Tylko gdy: skonfigurowano piksel i token oraz odwiedzający
 * zgodził się na cookies marketingowe (pole mc). Dane osobowe są hashowane SHA-256 po normalizacji.
 */
function pmbs_capi_lead(string $name, string $phone, string $email, string $form, string $pageUrl): void
{
    $pixel = trim((string) pmo('meta_pixel'));
    $token = trim((string) pmo('meta_capi_token'));
    if ($pixel === '' || $token === '' || pmbs_post('mc', 2) !== '1') {
        return;
    }
    $eid = preg_replace('/[^\w\-]/', '', pmbs_post('eid', 60));
    $h = static function (string $v): string {
        return hash('sha256', $v);
    };
    $digits = preg_replace('/\D+/', '', $phone);
    if ($digits !== '' && strlen($digits) === 9) {
        $digits = '48' . $digits;
    }
    $first = mb_strtolower(trim(explode(' ', trim($name))[0]));
    $user = array_filter([
        'em' => $email !== '' ? [$h(mb_strtolower(trim($email)))] : null,
        'ph' => $digits !== '' ? [$h($digits)] : null,
        'fn' => $first !== '' ? [$h($first)] : null,
        'country' => [$h('pl')],
        'client_ip_address' => isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : null,
        'client_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 400) : null,
        'fbp' => preg_match('/^fb\.\d\.\d+\.\d+$/', pmbs_post('fbp', 60)) ? pmbs_post('fbp', 60) : null,
        'fbc' => preg_match('/^fb\.\d\.\d+\.[\w\-]+$/', pmbs_post('fbc', 200)) ? pmbs_post('fbc', 200) : null,
    ]);
    $event = array_filter([
        'event_name' => 'Lead', 'event_time' => time(), 'event_id' => $eid !== '' ? $eid : null,
        'action_source' => 'website', 'event_source_url' => $pageUrl !== '' ? $pageUrl : home_url('/'),
        'user_data' => $user, 'custom_data' => ['content_name' => $form],
    ]);
    $payload = ['data' => [$event]];
    $test = trim((string) pmo('meta_test_code'));
    if ($test !== '') {
        $payload['test_event_code'] = $test;
    }
    $r = wp_remote_post('https://graph.facebook.com/v21.0/' . rawurlencode($pixel) . '/events?access_token=' . rawurlencode($token), [
        'timeout' => 4, 'headers' => ['Content-Type' => 'application/json'], 'body' => wp_json_encode($payload),
    ]);
    if (is_wp_error($r) || (int) wp_remote_retrieve_response_code($r) >= 300) {
        error_log('[PM Boxing] Meta CAPI: ' . (is_wp_error($r) ? $r->get_error_message() : wp_remote_retrieve_body($r)));
    }
}
