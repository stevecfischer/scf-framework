<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */

/*~~~~ FRAMEWORK FRONTEND SETUP */
/*~~~~ ADD IMAGE SIZES */
/*~~~~ REGISTERING NAVIGATION MENUS */
/*~~~~ CREATE CPT FOR CAMPAIGN SPACE */
/*~~~~ CREATE CPT FOR SUBPAGE BANNERS */
/*~~~~ REGISTER LEFT SIDEBAR */

/*
===============================
FRAMEWORK SETUP
===============================
*/
if ( ! function_exists( 'wfc_framework_setup' ) ):
   function wfc_framework_setup() {
      /*
      ===============================
      ADD IMAGE SIZES
      ===============================
      */
      add_theme_support( 'post-thumbnails' );
      set_post_thumbnail_size(980,328,true);
      add_image_size('subpage-banners', 980, 328,true);
      add_image_size('campaign', 980, 497,true);
      /*
      ===============================
      REGISTERING NAVIGATION MENUS
      ===============================
      */
      register_nav_menu('Primary', 'Primary Navigation');
   }
endif; // wfc_framework_setup
add_action( 'after_setup_theme', 'wfc_framework_setup' );


/*
===============================
CREATE CPT FOR CAMPAIGN SPACE
===============================
*/
$campaign_module_args = array(
   'cpt' => 'Campaign' /* CPT Name */,
   'menu_name' => 'Campaign' /* Overide the name above */,
   'supports' => array(
      'title',
      'page-attributes',
      'thumbnail'
      ) /* specify which metaboxes you want displayed. See Codex for more info*/,
   'meta_box' => array(
   'title'=>'Test all Meta Box Options',
   'new_boxes'=>array(
         array(
            'field_title' => 'Text Test: ',
            'type_of_box' => 'text',
            'desc' => 'Testing Description Area', /* optional */
         ),
         array(
            'field_title' => 'Textarea Test: ',
            'type_of_box' => 'textarea',
            'desc' => 'Testing Description Area', /* optional */
         ),
         array(
            'field_title' => 'Radio Test: ',
            'type_of_box' => 'radio',
            'options' => array('one'=> "<img src='http://1.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=64' />",'two' => '222','three' => '333'), /* required */
         ),
         array(
            'field_title' => 'Checkbox Test: ',
            'type_of_box' => 'checkbox',
            'options' => array('one'=> '111','two' => '222','three' => '333'), /* required */
         ),
         array(
            'field_title' => 'Select Dropdown Test: ',
            'type_of_box' => 'select',
            'options' => array('one'=> '111','two' => '222','three' => '333'), /* required */
         ),
         array(
            'field_title' => 'Uploader Test: ',
            'type_of_box' => 'uploader',
            'desc' => 'Upload Images and enter a caption.'
         ),
      )
   ),
   );
$campaign_module = new wfcfw($campaign_module_args);

/*
===============================
CREATE CPT FOR SUBPAGE BANNERS
===============================
*/
$subpage_banner_args = array(
   'cpt' => 'Subpage Banner', 'menu_name' => 'Subpage Img Pool',
   'supports' => array('title','thumbnail'),
   );
$subpage_banner = new wfcfw($subpage_banner_args);

/*
===============================
REGISTER LEFT SIDEBAR
===============================
*/
register_sidebar(array(
   'name' => 'Left Sidebar',
   'id' => 'sidebar-1',
   'before_widget' => '<div>',
   'after_widget' => '</div>',
   'before_title' => '<div class="widgettitle"><h2>',
   'after_title' => '</h2></div>',
   'description' => ''
));
