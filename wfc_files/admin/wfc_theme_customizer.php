<?php
    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 1/5/13
     * @version 2.2
     */
    $save_options = array(
        'siteurl', //Wordpress URL
        'home', //Index URL
        'active_plugins', //List of active plugins
        'template' //Active theme
    );
    $themename    = get_bloginfo( 'name' );
    $shortname    = "wfc_";
    $categories   = get_categories( 'hide_empty=0&orderby=name' );
    $wp_cats      = array();
    foreach( $categories as $category_list ){
        $wp_cats[$category_list->cat_ID] = $category_list->cat_name;
    }
    array_unshift( $wp_cats, "Choose a category" );
    $options = array(
        array(
            "name" => "To easily use the Web Full Circle theme, you can use the menu below.",
            "type" => "title"
        ),
        array(
            "name" => "General",
            "type" => "section"
        ),
        array("type" => "open"),
        array(
            "name" => "(**BETA**)<br />Click to peform auto settings check <a href='admin.php?wfc_update_wp_options=update_wp_options'>Update Settings</a><br /><br />**WARNING by performing this action a query will update select settings per WFC Standards. It will undo any custom settings.<br /><br />See here for <a target='_blank' href='https://github.com/stevecfischer/scf-framework/wiki/Default-Settings'>list of settings that will be updated.</a>",
            "type" => "information"
        ),
        array(
            "name"    => "WFC Core CPT",
            "desc"    => "Select the CPT you want to activate",
            "id"      => $shortname."activate_cpt",
            "type"    => "checkbox",
            "options" => array(
                "EXAMPLE_CPT",
                "CAMPAIGN_CPT",
                "SUBPAGE_BANNER_CPT",
                "HOME_BOXES_CPT",
                "NEWS_CPT",
                "TESTIMONIAL_CPT",
                "PORTFOLIO_CPT"
            ),
            "std"     => "EXAMPLE_CPT"
        ),
        array(
            "name"    => "WFC Sidebar Widgets",
            "desc"    => "(**BETA**) Select which widgets to disable. (**BETA**)",
            "id"      => $shortname."disabled_widgets",
            "type"    => "checkbox",
            "options" => array(
                "WFC_Custom_Nav_Widget"   => "WFC Custom Nav",
                "wfc_spotlight"           => "WFC Spotlight",
                "WFC_Widget_Recent_Posts" => "WFC Recent Posts"
            )
        ),
        array(
            "name"    => "WFC Dashboard Widgets",
            "desc"    => "(**BETA**) Select which dashboard widgets to disable. (**BETA**)",
            "id"      => $shortname."dashboard_disabled_widgets",
            "type"    => "checkbox",
            "options" => array(
                "dashboard_right_now-normal"     => "At a Glance",
                "dashboard_activity-normal"      => "Activity",
                "dashboard_primary-side"         => "WordPress News",
                "dashboard_quick_press-side"     => "Quick Draft",
                "wfc_develop_checklist-normal"   => "WFC Checklist (**BETA**)",
                "wfc_developer_dashboard-normal" => "WFC Dashboard",
            )
        ),
        array(
            "name"    => "WFC Client Admin menu",
            "desc"    => "Select admin menu items to hide",
            "id"      => $shortname."admin_menu",
            "type"    => "checkbox",
            "options" => array(
                "Comments",
                "Activity Monitor",
                "Theme Editor",
                "Widgets",
                "Menus",
                "Appearance",
                "Settings",
                "Posts",
                "Plugins",
                "Tools",
                "Yoast SEO"
            )
        ),
        array(
            "name"    => "Plugin Update Flags",
            "desc"    => "Select how to manage update flags",
            "id"      => $shortname."plugin_update_flags",
            "type"    => "checkbox",
            "options" => array(
                "plugin_update_flags-prevent-updating"  => "Disable ability for non-wfc users to update plugins",
                "plugin_update_flags-plugin-disclaimer" => "Enable WFC Plugin Disclaimer for non-wfc users",
                "plugin_update_flags-hide-update"       => "Hide update count from admin menu for non-wfc user",
            )
        ),
        array(
            "name"    => "WFC Default Content",
            "desc"    => "Display default content in empty pages",
            "id"      => $shortname."default_content",
            "type"    => "checkbox",
            "options" => array(
                "Do not display default content"
            )
        ),
        array(
            "name"    => "Enable Commenting",
            "desc"    => "Allow commenting",
            "id"      => $shortname."enable_commenting",
            "type"    => "checkbox",
            "options" => array(
                "Enable Commenting"
            )
        ),
        array(
            "name"    => "Adminbar Visibility",
            "desc"    => "Hide Adminbar",
            "id"      => $shortname."adminbar_visibility",
            "type"    => "checkbox",
            "options" => array(
                "Hide Adminbar"
            )
        ),
        array(
            "name"    => "Autoload Assests",
            "desc"    => "Automatically enqueue all CSS and JS files (**BETA**)",
            "id"      => $shortname."autoload_assets",
            "type"    => "checkbox",
            "options" => array(
                "Enable Autoload"
            )
        ),
        array(
            "name" => "Custom CSS",
            "desc" => "Want to add any custom CSS code? Put in here, and the rest is taken care of. This overrides any other stylesheets. eg: a.button{color:green}",
            "id"   => $shortname."custom_css",
            "type" => "textarea",
            "std"  => ""
        ),
        array(
            "name" => "WFC Pages excluded in sitemap",
            "desc" => "Enter the page id to be excluded followed by a comma.  Its common to exclude Thank You, Home, and Sitemap pages.",
            "id"   => $shortname."exclude_sitemap",
            "type" => "textarea",
            "std"  => ""
        ),
        array(
            "name" => "Reset Theme Options <a href='admin.php?page=wfc_theme_customizer.php&action=reset'>RESET THEME OPTIONS</a>",
            "type" => "information"
        ),
        array("type" => "close"),
        array(
            "name" => "Mail Settings (**RC 1)",
            "type" => "section"
        ),
        array("type" => "open"),
        array(
            "name" => "Smtp Host",
            "id"   => $shortname."mail_smtp_host",
            "type" => "text"
        ),
        array(
            "name" => "Smtp Port",
            "id"   => $shortname."mail_smtp_port",
            "type" => "text"
        ),
        array(
            "name"    => "Smtp Smtpsecure",
            "id"      => $shortname."mail_smtp_smtpsecure",
            "type"    => "select",
            "options" => array('None', 'tls', 'ssl')
        ),
        array(
            "name"    => "Smtp Smtpauth",
            "id"      => $shortname."mail_smtp_smtpauth",
            "type"    => "select",
            "options" => array('true', 'false')
        ),
        array(
            "name" => "Smtp User",
            "id"   => $shortname."mail_smtp_user",
            "type" => "text"
        ),
        array(
            "name" => "Smtp Password",
            "id"   => $shortname."mail_smtp_password",
            "type" => "text"
        ),
        array(
            "name" => "From Name",
            "desc" => "Enter the name you want emails to come from instead of the default WordPress",
            "id"   => $shortname."mail_from_name",
            "type" => "text"
        ),
        array(
            "name" => "From Email",
            "desc" => "Enter the email you want emails to come from instead of the default wordpress@sitename",
            "id"   => $shortname."mail_from_email",
            "type" => "text"
        ),
        array(
            "name" => "Test to Email",
            "desc" => "Enter email address to send a test to.",
            "id"   => $shortname."test_email_address",
            "type" => "text"
        ),
        array(
            "name" => "<a href='admin.php?page=wfc_theme_customizer.php&wfc_smtp_action=sendtest'>Send Test Email</a>",
            "type" => "information"
        ),
        array("type" => "close")
    );
    function wfc_save_options( $data, $refresh = 0 ){
        global $options;
        foreach( $options as $option ){
            if( isset($data[$option['id']]) ){
                //array_walk_recursive( $data[$option] , 'tie_clean_options');
                update_option( $option['id'], $data[$option['id']] );
            } else{
                delete_option( $option['id'] );
            }
        }
    }

    function Wfc_Add_Panel(){
        global $themename, $shortname, $options;
        $themename1 = !empty($themename) ? $themename : "Theme Settings";
        if( isset($_GET['page']) && $_GET['page'] == basename( __FILE__ ) ){
            if( isset($_GET['wfc_smtp_action']) && $_GET['wfc_smtp_action'] == "sendtest" ){
                new wfc_email();
            }
            if( isset($_POST['action']) && $_POST['action'] == "wfc_action_import_settings" ){
                if( !empty($_POST['wfc_import_settings']) ){
                    $refresh = 2;
                    $data    = unserialize( base64_decode( $_POST['wfc_import_settings'] ) );
                    wfc_save_options( $data, $refresh );
                }
            }
            if( isset($_REQUEST['action']) && 'save' == $_REQUEST['action'] ){
                $data    = $_POST;
                $refresh = 2;
                wfc_save_options( $data, $refresh );
                header( "Location: admin.php?page=wfc_theme_customizer.php&saved=true" );
                die;
            }
            if( isset($_GET['action']) && 'reset' == $_GET['action'] ){
                foreach( $options as $value ){
                    delete_option( $value['id'] );
                }
                header( "Location: admin.php?page=wfc_theme_customizer.php&reset=true" );
                die;
            }
        }
        add_menu_page( $themename1, $themename1, 'administrator', basename( __FILE__ ), 'Wfc_Panel' );
    }

    function Wfc_Panel(){
        global $themename, $shortname, $options, $save_options;
        $themename1 = !empty($themename) ? $themename : "Theme";
        $i          = 0;
        if( isset($_REQUEST['saved']) && $_REQUEST['saved'] ){
            echo '<div id="message" class="updated fade"><p><strong>'.$themename.
                ' settings saved.</strong></p></div>';
        }
        if( isset($_REQUEST['settings_updated']) && $_REQUEST['settings_updated'] ){
            echo '<div id="message" class="updated fade"><p><strong>'.$themename.
                ' WordPress Settings Updated.</strong></p></div>';
        }
        if( isset($_REQUEST['reset']) && $_REQUEST['reset'] ){
            echo '<div id="message" class="updated fade"><p><strong>'.$themename.
                ' settings reset.</strong></p></div>';
        }
        ?>
        <div class="wrap rm_wrap">
        <h2><?php echo $themename1; ?> Settings</h2>
        <div class="rm_opts">
        <form method="post">
        <?php foreach( $options as $value ){
            //print_r($value);
            switch( $value['type'] ){
                case "open":
                    ?>
                    <?php break;
                case "close":
                    ?>
                    </div>
                    </div>
                    <br/>
                    <?php break;
                case "title":
                    ?>
                    <p><?php echo $value['name']; ?></p>
                    <?php break;
                case "information":
                    ?>
                    <div class="rm_input rm_information">
                        <p><?php echo $value['name']; ?></p>
                        <p>
                            <small><?php echo $value['desc']; ?></small>
                        </p>
                    </div>

                    <?php break;
                case 'text':
                    ?>
                    <div class="rm_input rm_text">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if(
                            get_option( $value['id'] ) != ""
                        ){
                            echo stripslashes( get_option( $value['id'] ) );
                        } else{
                            echo "";
                        } ?>"/>
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    break;
                case 'textarea':
                    ?>
                    <div class="rm_input rm_textarea">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if(
                                get_option( $value['id'] ) != ""
                            ){
                                echo stripslashes( get_option( $value['id'] ) );
                            } else{
                                echo $value['std'];
                            } ?></textarea>
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    break;
                case 'select':
                    ?>
                    <div class="rm_input rm_select">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                            <?php foreach( $value['options'] as $option ){ ?>
                                <option <?php if( get_option( $value['id'] ) == $option ){
                                    echo 'selected="selected"';
                                } ?>><?php echo $option; ?></option><?php } ?>
                        </select>
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    break;
                case "colorpicker":
                    ?>
                    <div class="rm_input rm_text">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <input type="text" value="<?php echo get_option( $value['id'] ); ?>" class="wfc-color-picker" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"/>
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    break;
                case "checkbox":
                    ?>
                    <div class="rm_input rm_checkbox">
                        <?php foreach( $value['options'] as $option_k => $option_v ){ ?>
                            <label>
                                <?php $val = is_int( $option_k ) ? $option_v : $option_k; ?>
                                <?php $checked = ""; ?>
                                <?php if( is_array( get_option( $value['id'] ) ) ){ ?>
                                    <?php if( in_array( $val, get_option( $value['id'] ) ) ){
                                        $checked = "checked=\"checked\"";
                                    } ?>
                                <?php } ?>
                                <input type="checkbox" name="<?php echo $value['id']; ?>[]" id="<?php echo $value['id']; ?>" value="<?php echo $val; ?>" <?php echo $checked; ?> />
                                <?php echo $option_v; ?>
                            </label>
                            <br/>
                        <?php } ?>
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                    </div>
                    <?php break;
                case "section":
                    $i++;
                    ?>
                    <div class="rm_section">
                    <div class="rm_title"><h3>
                            <img src="<?php echo WFC_ADM_IMG_URI; ?>/trans.png" class="inactive" alt=""><?php echo $value['name']; ?>
                        </h3>
                        <span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="Save changes"/>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                    <div class="rm_options">
                    <?php break;
            }
        }
        ?>
        <input type="hidden" name="action" value="save"/>
        </form>

        <div class="rm_section">
            <div class="rm_title"><h3>
                    <img src="<?php echo WFC_ADM_IMG_URI; ?>/trans.png" class="inactive" alt="">Fast backup
                </h3>
                </span>
                <div class="clearfix"></div>
            </div>
            <div class="rm_options">
                <?php
                    $options_values = array();
                    foreach( $save_options as $opt ){
                        $options_values[$opt] = get_option( $opt );
                    }
                    $fb = new wfc_fastbackup_class( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
                    if( isset($_GET['download_db']) ){
                        if( !$fb->downloadDB( bloginfo( 'name' ).date( 'd-m-Y_H-i-s' ) ) ){
                            echo $fb->getErrors();
                        }
                    } elseif( isset($_FILES['restore']) && $_FILES['restore']['size'] > 0 ){
                        if( !$fb->restoreDB( $_FILES['restore']['tmp_name'] ) ){
                            echo $fb->getErrors();
                        } else{
                            $db = $fb->getDBObject();
                            foreach( $options_values as $n => $v ){
                                $db->exec( 'UPDATE `wp_options` SET `option_value`=\''.$v.'\' WHERE `option_name`=\''.$n.'\'' );
                            }
                            header( 'Location: index.php' );
                        }
                    } elseif( isset($_GET['backup_db']) ){
                        if( !$fb->backupDB( __DIR__.'/backups/'.bloginfo( 'name' ).date( 'd-m-Y_H-i-s' ) ) ){
                            echo $fb->getErrors();
                        }
                    } elseif( isset($_GET['replace']) ){
                        $fb->hostname = $_POST['host'];
                        $fb->user     = $_POST['user'];
                        $fb->password = $_POST['pass'];
                        $fb->database = $_POST['db'];
                        if( $fb->backupDB( __DIR__.'/tmp.sql' ) ){
                            $fb->hostname = DB_HOST;
                            $fb->user     = DB_USER;
                            $fb->password = DB_PASSWORD;
                            $fb->database = DB_NAME;
                            $fb->clearDB();
                            if( $fb->restoreDB( __DIR__.'/tmp.sql' )
                            ){
                                $db = $fb->getDBObject();
                                foreach( $options_values as $n => $v ){
                                    $db->exec( 'UPDATE `wp_options` SET `option_value`=\''.$v.'\' WHERE `option_name`=\''.$n.'\'' );
                                }
                                header( 'Location: index.php' );
                            } else{
                                echo $fb->getErrors();
                            }
                            @unlink( __DIR__.'/tmp.sql' );
                        } else{
                            echo $fb->getErrors();
                        }
                    }
                ?>
                <!--Replace with remote DB :-->
                <form method="POST" action="admin.php?page=wfc_theme_customizer.php&replace">
                    <div class="rm_input rm_text">
                        <label for="host">Host</label>
                        <input type="text" name="host"/>
                    </div>
                    <div class="rm_input rm_text">
                        <label for="user">User</label>
                        <input type="text" name="user"/>
                    </div>
                    <div class="rm_input rm_text">
                        <label for="pass">Password</label>
                        <input type="text" name="pass"/>
                    </div>
                    <div class="rm_input rm_text">
                        <label for="db">Database Name</label>
                        <input type="text" name="db"/>
                    </div>
                    <div class="rm_input rm_text">
                        Saved options :
                        <?php
                            $msg = '';
                            foreach( $save_options as $value ){
                                $msg .= $value.', ';
                            }
                            echo substr( $msg, 0, -2 );
                        ?>
                        <br/>
                        <input type="submit" value="Replace"/>
                    </div>
                </form>
                <div class="rm_input rm_text">
                    <a href="admin.php?page=wfc_theme_customizer.php&download_db">Download database</a>
                </div>
                <form method="POST" enctype="multipart/form-data" action="admin.php?page=wfc_theme_customizer.php">
                    <div class="rm_input rm_text">
                        <label>Restore database with a file :</label>
                        <input type="file" name="restore"/><br/>
                    </div>
                    <div class="rm_input rm_text">
                        <input type="submit" value="Restore"/>
                    </div>
                </form>
            </div>
        </div>
        <br/>
        <div class="rm_section">
            <div class="rm_title"><h3>
                    <img src="<?php echo WFC_ADM_IMG_URI; ?>/trans.png" class="inactive" alt="">Theme Update
                </h3>
                </span>
                <div class="clearfix"></div>
            </div>
            <div class="rm_options">
                <div class="rm_input">
                    <?php
                        wfc_manage_update();
                    ?>
                </div>
            </div>
        </div>
        <br/>
        <div class="rm_section">
            <div class="rm_title"><h3>
                    <img src="<?php echo WFC_ADM_IMG_URI; ?>/trans.png" class="inactive" alt="">Export / Import
                </h3>
                </span>
                <div class="clearfix"></div>
            </div>
            <div class="rm_options">
                <div class="rm_input">
                    <form method="post">
                        <?php
                            $current_options = array();
                            foreach( $options as $option ){
                                if( isset($option['id']) && get_option( $option['id'] ) ){
                                    $current_options[$option['id']] = get_option( $option['id'] );
                                }
                            }
                        ?>
                        <div class="wfc-item">
                            <h3>Export</h3>
                            <div class="option-item">
                                <textarea style="width:100%" rows="7"><?php echo $currentsettings = base64_encode( serialize( $current_options ) ); ?></textarea>
                            </div>
                        </div>
                        <div class="wfc-item">
                            <h3>Import</h3>
                            <div class="option-item">
                                <textarea id="wfc_import" name="wfc_import_settings" style="width:100%" rows="7"></textarea>
                            </div>
                        </div>
                        <div class="wfc-item">
                            <input type="hidden" name="action" value="wfc_action_import_settings"/>
                            <input type="submit" name="wfc_import_submit" value="Import Settings"/>
                            ** Caution this will overwrite existing settings!! **
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div><!-- //.rm_opts -->
    <?php
    }

?>
<?php
    if( wfc_is_dev() ){
        add_action( 'admin_menu', 'Wfc_Add_Panel' );
    }
    /*
     * add custom css from site options
     */
    add_action( 'wp_head', 'wfc_inject_custom_css' );
    function wfc_inject_custom_css(){
        $wfc_option = get_option( 'wfc_custom_css' );
        if( $wfc_option != '' && !empty($wfc_option) ){
            echo '<style>';
            echo $wfc_option;
            echo '</style>';
        }
    }

    /* Example of all Custom Metabox Options */
    if( $wfc_admin->wfc_is_active_cpt( "EXAMPLE_CPT" ) ){
        $example_module_args     = array(
            'cpt'       => 'Example' /* CPT Name */,
            'menu_name' => 'Example Menu Overide' /* Overide the name above */,
            'supports'  => array(
                'title',
                'page-attributes',
                'thumbnail',
                'editor'
            ) /* specify which metaboxes you want displayed. See Codex for more info*/,
        );
        $example_module          = new wfcfw( $example_module_args );
        $example_meta_boxes_args = array(
            'cpt'      => 'example' /* CPT Name */,
            'meta_box' => array(
                'title'     => 'Test all Meta Box Options',
                'new_boxes' => array(
                    array(
                        'field_title' => 'Text Test: ',
                        'type_of_box' => 'text',
                        'desc'        => 'Testing Text Field Notes', /* optional */
                    ),
                    array(
                        'field_title' => 'Textarea Test: ',
                        'type_of_box' => 'textarea',
                        'desc'        => 'Testing Description Textarea Field Notes', /* optional */
                    ),
                    array(
                        'field_title' => 'Radio Test: ',
                        'type_of_box' => 'radio',
                        'options'     => array(
                            'one'   => "<img src='http://lorempixel.com/75/75/nightlife/5' />",
                            'two'   => '222',
                            'three' => '333'
                        ), /* required */
                    ),
                    array(
                        'field_title' => 'Checkbox Test: ',
                        'type_of_box' => 'checkbox',
                        'options'     => array(
                            'one'   => "Checkbox One",
                            'two'   => "Checkbox Two",
                            'three' => "Checkbox Three"
                        ),
                    ),
                    /* This meta box is in the futur, be ready !
                    array(
                        'field_title' => 'Checkbox Test Images as values: ',
                        'type_of_box' => 'checkbox-img',
                        'options'     => array(
                            'one'   => "<img src='http://lorempixel.com/75/75/nightlife/1' />",
                            'two'   => "<img src='http://lorempixel.com/75/75/nightlife/2' />",
                            'three' => "<img src='http://lorempixel.com/75/75/nightlife/3' />"
                        ),
                    ),
                    */
                    array(
                        'field_title' => 'Select Dropdown Test: ',
                        'type_of_box' => 'select',
                        'options'     => array('one' => '111', 'two' => '222', 'three' => '333'), /* required */
                    ),
                    array(
                        'field_title' => 'Wysiwyg Test: ',
                        'type_of_box' => 'wysiwyg',
                    ),
                )
            ),
        );
        $example_meta_boxes      = new wfc_meta_box_class( $example_meta_boxes_args );
    }
    if( $wfc_admin->wfc_is_active_cpt( "CAMPAIGN_CPT" ) ){
        $campaign_module_args = array(
            'cpt'       => 'Campaign' /* CPT Name */,
            'menu_name' => 'Campaign' /* Overide the name above */,
            'supports'  => array(
                'title',
                'page-attributes',
                'thumbnail',
                'editor'
            ) /* specify which metaboxes you want displayed. See Codex for more info*/,
        );
        $campaign_module      = new wfcfw( $campaign_module_args );
    }
    if( $wfc_admin->wfc_is_active_cpt( "SUBPAGE_BANNER_CPT" ) ){
        $subpage_banner_args = array(
            'cpt'       => 'Subpage Banner' /* CPT Name */,
            'menu_name' => 'Subpage Banner' /* Overide the name above */,
            'supports'  => array(
                'title',
                'page-attributes',
                'thumbnail',
                'editor'
            ) /* specify which metaboxes you want displayed. See Codex for more info*/,
        );
        $subpage_banner      = new wfcfw( $subpage_banner_args );
    }
    if( $wfc_admin->wfc_is_active_cpt( "NEWS_CPT" ) ){
        $news_cpt_args = array(
            'cpt'       => 'News' /* CPT Name */,
            'menu_name' => 'News' /* Overide the name above */,
            'supports'  => array(
                'title',
                'page-attributes',
                'thumbnail',
                'editor'
            ) /* specify which metaboxes you want displayed. See Codex for more info*/,
        );
        $news_cpt      = new wfcfw( $news_cpt_args );
    }
    if( $wfc_admin->wfc_is_active_cpt( "HOME_BOXES_CPT" ) ){
        $home_boxes_module_args = array(
            'cpt'       => 'Home Page Boxes' /* CPT Name */,
            'menu_name' => 'Home Page Boxes' /* Overide the name above */,
            'supports'  => array(
                'title',
                'page-attributes',
                'thumbnail',
                'editor'
            ) /* specify which metaboxes you want displayed. See Codex for more info*/,
        );
        $home_boxes_module      = new wfcfw( $home_boxes_module_args );
    }
    if( $wfc_admin->wfc_is_active_cpt( "TESTIMONIAL_CPT" ) ){
        $testimonial_args = array(
            'cpt'       => 'Testimonial' /* CPT Name */,
            'menu_name' => 'Testimonial' /* Overide the name above */,
            'supports'  => array(
                'title',
                'page-attributes',
                'thumbnail',
                'editor'
            ) /* specify which metaboxes you want displayed. See Codex for more info*/,
        );
        $testimonial      = new wfcfw( $testimonial_args );
    }
