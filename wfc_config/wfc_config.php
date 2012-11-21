<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 * @since 0.1
 */

// WFC STANDARD FUNCTIONS //
// ---------------------- //


define( 'WFC_SITE_URL', get_bloginfo('url') . '/' );
define( 'WFC_PT', get_template_directory() . '/' );
define( 'WFC_CONFIG', WFC_PT . 'wfc_config' );
define( 'WFC_THEME_FUNCTIONS', WFC_PT . 'theme_functions' );
define( 'WFC_WIDGETS', WFC_PT . 'widgets' );
define( 'WFC_SHORTCODE', WFC_PT . 'admin/shortcode' );
define( 'WFC_GLOBAL', WFC_PT . 'admin/global' );

define( 'WFC_URI', get_template_directory_uri() );
define( 'WFC_ADM', get_template_directory() . '/admin' );

define( 'WFC_CSS_URI', WFC_URI . '/css' );
define( 'WFC_JS_URI', WFC_URI . '/js' );
define( 'WFC_IMG_URI', WFC_URI . '/images' );
define( 'WFC_ADM_CSS_URI', WFC_URI . '/admin/css' );
define( 'WFC_ADM_JS_URI', WFC_URI . '/admin/js' );
define( 'WFC_ADM_IMG_URI', WFC_URI . '/admin/images' );


/*
===============================
TOGGLE STANDARD CPT's

 * @since 2.2
==============================
*/

define('CAMPAIGN_CPT'       , 1);
define('SUBPAGE_BANNER_CPT' , 1);
define('HOME_BOXES_CPT'     , 1);
define('NEWS_CPT'           , 1);
define('TESTIMONIAL_CPT'    , 0);

/*
===============================
MAIN INCLUDE FILE

 * @since 2.2
==============================
*/
require_once(WFC_ADM.'/wfc_admin_config.php');

/*
===============================
REMOVE MENU ITEMS FOR OTHER USERS

 * @since 2.1


======= @TODO: NEED TO MAKE THIS TOGGLABLE JUST LIKE
                THE CPTS ABOVE
*/

function wfc_remove_menu_items( ){
   global $current_user;
   if($current_user->user_login != 'wfc'){
      remove_menu_page( 'wpseo_dashboard');
      remove_menu_page( 'redirect-options');
      remove_menu_page( 'tools.php');
      remove_menu_page( 'options-general.php');
      //remove_menu_page( 'themes.php');
      remove_menu_page( 'plugins.php');
      remove_menu_page( 'link-manager.php');
      remove_menu_page( 'edit-comments.php');
      remove_menu_page( 'edit.php');
      remove_submenu_page( 'themes.php', 'widgets.php' );
      remove_submenu_page( 'themes.php', 'customsidebars' );
      remove_submenu_page( 'themes.php', 'themes.php' );
   }
}
//add_action('admin_menu', 'wfc_remove_menu_items', 999);

/* ~~~~~~~~~~~~~~~~~~~~
~~~~~~~~~~~~~~~~~~~~

===============================
*/

