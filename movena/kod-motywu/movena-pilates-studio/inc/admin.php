<?php
/**
 * Panel: komunikat o wtyczce SCF oraz strona „Narzędzia” (test e-mail, test CRM, przywracanie domyślnych treści).
 */
if (!defined('ABSPATH')) {
    exit;
}

/** Komunikat, gdy brak wtyczki Secure Custom Fields. */
add_action('admin_notices', static function () {
    if (function_exists('acf_add_local_field_group') || !current_user_can('install_plugins')) {
        return;
    }
    $url = admin_url('plugin-install.php?s=secure+custom+fields&tab=search&type=term');
    echo '<div class="notice notice-warning"><p><strong>Motyw Movena Pilates Studio:</strong> do edycji treści potrzebna jest wtyczka '
        . '<a href="' . esc_url($url) . '">Secure Custom Fields</a> (autor: WordPress.org). Zainstaluj ją i aktywuj, a pola pojawią się '
        . 'w menu „Movena” oraz na stronie głównej. Do czasu instalacji strona wyświetla domyślne treści.</p></div>';
});

add_action('admin_menu', static function () {
    add_submenu_page('mvn-ustawienia', 'Narzędzia Movena', 'Narzędzia', 'manage_options', 'mvn-narzedzia', 'mvn_tools_page');
}, 100);

function mvn_tools_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $res = get_transient('mvn_tool_result_' . get_current_user_id());
    delete_transient('mvn_tool_result_' . get_current_user_id());
    $front = mvn_front_id();
    ?>
    <div class="wrap">
      <h1>Movena Pilates Studio — narzędzia</h1>

      <?php if ($res) : ?>
        <div class="notice notice-<?php echo !empty($res['ok']) ? 'success' : 'error'; ?>"><p><?php echo nl2br(esc_html($res['msg'])); ?></p></div>
      <?php endif; ?>

      <h2>Stan</h2>
      <table class="widefat striped" style="max-width:720px"><tbody>
        <tr><td>Secure Custom Fields</td><td><?php echo function_exists('acf_add_local_field_group') ? '<strong style="color:#1a7f37">aktywna</strong>' : '<strong style="color:#b32d2e">nieaktywna</strong>'; ?></td></tr>
        <tr><td>Domyślne treści zapisane w polach</td><td><?php echo get_option('mvn_seeded') ? 'tak' : 'jeszcze nie (zapisują się po aktywacji SCF)'; ?></td></tr>
        <tr><td>Strona główna</td><td><?php echo $front ? '<a href="' . esc_url(get_edit_post_link($front)) . '">' . esc_html(get_the_title($front)) . '</a> (edytuj treść sekcji)' : 'nie ustawiona'; ?></td></tr>
        <tr><td>Wysyłka e-mail</td><td><?php echo mvo('smtp_enabled') ? 'SMTP: ' . esc_html((string) mvo('smtp_host')) . ':' . (int) mvo('smtp_port') . ', hasło ' . (mvn_smtp_pass() !== '' ? 'ustawione' : '<strong style="color:#b32d2e">BRAK</strong>') : 'zwykła funkcja mail()'; ?></td></tr>
        <tr><td>CRM</td><td><?php echo mvo('crm_enabled') ? 'włączony, token ' . (mvn_crm_token() !== '' ? 'ustawiony' : '<strong style="color:#b32d2e">BRAK</strong>') : 'wyłączony'; ?></td></tr>
        <tr><td>Meta Conversions API</td><td><?php echo mvn_capi_token() !== '' ? 'token ustawiony' : 'brak tokena (zdarzenia z serwera nie są wysyłane)'; ?></td></tr>
        <tr><td>Strona programu Reformer 10</td><td><?php $rp = get_page_by_path(MVN_REFORMER_SLUG); echo $rp ? '<a href="' . esc_url(get_edit_post_link($rp->ID)) . '">edytuj</a> · <a href="' . esc_url(get_permalink($rp)) . '" target="_blank" rel="noopener">zobacz</a>' : 'brak'; ?></td></tr>
        <tr><td>Strona docelowa (reklamy)</td><td><?php $lp = get_page_by_path('pierwszy-trening'); echo $lp ? '<a href="' . esc_url(get_edit_post_link($lp->ID)) . '">edytuj</a> · <a href="' . esc_url(get_permalink($lp)) . '" target="_blank" rel="noopener">zobacz</a>' : 'brak'; ?></td></tr>
      </tbody></table>

      <h2 style="margin-top:2em">Test wysyłki e-mail</h2>
      <p>Wysyła wiadomość testową przez ustawione SMTP. Nie ujawnia hasła.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="mvn_test_mail">
        <?php wp_nonce_field('mvn_tools'); ?>
        <input type="email" name="to" class="regular-text" placeholder="<?php echo esc_attr((string) mvo('mail_to')); ?>" value="">
        <button class="button button-primary">Wyślij test</button>
      </form>

      <h2 style="margin-top:2em">Test CRM</h2>
      <p>Wysyła do CRM testowy lead („Test ze strony”) — pojawi się na liście leadów, można go potem usunąć.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="mvn_test_crm">
        <?php wp_nonce_field('mvn_tools'); ?>
        <button class="button button-primary">Wyślij testowy lead</button>
      </form>

      <h2 style="margin-top:2em">Przywróć strony prawne</h2>
      <p><em>Narzędzie awaryjne — na co dzień go nie używasz. Strony prawne są wypełnione same.</em> Wstawia domyślną treść stron <strong>Polityka prywatności</strong>, <strong>Polityka cookies</strong> i <strong>Regulamin</strong> (z poprzedniej wersji strony) i publikuje je. <strong>Uwaga:</strong> nadpisuje obecną treść tych trzech stron.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Nadpisać treść stron prawnych wersją domyślną?');">
        <input type="hidden" name="action" value="mvn_restore_legal">
        <?php wp_nonce_field('mvn_tools'); ?>
        <button class="button">Przywróć strony prawne</button>
      </form>

      <h2 style="margin-top:2em">Przywróć stronę docelową</h2>
      <p><em>Narzędzie awaryjne — klikasz tylko wtedy, gdy ktoś przez pomyłkę usunął stronę.</em> Tworzy stronę <strong>/pierwszy-trening/</strong>, jeśli ją usunięto, i ustawia jej szablon „Landing”. Gdy pole „Sekcje” jest puste, wstawia domyślne sekcje. Nie nadpisuje istniejących treści.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="mvn_restore_landing">
        <?php wp_nonce_field('mvn_tools'); ?>
        <button class="button">Przywróć landing</button>
      </form>

      <h2 style="margin-top:2em">Przywróć domyślne treści</h2>
      <p><em>Narzędzie awaryjne — nie klikaj, jeśli strona działa.</em> <strong>Uwaga:</strong> nadpisuje wszystkie treści strony głównej i ustawienia (menu „Movena”) wartościami początkowymi z motywu. Strony prawne nie są ruszane (do nich służy przycisk wyżej).</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Na pewno nadpisać wszystkie treści wartościami domyślnymi?');">
        <input type="hidden" name="action" value="mvn_reseed">
        <?php wp_nonce_field('mvn_tools'); ?>
        <button class="button">Przywróć domyślne</button>
      </form>
    </div>
    <?php
}

function mvn_tool_done(bool $ok, string $msg): void
{
    set_transient('mvn_tool_result_' . get_current_user_id(), ['ok' => $ok, 'msg' => $msg], 120);
    wp_safe_redirect(admin_url('admin.php?page=mvn-narzedzia'));
    exit;
}

function mvn_tool_guard(): void
{
    if (!current_user_can('manage_options')) {
        wp_die('Brak uprawnień.', 403);
    }
    check_admin_referer('mvn_tools');
}

add_action('admin_post_mvn_test_mail', static function () {
    mvn_tool_guard();
    $to = isset($_POST['to']) ? sanitize_email(wp_unslash($_POST['to'])) : '';
    if ($to === '' || !is_email($to)) {
        $to = (string) mvo('mail_to');
    }
    [$ok, $err] = mvn_mail($to, 'Test formularza — ' . mvo('brand'), "To jest wiadomość testowa z motywu Movena Pilates Studio.\nData: " . wp_date('Y-m-d H:i:s') . "\n\nJeśli ją widzisz, wysyłka działa.");
    mvn_tool_done($ok, $ok
        ? 'E-mail przyjęty przez serwer pocztowy. Sprawdź skrzynkę ' . $to . ' (także spam).'
        : 'Nie udało się wysłać e-maila. ' . $err);
});

add_action('admin_post_mvn_test_crm', static function () {
    mvn_tool_guard();
    [$ok, $info] = mvn_crm_forward([
        'name' => 'Test ze strony', 'phone' => '500000000', 'email' => (string) mvo('mail_to'),
        'message' => 'To jest testowy lead z panelu WordPress (Movena → Narzędzia).',
        'interest' => mvn_crm_value(mvn_form_first()), 'form_name' => 'Test z panelu', 'url' => home_url('/'), 'event_id' => 'test-' . time(), 'source' => 'strona', 'consent' => '1', 'website' => '',
    ]);
    mvn_tool_done($ok, ($ok ? 'Lead wysłany. Sprawdź listę leadów w CRM. ' : 'Błąd wysyłki do CRM. ') . $info);
});

add_action('admin_post_mvn_reseed', static function () {
    mvn_tool_guard();
    if (!function_exists('update_field')) {
        mvn_tool_done(false, 'Wtyczka Secure Custom Fields nie jest aktywna.');
    }
    mvn_seed(true);
    mvn_tool_done(true, 'Domyślne treści przywrócone.');
});

add_action('admin_post_mvn_restore_legal', static function () {
    mvn_tool_guard();
    $n = mvn_restore_legal();
    mvn_tool_done(true, 'Przywrócono treść stron prawnych: ' . $n . '.');
});

/**
 * Istniejące instalacje, jednorazowo i bez klikania:
 * 1) uzupełnia puste dane firmy (PM Boxing Studio Sp. z o.o.) w ustawieniach,
 * 2) odnawia strony prawne, które wciąż mają oznaczenia [UZUPEŁNIJ: …] (ręcznie zmienionych stron nie rusza).
 */
add_action('wp_loaded', static function () {
    if (get_option('mvn_legal_filled_v1') || !function_exists('get_field') || !(int) get_option('mvn_seeded', 0) || !function_exists('mvn_defaults')) {
        return;
    }
    $site = mvn_defaults()['site'] ?? [];
    foreach (['company_name', 'company_address', 'nip', 'regon', 'krs', 'postal', 'gdpr_email'] as $k) {
        if (trim((string) get_field('field_mvn_' . $k, 'option')) === '' && !empty($site[$k])) {
            update_field('field_mvn_' . $k, $site[$k], 'option');
        }
    }
    foreach (require MVN_DIR . '/inc/legal-defaults.php' as $slug => $d) {
        $page = get_page_by_path($slug);
        if (!$page || strpos((string) $page->post_content, 'UZUPEŁNIJ') !== false || trim((string) $page->post_content) === '') {
            $arr = ['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $d['title'], 'post_name' => $slug, 'post_content' => $d['content'], 'post_excerpt' => $d['desc']];
            if ($page) {
                $arr['ID'] = $page->ID;
                wp_update_post($arr);
            } else {
                wp_insert_post($arr);
            }
        }
    }
    update_option('mvn_legal_filled_v1', 1, false);
}, 30);

/** Istniejące instalacje: jednorazowo dodaj stronę /reformer-10/ i wpis w formularzu. */
add_action('wp_loaded', static function () {
    if (!function_exists('get_field') || !(int) get_option('mvn_seeded', 0) || get_option('mvn_reformer_seeded')) {
        return;
    }
    mvn_seed_reformer(false);
    update_option('mvn_reformer_seeded', 1, false);
});

add_action('admin_post_mvn_restore_landing', static function () {
    mvn_tool_guard();
    mvn_seed_landing(false);
    mvn_tool_done(true, 'Strona /pierwszy-trening/ jest gotowa.');
});
