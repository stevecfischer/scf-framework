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
            add_action( 'admin_head', array(&$this, 'Wfc_framework_variables') );
            add_action( 'wfc_footer', array(&$this, 'Wfc_framework_variables') );
            add_action( 'load-page-new.php', array(&$this, 'wfc_custom_help_page') );
            add_action( 'load-page.php', array(&$this, 'wfc_custom_help_page') );
            add_filter( 'manage_campaign_posts_columns', array(&$this, 'wfc_add_post_thumbnail_column'), 5 );
            add_filter( 'manage_news_posts_columns', array(&$this, 'wfc_add_post_thumbnail_column'), 5 );
            add_filter( 'manage_homepageboxes_posts_columns', array(&$this, 'wfc_add_post_thumbnail_column'), 5 );
            add_filter( 'manage_subpagebanner_posts_columns', array(&$this, 'wfc_add_post_thumbnail_column'), 5 );
            add_filter( 'manage_page_posts_columns', array(&$this, 'wfc_add_post_thumbnail_column'), 5 );
            add_action(
                'manage_campaign_posts_custom_column', array(
                                                            &$this,
                                                            'wfc_display_post_thumbnail_column'
                                                       ), 5, 2 );
            add_action( 'manage_news_posts_custom_column', array(&$this, 'wfc_display_post_thumbnail_column'), 5, 2 );
            add_action(
                'manage_homepageboxes_posts_custom_column', array(
                                                             &$this,
                                                             'wfc_display_post_thumbnail_column'
                                                        ), 5, 2 );
            add_action( 'manage_page_posts_custom_column', array(&$this, 'wfc_display_post_thumbnail_column'), 5, 2 );

            //test commit
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
         *
         * @param string $cpt cpt name
         *
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
         * @param array $cols old columns
         *
         * @return array $cols new columns
         * @since 5.1
         */
        function wfc_add_post_thumbnail_column( $cols ){
            $cols['wfc_post_thumb'] = __( 'Featured' );
            return $cols;
        }

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

        public function _wfc_deprecated_argument( $function, $version, $message = NULL ){
            do_action( 'wfc_deprecated_argument_run', $function, $message, $version );
            // Allow plugin to filter the output error trigger
            if( WP_DEBUG && apply_filters( 'deprecated_argument_trigger_error', true ) ){
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
            }
        }

        public function Wfc_framework_variables(){
            /**
             * @name Framework Variables
             *      this plugin allows js to use php definitions.
             *      I created it to help with ajax, image, and file paths.
             *      Issues almost always arise when working in between local, dev, and live
             *      enviroments.
             *
             *      Example:
             *      jQuery(function($){
             *          var wfcDefines = $('body').wfc_fw_variables();
             *          console.log(wfcDefines.define('wfc_theme_name'));
             *      });
             *
             * @version 0.1
             *
             */
            ?>
            <script type="text/javascript">
                (function ($) {
                    $.fn.wfc_fw_variables = function () {
                        var phpDefs = { <?php
                                echo ' "wfc_site_url" : "'.addslashes(WFC_SITE_URL).'",
                                "wfc_admin_url" : "'.WFC_ADMIN_URL.'",
                                "wfc_pt" : "'.addslashes(WFC_PT).'",
                                "wfc_config" : "'.addslashes(WFC_CONFIG).'",
                                "wfc_uri" : "'.addslashes(WFC_URI).'",
                                "wfc_theme_name" : "'.get_option('template').'",
                                "wfc_adm" : "'.addslashes(WFC_ADM).'" ';
                            ?> }
                        return {
                            phpDefs: phpDefs,
                            define : function (val) {
                                return this.phpDefs[val];
                            }
                        };
                    };
                }(jQuery));
            </script>
        <?php
        }

        public function wfc_custom_help_page(){
            add_filter( 'contextual_help', array(&$this, 'wfc_custom_page_help') );
        }

        public function wfc_custom_page_help( $help ){
            $help .= "<h5>WFC Shortcodes</h5>";
            $help .= '<p>- [wfc_sitmap] : displays a sitemap. params: [exclude](string) = Comma seperated page ids to exclude from sitemap.  Must be comma seperated. Ex. [wfc_sitemap exclude="10,20,30"]</p>';
            $help .= "<p>- [wfc_atoz] : displays all pages from A to Z in tab formatting. May require additional styling.</p>";
            return $help;
        }
    }

    /*
     * Init Wfc Admin Class
     *
     * @since 5.1
     */
    $GLOBALS['wfc_admin'] = new Wfc_Admin_Class();