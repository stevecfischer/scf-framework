<?php
/**
* WFC functions and definitions
* @author SCF
* @package WFC Framework
* @filename wfc_config.php
* @since WFC 1.0
*/

require_once('wfc_config/wfc_config.php');

// WFC STANDARD FUNCTIONS //
// ---------------------- //
		
/**!~~~~	REMOVE WORDPRESS VERSION */
/**!~~~~	HIDE LOGIN ERROR MESSAGES (Wrong Password, No Such User etc.) */
/**!~~~~	Remove admin name in comments class */
/**!~~~~	WFC LOGIN LOGO */
/**!~~~~	Disable RSS Feeds */
/**!~~~~	CLOSE COMMENTS GLOBALLY */
/**!~~~~	REMOVE ITEMS FROM ADMIN BAR */
/**!~~~~	REMOVE FROM USER PROFILE */
/**!~~~~	ADD EDITOR STYLESHEET FOR ADMIN WYSIWYG */


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
REMOVE WORDPRESS VERSION
===============================
*/
remove_action('wp_head', 'wp_generator');
/*
===============================
HIDE LOGIN ERROR MESSAGES (Wrong Password, No Such User etc.)
===============================
*/
add_filter('login_errors',create_function('$a', "return null;"));
/*
===============================
Remove admin name in comments class
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
Disable RSS Feeds
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



/*
===============================
File Includes
===============================
*/
 require_once(WFC_ADM_URI.'/wfc_post_type_manager.php');


