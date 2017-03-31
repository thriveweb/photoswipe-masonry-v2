<?php

class Regenerate_Thumbnails {

  public static function get_regeneration_form() {
    ob_start();
    ?>
    <form id="rt-form" method="post" action="" style="display:none;visibility:hidden;opacity:0;">
      <?php wp_nonce_field('regenerate-thumbnails') ?>
      <p>
        <input type="submit" name="regenerate-thumbnails" value="Regenerate All Thumbnails" />
      </p>
    </form>
    <?php
    return ob_get_clean();
  }

  public static function get_start_regeneration_button() {
    ob_start();
    if ((empty($_POST['regenerate-thumbnails']) && isset($_POST['show-regenerate-button'])) || (empty($_POST['regenerate-thumbnails']) && isset($_SESSION['show_regenerate_thumbnail_button']) && $_SESSION['show_regenerate_thumbnail_button'])) {
      $_SESSION['show_regenerate_thumbnail_button'] = true;
      ?>
      <button class="button hide-if-no-js" id="rt">Regenerate All Thumbnails</button>
      <script>
      jQuery(function($) {
        $('#rt').on('click', function(e) {
          e.preventDefault();
          $('#rt-form input[type="submit"]').trigger('click');
        });
      });
      </script>
      <?php
    } else {
      ?>
      <button class="button hide-if-no-js" id="rt-stop">Abort Resizing</button>
      <script>
      jQuery(function($) {
        $('#rt-stop').on('click', function(e) {
          e.preventDefault();
          $('#regenthumbs-stop').trigger('click');
          $(this).html('Stopping...');
        });
      });
      </script>
      <?php
    }
    return ob_get_clean();
  }

  public static function regenerate_thumbnails_log() {
    global $wpdb;
    ob_start();
    // If the button was clicked
    if (!empty($_POST['regenerate-thumbnails'])) {
      ?>
      <div id="message" class="updated fade" style="display:none"></div>
      <div class="wrap regenthumbs">
        <?php
        // Get all attachment ids from database
        if (!$images = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID DESC")) {
          echo '<p>' . sprintf(__("Unable to find any images. Are you sure <a href='%s'>some exist</a>?", 'regenerate-thumbnails'), admin_url( 'upload.php?post_mime_type=image' ) ) . "</p></div>";
          return;
        }
        // Generate the list of IDs
        $ids = array();
        foreach ($images as $image) {
          $ids[] = $image->ID;
        }
        $ids = implode(',', $ids);
        echo '<p>' . __( "Please be patient while the thumbnails are regenerated. This can take a while if your server is slow (inexpensive hosting) or if you have many images. Do not navigate away from this page until this script is done or the thumbnails will not be resized. You will be notified via this page when the regenerating is completed.", 'regenerate-thumbnails' ) . '</p>';
        $count = count($images);
        $text_goback = (!empty($_GET['goback'])) ? sprintf( __( 'To go back to the previous page, <a href="%s">click here</a>.', 'regenerate-thumbnails'), 'javascript:history.go(-1)') : '';
        $text_failures = sprintf( __('All done! %1$s image(s) were successfully resized in %2$s seconds and there were %3$s failure(s). To try regenerating the failed images again, <a href="%4$s">click here</a>. %5$s', 'regenerate-thumbnails' ), "' + rt_successes + '", "' + rt_totaltime + '", "' + rt_errors + '", esc_url( wp_nonce_url( admin_url( 'tools.php?page=regenerate-thumbnails&goback=1' ), 'regenerate-thumbnails' ) . '&ids=' ) . "' + rt_failedlist + '", $text_goback );
        $text_nofailures = sprintf( __( 'All done! %1$s image(s) were successfully resized in %2$s seconds and there were 0 failures. %3$s', 'regenerate-thumbnails' ), "' + rt_successes + '", "' + rt_totaltime + '", $text_goback );
        ?>

        <noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', 'regenerate-thumbnails' ) ?></em></p></noscript>
        <div id="regenthumbs-bar" style="position:relative;height:25px;">
          <div id="regenthumbs-bar-percent" style="position:absolute;left:50%;top:50%;width:300px;margin-left:-150px;height:25px;margin-top:-9px;font-weight:bold;text-align:center;"></div>
        </div>
        <p><input type="button" class="button hide-if-no-js" name="regenthumbs-stop" id="regenthumbs-stop" value="<?php _e( 'Abort Resizing Images', 'regenerate-thumbnails' ) ?>" style="display:none;visibility:hidden;opacity:0;" /></p>
        <h3 class="title"><?php _e( 'Debugging Information', 'regenerate-thumbnails' ) ?></h3>
        <p>
          <?php printf( __( 'Total Images: %s', 'regenerate-thumbnails' ), $count ); ?><br />
          <?php printf( __( 'Images Resized: %s', 'regenerate-thumbnails' ), '<span id="regenthumbs-debug-successcount">0</span>' ); ?><br />
          <?php printf( __( 'Resize Failures: %s', 'regenerate-thumbnails' ), '<span id="regenthumbs-debug-failurecount">0</span>' ); ?>
        </p>
        <ol id="regenthumbs-debuglist">
          <li style="display:none"></li>
        </ol>

        <script type="text/javascript">
        jQuery(document).ready(function($){
          var i,
          rt_images = [<?php echo $ids; ?>],
          rt_total = rt_images.length,
          rt_count = 1,
          rt_percent = 0,
          rt_successes = 0,
          rt_errors = 0,
          rt_failedlist = '',
          rt_resulttext = '',
          rt_timestart = new Date().getTime(),
          rt_timeend = 0,
          rt_totaltime = 0,
          rt_continue = true;

          // Create the progress bar
          $("#regenthumbs-bar").progressbar();
          $("#regenthumbs-bar-percent").html( "0%" );

          // Stop button
          $("#regenthumbs-stop").click(function() {
            rt_continue = false;
            $('#regenthumbs-stop').val("<?php echo self::esc_quotes( __( 'Stopping...', 'regenerate-thumbnails' ) ); ?>");
          });

          // Clear out the empty list element that's there for HTML validation purposes
          $("#regenthumbs-debuglist li").remove();

          // Called after each resize. Updates debug information and the progress bar.
          function RegenThumbsUpdateStatus( id, success, response ) {
            $("#regenthumbs-bar").progressbar( "value", ( rt_count / rt_total ) * 100 );
            $("#regenthumbs-bar-percent").html( Math.round( ( rt_count / rt_total ) * 1000 ) / 10 + "%" );
            rt_count = rt_count + 1;

            if ( success ) {
              rt_successes = rt_successes + 1;
              $("#regenthumbs-debug-successcount").html(rt_successes);
              $("#regenthumbs-debuglist").append("<li>" + response.success + "</li>");
            } else {
              rt_errors = rt_errors + 1;
              rt_failedlist = rt_failedlist + ',' + id;
              $("#regenthumbs-debug-failurecount").html(rt_errors);
              $("#regenthumbs-debuglist").append("<li>" + response.error + "</li>");
            }
          }

          // Called when all images have been processed. Shows the results and cleans up.
          function RegenThumbsFinishUp() {
            rt_timeend = new Date().getTime();
            rt_totaltime = Math.round( ( rt_timeend - rt_timestart ) / 1000 );

            $('#regenthumbs-stop').hide();

            if ( rt_errors > 0 ) {
              rt_resulttext = '<?php echo $text_failures; ?>';
            } else {
              rt_resulttext = '<?php echo $text_nofailures; ?>';
            }

            $("#message").html("<p><strong>" + rt_resulttext + "</strong></p>");
            $("#message").show();
          }

          // Regenerate a specified image via AJAX
          function RegenThumbs( id ) {
            $.ajax({
              type: 'POST',
              url: ajaxurl,
              data: { action: "regeneratethumbnail", id: id },
              success: function( response ) {
                if ( response !== Object( response ) || ( typeof response.success === "undefined" && typeof response.error === "undefined" ) ) {
                  response = new Object;
                  response.success = false;
                  response.error = "<?php printf( esc_js( __( 'The resize request was abnormally terminated (ID %s). This is likely due to the image exceeding available memory or some other type of fatal error.', 'regenerate-thumbnails' ) ), '" + id + "' ); ?>";
                }

                if ( response.success ) {
                  RegenThumbsUpdateStatus( id, true, response );
                } else {
                  RegenThumbsUpdateStatus( id, false, response );
                }

                if ( rt_images.length && rt_continue ) {
                  RegenThumbs( rt_images.shift() );
                } else {
                  RegenThumbsFinishUp();
                }
              },
              error: function( response ) {
                RegenThumbsUpdateStatus( id, false, response );

                if ( rt_images.length && rt_continue ) {
                  RegenThumbs( rt_images.shift() );
                } else {
                  RegenThumbsFinishUp();
                }
              }
            });
          }

          RegenThumbs( rt_images.shift() );
        });
        </script>
      </div>
      <?php
    }
    return ob_get_clean();
  }

  // Process a single image ID (this is an AJAX handler)
  public function ajax_process_image() {
    @error_reporting( 0 ); // Don't break the JSON result

    header( 'Content-type: application/json' );

    $id = (int) $_REQUEST['id'];
    $image = get_post( $id );

    unset($_SESSION['show_regenerate_thumbnail_button']);

    if ( ! $image || 'attachment' != $image->post_type || 'image/' != substr( $image->post_mime_type, 0, 6 ) ) {
      die( json_encode( array( 'error' => sprintf( __( 'Failed resize: %s is an invalid image ID.', 'regenerate-thumbnails' ), esc_html( $_REQUEST['id'] ) ) ) ) );
    }

    $fullsizepath = get_attached_file( $image->ID );

    if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ) {
      self::die_json_error_msg( $image->ID, sprintf( __( 'The originally uploaded image file cannot be found at %s', 'regenerate-thumbnails' ), '<code>' . esc_html( $fullsizepath ) . '</code>' ) );
    }

    @set_time_limit( 900 ); // 5 minutes per image should be PLENTY

    $metadata = wp_generate_attachment_metadata( $image->ID, $fullsizepath );

    if ( is_wp_error( $metadata ) ) {
      self::die_json_error_msg( $image->ID, $metadata->get_error_message() );
    }
    if ( empty( $metadata ) ) {
      self::die_json_error_msg( $image->ID, __( 'Unknown failure reason.', 'regenerate-thumbnails' ) );
    }

    // If this fails, then it just means that nothing was changed (old value == new value)
    wp_update_attachment_metadata( $image->ID, $metadata );

    die( json_encode( array( 'success' => sprintf( __( '&quot;%1$s&quot; (ID %2$s) was successfully resized in %3$s seconds.', 'regenerate-thumbnails' ), esc_html( get_the_title( $image->ID ) ), $image->ID, timer_stop() ) ) ) );
  }

  // Helper to make a JSON error message
  private static function die_json_error_msg( $id, $message ) {
    die( json_encode( array( 'error' => sprintf( __( '&quot;%1$s&quot; (ID %2$s) failed to resize. The error message was: %3$s', 'regenerate-thumbnails' ), esc_html( get_the_title( $id ) ), $id, $message ) ) ) );
  }

  // Helper function to escape quotes in strings for use in Javascript
  private static function esc_quotes( $string ) {
    return str_replace( '"', '\"', $string );
  }
}
