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
            if( isset($_GET) && isset($_GET['wfc_update_wp_options']) && $_GET['wfc_update_wp_options'] == "update_wp_options" ){
                $this->wfc_update_wp_options();
            }
            $this->wfc_shortcode_widget();
            $this->get_active_cpts();
            add_action( 'admin_head', array(&$this, 'wfc_framework_variables') );
            add_action( 'widgets_init', array(&$this, 'wfc_manage_sidebar_widgets') );
            add_action( 'the_content', array(&$this, 'wfc_auto_content') );
            add_shortcode( 'wfcimg', array(&$this, 'wfc_img_uri_deprecated') );
            add_shortcode( 'wfc_img_uri', array(&$this, 'wfc_img_uri') );
            add_action( 'wfc_footer', array(&$this, 'wfc_framework_variables') );
            add_action( 'load-page-new.php', array(&$this, 'wfc_custom_help_page') );
            add_action( 'load-page.php', array(&$this, 'wfc_custom_help_page') );
            add_action( 'wp_dashboard_setup', array(&$this, 'wfc_manage_dashboard_widgets') );
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
            /* @sftodo: working on moving cpt registering to here from wfc_theme_customizer. */
            //add_action( 'manage_page_posts_custom_column', array(&$this, 'wfc_display_post_thumbnail_column'), 5, 2 );
            //add_action( 'init', array(&$this, 'wfc_init_cpt') );
        }

        /**
         * The WFC Framework version string
         *
         * @global string $wfc_version
         * @since 5.2
         */
        public static function wfc_grab_version(){
            $_dir    = __DIR__.'/../../';
            $d       = scandir( $_dir );
            $version = 0;
            foreach( $d as $e ){
                if( substr( $e, 0, 4 ) == 'Ver_' && substr( $e, -4 ) == '.wfc' ){
                    $version = substr( $e, 4, -4 );
                }
            }
            return $version;
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

        public function wfc_framework_variables(){
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

        public function wfc_init_cpt(){
            if( empty($this->active_cpts) ){
                return;
            }
            foreach( $this->active_cpts as $cpt ){
                $module_args = array(
                    'cpt'       => $cpt /* CPT Name */,
                    'menu_name' => $cpt /* Overide the name above */,
                    'supports'  => array(
                        'title',
                        'page-attributes',
                        'thumbnail',
                        'editor'
                    ) /* specify which metaboxes you want displayed. See Codex for more info*/,
                );
                $module      = new wfcfw($module_args);
            }
        }

        /**
         * ENABLE SHORTCODES INSIDE TEXT WIDGETS
         *
         * @since 5.4
         */
        public function wfc_shortcode_widget(){
            add_filter( 'widget_text', 'do_shortcode' );
        }

        /**
         * use image contstant in shortcode
         *
         * @since 5.1
         *
         * @param int $deprecated Not Used.
         *
         * @return string uri to theme image folder
         */
        public function wfc_img_uri_deprecated( $deprecated = '' ){
            global $wfc_admin;
            if( empty($deprecated) ){
                $wfc_admin->_wfc_deprecated_argument( __FUNCTION__, '5.4', 'Current shortcode to use: WFC_IMG_URI' );
            }
            return WFC_IMG_URI;
        }

        /**
         * use image contstant in shortcode
         *
         * @since 5.4
         *
         *
         * @return string uri to theme image folder
         */
        public function wfc_img_uri(){
            return WFC_IMG_URI;
        }

        /**
         * If a page has no content
         * Displays a default one
         *
         * @package scf-framework
         * @author Steve (12/10/2012)
         *
         * @param string $content content before
         *
         * @return string $content content after
         */
        public function wfc_auto_content( $content ){
            if( is_page() ){
                $wfc_option = get_option( 'wfc_default_content' );
                if( $content == '' && empty($wfc_option) ){
                    $content = '<h1>HTML Ipsum Presents</h1>
<p>Aenectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper.
<em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci,
 sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>
<img src="8.3.342.1/themes/base/clear.gif" style="float:left; padding:0 12px 12px 0">
<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris
 placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac
 dui. <strong>Pellentesque habitant morbi tristique</strong> donec non enim in turpis pulvinar facilisis. Ut felis. Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt
 quis, accumsan porttitor, facilisis luctus, metus.</p>
<h2>Header Level 2</h2>
<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris
 placerat eleifend leo.</p>
<ol>
<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. </li><li><a href="#">Aliquam tincidunt mauris eu risus.</a> </li><li>Vestibulum auctor dapibus neque. </li></ol>
<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris
 placerat eleifend leo.</p>
<blockquote>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit
 sit amet quam. Vivamus pretium ornare est.</p>
</blockquote>
<h3>Header Level 3</h3>
<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris
 placerat eleifend leo.</p>
<img src="8.3.342.1/themes/base/clear.gif" style="float:right; padding:0 0 12px 12px">
<ul>
<li>Morbi in sem <a href="#">quis dui placerat</a> ornare. Pellentesque odio nisi, euismod in, pharetra a, ultricies in, diam. Sed arcu. Cras consequat.
</li><li>Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus.
</li><li>Phasellus ultrices nulla quis nibh. Quisque a lectus. Donec consectetuer ligula vulputate sem tristique cursus. Nam nulla quam, gravida non, commodo a, sodales sit amet, nisi.
</li><li>Pellentesque fermentum dolor. Aliquam quam lectus, facilisis auctor, ultrices ut, elementum vulputate, nunc.
</li></ul>
<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris
 placerat eleifend leo.</p>
<h4>Header Level 2</h4>
<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris
 placerat eleifend leo.</p>';
                    return $content;
                }
            }
            return $content;
        }

        public function wfc_update_wp_options(){
            /*
             * General
             * ===================
             * blogdescription -string
             * timezone_string -string
             * image_default_link_type -string
             *
             *
             * General > Discussion
             * ====================
             * comment_registration -boolean
             * require_name_email -boolean
             * default_comment_status -boolean
             * comment_moderation -boolean
             */
            $wfc_settings_array = array(
                'comment_registration'    => 1,
                'require_name_email'      => 1,
                'default_comment_status'  => 1,
                'comment_moderation'      => 1,
                'timezone_string'         => "America/New_York",
                'blogdescription'         => "",
                'image_default_link_type' => "none"
            );
            foreach( $wfc_settings_array as $setting_i => $setting_v ){
                update_option( $setting_i, $setting_v );
            }
            header( "Location: admin.php?page=wfc_theme_customizer.php&settings_updated=true" );
        }

        public function wfc_manage_sidebar_widgets(){
            $disable_widgets = get_option( 'wfc_disabled_widgets' );
            if( !is_array( $disable_widgets ) ){
                return;
            }
            foreach( $disable_widgets as $disable ){
                unregister_widget( $disable );
            }
        }

        public function wfc_manage_dashboard_widgets(){
            $disable_widgets = get_option( 'wfc_dashboard_disabled_widgets' );
            if( !is_array( $disable_widgets ) ){
                return;
            }
            foreach( $disable_widgets as $disable ){
                $arr = explode( "-", $disable );
                remove_meta_box( $arr[0], 'dashboard', $arr[1] );
            }
        }
    }

    /*
     * Init Wfc Admin Class
     *
     * @since 5.1
     */
    $GLOBALS['wfc_admin'] = new Wfc_Admin_Class();
    // @sftodo: not the best but it will work for now.
    $wfc_version = WFC_Admin_Class::wfc_grab_version();