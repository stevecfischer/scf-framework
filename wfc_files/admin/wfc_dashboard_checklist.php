<?php
    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 2/3/14
     */
    /*------------------------------------------
    ~~~~~~ development section ~~~~~~~
    ------------------------------------------*/
    if( $_GET['wfcchecklist'] == 'email' ){
        wfc_email_checklist();
    }
    function wfc_wfc_dev_display( $post ){
        $checklist       = new wfc_checklist();
        $dev_human_array = array(
            'seo-spreadsheet'     => 'Have you received the SEO Keywords, Description, and Title Spreadsheet?',
            'seo-added-meta-info' => 'Have the SEO Keywords, Description, and Title Spreadsheet been entered?',
            'seo-move-old-pages'  => 'Did you move over all of the sites old pages?'
        );

        ?>
        <form action="<?php echo admin_url(); ?>?wfcchecklist=email" method="POST">
            <input type="hidden" name="checklist_section" value="Developer Checklist"/>

            <?php $checklist->render_checklist_array( $checklist->wfc_chklist_plugin_array, 'wfc_plugin_check' ); ?>
            <?php //$checklist->render_checklist_array( $checklist->wfc_chklist_option_array, 'wfc_option_check' ); ?>
            <?php $checklist->render_checklist_array( $checklist->wfc_chklist_pages_array, 'wfc_page_check' ); ?>
            <?php $checklist->render_checklist_array( $checklist->wfc_chklist_files_array, 'wfc_file_check' ); ?>
            <?php //$checklist->render_checklist_array( $checklist->wfc_chklist_scrap_array, 'wfc_scrap_check' ); ?>

            <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo wfc_checklist::chk_wfc_footer(); ?>">
                <input class="wfc-dashboard-checkbox" type="checkbox" name="WFC_Footer"/>
                <label>Did you set the standard WFC Footer ("Website Design by webfullcircle.com")</label><br/>
            </span>
            <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo wfc_checklist::chk_wfc_login(); ?>">
                <input class="wfc-dashboard-checkbox" type="checkbox" name="WFC_Logo"/>
                <label>Did you add the WFC Logo to the WP Login Screen?</label><br/>
            </span>
            <!--<div>
                <span>
                    <input type="submit" value="Submit"/>
                </span>
            </div>-->
        </form>
    <?php
    }

    /*------------------------------------------
    ~~~~~~ seo section ~~~~~~~
    ------------------------------------------*/
    /*------------------------------------------
~~~~~~ development section ~~~~~~~
------------------------------------------*/
    add_action( 'admin_init', 'wfc_dev_checklist', 1 );
    function wfc_dev_checklist(){
        add_meta_box(
            'wfc_develop_checklist',
            'WFC Development Checklist',
            'wfc_wfc_dev_display',
            'dashboard', 'normal'
        );
        if( isset($_POST['dev_section']) ){
            $option   = "wfc_dev_chklist";
            $newvalue = $_POST;
            if( !update_option( $option, $newvalue ) ){
                die('could not update option');
            }
        }
    }

    /**
     * @property mixed pages
     */
    class wfc_checklist
    {
        private $active_plugins = array();
        public $wfc_options;
        private $pages = array();
        public $wfc_chklist_plugin_array = array(
            'gravityforms'             => 'Gravity Forms',
            'cms-tree-page-view'       => 'Page Tree View',
            'threewp-activity-monitor' => 'Activity Monitor',
            'video-user-manuals' => 'Video User Manual',
            'wordpress-seo'      => 'WordPress SEO'
        );
        public $wfc_chklist_option_array = array(
            'blog_public'            => 'block search engines',
            'blogdescription'        => 'wordpress tagline',
            'default_comment_status' => 'commenting off by default (Settings > Discussion)',
            'comment_moderation'     => 'set all comments to be approved by Admin by default (Settings > Discussion)',
            'permalink_structure'    => 'set the permalinks to /%postname%/',
        );
        public $wfc_chklist_pages_array = array(
            'Sitemap'   => "Sitemap",
            'Thank You' => "Thank You"
        );
        public $wfc_chklist_files_array = array(
            'favicon.ico'             => "favicon.ico",
            '404.php'                 => "404.php",
            'template-full-width.php' => 'template-full-width.php'
        );
        public $wfc_chklist_scrap_array = array();

        public function wfc_checklist(){
            $this->wfc_set_checklist_scrap_array();
            $this->setWfcOptions();
            $this->setActivePlugins();
            $this->setPages();
            /*'"/Web Full Circle/i"' => ''.get_bloginfo( 'url' ).'/'.'/cms-wfc/wp-login.php',
            '"/<a href=\"http:\/\/www.webfullcircle.com\" target=\"_blank\">Webfullcircle.com<\/a>/i"' => WFC_SITE_URL*/
        }

        public function wfc_set_checklist_scrap_array(){
            $x                             = WFC_SITE_URL;
            $this->wfc_chklist_scrap_array = array(
                'Webfullcircle.com' => $x
            );
        }

        public function render_checklist_item( $v, $k ){
            ?>
            <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php $this->wfc_plugin_check( $k ); ?>">
                <input class="wfc-dashboard-checkbox" type="checkbox" name="task"/>
                <label><?php _e( $v ); ?></label><br/>
            </span>
        <?php
        }

        public function render_checklist_array( $arr, $callback_check ){
            foreach( $arr as $k => $v ){
                if( $callback_check == "wfc_scrap_check" ){
                    $result = call_user_func_array( array($this, $callback_check), array($v, $k) );
                } else{
                    $result = call_user_func( array($this, $callback_check), $k );
                }
                ?>
                <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo $result; ?>">
                    <input class="wfc-dashboard-checkbox" type="checkbox" name="task"/>
                    <label><?php _e( $v ); ?></label><br/>
                </span>
            <?php
            }
        }

        /**
         * @param mixed $wfc_options
         */
        public function setWfcOptions(){
            global $wpdb;
            $where_clause_str = '';
            foreach( $this->wfc_chklist_option_array as $option_k => $option_v ){
                $where_clause_str .= "'$option_k',";
            }
            $tmp_options       =
                $wpdb->get_results( "SELECT `option_name`,`option_value` FROM $wpdb->options WHERE option_name IN (".substr( $where_clause_str, 0, -1 ).") ", ARRAY_A );
            $this->wfc_options = array();
            foreach( $tmp_options as $option ){
                $this->wfc_options[$option['option_name']] = $option['option_value'];
            }
        }

        private function setPages(){
            $pages = get_pages();
            foreach( $pages as $page ){
                $this->pages[$page->post_name] = $page->post_title;
            }
        }

        private function setActivePlugins(){
            $plugins = get_option( 'active_plugins' );
            foreach( $plugins as $plugin ){
                $x = explode( "/", $plugin );
                array_push( $this->active_plugins, $x[0] );
            }
        }

        public function wfc_plugin_check( $handle ){
            return $this->return_checked( in_array( $handle, $this->active_plugins ) );
        }

        public function wfc_option_check( $handle ){
            return $this->return_checked( in_array( $handle, $this->wfc_options ) );
        }

        public function wfc_page_check( $handle ){
            return $this->return_checked( in_array( $handle, $this->pages ) );
        }

        public function wfc_file_check( $handle ){
            $wfc_root = scandir( WFC_THEME_ROOT.'/' );
            return $this->return_checked( in_array( $handle, $wfc_root ) );
        }

        static function chk_wfc_footer(){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, WFC_SITE_URL );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close( $ch );
            if( preg_match( "/<a href=\"http:\/\/www.webfullcircle.com\" target=\"_blank\">Webfullcircle.com<\/a>/i", $output ) ){
                return " wfc-checklist-valid ";
            } else{
                return " wfc-checklist-invalid ";
            }
        }

        static function chk_wfc_login(){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, WFC_SITE_URL.'cms-wfc/wp-login.php' );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close( $ch );
            //Web Full Circle
            $search = "/Username/i";
            if( preg_match( $search, $output ) ){
                return " wfc-checklist-valid ";
            } else{
                return " wfc-checklist-invalid ";
            }
        }

        //@scftodo: this doesn't work yet. use the two static methods above.
        public function wfc_scrap_check( $url, $str ){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close( $ch );
            return $this->return_checked( preg_match( "/$str/i", $output ) );
        }

        public function return_checked( $checked ){
            if( $checked === true ){
                return " wfc-checklist-valid ";
            } else{
                return " wfc-checklist-invalid ";
            }
        }
    }

    //@scftodo: email is broken...
    function wfc_email_checklist(){
        global $current_user;
        $headers[] = 'From: Website Name <me@example.net>';
        $headers[] = 'Cc: steve fischer <steve.fischer@webfullcircle.com>';
        $to        = 'stevecfischer@gmail.com';
        $subject   = 'Checklist for '.$_POST['checklist_section'];
        $message   = 'Checklist completed and ready for next step.';
        if( !wp_mail( $to, $subject, $message, $headers ) ){
            die("Error emailing form");
        }
    }