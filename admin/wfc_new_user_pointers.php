<?php
/**
 *
 * @package scf-framework
 * @author Steve (9/02/2012)
 * @version 2.2
 */


class wfc_pointers_class {

   function __construct() {
      add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue' ) );
   }

   function enqueue() {
      $options = get_option('wfc_new_user_pointer');
      if ( isset( $_GET['wfc_point_on'] ) ) {
         $options['wfc_status'] = 'wfc_pointers_on';
         update_option( 'wfc_new_user_pointer', $options );
      }else if ( isset( $_GET['wfc_point_off'] ) ) {
         $options['wfc_status'] = 'wfc_pointers_off';
         update_option( 'wfc_new_user_pointer', $options );
      }else{ //first time
         $options = array('wfc_status' => 'wfc_pointers_on');
         update_option( 'wfc_new_user_pointer', $options );
      }

      if ( $options['wfc_status'] == 'wfc_pointers_on' ) {
         wp_enqueue_style( 'wp-pointer' );
         wp_enqueue_script( 'wp-pointer' );
         add_action( 'admin_print_footer_scripts', array( &$this, 'print_scripts' ));
      }
   }

   function print_scripts() {
      global $pagenow, $current_user;
      $ad = 'scf-framework'; //$ad = 'Admin Directory'
      $adminpages = array(
         '/'.$ad.'/wp-admin/' => array(
            'content'  => '<h3>WFC First</h3><p>Dashboard</p>'  ,
            'button2'  => 'Next',
            'anchor'   => '#menu-dashboard',
            'function' => 'window.location="'.admin_url('/profile.php').'";'
         ),
         '/'.$ad.'/wp-admin/index.php' => array(
            'content'  => '<h3>WFC First</h3><p>Dashboard</p>'  ,
            'button2'  => 'Next',
            'anchor'   => '#menu-dashboard',
            'function' => 'window.location="'.admin_url('/profile.php').'";'
         ),
         '/'.$ad.'/wp-admin/profile.php' => array(
            'content'  => '<h3>WFC Second</h3><p>User Profile: Change password, email, bio</p>'  ,
            'button2'  => 'Next',
            'anchor'   => '#menu-users',
            'function' => 'window.location="'.admin_url('/edit.php?post_type=campaign').'";'
         ),
         '/'.$ad.'/wp-admin/edit.php?post_type=campaign' => array(
            'content'  => '<h3>WFC Forth</h3><p>Manage Home Page Slides</p>'  ,
            'button2'  => 'Next',
            'anchor'   => '#menu-posts-campaign',
            'function' => 'window.location="'.admin_url('/post-new.php?post_type=campaign#postimagediv').'";'
         ),
         '/'.$ad.'/wp-admin/post-new.php?post_type=campaign#postimagediv' => array(
            'content'  => '<h3>WFC 5</h3><p>Upload a new image</p>',
            'anchor'   => '#postimagediv',
            'button2'  => 'Next',
            'function' => "$('#postimagediv').pointer('close');$('#publish').pointer('open');",
            'position_at_edge' => 'bottom',
            'position_at_align' => 'left'
         ),
         '/'.$ad.'/wp-admin/post-new.php?post_type=campaign#publish' => array(
            'content'  => '<h3>WFC 6</h3><p>When you are done click Publish here. If you want to continue working on this page and do not want it visible to the public click the "Save Draft" above. </p>',
            'anchor'   => '#publish',
            'button2'  => 'Next',
            'function' => "alert('all done');$('#publish').pointer('close');$('#titlewrap #title').pointer('open');",
            'position_at_edge' => 'right',
            'position_at_align' => 'center'
         ),
         '/'.$ad.'/wp-admin/post-new.php?post_type=campaign#title' => array(
            'content'  => '<h3>WFC 7</h3><p>Enter title for page</p>',
            'anchor'   => '#titlewrap #title',
            'button2'  => 'Next',
            'function' => "$('#titlewrap #title').pointer('close');",
            'position_at_edge' => 'top',
            'position_at_align' => 'center'
         )
      );


      $page = '';
      $page = $_SERVER['REQUEST_URI'];

      if ( 'admin.php' != $pagenow || !array_key_exists( $page, $adminpages ) ) {

            /**
             * add loop so a page can have multiple boxes
             */
            $counter = 0;
            foreach($adminpages as $k => $adminpage){
               $a = explode("#",$k);
               if ( '' != $page && $page == $a[0]){
                  $id         = 'wpseo_content_top'.$a[1];
                  $anchor        = $adminpage['anchor'];
                  $content       = $adminpage['content'];
                  $position_at_edge   = isset($adminpage['position_at_edge']) ? $adminpage['position_at_edge'] : 'left';
                  $position_at_align   = isset($adminpage['position_at_align']) ? $adminpage['position_at_align'] : 'right';
                  $button2       = $adminpage['button2'];
                  $button2_function      = $adminpage['function'];
                  $button1_function      = '';


                  $this->print_buttons( $anchor, $id, $content, $button2, $button1_function, $position_at_edge, $position_at_align, $button2, $button2_function,$counter );
                  $counter++;
               }
            }
         }

   }

   function admin_head() {
   ?>
      <style type="text/css" media="screen">
         #pointer-primary, #tour-close {
            margin: 0 5px 0 0;
         }
      </style>
   <?php
   }

   function print_buttons( $anchor, $id, $content, $button1, $button1_function = '', $position_at_edge, $position_at_align, $button2 = false, $button2_function = "t.element.pointer('close');",$counter ) {

   ?>
   <script type="text/javascript">
   //<![CDATA[
   jQuery(document).ready( function($) {

    $('<?php echo $anchor;?>').pointer({
        content: '<?php echo addslashes( $content ); ?>',
           position: {
            edge: '<?php echo $position_at_edge; ?>',
            align: '<?php echo $position_at_align; ?>'
        },
         buttons: function( event, t ) {
            button = jQuery('<a id="pointer-close" class="button-secondary">' + '<?php echo $button2; ?>' + '</a>');
            button.bind( 'click.pointer', function() {
//               t.element.pointer('close');
               <?php echo $button2_function; ?>
            });
            return button;
         },
        close: function() {
            // Once the close button is hit
        }
      })<?php if($counter > 0 ){
               echo ".pointer('close');";
              }else{
               echo ".pointer('open');";
              }?>
    <?php if ( $button1 ) { ?>
      jQuery('#wp-pointer-<?php echo $counter;?> #pointer-close')
         .after('<a id="pointer-primary" class="button-primary">' + '<?php echo $button1; ?>' + '</a>');
      jQuery('#wp-pointer-<?php echo $counter;?> #pointer-primary')
         .bind( 'click.pointer', function() {
            <?php echo $button1_function; ?>
      });
   <?php } ?>
   });
   //]]>
   </script>
   <?php
   }
}

$wfc_pointers_class = new wfc_pointers_class;

function example_dashboard_widget_function() {
   $options = get_option('wfc_new_user_pointer');
   if( $options['wfc_status'] == 'wfc_pointers_off' ) {
      echo '<p><a href="'.admin_url().'?wfc_point_on=true">Click here</a> to turn help boxes on</p>';
   }else{
      echo '<p><a href="'.admin_url().'?wfc_point_off=true">Click here</a> to turn help boxes off</p>';
   }

}

// Create the function use in the action hook

function example_add_dashboard_widgets() {
   wp_add_dashboard_widget('example_dashboard_widget', 'Example Dashboard Widget', 'example_dashboard_widget_function');
}

// Hook into the 'wp_dashboard_setup' action to register our other functions

add_action('wp_dashboard_setup', 'example_add_dashboard_widgets' ); // Hint: For Multisite Network Admin Dashboard use wp_network_dashboard_setup instead of wp_dashboard_setup.
