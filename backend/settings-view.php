<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function show_settings_view($options) {
  ob_start();
  ?>
  <div id="photoswipe_admin" class="wrap">
    <h2>PhotoSwipe Options</h2>
    <p>
      PhotoSwipe is a image gallery plugin for WordPress built using PhotoSwipe from  Dmitry Semenov.  <a href="http://photoswipe.com/">PhotoSwipe</a>
    </p>
    <form method="post" action="#" enctype="multipart/form-data">
      <div class="ps_border" ></div>
      <p style="font-style:italic; font-weight:normal; color:grey " >
        Please note: Images that are already on the server will not change size until you regenerate the thumbnails. Use <a title="http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/" href="http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/">AJAX thumbnail rebuild</a>
      </p>
      <div class="fl_box">
        <p>Thumbnail Width</p>
        <p>
          <input type="text" name="thumbnail_width" value="<?= ($options['thumbnail_width']); ?>" />
        </p>
      </div>
      <div class="fl_box">
        <p>Thumbnail Height</p>
        <p>
          <input type="text" name="thumbnail_height" value="<?= ($options['thumbnail_height']); ?>" />
        </p>
      </div>
      <div class="fl_box">
        <p>Max image width</p>
        <p>
          <input type="text" name="max_image_width" value="<?= ($options['max_image_width']); ?>" />
        </p>
      </div>
      <div class="fl_box">
        <p>Max image height</p>
        <p>
          <input type="text" name="max_image_height" value="<?= ($options['max_image_height']); ?>" />
        </p>
      </div>
      <div class="ps_border" ></div>
      <p>
        <label>
          <input name="item_count" type="number" value="<?= $options['item_count'] ? $options['item_count'] : 10 ?>" max="500" />
          Thumbnails per page
        </label>
      </p>
      <p>
        <label>
          <input name="white_theme" type="checkbox" value="checkbox" <?= $options['white_theme'] ? "checked='checked'" : '' ?> />
          Use white theme?
        </label>
      </p>
      <p>
        <label>
          <input name="show_captions" type="checkbox" value="checkbox" <?= $options['show_captions'] ? "checked='checked'" :  ''; ?> />
          Show captions on thumbnails?
        </label>
      </p>
      <p>
        <label>
          <input name="use_masonry" type="checkbox" value="checkbox" <?= $options['use_masonry'] ? "checked='checked'" : '' ?> />
          Don't use Masonry?
        </label>
      </p>
      <p>
        <input class="button-primary" type="submit" name="photoswipe_save" value="Save Changes" />
      </p>
    </form>
  </div>
  <?php
  return ob_get_clean();
}
