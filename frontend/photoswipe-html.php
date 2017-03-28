<?php

function get_html($post_id, $columns, $args, $attachments = array()) {
  ob_start();
  ?>
  <div style="clear:both"></div>
  <div id="psgal_<?= $post_id ?>" class="psgal gallery-columns-<?= $columns ?> gallery-size-<?= sanitize_html_class($args['size']) ?>" itemscope itemtype="http://schema.org/ImageGallery">
    <?php if (!empty($attachments)) :
      $i = 0;
      foreach ($attachments as $aid => $attachment) :
        $i++;
        $thumb = wp_get_attachment_image_src($aid , 'photoswipe_thumbnails');
        $full = wp_get_attachment_image_src($aid , 'photoswipe_full');
        $_post = get_post($aid);
        $image_alttext = get_post_meta($aid, '_wp_attachment_image_alt', true);
        $image_caption = $_post->post_excerpt;
        ?>
        <figure class="msnry_item" itemscope itemtype="http://schema.org/ImageObject" <?= ($i > $args['item_count'] ? 'style="display:none;"' : '') ?>>
          <a href="<?= $full[0] ?>" itemprop="contentUrl" data-size="<?= $full[1] . 'x' . $full[2] ?>" data-caption="<?= $image_caption ?>">
            <img
            data-src="<?= $thumb[0] ?>"
            src="<?= ($i <= $args['item_count'] ? $thumb[0] : '') ?>"
            itemprop="thumbnail"
            alt="<?= $image_alttext ?>" />
          </a>
          <figcaption class="photoswipe-gallery-caption"><?= $image_caption ?></figcaption>
        </figure>
      <?php endforeach;
    endif; ?>
  </div>
  <div style='clear:both'></div>
  <?php
  return ob_get_clean();
}
