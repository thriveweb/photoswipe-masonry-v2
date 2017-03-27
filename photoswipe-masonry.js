jQuery(function($) {
	photoswipe_masonry($);
});

var photoswipe_masonry = function($){

  var $pswp = $('.pswp')[0];
  var image = [];

  /////////////////////////////////////////////////////////////////////////////////////////////
  // Gallery
    //in this case the psgal class is actually on the <body> 
    //so only the first gallery is used to swipe through all the images
    //If you want separate galleries within article gallery of images in the text, 
    //then you need to decide how to handle nested galleries and image index numbers
    var $psgal     = $('.psgal').first();
    $galleryUID = $psgal.attr('id');
    if (undefined===$galleryUID) {
      $psgal.attr('id', 'psgalmain');
      $galleryUID = 'psgalmain';
    }
    getItems = function() {
      var items = [];
      $psgal.find('a').each(function() {
        //only if the link actually contains an image
        var $img = $(this).find('img').first();
        if ($img.length){
          var $href   = $(this).attr('href');
          var $ext = $href.split('.').pop();
          switch($ext) {
          case "jpg":
          case "png":
          case "jpeg":

            //default src width and height: TODO: alternative way of getting real dimensions if size missing
            var $width = 1800;
            var $height = 1800;
            var $size   = $(this).attr('data-size');
            if ($size){
              $size=$size.split('x');
              if ($size.length>1){
                $width  = $size[0];
                $height = $size[1];
              }
            }
            else{
              //if no saved size, use the ratio of the small image as basis for calculation..
              //..works great for images within an article
              var $imgWidth = $img.attr('width');
              var $imgHeight = $img.attr('height');
              try {
                $height=$width*$imgHeight/$imgWidth;
              }
              catch(e){}
            }
            
            //ADDED: set item photoswipe index to avoid recalculating it later
            $(this).attr('data-psindex', items.length);

            //$img = $(this).find('img');
            $title = $(this).attr('data-caption');
            if (!($title)) {
              $figcaption = $(this).find('figcaption');
              $title = $figcaption.val();
              if (!($title)) {
                $title= $img.attr('title');
                if (!($title)) {
                  $title= $img.attr('alt');
                  if (!($title)) {
                    $title=$href.substring($href.lastIndexOf('/')+1);
                  }
                }
              }
            };

              
            var item = {
              src 	: $href,
              w   	: $width, 
              h   	: $height,
              el		: $(this),
              msrc	: $img.attr('src'),
              title	: $title
            }
            items.push(item);

          break;
          default:
            //If the link is to a separate document, don't use the photoswipe
            //(unless the photoswipe supports popup pages not just images
            // - actually http://photoswipe.com/ does support html in the gallery, 
            // that could be a cool enhancement late..)
          }
        }
      });
      return items;
    }

    var items = getItems();
    $.each(items, function(index, value) {
      image[index]     = new Image();
      image[index].src = value['src'];
    });

    $psgal.on('click', 'a[data-psindex]', function(event) {

      event.preventDefault();
      var $index = 0;
      try{
        $index=Number($(this).attr('data-psindex'));
      }
      catch(e){
      }
      
      var options = {
        index: $index,
        bgOpacity: 0.9,
        showHideOpacity: false,
        galleryUID: $galleryUID,
        getThumbBoundsFn: function(index) {
          var image = items[index].el.find('img'),
          offset = image.offset();
          return {x:offset.left, y:offset.top, w:image.width()};
        }
      }

      var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
      lightBox.init();

    });

	/////////////////////////////////////////////////////////////////////////////////////////////
	// Parse URL and open gallery if it contains #&pid=3&gid=1
	var hashData = parseHash();

	if(hashData.gid) {

		$('#' + hashData.gid).each( function() {

      $index=Number(hashData.pid);

      try{
        
      }
      catch(e){
      }

			var options = {
				index: $index,
				bgOpacity: 0.9,
				showHideOpacity: false,
				galleryUID: hashData.gid,
				getThumbBoundsFn: function(index) {
					var image = items[index].el.find('img'),
					offset = image.offset();
					return {x:offset.left, y:offset.top, w:image.width()};
				}
			}

			var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
			lightBox.init();

		});
	}
};

 




var parseHash = function() {

	var hash = window.location.hash.substring(1),
	params = {};

	if(hash.length < 5) {
		return params;
	}

	var vars = hash.split('&');
	for (var i = 0; i < vars.length; i++) {
		if(!vars[i]) {
			continue;
		}
		var pair = vars[i].split('=');
		if(pair.length < 2) {
			continue;
		}
		params[pair[0]] = pair[1];
	}

	params.pid = parseInt(params.pid, 10);
	return params;
};
