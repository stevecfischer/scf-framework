<?php

    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 7/20/13
     */
    /*
     * @scftodo: We should really integrate this with post types, meta boxes. Ex user enters custom post type like staff in form field and this class will build required files.  like staff-archive.php, dept-taxonomy.php, staff-single.php etc etc.
     */
    $themename = get_bloginfo( 'name' );
    $shortname = "wfc_";
    function Wfc_Build_Theme_Page(){
        global $themename, $shortname, $options;
        if( isset($_GET['page']) && $_GET['page'] == basename( __FILE__ ) ){
            if( isset($_REQUEST['build']) && 'Build Out Theme' == $_REQUEST['build'] ){
                $url = 'admin.php?page=build_theme.php&build=true';
                //Place post infos in url, . will be replaced by _ automatically
                foreach( $_POST as $k => $p ){
                    if( $p ){
                        $url .= '&'.$k.'=true';
                    }
                }
                header( 'Location: '.$url );
                die;
            }
        }
        add_menu_page( "WFC Theme Builder", "WFC Theme Builder", 'administrator', basename( __FILE__ ), 'Wfc_Build_Theme' );
    }

    function Wfc_Build_Theme(){
        global $themename, $shortname, $options;
        $i = 0;
        if( file_exists( WFC_PT.'../header.php' ) ){
            //die("Theme already built.  Go Fish.");
        }
        $header      = '
<!DOCTYPE html>
<!--
 __      __     _____   ______
/\ \  __/\ \  /\  ___\ /\  _  \
\ \ \/\ \ \ \ \ \ \__/ \ \ \/\_\
 \ \ \ \ \ \ \ \ \  __\ \ \ \/ /_
  \ \ \_/\ _\ \ \ \ \_/  \ \ \_\ \
   \ \__/ \___/  \ \_\    \ \____/
    \/__/ /__/    \/_/     \/___/
-->
<!--[if lt IE 7 ]>
<html class="ie ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7 ]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8 ]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( "charset" ); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo("name"); ?> | <?php is_front_page() ? bloginfo("description") : wp_title(""); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( "stylesheet_url" ); ?>"/>
    <link rel="pingback" href="<?php bloginfo( "pingback_url" ); ?>"/>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php $wfc_main_nav_args = array(
    "menu"            => "",
    "container"       => "nav",
    "container_class" => "navbar navbar-default",
    "container_id"    => "",
    "menu_class"      => "nav navbar-nav",
    "menu_id"         => "",
    "echo"            => true,
    "fallback_cb"     => "wp_page_menu",
    "before"          => "",
    "after"           => "",
    "link_before"     => "",
    "link_after"      => "",
    "depth"           => 1,
    "walker"          => "",
    "theme_location"  => ""
);
    wp_nav_menu( $wfc_main_nav_args );?>';
        $footer      = '
<footer>
    <div id="wfc-footer-links">
        <p>Copyright 2014 All Rights Reserved. Website Design by
            <a href="http://www.webfullcircle.com" target="_blank">Webfullcircle.com</a>
        </p>
    </div>
</footer>
</div>
<!-- /.container -->
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<?php wp_footer(); ?>
<?php wfc_footer(); ?>
</body>
</html>';
        $page        = '
                <?php get_header(); ?>
                <?php Wfc_Core_Page_Loop(); ?>
                <?php get_footer(); ?>';
        $frontpage   = '<?php get_header(); ?>
                <?php Wfc_Core_Home_Page_Loop(); ?>
                <?php get_footer(); ?>';
        $search      = '
                <?php get_header(); ?>
                <h2><?php printf( \'Search Results for: %s\',get_search_query() ); ?></h2>
                <?php
                   if( have_posts()):
                   while ( have_posts() ) : the_post(); ?>
                    <h3><a href="<?php the_permalink(); ?>">> <?php the_title(); ?></a></h3>
                     <?php endwhile;
                    else:
                        echo \'<p>No results.</p>\';
                    endif;?>
                <?php get_footer(); ?>';
        $four_o_four = '<?php get_header(); ?>
                <h2>404</h2>
                <p>The page you are looking for doesn\'t exists...</p>
                <?php echo do_shortcode("[wfc_sitemap]"); ?>
                <?php get_footer(); ?>';
        $editor_styles = "";
        $archive     = '<?php get_header(); ?>
                <?php get_template_part("content"); ?>
                <?php get_footer(); ?>';
        $single      = '<?php get_header(); ?>
                <?php get_template_part("content"); ?>
                <?php get_footer(); ?>';
        $content     = '<?php
    if( have_posts() ) : while( have_posts() ) : the_post(); ?>
        <div class="<?php echo get_post_type(); ?>-entry">
            <?php
                $wfc_post_title =  \'<h2 class="page_title"><a href="%1$s">%2$s</a></h2>\';
                printf(
                    apply_filters("wfc_post_title",$wfc_post_title),
                    get_permalink(),
                    get_the_title()
                );
                $wfc_post_meta =  \'<p class="blog-meta">
        Published on: <span>%1$s</span>
        By: <a class="url fn n" href="%2$s" rel="author">%3$s</a>
        Categories: <span>%4$s</span>
                    </p>\';
                printf(
                    apply_filters("wfc_post_meta",$wfc_post_meta),
                    get_the_time( apply_filters("wfc_post_time_format","F j, Y") ),
                    esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ),
                    get_the_author(),
                    get_the_category_list( " ,", "Categories: " )
                );
            ?>
            <div class="post-thumbnail">
                <a href="<?php echo get_permalink(); ?>"><?php echo wfc_the_post_thumbnail(null, "medium"); ?></a>
            </div>
            <?php the_excerpt( 425 ); ?>
            <a class="read-more" href="<?php echo get_permalink(); ?>">Read More &#187;</a>
            <div style="clear:both;"></div>
            <?php
                if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }
            ?>
        </div>
    <?php
    endwhile;endif;
    wp_reset_query();
?>';
        $comments    = '<div id=\"comments\" class=\"comments-area\">

    <?php if ( have_comments() ) : ?>

    <h2 class=\"comments-title\">
        <?php
            printf( _n( \'One thought on &ldquo;%2\$s&rdquo;\', \'%1\$s thoughts on &ldquo;%2\$s&rdquo;\', get_comments_number(), \'twentyfourteen\' ),
                number_format_i18n( get_comments_number() ), get_the_title() );
        ?>
    </h2>

    <?php if ( get_comment_pages_count() > 1 && get_option( \'page_comments\' ) ) : ?>
    <nav id=\"comment-nav-above\" class=\"navigation comment-navigation\" role=\"navigation\">
        <h1 class=\"screen-reader-text\"><?php _e( \'Comment navigation\', \'twentyfourteen\' ); ?></h1>
        <div class=\"nav-previous\"><?php previous_comments_link( __( \'&larr; Older Comments\', \'twentyfourteen\' ) ); ?></div>
        <div class=\"nav-next\"><?php next_comments_link( __( \'Newer Comments &rarr;\', \'twentyfourteen\' ) ); ?></div>
    </nav><!-- #comment-nav-above -->
    <?php endif; // Check for comment navigation. ?>

    <ol class=\"comment-list\">
        <?php
            wp_list_comments( array(
                \'style\'      => \'ol\',
                \'short_ping\' => true,
                \'avatar_size\'=> 34,
            ) );
        ?>
    </ol><!-- .comment-list -->

    <?php if ( get_comment_pages_count() > 1 && get_option( \'page_comments\' ) ) : ?>
    <nav id=\"comment-nav-below\" class=\"navigation comment-navigation\" role=\"navigation\">
        <h1 class=\"screen-reader-text\"><?php _e( \'Comment navigation\', \'twentyfourteen\' ); ?></h1>
        <div class=\"nav-previous\"><?php previous_comments_link( __( \'&larr; Older Comments\', \'twentyfourteen\' ) ); ?></div>
        <div class=\"nav-next\"><?php next_comments_link( __( \'Newer Comments &rarr;\', \'twentyfourteen\' ) ); ?></div>
    </nav><!-- #comment-nav-below -->
    <?php endif; // Check for comment navigation. ?>

    <?php if ( ! comments_open() ) : ?>
    <p class=\"no-comments\"><?php _e( \'Comments are closed.\', \'twentyfourteen\' ); ?></p>
    <?php endif; ?>

    <?php endif; // have_comments() ?>

    <?php comment_form(); ?>

</div>';
        $theme_array =
            array(
                array('file' => 'header.php', 'content' => $header),
                array('file' => 'footer.php', 'content' => $footer),
                array('file' => 'page.php', 'content' => $page),
                array('file' => 'search.php', 'content' => $search),
                array('file' => '404.php', 'content' => $four_o_four),
                array('file' => 'editor-style.css', 'content' => $editor_styles),
                array('file' => 'single.php', 'content' => $single),
                array('file' => 'front-page.php', 'content' => $frontpage),
                array('file' => 'content.php', 'content' => $content),
                array('file' => 'comments.php', 'content' => $comments),
                array('file' => 'archive.php', 'content' => $archive)
            );
        if( isset($_REQUEST['build']) && $_REQUEST['build'] ){
            //Replace _ by . in Request variable
            $keys                   = implode( ',', array_keys( $_REQUEST ) );
            $keys                   = str_replace( '_', '.', $keys );
            $_REQUEST               = array_combine( explode( ',', $keys ), array_values( $_REQUEST ) );
            $_REQUEST['header.php'] = true;
            $_REQUEST['footer.php'] = true;
            foreach( $theme_array as $page ){
                if( isset($_REQUEST[$page['file']]) ){
                    echo WFC_THEME_ROOT.'/'.$page['file'].' - Created<br />';
                    $fp = fopen( WFC_THEME_ROOT.'/'.$page['file'], "w" );
                    fwrite( $fp, $page['content'] );
                    fclose( $fp );
                }
            }
            if( !file_exists( WFC_THEME_ROOT.'/images' ) ){
                rename( WFC_PT.'/images', WFC_THEME_ROOT.'/images' );
                echo WFC_THEME_ROOT.'/images - Created<br />';
            }
            if( !file_exists( WFC_THEME_ROOT.'/css' ) ){
                rename( WFC_PT.'/css', WFC_THEME_ROOT.'/css' );
                echo WFC_THEME_ROOT.'/css - Created<br />';
            }
            if( !file_exists( WFC_THEME_ROOT.'/js' ) ){
                rename( WFC_PT.'/js', WFC_THEME_ROOT.'/js' );
                echo WFC_THEME_ROOT.'/js - Created<br />';
            }
            if( !file_exists( WFC_THEME_ROOT.'/fonts' ) ){
                rename( WFC_PT.'/fonts', WFC_THEME_ROOT.'/fonts' );
                echo WFC_THEME_ROOT.'/fonts - Created<br />';
            }
            echo 'Theme built successfully.';
        } else{
            ?>

            <form method="post">
                <p class="choices">
                    The following files will be created automatically:<br/>
                    Header.php <br/>
                    Footer.php <br/>
                    images/ <br/>
                    css/ <br/>
                    js/ <br/>
                    fonts/ <br/>
                    <br/>
                    <!-- Required since we check if header.php exists to know if we need to build out the theme -->
                    You can choose to build the following files or not:<br/>
                    <?php
                        foreach( $theme_array as $template ){
                            if( $template['file'] != "header.php" && $template['file'] != "footer.php" ){
                                ?><label>
                                <input type="checkbox" name="<?php echo $template['file']; ?>"/> <?php echo $template['file']; ?></label>
                                <br/>
                            <?php
                            }
                        }
                    ?>
                </p>
                <p class="submit">
                    <input name="build" type="submit" value="Build Out Theme"/>
                </p>
            </form>
        <?php
        }
    }

    if( wfc_is_dev() ){
        add_action( 'admin_menu', 'Wfc_Build_Theme_Page' );
    }