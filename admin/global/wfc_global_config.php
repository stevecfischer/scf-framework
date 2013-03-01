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
    FRAMEWORK JS INCLUDES

     * @since 1.0
    ===============================
    */
    function wfc_load_js_scripts(){
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'jquery.easing.1.3', WFC_ADM_JS_URI.'/jquery.easing.1.3.js', array('jquery') );
        wp_register_script( 'jquery.lightbox', WFC_ADM_JS_URI.'/lightbox.js', array('jquery') );
        wp_register_script(
            'jquery-idea-gallery', WFC_ADM_JS_URI.'/jquery.ideagallery.2.2.js',
            array('jquery', 'jquery.easing.1.3', 'jquery.lightbox') );
        wp_enqueue_script( 'jquery.lightbox' );
        wp_enqueue_script( 'jquery-idea-gallery' );
    }

    add_action( 'wp_enqueue_scripts', 'wfc_load_js_scripts' );
    /*
    ===============================
    FRAMEWORK CSS INCLUDES

     * @since 1.0
    ===============================
    */
    function wfc_load_css_styles(){
        wp_register_style( 'ideagallery-style', WFC_ADM_CSS_URI.'/style.css' );
        wp_register_style( 'lightbox-style', WFC_ADM_CSS_URI.'/lightbox.css' );
        wp_enqueue_style( 'ideagallery-style' );
        wp_enqueue_style( 'lightbox-style' );
    }

    add_action( 'wp_print_styles', 'wfc_load_css_styles' );
    /*
    ===============================
    DISABLE RSS FEEDS

     * @since 1.8
    ===============================
    */
    function wfc_disable_feed(){
        wp_die( __( 'No feed available,please visit our <a href="'.get_bloginfo( 'url' ).'">homepage</a>!' ) );
    }

    add_action( 'do_feed', 'wfc_disable_feed', 1 );
    add_action( 'do_feed_rdf', 'wfc_disable_feed', 1 );
    add_action( 'do_feed_rss', 'wfc_disable_feed', 1 );
    add_action( 'do_feed_rss2', 'wfc_disable_feed', 1 );
    add_action( 'do_feed_atom', 'wfc_disable_feed', 1 );
    /*
    ===============================
    CLOSE COMMENTS GLOBALLY

     * @since 1.0
    ===============================
    */
    function wfc_close_comments( $data ){
        return false;
    }

    add_filter( 'comments_number', 'wfc_close_comments' );
    add_filter( 'comments_open', 'wfc_close_comments' );
    /*
    ===============================
    REMOVE ITEMS FROM ADMIN BAR

     * @since 1.8
    ===============================
    */
    function wfc_remove_admin_bar_items(){
        global $wp_admin_bar;
        global $current_user;
        if( $current_user->user_login != 'wfc' ){
            $wp_admin_bar->remove_menu( 'wpseo-menu' );
            $wp_admin_bar->remove_menu( 'comments' );
            $wp_admin_bar->remove_menu( 'new-content' );
            $wp_admin_bar->remove_menu( 'ngg-menu' );
        }
    }

    add_action( 'wp_before_admin_bar_render', 'wfc_remove_admin_bar_items' );
    /*
    ===============================
    REMOVE FROM USER PROFILE

     * @since 1.8
    ===============================
    */
    function wfc_hide_admin_bar_prefs(){
        echo '<style type="text/css">.show-admin-bar { display: none; }</style>';
    }

    add_action( 'admin_print_scripts-profile.php', 'wfc_hide_admin_bar_prefs' );
    /*
    ===============================
    REMOVE DASHBOARD WIDGETS

     * @since 2.0
    ===============================
    */
    function wfc_custom_dashboard_widgets(){
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

    add_action( 'wp_dashboard_setup', 'wfc_custom_dashboard_widgets' );
    /*
    ===============================
    SHOW FAVICON

     * @since 1.1
    ===============================
    */
    function wfc_fw_favicon(){
        echo '<link rel="shortcut icon" href="'.WFC_URI.'/favicon.ico"/>'."\n";
    }

    add_action( 'wp_head', 'wfc_fw_favicon' );
    /*
    ===============================
    ADD EDITOR STYLESHEET FOR ADMIN WYSIWYG

     * @since 1.0
    ===============================
    */
    add_editor_style( '/editor-style.css' );
    /*
    ===============================
    CONVERT IMG URL TO POST ID

     * @since 2.1
     *
     * @param string $image_src
     *
     * @return array
    ===============================
    */
    function wfc_imgurl_to_postid( $image_src ){
        global $wpdb;
        $new_img_src  = explode( 'uploads/', $image_src );
        $query        = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value ='$new_img_src[1]'";
        $id           = $wpdb->get_var( $query );
        $img_thumb    = wp_get_attachment_image_src( $id, 'idea-gallery-thumb' );
        $img_frame    = wp_get_attachment_image_src( $id, 'idea-gallery-frame' );
        $img_max_size = wp_get_attachment_image_src( $id, 'max-size-lightbox' );
        $arr          = array('thumb' => $img_thumb, 'frame' => $img_frame, 'max_size' => $img_max_size);
        return $arr;
    }

    /*
    ===============================
    ADD IMAGE SIZES FOR IDEA GALLERY SLIDER

     * @since 2.1
    ===============================
    */
    add_image_size( 'idea-gallery-thumb', 75, 50, true );
    add_image_size( 'idea-gallery-frame', 440, 304 );
    add_image_size( 'max-size-lightbox', 800, 600 );
