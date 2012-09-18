<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 * @since 2.2
 */

/*
===============================
ADMIN JS INCLUDES

 * @since 1.0
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
ADMIN CSS INCLUDES

 * @since 1.0
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
FILE INCLUDES

 * @since 1.0
===============================
*/
require_once(WFC_ADM.'/wfc_post_type_manager.php'); //CPT / Tax / Metabox Class
require_once(WFC_CONFIG.'/wfc_default_theme_setup.php'); //Register default CPT's
require_once(WFC_GLOBAL.'/wfc_global_config.php'); //Global hooks/functions
require_once(WFC_CONFIG.'/wfc_security.php'); //Setup Framework Security
require_once(WFC_CONFIG.'/wfc_developer_login.php'); //Auto login inside WFC IP Address
//require_once(WFC_ADM_URI.'/wfc_new_user_pointers.php'); //Creates tour for new users **BETA**

/*
===============================
SHORTCODE INCLUDE FILES

 * @since 2.0
===============================
*/
require_once(WFC_SHORTCODE.'/wfc_sitemap.php');

/*
===============================
WIDGETS INCLUDE FILES

 * @since 1.0
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

 * @since 1.0
===============================
*/
function wfc_login_logo() {
   echo '<style type="text/css">
   .login h1 a{background-size:250px 49px !important;}
   h1 a { background-image:url('.WFC_ADM_IMG_URI.'/wfc_logo.png) !important;}
   </style>';

   echo '<script type="text/javascript">
      jQuery(function($){
         $("a:first").addClass("external").attr({ target: "_blank" });
      });</script>';
}
add_action('login_head', 'wfc_login_logo');

function wfc_load_jquery(){
   wp_enqueue_script('jquery');
}
add_action('login_enqueue_scripts', 'wfc_load_jquery');

function wfc_login_url(){
   return('http://www.webfullcircle.com');
}
add_filter('login_headerurl', 'wfc_login_url');

function wfc_login_title(){
   return('Web Full Circle');
}
add_filter('login_headertitle', 'wfc_login_title');


