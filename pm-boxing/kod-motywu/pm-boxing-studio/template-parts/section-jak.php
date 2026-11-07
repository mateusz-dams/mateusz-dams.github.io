<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$skills = array_values(array_filter(pmsa($s, 'skills'), static function ($x) { return !empty($x['title']); }));
?>
<!-- FILOZOFIA -->
<section<?php echo pmbs_sec_attrs($s); ?>>
  <?php echo pmbs_ghost($s, 'Technika'); ?><div class="wrap">
    <div class="head" data-reveal>
      <h2><?php echo esc_html((string) pms($s, 'title')); ?></h2>
      <p class="prose"><?php echo esc_html((string) pms($s, 'intro')); ?></p>
    </div>
    <div class="philo">
      <figure class="philo-photo" data-reveal>
        <?php echo pmbs_img(pms($s, 'photo'), 'trener-cios.webp', (string) pms($s, 'photo_alt'), 1200, 1800); ?>
      </figure>
      <ul class="skills">
        <?php foreach ($skills as $i => $x) : ?>
        <li class="skill" data-reveal><span class="num"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span><h3><?php echo esc_html($x['title']); ?></h3><p><?php echo esc_html((string) ($x['text'] ?? '')); ?></p></li>
        <?php endforeach; ?>
        <?php if (pms($s, 'pull_title') !== '') : ?>
        <li class="skill pullcard" data-reveal><span class="num"><?php echo esc_html(sprintf('%02d', count($skills) + 1)); ?></span><h3><?php echo esc_html((string) pms($s, 'pull_title')); ?></h3><p><?php echo esc_html((string) pms($s, 'pull_text')); ?><?php if (pms($s, 'pull_link') !== '') : ?> <a class="link-gold" href="<?php echo esc_url(pmbs_href(pms($s, 'pull_cel'), '#inspiracje')); ?>"><?php echo esc_html((string) pms($s, 'pull_link')); ?></a><?php endif; ?></p></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</section>
