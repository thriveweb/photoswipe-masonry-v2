<?php

function get_script($post_id, $args, $photoswipe_options) {
  ob_start();
  ?>
  <script type='text/javascript'>
  <?php if($photoswipe_options['use_masonry']) : ?>
  var psm_gallery_<?= $post_id ?> = new psm_gallery(
    '#psgal_<?= $post_id ?>',
    <?= $args['item_count'] ?>,
    {
      itemSelector: '.msnry_item',
      isFitWidth: true
    }
  );
  psm_gallery_<?= $post_id ?>.init();
  <?php endif; ?>
  </script>
  <div style="width: 100%; height: 1000px;">

  </div>
  <?php
  return ob_get_clean();
}
