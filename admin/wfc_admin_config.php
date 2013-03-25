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
    FILE INCLUDES

     * @since 1.0
    ===============================
    */
    require_once(WFC_CONFIG.'/wfc_developer_login.php'); //Auto login inside WFC IP Address
    require_once(WFC_ADM.'/wfc_post_type_manager.php'); //CPT / Tax / Metabox Class
    require_once(WFC_CONFIG.'/wfc_default_theme_setup.php'); //Register default CPT's
    require_once(WFC_GLOBAL.'/wfc_global_config.php'); //Global hooks/functions
    require_once(WFC_CONFIG.'/wfc_security.php'); //Setup Framework Security
    require_once(WFC_ADM.'/wfc_expanded_menu_manager.php'); //CPT / Tax / Metabox Class
    require_once(WFC_THEME_FUNCTIONS.'/wfc_helper_functions.php'); //Small Helper Functions
    require_once(WFC_ADM.'/wfc_browser_check.php'); //Alerts Old Browsers **BETA**
    /*
    ===============================
    ADMIN JS INCLUDES

     * @since 1.0
    ===============================
    */
    function wfc_admin_js_scripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_register_script( 'jquery.wfc.fn', WFC_ADM_JS_URI.'/wfc.admin.fn.js', array('jquery') );
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'jquery.wfc.fn' );
    }

    add_action( 'admin_enqueue_scripts', 'wfc_admin_js_scripts' );
    /*
    ===============================
    ADMIN CSS INCLUDES

     * @since 1.0
    ===============================
    */
    function wfc_admin_css_styles(){
        wp_register_style( 'wfc-admin-style', WFC_ADM_CSS_URI.'/wfc-admin-styles.css' );
        wp_register_style( 'wfc-jquery-ui', 'http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css' );
        wp_enqueue_style( 'wfc-jquery-ui' );
        wp_enqueue_style( 'wfc-admin-style' );
        wp_enqueue_style( 'thickbox' );
    }

    add_action( 'admin_enqueue_scripts', 'wfc_admin_css_styles' );
    /*
    ===============================
    SHORTCODE INCLUDE FILES

     * @since 2.0
    ===============================
    */
    require_once(WFC_SHORTCODE.'/wfc_sitemap.php');
    require_once(WFC_SHORTCODE.'/wfc_atoz.php');
    /*
    ===============================
    WIDGETS INCLUDE FILES

     * @since 1.0
    ===============================
    */
    require_once(WFC_WIDGETS.'/wfc_custom_nav/wfc_custom_nav.php');
    //require_once(WFC_WIDGETS.'/wfc_custom_drag_menu/wfc_custom_drag_menu.php');
    require_once(WFC_WIDGETS.'/wfc_custom_recent_posts/wfc_custom_recent_posts.php');
    require_once(WFC_WIDGETS.'/wfc_custom_tax_widget/wfc_custom_tax_widget.php');
    require_once(WFC_WIDGETS.'/wfc_spotlight/wfc_spotlight.php');
    /*
    ===============================
    REMOVE ABILITY TO ADD NEW HOME POSTS
     * @since 2.3
    ===============================
    */
    function hide_add_new_custom_type(){
        global $submenu;
        unset($submenu['edit.php?post_type=homeboxes'][10]);
    }

    function hide_buttons(){
        global $pagenow;
        if( is_admin() ){
            if( ($pagenow == 'edit.php') && $_GET['post_type'] == 'homeboxes' ){
                echo "<style type=\"text/css\">.add-new-h2{display: none;}</style>";
            }
        }
    }

    /*
     * Removes ability to add new Home Content Posts
     *
     * @since 2.3
     */
    if( !wfc_is_dev() ){
        add_action( 'admin_menu', 'Wfc_homecontentposts_admin_redirect' );
        add_action( 'admin_init', 'Wfc_homecontentposts_show_notice' );
        function Wfc_homecontentposts_admin_redirect(){
            $result = stripos( $_SERVER['REQUEST_URI'], 'post-new.php?post_type=homeboxes' );
            if( $result !== false ){
                wp_redirect( get_option( 'siteurl' ).'/wp-admin/index.php?permissions_error=true' );
            }
        }

        function Wfc_homecontentposts_admin_notice(){
            // use the class "error" for red notices, and "update" for yellow notices
            echo"<div id='permissions-warning' class='error fade'><p><strong>".
                __( 'You do not have permission to access that page.' )."</strong></p></div>";
        }

        function Wfc_homecontentposts_show_notice(){
            if( $_GET['permissions_error'] ){
                add_action( 'admin_notices', 'Wfc_homecontentposts_admin_notice' );
            }
        }

        function Wfc_check_current_screen(){
            if( !is_admin() ){
                return;
            }
            global $current_screen;
            if( $current_screen->post_type == "homeboxes" ){
                echo "<style type=\"text/css\">.add-new-h2{display: none;}</style>";
            }
        }

        add_action( 'admin_notices', 'Wfc_check_current_screen' );
        add_action( 'admin_menu', 'hide_add_new_custom_type' );
        add_action( 'admin_head', 'hide_buttons' );
    }
    /*
    ===============================
    WFC LOGIN LOGO

     * @since 1.0
    ===============================
    */
    function wfc_login_logo(){
        echo '<style type="text/css">
   .login h1 a{background-size:250px 49px !important;}
   h1 a { background-image:url('.WFC_ADM_IMG_URI.'/wfc_logo.png) !important;}
   </style>';
        echo '<script type="text/javascript">
      jQuery(function($){
         $("a:first").addClass("external").attr({ target: "_blank" });
      });</script>';
    }

    add_action( 'login_head', 'wfc_login_logo' );
    function wfc_load_jquery(){
        wp_enqueue_script( 'jquery' );
    }

    add_action( 'login_enqueue_scripts', 'wfc_load_jquery' );
    function wfc_login_url(){
        return ('http://www.webfullcircle.com');
    }

    add_filter( 'login_headerurl', 'wfc_login_url' );
    function wfc_login_title(){
        return ('Web Full Circle');
    }

    add_filter( 'login_headertitle', 'wfc_login_title' );
    /*
    ===============================
    ADD AN UPDATE AND FINISH BUTTON
        SO USERS CAN GO TO LIST VIEW INSTEAD OF EDITING THE POST

     * @since 2.1
    ===============================
    */
    add_action( 'post_submitbox_start', 'wfc_add_update_and_finish_button' );
    function wfc_add_update_and_finish_button( $data ){
        global $current_screen;
        global $post;
        /*
         * auto-draft
         * draft
         * pending
         * publish
         */
        if( $post->post_status == 'auto-draft' ){
            echo '<div id="wfc_publish_block"><input name="original_publish" type="hidden" id="original_publish" value="Publish"><input type="hidden" name="publish" id="publish" class="button button-primary button-large" value="Publish" accesskey="p"><input name="wfc_continue" type="submit" class="wfc-continue-button button-primary" id="wfc_continue"  accesskey="p" value="Publish &amp; Done"></div>';
        } else {
            if( $post->post_status == 'publish' ){
                echo '<div id="wfc_publish_block"><input name="original_publish" type="hidden" id="original_publish" value="Update"><input type="hidden" name="save" id="publish" class="button button-primary button-large" value="Update" accesskey="p"><input name="wfc_continue" type="submit" class="wfc-continue-button button-primary" id="wfc_continue"  accesskey="p" value="Update &amp; Done"></div>';
            }
        }
    }

    add_filter( 'wp_redirect', 'wfc_continue_after_update_redirect', 10, 2 );
    function wfc_continue_after_update_redirect( $location, $status ){
        if( isset($_REQUEST['wfc_continue']) ){
            $location = admin_url().'edit.php?post_type='.$_REQUEST['post_type'].'';
        }
        return $location;
    }

    /*
    ===============================
    REMOVE PLUGIN UPDATE WARNINGS
    * @since 2.3
    */
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
    add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
    /*
    ===============================
    REMOVE MENU ITEMS FOR OTHER USERS
     * @since 2.1
    */
    function wfc_remove_menu_items(){
        global $current_user;
        if( $current_user->user_login != 'wfc' ){
            remove_menu_page( 'wpseo_dashboard' );
                remove_submenu_page('index.php', 'update-core.php');
                /*
                    Hides ThreeWP Activity Monitor Plugin
                 */
                remove_submenu_page( 'index.php', 'ThreeWP_Activity_Monitor' );
            remove_menu_page( 'tools.php' );
            remove_menu_page( 'options-general.php' );
            remove_menu_page( 'plugins.php' );
            remove_menu_page( 'link-manager.php' );
            remove_menu_page( 'edit-comments.php' );
            remove_menu_page( 'edit.php' );
            remove_menu_page( 'edit.php?post_type=acf' );
            remove_menu_page( 'woocommerce' );
                /*remove_submenu_page( 'edit.php?post_type=product', 'post-new.php?post_type=product' );
                remove_submenu_page( 'edit.php?post_type=product', 'edit-tags.php' );*/
            remove_menu_page( 'themes.php' );
            /* remove_submenu_page( 'themes.php', 'nav-menus.php' );
            remove_submenu_page( 'themes.php', 'all-in-one-event-calendar-themes' );
            remove_submenu_page( 'themes.php', 'widgets.php' );
            remove_submenu_page( 'themes.php', 'customsidebars' );
            remove_submenu_page( 'themes.php', 'themes.php' );
            remove_submenu_page( 'themes.php', 'theme-editor.php' );*/
        }
    }
    add_action( 'admin_menu', 'wfc_remove_menu_items', 999 );
    /*
    ===============================
    CUSTOMIZE ADMIN MENU ORDER
    ===============================
    */
    function wfc_custom_menu_order( $menu_ord ){
        if( !$menu_ord ){
            return true;
        }
        return array(
            'index.php',
            'video-user-manuals/plugin.php',
            'edit.php?post_type=page',
            'edit.php?post_type=homeboxes',
            'edit.php?post_type=news',
            'edit.php?post_type=campaign',
            'edit.php?post_type=subpagebanner',
            'upload.php',
            'admin.php?page=gf_edit_forms',
        );
    }

    add_filter( 'custom_menu_order', 'wfc_custom_menu_order' );
    add_filter( 'menu_order', 'wfc_custom_menu_order' );
    function getActiveCPT( $cpt ){
        $activeCPT = get_option( 'wfc_activate_cpt' );
        if( !is_array( $activeCPT ) ){
            return false;
        } elseif( in_array( $cpt, $activeCPT ) ){
            return true;
        } else{
            return false;
        }
    }

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

    add_action( 'admin_footer', 'Wfc_post_list_highlighting' );
    function Wfc_contextual_help( $contextual_help, $screen_id, $screen ){
        add_filter( 'gettext', 'theme_change_comment_field_names', 20, 3 );
        ob_start(); ?>
    <h3>Help for Home Content Posts</h3>
    <p>The ability to add new posts has been removed. The website home page has been programmed to handle only a certain amount. Adding more could result in `breaking` the website. </p>
    <?php
        return ob_get_clean();
    }

    if( isset($_GET['post_type']) && $_GET['post_type'] == 'homeboxes' ){
        add_action( 'contextual_help', 'Wfc_contextual_help', 10, 3 );
    }
    /**
     * Change comment form default field names.
     *
     * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
     */
    function theme_change_comment_field_names( $translated_text, $text, $domain ){
        switch( $translated_text ){
            case 'Help' :
                $translated_text = "WFC Help";
                break;
        }
        return $translated_text;
    }