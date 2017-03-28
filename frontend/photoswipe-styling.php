<?php

function get_style($photoswipe_options) {
  ob_start();
  ?>
  <style type='text/css'>
  .psgal {
    margin: auto;
    padding-bottom: 40px;
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
    <?php if($photoswipe_options['use_masonry']) : ?>
    opacity: 1;
    text-align: center;
    <?php endif; ?>
  }

  .psgal.photoswipe_showme{
    opacity: 1;
  }

  .psgal figure {
    float: left;
    <?php if($photoswipe_options['use_masonry']) : ?>
    float: none;
    display: inline-block;
    <?php endif; ?>
    text-align: center;
    width: <?= $photoswipe_options['thumbnail_width'] . 'px' ?>;
    padding: 5px;
    margin: 0px;
    box-sizing: border-box;
  }

  .psgal a{
    display: block;
  }

  .psgal img {
    margin: auto;
    max-width: 100%;
    width: auto;
    height: auto;
    border: 0;
  }

  .psgal figure figcaption{
    font-size: 13px;
  }

  .msnry{
    margin: auto;
  }

  .pswp__caption__center{
    text-align: center;
  }

  <?php if(!$photoswipe_options['show_captions']) : ?>
  .photoswipe-gallery-caption{
    display: none;
  }
  <?php endif; ?>
  </style>
  <?php
  return ob_get_clean();
}
