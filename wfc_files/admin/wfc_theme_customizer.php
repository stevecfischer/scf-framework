<?php
    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 1/5/13
     * @version 2.2
     */
    $themename = get_bloginfo( 'name' );
    $shortname = "wfc_";
    $categories = get_categories( 'hide_empty=0&orderby=name' );
    $wp_cats = array();
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
            "name"    => "WFC Core CPT",
            "desc"    => "Select the CPT you want to activate",
            "id"      => $shortname."activate_cpt",
            "type"    => "checkbox",
            "options" => array(
                "steve" => "EXAMPLE_CPT",
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
            "name"    => "WFC Client Admin menu",
            "desc"    => "Select admin menu items to hide",
            "id"      => $shortname."admin_menu",
            "type"    => "checkbox",
            "options" => array(
                "Theme Editor",
                "Widgets",
                "Menus",
                "ai1ec Themes",
                "Appearance",
                "Settings",
                "Posts",
                "Plugins",
                "Yoast SEO"
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
        array("type" => "close"),
        array(
            "name" => "Easter Eggs",
            "type" => "section"
        ),
        array("type" => "open"),
        array(
            "name" => "Easter Egg Video",
            "desc" => "enter url to video",
            "id"   => $shortname."easter_egg_video",
            "type" => "text"
        ),
        array("type" => "close"),
        array(
            "name" => "To Rebuild WFC Cached Javascript and CSS click the button <a href='admin.php?wfc_renew_cache=renew' target='_blank'>Renew</a>",
            "type" => "title"
        ),
    );
    function Wfc_Add_Panel(){
        global $themename, $shortname, $options;
        $themename1 = !empty($themename) ? $themename : "Theme Settings";
        if( isset($_GET['page']) && $_GET['page'] == basename( __FILE__ ) ){
            if( isset($_REQUEST['action']) && 'save' == $_REQUEST['action'] ){
                foreach( $options as $value ){
                    update_option( $value['id'], $_REQUEST[$value['id']] );
                }
                foreach( $options as $value ){
                    if( isset($_REQUEST[$value['id']]) ){
                        update_option( $value['id'], $_REQUEST[$value['id']] );
                    } else{
                        delete_option( $value['id'] );
                    }
                }
                header( "Location: admin.php?page=wfc_theme_customizer.php&saved=true" );
                die;
            } else{
                if( isset($_REQUEST['action']) && 'reset' == $_REQUEST['action'] ){
                    foreach( $options as $value ){
                        delete_option( $value['id'] );
                    }
                    header( "Location: admin.php?page=wfc_theme_customizer.php&reset=true" );
                    die;
                }
            }
        }
        add_menu_page( $themename1, $themename1, 'administrator', basename( __FILE__ ), 'Wfc_Panel' );
    }

    function Wfc_Panel(){
        global $themename, $shortname, $options;
        $themename1 = !empty($themename) ? $themename : "Theme";
        $i = 0;
        if( isset($_REQUEST['saved']) && $_REQUEST['saved'] ){
            echo '<div id="message" class="updated fade"><p><strong>'.$themename.
                ' settings saved.</strong></p></div>';
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
                case "checkbox":
                    ?>
                    <div class="rm_input rm_checkbox">
                        <?php foreach( $value['options'] as $option ){ ?>
                            <label>
                                <?php //print_r(get_option( $value['id'] )); ?>
                                <?php $checked = ""; ?>
                                <?php if( is_array( get_option( $value['id'] ) ) ){ ?>
                                    <?php if( in_array( $option, get_option( $value['id'] ) ) ){
                                        $checked = "checked=\"checked\"";
                                    } ?>
                                <?php } ?>
                                <input type="checkbox" name="<?php echo $value['id']; ?>[]" id="<?php echo $value['id']; ?>" value="<?php echo $option; ?>" <?php echo $checked; ?> />
                                <?php echo $option; ?>
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
        <form method="post">
            <p class="submit">
                <input name="reset" type="submit" value="Reset"/> <input type="hidden" name="action" value="reset"/>
            </p>
        </form>
        </div>
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
    if( getActiveCPT( "EXAMPLE_CPT" ) ){
        $campaign_module_args = array(
            'cpt'       => 'Example' /* CPT Name */,
            'menu_name' => 'Example Menu Overide' /* Overide the name above */,
            'supports'  => array(
                'title',
                'page-attributes',
                'thumbnail',
                'editor'
            ) /* specify which metaboxes you want displayed. See Codex for more info*/,
        );
        $campaign_module      = new wfcfw($campaign_module_args);
        $campaign_meta_boxes_args = array(
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
                            'one'   => "<img src='http://lorempixel.com/75/75/nightlife/1' />",
                            'two'   => "<img src='http://lorempixel.com/75/75/nightlife/2' />",
                            'three'   => "<img src='http://lorempixel.com/75/75/nightlife/3' />"
                        ),
                    ),
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
        $campaign_meta_boxes      = new wfc_meta_box_class($campaign_meta_boxes_args);
    }
    if( getActiveCPT( "CAMPAIGN_CPT" ) ){
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
        $campaign_module      = new wfcfw($campaign_module_args);
    }
    if( getActiveCPT( "SUBPAGE_BANNER_CPT" ) ){
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
        $subpage_banner      = new wfcfw($subpage_banner_args);
    }
    if( getActiveCPT( "NEWS_CPT" ) ){
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
        $news_cpt      = new wfcfw($news_cpt_args);
    }
    if( getActiveCPT( "HOME_BOXES_CPT" ) ){
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
        $home_boxes_module      = new wfcfw($home_boxes_module_args);
    }
    if( getActiveCPT( "TESTIMONIAL_CPT" ) ){
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
        $testimonial      = new wfcfw($testimonial_args);
    }
