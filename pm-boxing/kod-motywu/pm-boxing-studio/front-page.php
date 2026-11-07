<?php
/**
 * Strona główna: sekcje z pola elastycznego „Sekcje” (Strony → Strona główna), każda w template-parts/section-{układ}.php.
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main">
<?php
foreach (pmbs_sections() as $i => $section) {
    if (empty($section['_show']) || !locate_template('template-parts/section-' . $section['acf_fc_layout'] . '.php')) {
        continue;
    }
    get_template_part('template-parts/section', $section['acf_fc_layout'], ['s' => $section, 'i' => $i]);
}
?>
</main>
<?php get_footer(); ?>
