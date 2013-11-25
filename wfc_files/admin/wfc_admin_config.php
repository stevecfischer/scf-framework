<?php
    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     * @since 2.2
     */

    define('AUTOLOAD_MINIFY', true); //Toggle if site minifies and compresses js|css


    require_once(WFC_ADM.'/wfc_admin_class.php');
    /**
     * Require each parts of the framework
     *
     * @since 1.0
     */
    require_once(WFC_CONFIG.'/wfc_developer_login.php'); //Auto login inside WFC IP Address
    require_once(WFC_ADM.'/wfc_post_type_manager.php'); //CPT / Tax / Metabox Class
    require_once(WFC_GLOBAL.'/wfc_global_config.php'); //Global hooks/functions
    require_once(WFC_CONFIG.'/wfc_security.php'); //Setup Framework Security
    require_once(WFC_ADM.'/wfc_expanded_menu_manager.php'); //CPT / Tax / Metabox Class
    require_once(WFC_THEME_FUNCTIONS.'/wfc_helper_functions.php'); //Small Helper Functions
    require_once(WFC_ADM.'/wfc_browser_check.php'); //Alerts Old Browsers
    require_once(WFC_ADM.'/wfc_theme_customizer.php'); //Site Options Panel
    //require_once(WFC_ADM.'/wfc_restricted_access_alert.php'); //Beta not ready for release
    require_once(WFC_ADM.'/wfc_update_script.php'); //Update from github
    require_once(WFC_THEME_FUNCTIONS.'/build_theme.php'); //Auto theme builder
    require_once(WFC_THEME_FUNCTIONS.'/wfc_autoload_script_class.php'); //Auto theme builder
    require_once(WFC_ADM.'/wfc_admin_hooks.php'); //Auto theme builder
    require_once(WFC_ADM.'/wfc_fastbackup_class.php'); //Fast backup
    /**
     * Includes WFC Shortcodes
     *
     * @since 2.0
     */
    require_once(WFC_SHORTCODE.'/wfc_sitemap.php');
    require_once(WFC_SHORTCODE.'/wfc_atoz.php');
    /**
     * Includes WFC Widgets
     *
     * @since 1.0
     */
    require_once(WFC_WIDGETS.'/wfc_custom_nav/wfc_custom_nav.php');
    require_once(WFC_WIDGETS.'/wfc_custom_recent_posts/wfc_custom_recent_posts.php');
    require_once(WFC_WIDGETS.'/wfc_spotlight/wfc_spotlight.php');
    /*
     * @scftodo: move all functions into wfc admin class. or if there is somewhere better send there.
     */
    /**
     * Includes JS into WP head
     *
     * @since 1.0
     */
    add_action( 'admin_enqueue_scripts', 'wfc_admin_js_scripts' );
    function wfc_admin_js_scripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_register_script( 'jquery.wfc.fn', WFC_ADM_JS_URI.'/wfc.admin.fn.js', array('jquery') );
        wp_enqueue_script( 'jquery.wfc.fn' );
    }

    /**
     * Includes CSS into WP head
     *
     * @since 1.0
     */
    add_action( 'admin_enqueue_scripts', 'wfc_admin_css_styles' );
    function wfc_admin_css_styles(){
        //Framework Styles includes jqueryui and bootstrap
        wp_register_style( 'wfc-admin-style', WFC_ADM_CSS_URI.'/wfc-admin-styles.css' );
        wp_enqueue_style( 'wfc-admin-style' );
    }

    /**
     * Change the wordpress logo into a WFC logo
     *
     * @since 1.0
     */
    add_action( 'login_head', 'wfc_login_logo' );
    function wfc_login_logo(){
        echo '<style type="text/css">.login h1 a{background-size:250px 49px !important;}h1 a { background-image:url('.
            WFC_ADM_IMG_URI.'/wfc_logo.png) !important;}</style>';
        echo '<script type="text/javascript">
            jQuery(function($){
                $("a:first").addClass("external").attr({ target: "_blank" });
            });</script>';
    }

    /**
     * Change login url into webfullcirle.com
     *
     * @since 1.0
     * @return string $link 'http://www.webfullcircle.com'
     */
    add_filter( 'login_headerurl', 'wfc_login_url' );
    function wfc_login_url(){
        return ('http://www.webfullcircle.com');
    }

    /**
     * Change login title into Web Full Circle
     *
     * @since 1.0
     * @return string $text 'Web Full Circle'
     */
    add_filter( 'login_headertitle', 'wfc_login_title' );
    function wfc_login_title(){
        return ('Web Full Circle');
    }

    /**
     * Add an 'Update & Done' button in the publish metabox
     * So that the user can just get back to the list after editing a page/post
     *
     * @since 2.1
     * @global $current_screen
     * @global $post
     */
    add_action( 'post_submitbox_start', 'wfc_add_update_and_finish_button' );
    function wfc_add_update_and_finish_button( $data ){
        global $current_screen;
        global $post;
        if( $post->post_status == 'auto-draft' ){
            echo '<div id="wfc_publish_block"><input name="original_publish" type="hidden" id="original_publish" value="Publish"><input type="hidden" name="publish" id="publish" class="button button-primary button-large" value="Publish" accesskey="p"><input name="wfc_continue" type="submit" class="wfc-continue-button button-primary" id="wfc_continue"  accesskey="p" value="Publish &amp; Done"></div>';
        } else{
            if( $post->post_status == 'publish' ){
                echo '<div id="wfc_publish_block"><input name="original_publish" type="hidden" id="original_publish" value="Update"><input type="hidden" name="save" id="publish" class="button button-primary button-large" value="Update" accesskey="p"><input name="wfc_continue" type="submit" class="wfc-continue-button button-primary" id="wfc_continue"  accesskey="p" value="Update &amp; Done"></div>';
            }
        }
    }

    /**
     * Redirect to list of page/post
     * if $_REQUEST['wfc_continue'] exists
     * Used for the update & done button
     *
     * @since 2.1
     *
     * @param string $location old location url
     *
     * @return string $url new location url
     */
    add_filter( 'wp_redirect', 'wfc_continue_after_update_redirect', 10, 2 );
    function wfc_continue_after_update_redirect( $location ){
        if( isset($_REQUEST['wfc_continue']) ){
            $location = admin_url().'edit.php?post_type='.$_REQUEST['post_type'].'';
        }
        return $location;
    }

    /**
     * Removes plugin update warnings
     *
     * @since 2.3
     */
    add_filter( 'admin_init', 'wfc_remove_plugin_update_warning' );
    function wfc_remove_plugin_update_warning(){
        if( !wfc_is_dev() ){
            remove_action( 'load-update-core.php', 'wp_update_plugins' );
            add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
        }
    }

    /**
     * Removes menus & submenus from the admin panel
     * if user != WFC
     *
     * @since 2.1
     * @global $current_user
     */
    add_action( 'admin_menu', 'wfc_remove_menu_items', 999 );
    function wfc_remove_menu_items(){
        global $current_user;
        if( $current_user->user_login != 'wfc' ){
            remove_submenu_page( 'index.php', 'ThreeWP_Activity_Monitor' );
            remove_submenu_page( 'index.php', 'update-core.php' );
            remove_menu_page( 'tools.php' );
            remove_menu_page( 'options-general.php' );
            remove_menu_page( 'edit-comments.php' );
            if( getAdminMenu( "Plugins" ) ){
                remove_menu_page( 'plugins.php' );
            }
            if( getAdminMenu( "Yoast SEO" ) ){
                remove_menu_page( 'wpseo_dashboard' );
            }
            if( getAdminMenu( "Posts" ) ){
                remove_menu_page( 'edit.php' );
            }
            if( getAdminMenu( "Appearance" ) ){
                remove_menu_page( 'themes.php' );
            }
            if( getAdminMenu( "Theme Editor" ) ){
                remove_submenu_page( 'themes.php', 'theme-editor.php' );
            }
            if( getAdminMenu( "Widgets" ) ){
                remove_submenu_page( 'themes.php', 'widgets.php' );
            }
            if( getAdminMenu( "Menus" ) ){
                remove_submenu_page( 'themes.php', 'nav-menus.php' );
            }
            if( getAdminMenu( "ai1ec Themes" ) ){
                remove_submenu_page( 'themes.php', 'all-in-one-event-calendar-themes' );
            }
        }
    }

    /**
     * Toggle admin menu by site options
     *
     * @since 4.0
     */
    function getAdminMenu( $menu_item ){
        $menu_items = get_option( 'wfc_admin_menu' );
        if( !is_array( $menu_items ) ){
            return false;
        } elseif( in_array( $menu_item, $menu_items ) ){
            return true;
        } else{
            return false;
        }
    }

    /**
     * Customize admin menu order
     *
     * @since 4.0
     */
    add_filter( 'custom_menu_order', 'wfc_custom_menu_order' );
    add_filter( 'menu_order', 'wfc_custom_menu_order' );
    function wfc_custom_menu_order( $menu_ord ){
        if( !$menu_ord ){
            return true;
        }
        return array(
            'index.php',
            'video-user-manuals/plugin.php',
            'edit.php?post_type=page',
            'edit.php?post_type=homepageboxes',
            'edit.php?post_type=news',
            'edit.php?post_type=campaign',
            'edit.php?post_type=subpagebanner',
            'upload.php',
            'admin.php?page=gf_edit_forms',
        );
    }

    /**
     * Tell if the cpt is active or not
     *
     * @since 2.3
     *
     * @param string $cpt cpt name
     * @param int $deprecated Not Used.
     *
     * @return boolean active or not
     */
    function getActiveCPT( $cpt, $deprecated = '' ){
        global $wfc_admin;
        if( empty($deprecated) ){
            $wfc_admin->_wfc_deprecated_argument( __FUNCTION__, '5.1', 'Current method to use: $wfc_admin->is_active_cpt($cpt)' );
        }
        return $wfc_admin->is_active_cpt( $cpt );
    }

    /**
     * Launches script to highlight post
     *
     * @since 2.3
     */
    add_action( 'admin_footer', 'Wfc_post_list_highlighting' );
    function Wfc_post_list_highlighting(){
        $args           = array(
            'public' => true,
        );
        $output         = 'names'; // names or objects, note names is the default
        $operator       = 'and'; // 'and' or 'or'
        $activeCPT      = get_post_types( $args, $output, $operator );
        $jqueryElements = '';
        foreach( $activeCPT as $cpt ){
            $jqueryElements .= '.type-'.$cpt.', ';
        }?>
        <script type="text/javascript">
            jQuery(function ($) {
                $("<?php echo substr( $jqueryElements, 0, -2 ); ?>").wfc_AdminTools();
            });
        </script>
    <?php
    }