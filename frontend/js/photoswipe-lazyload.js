var psm_gallery = function(gallery_selector, items_per_page, masonry_options) {
  if (!(this instanceof psm_gallery)) {
    return new psm_gallery(gallery_selector, masonry_options);
  }
  var gallery_selector = gallery_selector;
  var items_per_page = items_per_page;
  var masonry_options = masonry_options;
  var grid = false;
  var load_count = 1;
  var images_loading = false;
  function init_grid() {
    grid = jQuery(gallery_selector).masonry(masonry_options);
    grid_loaded(function() {
      grid.masonry('layout');
    });
  }
  function init_lazyload() {
    if ((jQuery(gallery_selector + ' ' + masonry_options.itemSelector + ':last-of-type').index() + 1) > load_count) {
      jQuery(gallery_selector + ' button.psgal_load_more').on('click', load_images);
    }
  }
  function load_images() {
    if (!images_loading) {
      var last_img_nth = (items_per_page * load_count);
      var $last_img = jQuery(gallery_selector + ' ' + masonry_options.itemSelector + ':nth-child(' + last_img_nth + ')');
      if ($last_img.length) {
        load_count++;
        images_loading = true;
        for (var i = 1; i <= items_per_page; i++) {
          if ((((load_count - 1) * items_per_page) + i) >= (jQuery(gallery_selector + ' ' + masonry_options.itemSelector + ':last-of-type').index() + 1)) {
            jQuery(gallery_selector + ' button.psgal_load_more').fadeOut();
            break;
          }
          var $img = jQuery(gallery_selector + ' ' + masonry_options.itemSelector + ':nth-child(' + (last_img_nth + i) + ') a img');
          $img.attr('src', $img.data('src'));
          jQuery(gallery_selector + ' ' + masonry_options.itemSelector + ':nth-child(' + (last_img_nth + i)).css('display', 'block');
        }
        grid_loaded(function() {
          grid.masonry('layout');
          setTimeout(function() {
            images_loading = false;
          }, 1000);
        });
      } else {
        jQuery(gallery_selector + ' button.psgal_load_more').fadeOut();
      }
    }
  }
  function grid_loaded(callback) {
    grid.imagesLoaded().progress(callback);
  }
  this.init = function () {
    init_grid();
    init_lazyload();
  }
};
