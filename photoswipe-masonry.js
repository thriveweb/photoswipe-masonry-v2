jQuery(function($) {
	
	var $pswp = $('.pswp')[0];
    var image = [];

    jQuery('.photoswipe_gallery').each( function() {
        
        console.log('psgal');
        
        var $pic     = $(this),        
        getItems = function() {
            
            var items = [];
            
            $pic.find('a').each(function() {
                var $href   = jQuery(this).attr('href'),
                    $size   = jQuery(this).data('size').split('x'),
                    $width  = $size[0],
                    $height = $size[1];

                var item = {
                    src : $href,
                    w   : $width,
                    h   : $height,
                    el	: $(this),
                    msrc: $(this).find('img').attr('src')
                }
				
				console.log($(this).find('img'));				
				
                items.push(item);
            });
            return items;
        }

        var items = getItems();
        
        console.log(items);

        $.each(items, function(index, value) {
            image[index]     = new Image();
            image[index].src = value['src'];
        });

        $pic.on('click', 'figure', function(event) {
            
            event.preventDefault();
            
            //console.log(jQuery(this).index());
             
            var $index = jQuery(this).index();
            
            //console.log($index);
            //return;
            
            
            var testfunc = function(index) {
					
					var image = items[index].el.find('img');
					
					//console.log(image.offset());
					
					offset = image.offset();
					console.log(offset.left, offset.top, image.width());
			}
			testfunc($index);
						
            
            var options = {
                index: $index,
                bgOpacity: 0.9,
                showHideOpacity: false,
                getThumbBoundsFn: function(index) {
					var image = items[index].el.find('img'),
					offset = image.offset();
					return {x:offset.left, y:offset.top, w:image.width()};
				}
            }

            var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
            lightBox.init();
        });     
                
    });
    

	
	
	
    jQuery('.single_photoswipe').each( function() {
        
        console.log('ps_single');
        
        var $pic     = $(this),
        
        getItems = function() {
            var items = [];
            $pic.each(function() {
                var $href   = jQuery(this).attr('href'),
                    $size   = jQuery(this).data('size').split('x'),
                    $width  = $size[0],
                    $height = $size[1];

                var item = {
                    src : $href,
                    w   : $width,
                    h   : $height,
                    el	: $(this),
                    msrc: $(this).find('img').attr('src')
                }

                items.push(item);
            });
            return items;
        }

        var items = getItems();
        
        console.log(items);

        $.each(items, function(index, value) {
            image[index]     = new Image();
            image[index].src = value['src'];
        });

        $pic.on('click', 'img', function(event) {
            
            event.preventDefault();
            
            //console.log(jQuery(this).index());
             
            var $index = jQuery(this).index();
            
            console.log($index);
            //return;
            
            var options = {
                index: $index,
                //bgOpacity: 0.9,
                //showHideOpacity: true,
                getThumbBoundsFn: function(index) {
					var image = items[index].el.find('img'),
					offset = image.offset();
					return {x:offset.left, y:offset.top, w:image.width()};
				}
            }

            var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
            lightBox.init();
        });     
                
    });
    
    
    

});
