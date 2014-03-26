<?php
    /**
     * Framework configuration
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     * @since 2.2
     */
    /** @var $wfc_admin Wfc_Admin_Class */
    /**
     * Disable RSS feeds
     *
     * @since 1.8
     */
    function wfc_disable_feed(){
        if( $this->wfc_full_access() ){
            return;
        }
        wp_die( __( 'No feed available,please visit our <a href="'.get_bloginfo( 'url' ).'">homepage</a>!' ) );
    }

    add_action( 'do_feed', 'wfc_disable_feed', 1 );
    add_action( 'do_feed_rdf', 'wfc_disable_feed', 1 );
    add_action( 'do_feed_rss', 'wfc_disable_feed', 1 );
    add_action( 'do_feed_rss2', 'wfc_disable_feed', 1 );
    add_action( 'do_feed_atom', 'wfc_disable_feed', 1 );
    /**
     * Close comments everywhere
     *
     * @since 1.0
     */
    function wfc_close_comments( $data ){
        return false;
    }

    if( !$wfc_admin->wfc_full_access() ){
        add_filter( 'comments_number', 'wfc_close_comments' );
        add_filter( 'comments_open', 'wfc_close_comments' );
    }
