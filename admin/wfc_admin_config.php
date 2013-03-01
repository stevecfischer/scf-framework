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
    function wfc_admin_js_scripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_register_script( 'jquery.wfc.fn', WFC_ADM_JS_URI.'/wfc.admin.fn.js', array('jquery') );
        wp_register_script( 'jquery.media-up', WFC_ADM_JS_URI.'/media-up.js', array('jquery') );
        //wp_register_script( 'jquery.order-images', WFC_ADM_JS_URI.'/jquery.tablednd.0.7.min.js', array('jquery') );
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'jquery.wfc.fn' );
        wp_enqueue_script( 'jquery.media-up' );
        //wp_enqueue_script( 'jquery.order-images' );
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
    FILE INCLUDES

     * @since 1.0
    ===============================
    */
    require_once(WFC_ADM.'/wfc_post_type_manager.php'); //CPT / Tax / Metabox Class
    require_once(WFC_CONFIG.'/wfc_default_theme_setup.php'); //Register default CPT's
    require_once(WFC_GLOBAL.'/wfc_global_config.php'); //Global hooks/functions
    require_once(WFC_CONFIG.'/wfc_security.php'); //Setup Framework Security
    require_once(WFC_CONFIG.'/wfc_developer_login.php'); //Auto login inside WFC IP Address
    require_once(WFC_ADM.'/wfc_expanded_menu_manager.php'); //CPT / Tax / Metabox Class
    require_once(WFC_ADM.'/wfc_new_user_pointers.php'); //Creates tour for new users **BETA**
    require_once(WFC_THEME_FUNCTIONS.'/wfc_helper_functions.php'); //Creates tour for new users **BETA**
    require_once(WFC_ADM.'/wfc_theme_customizer.php'); //Trying new WP feature **BETA**
    require_once(WFC_ADM.'/wfc_plugin_disclaimer.php'); //Trying new WP feature **BETA**
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

    function permissions_admin_redirect(){
        $result = stripos( $_SERVER['REQUEST_URI'], 'post-new.php?post_type=homeboxes' );
        if( $result !== false ){
            wp_redirect( get_option( 'siteurl' ).'/wp-admin/index.php?permissions_error=true' );
        }
    }

    function permissions_admin_notice(){
        // use the class "error" for red notices, and "update" for yellow notices
        echo"<div id='permissions-warning' class='error fade'><p><strong>".
            __( 'You do not have permission to access that page.' )."</strong></p></div>";
    }

    function permissions_show_notice(){
        if( $_GET['permissions_error'] ){
            add_action( 'admin_notices', 'permissions_admin_notice' );
        }
    }

    function dev_check_current_screen(){
        if( !is_admin() ){
            return;
        }
        global $current_screen;
        if( $current_screen->post_type == "homeboxes" ){
            echo "<style type=\"text/css\">.add-new-h2{display: none;}</style>";
        }
    }

    add_action( 'admin_notices', 'dev_check_current_screen' );
    add_action( 'admin_menu', 'hide_add_new_custom_type' );
    add_action( 'admin_head', 'hide_buttons' );
    add_action( 'admin_menu', 'permissions_admin_redirect' );
    add_action( 'admin_init', 'permissions_show_notice' );
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
    function wfc_add_update_and_finish_button( $data ){
        echo '<div id="wfc_publish_block"><input name="original_publish" type="hidden" id="original_publish" value="Publish"><input type="hidden" name="publish" id="publish" class="button button-primary button-large" value="Publish" accesskey="p"><input name="wfc_continue" type="submit" class="wfc-continue-button button-primary" id="wfc_continue"  accesskey="p" value="Update &amp; Done"></div>';
    }

    add_action( 'post_submitbox_start', 'wfc_add_update_and_finish_button' );
    function wfc_continue_after_update_redirect( $location, $status ){
        if( isset($_REQUEST['wfc_continue']) ){
            $location = admin_url().'edit.php?post_type='.$_REQUEST['post_type'].'';
        }
        return $location;
    }

    add_filter( 'wp_redirect', 'wfc_continue_after_update_redirect', 10, 2 );
    /*
    ===============================
    REMOVE PLUGIN UPDATE WARNINGS
    * @since 2.3
    */
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
    add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
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

    function applyMagentoTools(){
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

    add_action( 'admin_footer', 'applyMagentoTools' );
    function Wfc_contextual_help( $contextual_help, $screen_id, $screen ){
        ob_start(); ?>

    <h3>Help Section Title</h3>
    <p>This is text that provides helpful information</p>
    <h3>Help Section Title</h3>
    <p>This is text that provides helpful information</p>
    <h3>Help Section Title</h3>
    <p>This is text that provides helpful information</p>

    <?php
        return ob_get_clean();
    }

    if( isset($_GET['post_type']) && $_GET['post_type'] == 'homeboxes' ){
        add_action( 'contextual_help', 'Wfc_contextual_help', 10, 3 );
    }