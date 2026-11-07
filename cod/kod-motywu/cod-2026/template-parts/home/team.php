<?php
/**
 * Strona główna – zespół (Opcje motywu → COD 2026 → Zespół).
 *
 * @package cod2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cod_team = cod_team();
if ( ! $cod_team ) {
	return;
}
?>
<section class="section" id="zespol" aria-labelledby="h-zespol">
	<div class="wrap">
		<div class="sec-head reveal">
			<div><span class="num-tag"><?php echo esc_html( cod_home( 'cod_team_kicker' ) ); ?></span><h2 id="h-zespol"><?php echo esc_html( cod_home( 'cod_team_title' ) ); ?></h2></div>
			<p class="lede"><?php echo esc_html( cod_home( 'cod_team_lead' ) ); ?></p>
		</div>
		<?php get_template_part( 'template-parts/cards/team-list', null, array( 'people' => $cod_team ) ); ?>
	</div>
</section>
