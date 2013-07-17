<?php
    /**
     *
     * @package scf-framework
     * @author Steve (7/17/2013)
     * @version 4.0
     */
    require_once('wfc_config/wfc_config.php');
    /***********************/
    /*
    === Add theme specific functions below.
    === If you feel you need to edit the framework files consult a manager first.
    */
    add_image_size( 'spotlight-thumb', 255, 131, true );
    register_nav_menu( 'Quick Links', 'Quick Links' );
    add_action( 'wp_enqueue_scripts', 'wfc_js_scripts' );
    function wfc_js_scripts(){
        wp_register_script( 'wfc.extensions', WFC_JS_URI.'/extensions.js', '', '', true );
        wp_register_script( 'wfc.plugins', WFC_JS_URI.'/plugins.js', '', '', true );
        wp_enqueue_script( 'wfc.plugins' );
        wp_enqueue_script( 'wfc.extensions' );
    }

    add_action( 'wp_enqueue_scripts', 'wfc_css_styles' );
    function wfc_css_styles(){
        wp_register_style( 'wfc-extensions', WFC_CSS_URI.'/extensions.css' );
        wp_enqueue_style( 'wfc-extensions' );
    }

    function Wfc_Core_Homecontent_Loop(){
        global $wpdb;
        $query = new WP_Query(array('post_type' => 'homeboxes', 'order' => 'ASC'));
        if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post(); ?>
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php echo get_post_meta( $post->ID, 'wfc_homeboxes_link', true ); ?>">Learn More</a>
        <?php
        endwhile;endif;
        wp_reset_query();
    }

    function Wfc_Core_Campaign_Loop(){
        global $wpdb;
        $query = new WP_Query(array('post_type' => 'campaign', 'order' => 'ASC'));
        if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post(); ?>
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php echo get_post_meta( $post->ID, 'wfc_Campaign_read_more', true ); ?>">Read More</a>
        <?php
        endwhile;endif;
        wp_reset_query();
    }

    function Wfc_Core_News_Loop(){
        global $wpdb;
        $query = new WP_Query(array('post_type' => 'news', 'order' => 'ASC'));
        if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post(); ?>
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php echo get_post_meta( $post->ID, 'wfc_News_read_more', true ); ?>">Learn More</a>
        <?php
        endwhile;endif;
        wp_reset_query();
    }

    function Wfc_Core_Testimonial_Loop(){
        global $wpdb;
        $query = new WP_Query(array('post_type' => 'testimonial', 'order' => 'ASC'));
        if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post(); ?>
            <div id="block">
                <h2><?php echo get_the_title(); ?></h2>
                <?php the_post_thumbnail( 'large' ); ?>
                <?php echo get_the_content(); ?>
            </div>
            <a class="learn_more" href="<?php echo get_post_meta( $post->ID, 'wfc_testimonial_read_more', true ); ?>">Learn More</a>
        <?php
        endwhile;endif;
        wp_reset_query();
    }

    function Wfc_Core_Page_Loop(){
        global $wpdb;
        global $post;
        echo '<div id="content">';
        while( have_posts() ) : the_post();
            echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
            the_content();
        endwhile;
        wp_reset_query();
        echo '</div><!-- //#content -->';
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
        if( !dynamic_sidebar( $handle ) ){
            echo 'no sidebar';
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



















