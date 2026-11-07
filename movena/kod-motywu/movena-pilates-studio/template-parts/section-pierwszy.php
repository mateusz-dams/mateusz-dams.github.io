<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
$boxItems = mvn_lines(mvsa($s, 'box_items'));
?>
<!-- PIERWSZY RAZ -->
<section<?php echo mvn_sec_attrs($s, 'section'); ?>>
  <div class="container">
    <div class="reassurance-grid">
      <div>
        <?php echo mvn_head($s); ?>
        <ul class="reassurance-list">
          <?php foreach (mvsa($s, 'items') as $row) : if (empty($row['title'])) { continue; } ?>
          <li>
            <?php echo mvn_icon('check'); ?>
            <span><strong><?php echo esc_html(mvn_tokens((string) $row['title'])); ?></strong><?php if (!empty($row['text'])) : ?><span class="desc"><?php echo esc_html(mvn_tokens((string) $row['text'])); ?></span><?php endif; ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="recommend-box">
        <?php if (mvs($s, 'box_tag') !== '' && mvn_first_price() > 0) : ?><span class="recommend-box-price-tag"><?php echo mvn_price_tag((string) mvs($s, 'box_tag')); ?></span><?php endif; ?>
        <h3><?php echo esc_html((string) mvs($s, 'box_title')); ?></h3>
        <ul>
          <?php foreach ($boxItems as $line) : ?><li><?php echo esc_html(mvn_tokens($line)); ?></li><?php endforeach; ?>
        </ul>
        <?php echo mvn_btn((string) mvs($s, 'box_btn'), mvs($s, 'box_cel', '#classes'), 'btn btn-primary btn-block', ['offer' => 'first', 'name' => 'Pierwszy trening', 'value' => (string) mvn_first_price(), 'sessions' => '1']); ?>
      </div>
    </div>
  </div>
</section>
