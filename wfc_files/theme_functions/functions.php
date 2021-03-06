<?php
    /**
     *
     * @package scf-framework
     * @author Steve (7/17/2013)
     */
    require_once('wfc_files/wfc_config/wfc_config.php');
    /*
    === Add theme specific functions below.
    === If you feel you need to edit the framework files consult a manager first.
    */
    /*
    ===============================
    BASIC FRAMEWORK SETUP
    ===============================
    */
    add_action( 'after_setup_theme', 'wfc_framework_setup' );
    if( !function_exists( 'wfc_framework_setup' ) ):
        function wfc_framework_setup(){
            /*
            ===============================
            ADD IMAGE SIZES
            ===============================
            */
            add_theme_support( 'post-thumbnails' );
            set_post_thumbnail_size( 980, 328, true );
            add_image_size( 'subpage-banners', 980, 328, true );
            add_image_size( 'campaign', 980, 497, true );
            /*
            ===============================
            REGISTERING NAVIGATION MENUS
            ===============================
            */
            register_nav_menu( 'Primary', 'Primary Navigation' );
        }
    endif; // wfc_framework_setup
    /*
    ===============================
    EXAMPLE OF A CUSTOM POST TYPE WITH CUSTOM META BOX OPTIONS
    ===============================
    */
    $theme_cpt_args = array(
        'cpt'       => 'Theme Custom Post Type' /* CPT Name */,
        'menu_name' => 'Theme Custom Post Type' /* Overide the name above */,
        'supports'  => array(
            'title',
            'page-attributes',
            'thumbnail',
            'editor'
        ),
        'meta_box'  => array(
            'title' => 'Theme Custom Post Type Meta Info',
            'new_boxes' => array(
                array(
                    'field_title' => 'Link to URL: ',
                    'type_of_box' => 'text',
                    'desc'        => 'Ex. http://www.google.com/', /* optional */
                )
            )
        ),
    );
    $theme_cpt      = new wfcfw( $theme_cpt_args );
    /**
     * AUTO ENQUEUE ALL JS FILES THAT ARE IN THE JS FOLDER.  TO EXCLUDE A FILE ADD `EXCL` TO THE FILENAME
     *
     * @SINCE: 5.2
     */
    add_action( 'wp_enqueue_scripts', 'wfc_js_scripts' );
    function wfc_js_scripts(){
        // must do this so jquery loads in the footer.
        if( !is_admin() ){
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', ("http://code.jquery.com/jquery-latest.min.js"), false, "", true );
            wp_enqueue_script( 'jquery' );
        }
        if( AUTOLOAD_MINIFY === true ){
            wp_register_script(
                "extended_assets_compressed",
                WFC_URI.'/comp_assets/extended_assets_compressed.js', array('jquery'), '', true );
            wp_enqueue_script( "extended_assets_compressed" );
        } else{
            if( get_option( 'wfc_autoload_assets' ) ){
                $all_js = new wfc_auto_load_assets();
                if( !is_array( $all_js->autoload( 'js' ) ) ){
                    die($all_js->autoload( 'js' ));
                }
                foreach( $all_js->autoload( 'js' ) as $k => $v ){
                    wp_register_script( $k, WFC_JS_URI.'/'.$v, array('jquery'), '', true );
                    wp_enqueue_script( $k );
                }
            }
        }
    }

    /**
     * AUTO ENQUEUE ALL CSS FILES THAT ARE IN THE CSS FOLDER.  TO EXCLUDE A FILE ADD `EXCL` TO THE FILENAME
     *
     * @SINCE: 5.2
     */
    add_action( 'wp_enqueue_scripts', 'wfc_css_styles' );
    function wfc_css_styles(){
        if( AUTOLOAD_MINIFY === true ){
            wp_register_style( "extended_assets_compressed", WFC_URI.'/comp_assets/extended_assets_compressed.css' );
            wp_enqueue_style( "extended_assets_compressed" );
        } else{
            if( get_option( 'wfc_autoload_assets' ) ){
                $all_css = new wfc_auto_load_assets();
                if( !is_array( $all_css->autoload( 'css' ) ) ){
                    die($all_css->autoload( 'css' ));
                }
                foreach( $all_css->autoload( 'css' ) as $k => $v ){
                    wp_register_style( $k, WFC_CSS_URI.'/'.$v );
                    wp_enqueue_style( $k );
                }
            }
        }
    }

    function Wfc_Core_Page_Loop(){
        global $post;
        echo '<div class="container">';
        echo '<div class="row row-offcanvas row-offcanvas-right">';
        echo '<div class="col-xs-12 col-sm-9">';
        while( have_posts() ) : the_post();
            echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
            the_content();
        endwhile;
        wp_reset_query();
        echo '</div><!-- //.col-xs-12.col-sm-9 -->';
        echo '<div class="col-xs-6 col-sm-3 sidebar-offcanvas">';
        dynamic_sidebar();
        echo '</div><!-- //.col-xs-6.col-sm-3.sidebar-offcanvas -->';
        echo '</div><!-- //.container -->';
    }

    function Wfc_Core_Home_Page_Loop(){
        global $post;
        $query = new WP_Query(array('post_type' => 'pages', 'pagename' => 'home'));
        while( $query->have_posts() ) : $query->the_post();
            echo '<div id="content">';
            echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
            the_content();
        endwhile;
        wp_reset_query();
        echo '</div><!-- //#content -->';
    }

    function Wfc_Core_Homecontent_Loop(){
        $query = new WP_Query(array('post_type' => 'homeboxes', 'order' => 'ASC', 'orderby' => 'menu_order'));
        if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post(); ?>
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php echo get_post_meta( get_the_ID(), 'wfc_homeboxes_link', true ); ?>">Learn More</a>
        <?php
        endwhile;endif;
        wp_reset_query();
    }

    /*
    ===============================
    REGISTER SIDEBARS
    ===============================
    */
    if( !function_exists( 'Wfc_Register_Sidebars' ) ):
        function Wfc_Register_Sidebars(){
            register_sidebar(
                array(
                    'name'          => 'Right Sidebar',
                    'id'            => 'sidebar-1',
                    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                    'after_widget'  => "</aside>",
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>',
                    'description'   => ''
                ) );
        }
    endif; // wfc_framework_setup
    add_action( 'after_setup_theme', 'Wfc_Register_Sidebars' );