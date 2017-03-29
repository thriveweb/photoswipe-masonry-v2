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
      jQuery(document).on('scroll', function() {
        if (!images_loading) {
          var last_img_nth = (items_per_page * load_count);
          var $last_img = jQuery(gallery_selector + ' ' + masonry_options.itemSelector + ':nth-child(' + last_img_nth + ')');
          if ($last_img.length && (jQuery(document).scrollTop() + (jQuery(window).height() / 2)) >= ($last_img.offset().top + $last_img.height())) {
            load_count++;
            images_loading = true;
            for (var i = 1; i <= items_per_page; i++) {
              if (i >= (jQuery(gallery_selector + ' ' + masonry_options.itemSelector + ':last-of-type').index() + 1)) {
                return;
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
          }
        }
      });
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
