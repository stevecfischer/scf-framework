<?php
    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     * @version 2.2
     */
    require_once('wfc_config/wfc_config.php');
    /***********************/
    /*
    === Add theme specific functions below.
    === If you feel you need to edit the framework files consult a manager first.
    */
    add_image_size( 'spotlight-thumb', 255, 131, true );
    register_nav_menu( 'Quick Links', 'Quick Links' );
    function wfc_js_scripts(){
        wp_register_script( 'wfc.plugins', WFC_JS_URI.'/plugins.js', array('jquery') );
        wp_enqueue_script( 'wfc.plugins' );
        wp_enqueue_script( 'jquery' );
    }

    add_action( 'wp_enqueue_scripts', 'wfc_js_scripts' );
    function wfc_css_styles(){
        wp_register_style( 'wfc.fonts', WFC_CSS_URI.'/fonts.css' );
        wp_enqueue_style( 'wfc.fonts' );
    }

    add_action( 'wp_enqueue_scripts', 'wfc_css_styles' );
    function Wfc_Core_Homecontent_Loop(){
        global $wpdb;
        $query = new WP_Query(array('post_type' => 'homeboxes', 'order' => 'ASC'));
        $i     = 1;
        if( $query->have_posts() ) : while( $query->have_posts() && $i <= 3 ) : $query->the_post(); ?>
        <div class="col_<?php echo $i++;?>">
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php  echo get_post_meta( $post->ID, 'homeposts_link_', true ); ?>">Learn More</a>
        </div><!--/ col_1-->
        <?php if( $i <= 3 ){
                echo '<div id="vert_div">&nbsp;</div>';
            }
        endwhile;endif;
        wp_reset_query();
    }

    function Wfc_Core_Campaign_Loop(){
        global $wpdb;
        $query = new WP_Query(array('post_type' => 'campaign', 'order' => 'ASC'));
        if( $query->have_posts() ) : while( $query->have_posts() && $i <= 3 ) : $query->the_post(); ?>
        <div class="col_<?php echo $i++;?>">
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php  echo get_post_meta( $post->ID, 'homeposts_link_', true ); ?>">Learn More</a>
        </div>
        <?php
        endwhile;endif;
        wp_reset_query();
    }

    function Wfc_Core_News_Loop(){
        global $wpdb;
        $query = new WP_Query(array('post_type' => 'news', 'order' => 'ASC'));
        $i     = 1;
        if( $query->have_posts() ) : while( $query->have_posts() && $i <= 3 ) : $query->the_post(); ?>
        <div class="col_<?php echo $i++;?>">
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php  echo get_post_meta( $post->ID, 'homeposts_link_', true ); ?>">Learn More</a>
        </div><!--/ col_1-->
        <?php if( $i <= 3 ){
                echo '<div id="vert_div">&nbsp;</div>';
            }
        endwhile;endif;
        wp_reset_query();
    }

    function Wfc_Core_Testimonial_Loop(){
        global $wpdb;
        $query = new WP_Query(array('post_type' => 'testimonial', 'order' => 'ASC'));
        $i     = 1;
        if( $query->have_posts() ) : while( $query->have_posts() && $i <= 3 ) : $query->the_post(); ?>
        <div class="col_<?php echo $i++;?>">
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php  echo get_post_meta( $post->ID, 'homeposts_link_', true ); ?>">Learn More</a>
        </div><!--/ col_1-->
        <?php if( $i <= 3 ){
                echo '<div id="vert_div">&nbsp;</div>';
            }
        endwhile;endif;
        wp_reset_query();
    }

    function Wfc_Core_Page_Loop(){
        while( have_posts() ) : the_post();
            echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
            the_content();
        endwhile;
        wp_reset_query();
    }

    function Wfc_Core_Subbanner_Loop(){
        global $wpdb;
        global $post;
        if( has_post_thumbnail( $post->ID ) && is_page() ){
            $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'subpagebanners' );
            echo '<img src="'.$src[0].'"/>';
        } else{
            query_posts( "post_type=subpagebanner&orderby=rand&posts_per_page=1" );
            if( have_posts() ) : while( have_posts() ) : the_post();
                the_post_thumbnail( 'subpagebanners' );
            endwhile;endif;
            wp_reset_query();
        }
    }

    function Wfc_Core_Sidebar( $handle = 1 ){
        if( is_page( 'home' ) ){
            echo '<div id="sidebar-wrapper">';
            if( !dynamic_sidebar( $handle ) ){
                echo 'no sidebar';
            }
            echo '</div><!-- //#sidebar-wrapper-->';
        } else{
            if( !dynamic_sidebar( $handle ) ){
                echo 'no sidebar';
            }
        }
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



















