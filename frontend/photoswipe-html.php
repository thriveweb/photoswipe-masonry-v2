<?php

function get_html($post_id, $columns, $args, $attachments = array(), $photoswipe_options = array()) {
  ob_start();
  ?>
  <div style="clear:both"></div>
  <div id="psgal_<?= $post_id ?>" class="psgal gallery-columns-<?= $columns ?> gallery-size-<?= sanitize_html_class($args['size']) ?>" itemscope itemtype="http://schema.org/ImageGallery" data-cropped-thumbnails="<?= $photoswipe_options['crop_thumbnails'] ? 'true' : 'false' ?>">
    <?php if (!empty($attachments)) :
      $i = 0;
      foreach ($attachments as $aid => $attachment) :
        $i++;
        $thumb = wp_get_attachment_image_src($aid , apply_filters( 'photoswipe_thumbnail_size', 'photoswipe_thumbnails') );
        $full = wp_get_attachment_image_src($aid , 'photoswipe_full');
        $_post = get_post($aid);
        $image_alttext = get_post_meta($aid, '_wp_attachment_image_alt', true);
        $image_caption = $_post->post_excerpt;
        ?>
        <figure class="msnry_item" itemscope itemtype="http://schema.org/ImageObject" <?= ($photoswipe_options['use_masonry'] && $i > $args['item_count'] ? 'style="display:none;"' : '') ?>>
          <a href="<?= $full[0] ?>" itemprop="contentUrl" data-size="<?= $full[1] . 'x' . $full[2] ?>" data-caption="<?= $image_caption ?>">
            <img
            data-src="<?= $thumb[0] ?>"
            src="<?= ($i <= $args['item_count'] || !$photoswipe_options['use_masonry'] ? $thumb[0] : '') ?>"
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
