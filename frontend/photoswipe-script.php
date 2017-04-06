<?php

function get_script($post_id, $args, $photoswipe_options) {
	ob_start();
	?>
	<script type='text/javascript'>
	<?php if($photoswipe_options['use_masonry']) : 
	$masonry_options = "
		itemSelector: '.msnry_item',
		isFitWidth: true
	";
	$masonry_options = apply_filters( 'photoswipe_masonry_options', $masonry_options );
	?>
	var psm_gallery_<?= $post_id ?> = new psm_gallery(
		'#psgal_<?= $post_id ?>',
		<?= $args['item_count'] ?>,
		{
			<?= $masonry_options ?>
		}
	);
	psm_gallery_<?= $post_id ?>.init();
	<?php endif; ?>
	</script>
	<?php
	return ob_get_clean();
}
