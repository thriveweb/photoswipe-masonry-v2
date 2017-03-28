<?php

function get_backend_head() {
  ob_start(); ?>
  <link rel="stylesheet" type="text/css" href="<?= plugins_url( '/css/style.css', __FILE__ ) ?>" />
  <?php
  return ob_get_clean();
}
