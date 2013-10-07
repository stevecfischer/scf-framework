<?php
    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 1/5/13
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
            "name" => $themename." Options",
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
            "options" => array("CAMPAIGN_CPT", "SUBPAGE_BANNER_CPT", "HOME_BOXES_CPT", "NEWS_CPT", "TESTIMONIAL_CPT"),
            "std"     => "CAMPAIGN_CPT"
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
    );
    function Wfc_Add_Panel(){
        global $themename, $shortname, $options;
        if( isset($_GET['page']) && ($_GET['page'] == basename( __FILE__ )) ){
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
        add_menu_page( $themename, $themename, 'administrator', basename( __FILE__ ), 'Wfc_Panel' );
    }

    function Wfc_Panel(){
        global $themename, $shortname, $options;
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
            <h2><?php echo $themename; ?> Settings</h2>
            <div class="rm_opts">
                <form method="post">
                    <?php foreach ($options as $value){
                        //print_r($value);
                        switch ($value['type']){
                        case "open":
                            break;
                        case "close":
                    ?>
            </div>
        </div>
    <br/>
        <?php break;
        case "title":
            ?>
            <p>To easily use the <?php echo $themename; ?> theme, you can use the menu below.</p>
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
                    echo $value['std'];
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
                        <?php $checked = ""; ?>
                        <?php if( is_array( get_option( $value['id'] ) ) ){ ?>
                            <?php
                            if( in_array( $option, get_option( $value['id'] ) ) ){
                                $checked = "checked=\"checked\"";
                            }
                            ?>
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
                    <input name="reset" type="submit" onclick="return wfc_confirm();" value="Reset"/>
                    <input type="hidden" name="action" value="reset"/>
                </p>
            </form>
        </div>
        <div id="theme_update">
        This section will allow you to easly update your WFC Theme.<br/>
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
                        wfc_callsLeft();
                        $monitor = new Monitor();
                        $monitor->StartTimer();
                        echo wfc_manage_update();
                        $monitor->StopTimer();
                        echo '<br />';
                        wfc_DisplayMonitor( $monitor );
                        wfc_print_api_limit();
                    ?>
                </div>
            </div>
        </div>
        <script>
            function wfc_confirm() {
                if (confirm('Are You Sure?')) {
                    alert('If you insist!');
                    return true;
                } else {
                    alert('A wise decision!');
                    return false;
                }
            }
        </script>
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

    //@sftodo: I moved the CPT instances here in order to make the framework "update-able".  I don't like the code below.  Please optimize it!
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