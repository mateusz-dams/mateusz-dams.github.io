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
    echo '<div class="notice notice-warning"><p><strong>Motyw PM Boxing Studio:</strong> do edycji treści potrzebna jest wtyczka '
        . '<a href="' . esc_url($url) . '">Secure Custom Fields</a> (autor: WordPress.org). Zainstaluj ją i aktywuj, a pola pojawią się '
        . 'w menu „PM Boxing” oraz na stronie głównej. Do czasu instalacji strona wyświetla domyślne treści.</p></div>';
});

add_action('admin_menu', static function () {
    add_submenu_page('pmbs-ustawienia', 'Narzędzia PM Boxing', 'Narzędzia', 'manage_options', 'pmbs-narzedzia', 'pmbs_tools_page');
}, 100);

function pmbs_tools_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $res = get_transient('pmbs_tool_result_' . get_current_user_id());
    delete_transient('pmbs_tool_result_' . get_current_user_id());
    $front = pmbs_front_id();
    ?>
    <div class="wrap">
      <h1>PM Boxing Studio — narzędzia</h1>

      <?php if ($res) : ?>
        <div class="notice notice-<?php echo !empty($res['ok']) ? 'success' : 'error'; ?>"><p><?php echo nl2br(esc_html($res['msg'])); ?></p></div>
      <?php endif; ?>

      <h2>Stan</h2>
      <table class="widefat striped" style="max-width:720px"><tbody>
        <tr><td>Secure Custom Fields</td><td><?php echo function_exists('acf_add_local_field_group') ? '<strong style="color:#1a7f37">aktywna</strong>' : '<strong style="color:#b32d2e">nieaktywna</strong>'; ?></td></tr>
        <tr><td>Domyślne treści zapisane w polach</td><td><?php echo get_option('pmbs_seeded') ? 'tak' : 'jeszcze nie (zapisują się po aktywacji SCF)'; ?></td></tr>
        <tr><td>Strona główna</td><td><?php echo $front ? '<a href="' . esc_url(get_edit_post_link($front)) . '">' . esc_html(get_the_title($front)) . '</a> (edytuj treść sekcji)' : 'nie ustawiona'; ?></td></tr>
        <tr><td>Wysyłka e-mail</td><td><?php echo pmo('smtp_enabled') ? 'SMTP: ' . esc_html((string) pmo('smtp_host')) . ':' . (int) pmo('smtp_port') . ', hasło ' . (pmbs_smtp_pass() !== '' ? 'ustawione' : '<strong style="color:#b32d2e">BRAK</strong>') : 'zwykła funkcja mail()'; ?></td></tr>
        <tr><td>CRM</td><td><?php echo pmo('crm_enabled') ? 'włączony' : 'wyłączony'; ?></td></tr>
      </tbody></table>

      <h2 style="margin-top:2em">Test wysyłki e-mail</h2>
      <p>Wysyła wiadomość testową przez ustawione SMTP. Nie ujawnia hasła.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="pmbs_test_mail">
        <?php wp_nonce_field('pmbs_tools'); ?>
        <input type="email" name="to" class="regular-text" placeholder="<?php echo esc_attr((string) pmo('mail_to')); ?>" value="">
        <button class="button button-primary">Wyślij test</button>
      </form>

      <h2 style="margin-top:2em">Test CRM</h2>
      <p>Wysyła do CRM testowy lead („Test ze strony”) — pojawi się na liście leadów, można go potem usunąć.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="pmbs_test_crm">
        <?php wp_nonce_field('pmbs_tools'); ?>
        <button class="button button-primary">Wyślij testowy lead</button>
      </form>

      <h2 style="margin-top:2em">Przywróć strony prawne</h2>
      <p>Wstawia domyślną treść stron <strong>Polityka prywatności</strong>, <strong>Pliki cookies</strong> i <strong>Regulamin</strong> (z poprzedniej wersji strony) i publikuje je. <strong>Uwaga:</strong> nadpisuje obecną treść tych trzech stron.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Nadpisać treść stron prawnych wersją domyślną?');">
        <input type="hidden" name="action" value="pmbs_restore_legal">
        <?php wp_nonce_field('pmbs_tools'); ?>
        <button class="button">Przywróć strony prawne</button>
      </form>

      <h2 style="margin-top:2em">Przywróć domyślne treści</h2>
      <p><strong>Uwaga:</strong> nadpisuje wszystkie treści strony głównej i ustawienia (menu „PM Boxing”) wartościami początkowymi z motywu. Strony prawne nie są ruszane (do nich służy przycisk wyżej).</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Na pewno nadpisać wszystkie treści wartościami domyślnymi?');">
        <input type="hidden" name="action" value="pmbs_reseed">
        <?php wp_nonce_field('pmbs_tools'); ?>
        <button class="button">Przywróć domyślne</button>
      </form>
    </div>
    <?php
}

function pmbs_tool_done(bool $ok, string $msg): void
{
    set_transient('pmbs_tool_result_' . get_current_user_id(), ['ok' => $ok, 'msg' => $msg], 120);
    wp_safe_redirect(admin_url('admin.php?page=pmbs-narzedzia'));
    exit;
}

function pmbs_tool_guard(): void
{
    if (!current_user_can('manage_options')) {
        wp_die('Brak uprawnień.', 403);
    }
    check_admin_referer('pmbs_tools');
}

add_action('admin_post_pmbs_test_mail', static function () {
    pmbs_tool_guard();
    $to = isset($_POST['to']) ? sanitize_email(wp_unslash($_POST['to'])) : '';
    if ($to === '' || !is_email($to)) {
        $to = (string) pmo('mail_to');
    }
    [$ok, $err] = pmbs_mail($to, 'Test formularza — ' . pmo('brand'), "To jest wiadomość testowa z motywu PM Boxing Studio.\nData: " . wp_date('Y-m-d H:i:s') . "\n\nJeśli ją widzisz, wysyłka działa.");
    pmbs_tool_done($ok, $ok
        ? 'E-mail przyjęty przez serwer pocztowy. Sprawdź skrzynkę ' . $to . ' (także spam).'
        : 'Nie udało się wysłać e-maila. ' . $err);
});

add_action('admin_post_pmbs_test_crm', static function () {
    pmbs_tool_guard();
    [$ok, $info] = pmbs_crm_forward([
        'name' => 'Test ze strony', 'phone' => '500000000', 'email' => (string) pmo('mail_to'),
        'message' => 'To jest testowy lead z panelu WordPress (PM Boxing → Narzędzia).',
        'interest' => 'personal', 'source' => 'strona', 'consent' => '1', 'website' => '',
    ]);
    pmbs_tool_done($ok, ($ok ? 'Lead wysłany. Sprawdź listę leadów w CRM. ' : 'Błąd wysyłki do CRM. ') . $info);
});

add_action('admin_post_pmbs_reseed', static function () {
    pmbs_tool_guard();
    if (!function_exists('update_field')) {
        pmbs_tool_done(false, 'Wtyczka Secure Custom Fields nie jest aktywna.');
    }
    pmbs_seed(true);
    pmbs_tool_done(true, 'Domyślne treści przywrócone.');
});

add_action('admin_post_pmbs_restore_legal', static function () {
    pmbs_tool_guard();
    $n = pmbs_restore_legal();
    pmbs_tool_done(true, 'Przywrócono treść stron prawnych: ' . $n . '.');
});
