<?php
    /**
     * Class to manage all admin functions.
     *
     * @package scf-framework
     * @author Steve (11/01/2013)
     */
    class Wfc_Admin_Class
    {
        /**
         * @var array $new_cpts
         */
        public $active_cpts = array();

        public function Wfc_Admin_Class(){
            $this->get_active_cpts();
        }
        /**
         * Get all active custom post types
         *
         * @since 5.1
         */
        public function get_active_cpts(){
            $this->active_cpts = get_option( 'wfc_activate_cpt' );
        }

        /**
         * Tell if the cpt is active or not
         *
         * @since 5.1
         * @param string $cpt cpt name
         * @return boolean active or not
         */
        public function is_active_cpt( $cpt ){
            if( !is_array( $this->active_cpts ) ){
                return false;
            } elseif( in_array( $cpt, $this->active_cpts ) ){
                return true;
            } else{
                return false;
            }
        }

        /**
         * Add featured image in admin list view for custom CPT's
         *
         * @since 5.1
         */
        public function featured_img_column(){
            /**
             * Add featured image in admin list view for custom CPT's
             *
             * @param array $cols old columns
             *
             * @return array $cols new columns
             * @since 5.1
             */
            function wfc_add_post_thumbnail_column( $cols ){
                $cols['wfc_post_thumb'] = __( 'Featured' );
                return $cols;
            }

            add_filter( 'manage_campaign_posts_columns', 'wfc_add_post_thumbnail_column', 5 );
            add_filter( 'manage_news_posts_columns', 'wfc_add_post_thumbnail_column', 5 );
            add_filter( 'manage_homeboxes_posts_columns', 'wfc_add_post_thumbnail_column', 5 );
            add_filter( 'manage_subpagebanner_posts_columns', 'wfc_add_post_thumbnail_column', 5 );
            /**
             * Displays featured image in admin list view for custom CPT's
             * Called during the loop to display
             *
             * @param array $col the column currently parsed
             * @param integer $id id of the post
             *
             * @since 5.1
             */
            function wfc_display_post_thumbnail_column( $col, $id ){
                switch( $col ){
                    case 'wfc_post_thumb':
                        echo the_post_thumbnail( 'thumbnail' );
                        break;
                }
            }

            add_action( 'manage_campaign_posts_custom_column', 'wfc_display_post_thumbnail_column', 5, 2 );
            add_action( 'manage_news_posts_custom_column', 'wfc_display_post_thumbnail_column', 5, 2 );
            add_action( 'manage_homeboxes_posts_custom_column', 'wfc_display_post_thumbnail_column', 5, 2 );
            add_action( 'manage_subpagebanner_posts_custom_column', 'wfc_display_post_thumbnail_column', 5, 2 );
        }

        public function _wfc_deprecated_argument( $function, $version, $message = NULL ){
            do_action( 'wfc_deprecated_argument_run', $function, $message, $version );
            // Allow plugin to filter the output error trigger
            if( WP_DEBUG && apply_filters( 'deprecated_argument_trigger_error', true ) ){
                echo 'ssssssssss';
                if( function_exists( '__' ) ){
                    if( !is_null( $message ) ){
                        trigger_error( sprintf( __( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s' ), $function, $version, $message ) );
                    } else{
                        trigger_error( sprintf( __( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.' ), $function, $version ) );
                    }
                } else{
                    if( !is_null( $message ) ){
                        trigger_error( sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s', $function, $version, $message ) );
                    } else{
                        trigger_error( sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.', $function, $version ) );
                    }
                }
            }else{
                echo 'ererer';
            }
        }
    }

    /*
     * Init Wfc Admin Class
     *
     * @since 5.1
     */
    $GLOBALS['wfc_admin'] = new Wfc_Admin_Class();