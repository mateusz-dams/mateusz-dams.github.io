<?php
/**
 * Template Name: Landing – trening wdrożeniowy (reklamy)
 * Strona docelowa pod reklamy: sekcje z pola „Sekcje” tej strony, uproszczony nagłówek bez menu.
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
