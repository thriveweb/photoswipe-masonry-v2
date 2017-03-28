<?php

function get_script($post_id, $args, $photoswipe_options) {
  ob_start();
  ?>
  <script type='text/javascript'>
  var container_<?= $post_id ?> = document.querySelector('#psgal_<?= $post_id ?>');
  var grid_<?= $post_id ?>;
  <?php if(!$photoswipe_options['use_masonry']) : ?>
  grid_<?= $post_id ?> = jQuery('#psgal_<?= $post_id ?>').masonry({
    itemSelector: '.msnry_item',
    isFitWidth: true
  });
  grid_<?= $post_id ?>.imagesLoaded().progress( function() {
    grid_<?= $post_id ?>.masonry('layout');
  });
  <?php endif; ?>
  if (jQuery('#psgal_<?= $post_id ?> .msnry_item:last-of-type').index() + 1 > <?= $args['item_count'] ?>) {
    var loadCount_<?= $post_id ?> = 1,
    loadingImages_<?= $post_id ?> = false;
    jQuery(document).on('scroll', function() {
      var lastImgNth_<?= $post_id ?> = (<?= $args['item_count'] ?> * loadCount_<?= $post_id ?>),
      lastImg_<?= $post_id ?> = jQuery('#psgal_<?= $post_id ?> .msnry_item:nth-child(' + lastImgNth_<?= $post_id ?> + ')');
      if (!loadingImages_<?= $post_id ?> && (lastImg_<?= $post_id ?>.length && (jQuery(document).scrollTop() + (jQuery(window).height() / 2)) >= (lastImg_<?= $post_id ?>.offset().top + lastImg_<?= $post_id ?>.height()))) {
        loadCount_<?= $post_id ?>++;
        loadingImages_<?= $post_id ?> = true;
        for (var i = 1; i <= <?= $args['item_count'] ?>; i++) {
          if (i >= (jQuery('#psgal_<?= $post_id ?> .msnry_item:last-of-type').index() + 1)) {
            return;
          }
          var img = jQuery('#psgal_<?= $post_id ?> .msnry_item:nth-child(' + (lastImgNth_<?= $post_id ?> + i) + ') a img');
          img.attr('src', img.data('src'));
        }
        <?php if(!$photoswipe_options['use_masonry']) : ?>
        grid_<?= $post_id ?>.imagesLoaded().progress(function() {
          jQuery('#psgal_<?= $post_id ?> .msnry_item').css('display', 'block');
          grid_<?= $post_id ?>.masonry('layout');
          loadingImages_<?= $post_id ?> = false;
        });
        <?php endif; ?>
      }
    });
  }
  </script>
  <?php
  return ob_get_clean();
}
