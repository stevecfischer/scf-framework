<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 0.1
 */

// WFC STANDARD FUNCTIONS //
// ---------------------- //

/*~~~~ REMOVE WORDPRESS VERSION */
/*~~~~ HIDE LOGIN ERROR MESSAGES (WRONG PASSWORD, NO SUCH USER ETC.) */
/*~~~~ REMOVE ADMIN NAME IN COMMENTS CLASS */
/*~~~~ WFC LOGIN LOGO */
/*~~~~ DISABLE RSS FEEDS */
/*~~~~ CLOSE COMMENTS GLOBALLY */
/*~~~~ REMOVE ITEMS FROM ADMIN BAR */
/*~~~~ REMOVE FROM USER PROFILE */
/*~~~~ ADD EDITOR STYLESHEET FOR ADMIN WYSIWYG */


define( 'WFC_PT', get_template_directory() . '/' );

define( 'WFC_URI', get_template_directory_uri() );
define( 'WFC_CSS_URI', get_template_directory_uri() . '/css' );
define( 'WFC_JS_URI', get_template_directory_uri() . '/js' );
define( 'WFC_IMG_URI', get_template_directory_uri() . '/images' );

define( 'WFC_ADM_URI', get_template_directory() . '/admin' );
define( 'WFC_ADM_CSS_URI', get_template_directory_uri() . '/admin/css' );
define( 'WFC_ADM_JS_URI', get_template_directory_uri() . '/admin/js' );

/*
===============================
File Includes
===============================
*/
require_once(WFC_ADM_URI.'/wfc_post_type_manager.php');

/*
===============================
WIDGETS INCLUDE FILES
===============================
*/
require_once(WFC_PT.'widgets/wfc_custom_nav/wfc_custom_nav.php');
require_once(WFC_PT.'widgets/wfc_custom_news/wfc_custom_news.php');
require_once(WFC_PT.'widgets/wfc_custom_recent_posts/wfc_custom_recent_posts.php');
require_once(WFC_PT.'widgets/wfc_custom_tax_widget/wfc_custom_tax_widget.php');

/*
===============================
REMOVE WORDPRESS VERSION
===============================
*/
remove_action('wp_head', 'wp_generator');

/*
===============================
HIDE LOGIN ERROR MESSAGES (WRONG PASSWORD, NO SUCH USER ETC.)
===============================
*/
add_filter('login_errors',create_function('$a', "return null;"));

/*
===============================
REMOVE ADMIN NAME IN COMMENTS CLASS
===============================
*/
function wfc_remove_comment_author_class( $classes ) {
    foreach( $classes as $key => $class ) {
        if(strstr($class, "comment-author-")) unset( $classes[$key] );
    }
    return $classes;
}
add_filter( 'comment_class' , 'wfc_remove_comment_author_class' );

/*
===============================
WFC LOGIN LOGO
===============================
*/
function wfc_login_logo() {
   echo '<style type="text/css">
   h1 a { background-image:url('.get_bloginfo('template_directory').'/images/wfc_logo.png) !important; }
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
function wfc_close_comments($data) { return false; }
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
add_editor_style('editor-style.css');



