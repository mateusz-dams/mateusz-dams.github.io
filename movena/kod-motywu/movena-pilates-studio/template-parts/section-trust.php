<?php
if (!defined('ABSPATH')) {
    exit;
}
$s = (array) ($args['s'] ?? []);
?>
<section<?php echo mvn_sec_attrs($s, 'trust-strip'); ?>>
  <div class="container"><?php echo mvn_icon_list(mvsa($s, 'items'), ''); ?></div>
</section>
