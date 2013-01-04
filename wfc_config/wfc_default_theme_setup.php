<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
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
Filter excerpt 
===============================
*/
function wfc_excerpt_length( $length ) {
	return $length;
}
add_filter( 'excerpt_length', 'wfc_excerpt_length', 999 );

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
if( CAMPAIGN_CPT ){
    $campaign_module = new wfcfw($campaign_module_args);
}

/*
===============================
CREATE CPT FOR SUBPAGE BANNERS
===============================
*/
$subpage_banner_args = array(
   'cpt' => 'Subpage Banner', 'menu_name' => 'Subpage Img Pool',
   'supports' => array('title','thumbnail'),
   );
if( SUBPAGE_BANNER_CPT ){
    $subpage_banner = new wfcfw($subpage_banner_args);
}

/*
===============================
CREATE CPT FOR SUBPAGE BANNERS
===============================
*/
$news_cpt_args = array(
   'cpt' => 'News', 'menu_name' => 'News',
   'tax' => array( array('tax_label' => 'topics', 'menu_name' => 'Topics' ), ),
   'supports' => array('editor', 'title', 'page-attributes', 'thumbnail'),
   );
if( NEWS_CPT ){
    $news_cpt = new wfcfw($news_cpt_args);
}

/*
===============================
CREATE CPT FOR HOME PAGE POSTS SPACE
===============================
*/
$home_boxes_module_args = array(
   'cpt' => 'homeboxes' /* CPT Name */,
   'menu_name' => 'Home Posts' /* Overide the name above */,
   'supports' => array(
      'title',
      'page-attributes',
      'thumbnail',
      'editor'
      ) /* specify which metaboxes you want displayed. See Codex for more info*/,
   'meta_box' => array(
   'title'=>'Learn More Link',
   'new_boxes'=>array(
         array(
         'field_title' => 'Link: ',
         'type_of_box' => 'text',
         'desc' => '', /* optional */
         'std' => '' /* optional */
         ),
      )
   ),
);
if( HOME_BOXES_CPT ){
    $home_boxes_module = new wfcfw($home_boxes_module_args);
}

/*
===============================
CREATE CPT FOR TESTIMONIALS
===============================
*/
$testimonial_args = array(
    'cpt' => 'Testimonial', 'menu_name' => 'Testimonial',
    'supports' => array('title','editor'),
    'meta_box' => array(
        'title'=>'Author Information',
        'new_boxes'=>array(
            array(
                'field_title' => 'Name: ',
                'type_of_box' => 'text',
                'desc' => 'Ex. John Smith', /* optional */
            ),
            array(
                'field_title' => 'Position: ',
                'type_of_box' => 'text',
                'desc' => 'Ex. Director, USA', /* optional */
            ),
        )
    ),
);
if( TESTIMONIAL_CPT ){
    $testimonial = new wfcfw($testimonial_args);
}
/*
===============================
REGISTER LEFT SIDEBAR
===============================
*/
register_sidebar(array(
   'name' => 'Left Sidebar',
   'id' => 'sidebar-1',
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget' => "</aside>",
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>',
   'description' => ''
));
