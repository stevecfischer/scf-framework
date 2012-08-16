<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */

// WFC STANDARD FUNCTIONS //
// ---------------------- //


/*~~~~ WFC LOGIN LOGO */
/*~~~~ DISABLE RSS FEEDS */
/*~~~~ CLOSE COMMENTS GLOBALLY */
/*~~~~ REMOVE ITEMS FROM ADMIN BAR */
/*~~~~ REMOVE FROM USER PROFILE */
/*~~~~ ADD EDITOR STYLESHEET FOR ADMIN WYSIWYG */
/*~~~~ REMOVE DASHBOARD WIDGETS */


define( 'WFC_PT', get_template_directory() . '/' );
define( 'WFC_CONFIG', get_template_directory() . '/wfc_config' );
define( 'WFC_WIDGETS', get_template_directory() . '/widgets' );
define( 'WFC_SHORTCODE', get_template_directory() . '/admin/shortcode' );


define( 'WFC_URI', get_template_directory_uri() );
define( 'WFC_CSS_URI', get_template_directory_uri() . '/css' );
define( 'WFC_JS_URI', get_template_directory_uri() . '/js' );
define( 'WFC_IMG_URI', get_template_directory_uri() . '/images' );

define( 'WFC_ADM_URI', get_template_directory() . '/admin' );
define( 'WFC_ADM_CSS_URI', get_template_directory_uri() . '/admin/css' );
define( 'WFC_ADM_JS_URI', get_template_directory_uri() . '/admin/js' );
define( 'WFC_ADM_IMG_URI', get_template_directory_uri() . '/admin/images' );

/*
===============================
ADMIN JS INCLUDES
===============================
*/
function wfc_admin_js_scripts() {
   wp_enqueue_script('jquery');

   wp_register_script('jquery.wfc.fn', WFC_ADM_JS_URI.'/wfc.admin.fn.js', array('jquery') );
   wp_register_script('jquery.media-up', WFC_ADM_JS_URI.'/media-up.js', array('jquery') );
   wp_register_script('jquery.order-images', WFC_ADM_JS_URI.'/jquery.tablednd.0.7.min.js', array('jquery') );

   wp_enqueue_script('media-upload');
   wp_enqueue_script('thickbox');
   wp_enqueue_script('jquery.wfc.fn');
   wp_enqueue_script('jquery.media-up');
   wp_enqueue_script('jquery.order-images');

}
add_action('admin_enqueue_scripts', 'wfc_admin_js_scripts');


/*
===============================
FRAMEWORK JS INCLUDES
===============================
*/
function wfc_load_js_scripts() {
   wp_enqueue_script('jquery');

   wp_register_script('jquery.easing.1.3', WFC_ADM_JS_URI.'/jquery.easing.1.3.js',array('jquery') );
   wp_register_script('jquery.lightbox', WFC_ADM_JS_URI.'/lightbox.js',array('jquery') );
   wp_register_script('jquery-idea-gallery', WFC_ADM_JS_URI.'/jquery.ideagallery.1.1.js',array('jquery','jquery.easing.1.3','jquery.lightbox') );



   wp_enqueue_script('jquery.scf-framework');
   wp_enqueue_script('jquery.lightbox');
   wp_enqueue_script('jquery-idea-gallery');
}
add_action('wp_enqueue_scripts', 'wfc_load_js_scripts');


/*
===============================
ADMIN CSS INCLUDES
===============================
*/
function wfc_admin_css_styles() {
   wp_register_style('wfc-admin-style', WFC_ADM_CSS_URI.'/wfc-admin-styles.css');
   wp_enqueue_style( 'wfc-admin-style');

   wp_enqueue_style('thickbox');
}
add_action('admin_enqueue_scripts', 'wfc_admin_css_styles');

/*
===============================
FRAMEWORK CSS INCLUDES
===============================
*/
function wfc_load_css_styles() {
   wp_register_style('ideagallery-style', WFC_ADM_CSS_URI.'/style.css');
   wp_register_style('lightbox-style', WFC_ADM_CSS_URI.'/lightbox.css');
   wp_enqueue_style( 'ideagallery-style');
   wp_enqueue_style( 'lightbox-style');
}
add_action('wp_print_styles', 'wfc_load_css_styles');


/*
===============================
FILE INCLUDES
===============================
*/
require_once(WFC_ADM_URI.'/wfc_post_type_manager.php'); //CPT / Tax / Metabox Class
require_once(WFC_CONFIG.'/wfc_default_theme_setup.php'); //Register default CPT's
require_once(WFC_CONFIG.'/wfc_security.php'); //Setup Framework Security

/*
===============================
SHORTCODE INCLUDE FILES
===============================
*/
require_once(WFC_SHORTCODE.'/wfc_sitemap.php');

/*
===============================
WIDGETS INCLUDE FILES
===============================
*/
require_once(WFC_WIDGETS.'/wfc_custom_nav/wfc_custom_nav.php');
//require_once(WFC_WIDGETS.'/wfc_custom_drag_menu/wfc_custom_drag_menu.php');
require_once(WFC_WIDGETS.'/wfc_custom_news/wfc_custom_news.php');
require_once(WFC_WIDGETS.'/wfc_custom_recent_posts/wfc_custom_recent_posts.php');
require_once(WFC_WIDGETS.'/wfc_custom_tax_widget/wfc_custom_tax_widget.php');


/*
===============================
WFC LOGIN LOGO
===============================
*/
function wfc_login_logo() {
   echo '<style type="text/css">
      .login h1 a{background-size:250px 49px !important;}
   h1 a { background-image:url('.WFC_ADM_IMG_URI.'/wfc_logo.png) !important; }
   </style>';
}
add_action('login_head', 'wfc_login_logo');

function wfc_login_url(){
   return ('http://www.webfullcircle.com/');
}
add_filter('login_headerurl', 'wfc_login_url');

function wfc_login_title(){
   return ('Web Full Circle');
}
add_filter('login_headertitle', 'wfc_login_title');

/*
===============================
DISABLE RSS FEEDS
===============================
*/
function wfc_disable_feed() {
   wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
}
add_action('do_feed', 'wfc_disable_feed', 1);
add_action('do_feed_rdf', 'wfc_disable_feed', 1);
add_action('do_feed_rss', 'wfc_disable_feed', 1);
add_action('do_feed_rss2', 'wfc_disable_feed', 1);
add_action('do_feed_atom', 'wfc_disable_feed', 1);

/*
===============================
CLOSE COMMENTS GLOBALLY
===============================
*/
function wfc_close_comments($data) {
   return false;
}
add_filter('comments_number', 'wfc_close_comments');
add_filter('comments_open', 'wfc_close_comments');

/*
===============================
REMOVE ITEMS FROM ADMIN BAR
===============================
*/
function wfc_remove_admin_bar_items() {
   global $wp_admin_bar;
   global $current_user;
   if($current_user->user_login != 'wfc'){
      $wp_admin_bar->remove_menu('wpseo-menu');
      $wp_admin_bar->remove_menu('comments');
      $wp_admin_bar->remove_menu('new-content');
      $wp_admin_bar->remove_menu('ngg-menu');
   }
}
add_action( 'wp_before_admin_bar_render', 'wfc_remove_admin_bar_items' );

/*
===============================
REMOVE FROM USER PROFILE
===============================
*/
function wfc_hide_admin_bar_prefs() {
   echo '<style type="text/css">.show-admin-bar { display: none; }</style>';
}
add_action( 'admin_print_scripts-profile.php', 'wfc_hide_admin_bar_prefs' );

/*
===============================
ADD EDITOR STYLESHEET FOR ADMIN WYSIWYG
===============================
*/
add_editor_style('/editor-style.css');

/*
===============================
REMOVE DASHBOARD WIDGETS
===============================
*/
function wfc_custom_dashboard_widgets() {
   global $wp_meta_boxes;
   unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
   unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
   unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
   unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
   unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
   unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
   unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
   unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
}
add_action('wp_dashboard_setup', 'wfc_custom_dashboard_widgets');

/*
===============================
SHOW FAVICON
===============================
*/
function wfc_fw_favicon() {
   echo '<link rel="shortcut icon" href="'.WFC_URI.'/favicon.ico"/>'."\n";
}
add_action('wp_head', 'wfc_fw_favicon');

/**
 * CONVERT IMG URL TO POST ID.
 *
 * @since 2.1
 *
 * @param string $image_src
 *
 * @return array
 *
 */

function wfc_imgurl_to_postid($image_src) {
   global $wpdb;
   $new_img_src = explode('uploads/',$image_src);

   $query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value ='$new_img_src[1]'";
   $id = $wpdb->get_var($query);

   $img_thumb = wp_get_attachment_image_src( $id, 'idea-gallery-thumb');
   $img_frame = wp_get_attachment_image_src( $id, 'idea-gallery-frame');
   $img_max_size = wp_get_attachment_image_src( $id, 'max-size-lightbox');
   $arr = array('thumb'=>$img_thumb,'frame'=>$img_frame,'max_size'=>$img_max_size);

   return $arr;
}

/**
 * ADD IMAGE SIZES FOR SLIDER
 *
 * @since 2.1
 */

add_image_size('idea-gallery-thumb',75,50,true);
add_image_size('idea-gallery-frame',440,304);
add_image_size('max-size-lightbox',800,600);


