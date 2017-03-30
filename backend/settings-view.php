<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function show_settings_view($options) {
	ob_start();
	echo Regenerate_Thumbnails::get_regeneration_form();
	?>
	<div id="photoswipe_admin" class="wrap">
		<div class="head">
			<h2>PhotoSwipe Masonry Options</h2>
			<p class="info">
				PhotoSwipe Masonry is a image gallery plugin for WordPress built using PhotoSwipe from Dmitry Semenov. <a href="http://photoswipe.com/" target="_blank">PhotoSwipe</a><br />
				Plugin by <a href="https://thriveweb.com.au/" title="Thrive Web" target="_blank">Thrive Web</a>, <a href="https://thriveweb.com.au/the-lab/" target="_blank">see our plugins</a>.
			</p>
			<a class="logo" href="https://thriveweb.com.au/" title="Thrive Web" target="_blank">
				<img src="<?= plugins_url('photoswipe-masonry/thrive-logo.png') ?>" alt="Logo Thrive Web" />
			</a>
		</div>
		<form method="post" action="#" enctype="multipart/form-data">
			<div class="block left">
				<div class="block-head">
					Sizing
				</div>
				<div class="block-content">
					<div>
						<p>Thumbnail Width</p>
						<p>
							<input type="text" name="thumbnail_width" value="<?= ($options['thumbnail_width']); ?>" />
						</p>
					</div>
					<div>
						<p>Thumbnail Height</p>
						<p>
							<input type="text" name="thumbnail_height" value="<?= ($options['thumbnail_height']); ?>" />
						</p>
					</div>
					<div>
						<p>Max image width</p>
						<p>
							<input type="text" name="max_image_width" value="<?= ($options['max_image_width']); ?>" />
						</p>
					</div>
					<div>
						<p>Max image height</p>
						<p>
							<input type="text" name="max_image_height" value="<?= ($options['max_image_height']); ?>" />
						</p>
					</div>
					<div>
						<p>Thumbnails per page</p>
						<input name="item_count" type="number" value="<?= $options['item_count'] ? $options['item_count'] : 10 ?>" max="500" />
					</div>
					<?php
					echo Regenerate_Thumbnails::get_start_regeneration_button();
					?>
				</div>
			</div>
			<div class="block right">
				<div class="block-head">
					Settings
				</div>
				<div class="block-content">
					<p>
						<label>
							<input name="white_theme" type="checkbox" value="checkbox" <?= $options['white_theme'] ? "checked='checked'" : '' ?> />
							Use white theme?
						</label>
					</p>
					<p>
						<label>
							<input name="crop_thumbnails" type="checkbox" value="checkbox" <?= $options['crop_thumbnails'] ? "checked='checked'" : '' ?> />
							Crop thumbnails?
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
							Use Masonry?
						</label>
					</p>
					<p>
						<input class="button-primary" type="submit" name="photoswipe_save" value="Save Changes" />
					</p>
				</div>
			</div>
		</form>
		<?php
		echo Regenerate_Thumbnails::regenerate_thumbnails_log();
		?>
	</div>
	<p class="made-by">
		Made with
		<svg width="12" height="12" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
			<path fill="red" d="M12 4.168C7.943-2.045 0 2.028 0 7.758c0 4.418 6.904 8.223 12 15.187 5.094-6.963 12-10.768 12-15.187 0-5.73-7.693-9.803-12-3.59" />
		</svg>
		on the <a class="logo" href="https://thriveweb.com.au/" title="Thrive Web" target="_blank">Gold Coast</a>
	</p>
	<?php
	return ob_get_clean();
}
