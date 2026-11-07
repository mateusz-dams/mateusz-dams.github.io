<?php
/**
 * Generator plików acf-json z definicji w inc/fields.php.
 * Użycie (z katalogu motywu): php tools/gen-acf-json.php
 * Pliki trafiają do acf-json/ i są wczytywane przez Secure Custom Fields (widoczne w SCF → Grupy pól).
 */
define('ABSPATH', __DIR__ . '/');
$theme = dirname(__DIR__);
define('MVN_DIR', $theme);
function add_action() {}
function add_filter() {}
function esc_html($s) { return $s; }
function __($s) { return $s; }
function get_template_directory() { return MVN_DIR; }
require $theme . '/inc/fields.php';

$groups = ['group_mvn_site' => mvn_group_site(), 'group_mvn_front' => mvn_group_front()];
if (function_exists('mvn_group_landing')) {
    $groups['group_mvn_landing'] = mvn_group_landing();
}
@mkdir($theme . '/acf-json', 0775, true);
foreach ($groups as $key => $g) {
    $g['modified'] = 1700000000;
    $json = json_encode($g, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    file_put_contents($theme . '/acf-json/' . $key . '.json', $json . "\n");
    echo $key . '.json — ' . strlen($json) . " B\n";
}
