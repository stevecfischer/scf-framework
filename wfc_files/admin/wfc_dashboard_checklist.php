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
        $value = get_option( "wfc_dev_chklist" );
        $chked = false;
        $dev_check = new wfc_checklist();
        $dev_options_array = array(
            'wordpress-seo' => 'Wordpress SEO'
        );
        $dev_human_array   = array(
            'seo-spreadsheet'     => 'Have you received the SEO Keywords, Description, and Title Spreadsheet?',
            'seo-added-meta-info' => 'Have the SEO Keywords, Description, and Title Spreadsheet been entered?',
            'seo-move-old-pages'  => 'Did you move over all of the sites old pages?'
        );
        $dev_plugin_array = array(
            'gravity-forms' => 'Gravity Forms',
            'cms-tree-page-view' => 'Page Tree View',
            'threewp-activity-monitor' => 'Activity Monitor',
            'video-user-manuals' => 'Video User Manual'
        );
        $wfc_option_array = array(
            'blog_public' => 'block search engines',
            'blogdescription' => 'Wordpress tagline'
        );
        ?>
        <form action="<?php echo admin_url(); ?>?wfcchecklist=email" method="POST">
            <input type="hidden" name="checklist_section" value="Developer Checklist"/>
            <label>Team Member Name</label><br/>
            <input type="text" name="fname"/><br/>


            <?php foreach( $dev_plugin_array as $dev_plugin_k => $dev_plugin_v ){ ?>
                <span <?php $dev_check->wfc_plugin_check( $dev_plugin_k ); ?>>
                    <input type="checkbox" name="task"/>
                    <label>Did you install <?php _e( $dev_plugin_v ); ?> plugin?</label><br/>
                </span>
            <?php } ?>

        <?php foreach( $wfc_option_array as $wfc_option_k => $wfc_option_v ){ ?>
            <span <?php $dev_check->wfc_option_check( $wfc_option_k ); ?>>
                <input type="checkbox" name="block_search_engines"/>
                <label>Did you set WordPress up to block search engines (Settings > Privacy)?</label><br/>
            </span>
            <?php } ?>

            <span <?php wfc_dev_cl::chk_coms(); ?>>
                <input type="checkbox" name="commenting"/>
                <label>Did you turn commenting off by default (Settings > Discussion)?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_moder(); ?>>
                <input type="checkbox" name="comments_to_be_approved" <?php echo $chked['comments_to_be_approved']; ?>/>
                <label>Did you set all comments to be approved by Admin by default (Settings > Discussion)?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_perma(); ?>>
                <input type="checkbox" name="permalinks"/>
                <label>Did you set the permalinks to /%postname%/?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_plug( 'cms-tree-page-view', 'index' ); ?>>
                <input type="checkbox" name="Page_Tree_View"/>
                <label>Did you install Page Tree View Plugin?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_plug( 'video-user-manual', 'plugin' ); ?>>
                <input type="checkbox" name="WordPress_Manual"/>
                <label>Did you install WordPress Manual Plugin?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_stmap(); ?>>
                <input type="checkbox" name="Sitemap_Page"/>
                <label>Did you setup a Sitemap Page?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_smxml(); ?>>
                <input type="checkbox" name="XML_Sitemap"/>
                <label>Did you setup a XML Sitemap?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_wfc_footer(); ?>>
                <input type="checkbox" name="WFC_Footer"/>
                <label>Did you set the standard WFC Footer (website design & internet marketing)</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_thk_u(); ?>>
                <input type="checkbox" name="Thank_You_page"/>
                <label>Did you create a Thank You page?</label><br/>
            </span>
            <span>
                <input type="checkbox" name="test_forms"/>
                <label>How many forms did you test? __________</label><br/>
            </span>
            <span>
                <input type="checkbox" name="confirm_forms_submit"/>
                <label>Did you confirm all form submissions were received to designated address?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_favi(); ?>>
                <input type="checkbox" name="Favicon"/>
                <label>Did you create a Favicon?</label><br/>
            </span>
            <span <?php wfc_dev_cl::chk_wfc_login(); ?>>
                <input type="checkbox" name="WFC_Logo"/>
                <label>Did you add the WFC Logo to the WP Login Screen?</label><br/>
            </span>
            <span <?php //wfc_dev_cl::chk_four_o_four(); ?>>
                <input type="checkbox" name="friendly_404_500"/>
                <label>Did you create a friendly 404/500 page?</label><br/>
            </span>
            <span>
                <input type="checkbox" name="remove_wp_version"/>
                <label>Did you remove wordpress version from source code?</label><br/>
            </span>
            <span>
                <input type="submit" value="Submit"/>
            </span>
        </form>
    <?php
    }

    /*------------------------------------------
    ~~~~~~ seo section ~~~~~~~
    ------------------------------------------*/
    function wfc_wfc_seo_display( $post ){
        $chked     = false;
        $seo_check = new wfc_checklist();
        $seo_options_array = array(
            'wordpress-seo' => 'Wordpress SEO'
        );
        $seo_human_array   = array(
            'seo-spreadsheet'     => 'Have you received the SEO Keywords, Description, and Title Spreadsheet?',
            'seo-added-meta-info' => 'Have the SEO Keywords, Description, and Title Spreadsheet been entered?',
            'seo-move-old-pages'  => 'Did you move over all of the sites old pages?'
        );
        $seo_plugin_array = array(
            'wordpress-seo' => 'Wordpress SEO'
        );
        ?>
        <form action="<?php echo admin_url(); ?>?wfcchecklist=email" method="POST">
            <input type="hidden" name="checklist_section" value="SEO Checklist"/>
            <span>
                <label>Team Member Name</label><br/>
                <input type="text" name="fname"/><br/>
            </span>
            <?php foreach( $seo_human_array as $seo_human_k => $seo_human_v ){ ?>
                <span>
                    <input type="checkbox" name="<?php _e( $seo_human_k ); ?>"/>
                    <label><?php _e( $seo_human_v ); ?></label><br/>
                </span>
            <?php } ?>
            <?php foreach( $seo_plugin_array as $seo_plugin_k => $seo_plugin_v ){ ?>
                <span <?php $seo_check->wfc_plugin_check( $seo_plugin_k ); ?>>
                    <input type="checkbox" name="task"/>
                    <label>Did you install <?php _e( $seo_plugin_v ); ?> plugin?</label><br/>
                </span>
            <?php } ?>
            <input type="submit" value="Submit"/>
        </form>
    <?php
    }

    /*------------------------------------------
    ~~~~~~ seo section ~~~~~~~
    ------------------------------------------*/
    function wfc_wfc_cont_display( $post ){
        $chked = false;
    }

    /*------------------------------------------
~~~~~~ development section ~~~~~~~
------------------------------------------*/
    if( wfc_is_dev() ){
        add_action( 'admin_init', 'wfc_dev_checklist', 1 );
    }
    add_action( 'admin_init', 'wfc_seo_checklist', 1 );
    //add_action( 'admin_init', 'wfc_cont_checklist', 1 );
    function wfc_dev_checklist(){
        add_meta_box(
            'develop_checklist',
            'WFC Developer Checklist',
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

    function wfc_seo_checklist(){
        add_meta_box(
            'seo_checklist',
            'WFC SEO Checklist',
            'wfc_wfc_seo_display',
            'dashboard', 'normal'
        );
    }

    function wfc_cont_checklist(){
        add_meta_box(
            'content_checklist',
            'WFC Design/Content Checklist',
            'wfc_wfc_cont_display',
            'dashboard', 'normal'
        );
    }

    class  wfc_dev_cl
    {

        public $wfc_options;

        public function wfc_dev_cl(){
            $this->setWfcOptions();
        }
        /**
         * @param mixed $wfc_options
         */
        public function setWfcOptions(){
            global $wpdb;

            $this->wfc_options = $wpdb->query("SELECT * FROM $wpdb->options")->fetchAll();
            print_r($this->wfc_options);
        }



        static function chk_plug( $dir, $file ){
            if( is_plugin_active( $dir.'/'.$file.'.php' ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_ping(){
            if( get_option( 'default_ping_status' ) == 'closed' ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_coms(){
            if( get_option( 'default_comment_status' ) == 'closed' && get_option( 'close_comments_days_old' ) == 1 ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_moder(){
            if( get_option( 'comment_moderation' ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_perma(){
            if( get_option( 'permalink_structure' ) == '/%postname%/' ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_priv(){
            if( !get_option( 'blog_public' ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_bldes(){
            if( get_option( 'blogdescription' ) != 'Just another WordPress site' ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_favi(){
            $wfc_root = scandir( $_SERVER['DOCUMENT_ROOT'].'/' );
            if( in_array( 'favicon.ico', $wfc_root ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_usr(){
            $blogusers = get_users();
            foreach( $blogusers as $user ){
                if( $user->display_name != 'wfc' && $user->display_name != 'admin' ){
                    echo 'class ="good" ';
                } else{
                    echo 'class ="bad" ';
                }
            }
        }

        static function chk_smxml(){
            $wfc_root = scandir( $_SERVER['DOCUMENT_ROOT'].'/' );
            if( in_array( 'sitemap.xml', $wfc_root ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_four_o_four(){
            $theme_root = scandir( get_bloginfo( 'stylesheet_directory' ) );
            print_r( $theme_root );
            if( in_array( '404.php', $theme_root ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_thk_u(){
            $pages = get_pages();
            foreach( $pages as $page ){
                if( $page->post_title == 'Thank You' ){
                    echo 'class ="good" ';
                } else{
                    echo 'class ="bad" ';
                }
            }
        }

        static function chk_stmap(){
            $pages = get_pages();
            foreach( $pages as $page ){
                if( $page->post_title == 'Sitemap' ){
                    echo 'class ="good" ';
                } else{
                    echo 'class ="bad" ';
                }
            }
        }

        static function chk_wfc_footer(){
            $homePage = get_bloginfo( 'url' );
            $ch       = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $homePage );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $output = curl_exec( $ch );
            curl_close( $ch );
            if( preg_match( "/Website Design/i", $output ) && preg_match( "/Internet Marketing/i", $output ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }

        static function chk_wfc_login(){
            $adminLogin = get_bloginfo( 'url' ).'/cms-wfc/';
            $ch         = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $adminLogin );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $output = curl_exec( $ch );
            curl_close( $ch );
            if( preg_match( "/Web Full Circle/i", $output ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }
    } //EOC
    /*
    ===============================
       Class for SEO checklist
    ===============================
    */
    class  wfc_seo_cl
    {
        static function chk_plug( $dir, $file = "" ){
            if( is_plugin_active( $dir.'/'.$file.'.php' ) ){
                echo 'class ="good" ';
            } else{
                echo 'class ="bad" ';
            }
        }
    }

    //EOC
    class wfc_checklist
    {
        private $active_plugins;
        public $wfc_options;

        public function wfc_checklist(){
            $this->setWfcOptions();
            $this->setActivePlugins();
        }
        /**
         * @param mixed $wfc_options
         */
        public function setWfcOptions(){
            global $wpdb;
            $options_needed = array(
                'blog_public' => 'block search engines',
                'blogdescription' => 'Wordpress tagline'
            );
            $where_clause_str = '';
            foreach( $options_needed as $option_k => $option_v ){
                $where_clause_str .= "'$option_k',";
            }
            $tmp_options = $wpdb->get_results("SELECT `option_name`,`option_value` FROM $wpdb->options WHERE option_name IN (".substr( $where_clause_str, 0, -1 ).") ", ARRAY_A);
            $this->wfc_options = array();
            foreach( $this->wfc_options as $option ){
                $this->wfc_options[$option['option_name']] = $option['option_value'];
            }
            print_r($this->wfc_options);
            echo "<br />";
            echo "<br />";
        }

        private function setActivePlugins(){
            $this->active_plugins = get_option( 'active_plugins' );
            print_r($this->active_plugins);
            echo "<br />";
            echo "<br />";
            //print_r($this->wfc_options);
            die();
        }

        public function wfc_plugin_check( $plugin_handle ){
            array_filter($this->active_plugins, function($el) use ($plugin_handle) {
                if( strpos($el, $plugin_handle) !== false ){
                    echo 'class ="good" ';
                } else{
                    echo 'class ="bad" ';
                }
            });
        }
        public function wfc_option_check( $option_handle ){
            array_filter($this->wfc_options, function($el) use ($option_handle) {
                if( strpos($el, $option_handle) !== false ){
                    echo 'class ="good" ';
                } else{
                    echo 'class ="bad" ';
                }
            });
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
        if(!wp_mail( $to, $subject, $message, $headers )){
            die("Error emailing form");
        }

    }