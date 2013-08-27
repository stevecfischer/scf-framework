<?php
    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 7/20/13
     * @version 5.0.1
     */
    // @sftodo: this file will create all theme files Ex: index.php, header.php,footer.php, page.php etc etc.  I foresee a admin form will check boxes for the different files/modules to create.  the main reason for this is to utilize Git more.  Specifically in the case of bugs.  currently i have to make the bug fix twice.  first is in the active project and second is in git. with getting this class setup i'll be able to make the fix in git and then update the active project.
    /*
     * @sftodo: i should really integrate this with post types, meta boxes. Ex user enters custom post type like staff in form field and this class will build required files.  like staff-archive.php, dept-taxonomy.php, staff-single.php etc etc.
     */
    /*
     * @sftodo: phase 1: get this class to create the exact files in current package.
     *
     * Completed 7/24/13
     */
    /*
     * @sftodo: phase 2: create checkbox form for user to select what files they need.
     */
    /*
     * @sftodo: phase 3: create input form for user to type what files they need.
     */
    $themename = get_bloginfo( 'name' );
    $shortname = "wfc_";
    function Wfc_Build_Theme_Page(){
        global $themename, $shortname, $options;
        if( isset($_GET['page']) && $_GET['page'] == basename( __FILE__ ) ){
            if( isset($_REQUEST['build']) && 'Build Out Theme' == $_REQUEST['build'] ){
                header( "Location: admin.php?page=build_theme.php&build=true" );
                die;
            }
        }
        add_menu_page( "WFC Theme Builder", "WFC Theme Builder", 'administrator', basename( __FILE__ ), 'Wfc_Build_Theme' );
    }

    function Wfc_Build_Theme(){
        global $themename, $shortname, $options;
        $i = 0;
        if( isset($_REQUEST['build']) && $_REQUEST['build'] ){
            if( file_exists( WFC_PT.'header.php' ) ){
                die("Theme already built.  Go Fish.");
            }
            $header       = '<!DOCTYPE html>
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
                <body <?php body_class(); ?>>';
            $footer       = '</div><!-- //#wrapper -->
                <div id="footer">
                    <div id="wfc-footer-links">
                        <p>
                            <a target="_blank" href="http://www.webfullcircle.com">Internet Marketing</a>
                            |
                            <a target="_blank" href="http://www.webfullcircle.com">Website Design</a>
                        </p>
                    </div>
                    <!-- //#wfc-footer-links -->
                </div><!-- //#footer -->
                </div><!-- //#container -->
                <?php wp_footer(); ?>
                </body>
                </html>';
            $page         = '<?php get_header(); ?>
                <?php get_footer(); ?>';
            $frontpage    = '<?php get_header(); ?>
                <?php get_footer(); ?>';
            $search       = '<?php get_header(); ?>
            if( have_posts() ) :
                    while( have_posts() ) : the_post();
                        the_title();
                        the_content();
                    endwhile; else:
                    echo "There are no posts matching that search term.";
                endif;
                wp_reset_query();
                <?php get_footer(); ?>';
            $four_o_four  = 'echo "404 error."';
            $editor_style = "";
            $single       = "";
            $archive      = "";
            $theme_array  =
                array(
                    array('file' => 'header.php', 'content' => $header),
                    array('file' => 'footer.php', 'content' => $footer),
                    array('file' => 'page.php', 'content' => $page),
                    array('file' => 'search.php', 'content' => $search),
                    array('file' => '404.php', 'content' => $four_o_four),
                    array('file' => 'editor-style.css', 'content' => $editor_style),
                    array('file' => 'single.php', 'content' => $single),
                    array('file' => 'front-page.php', 'content' => $frontpage),
                    array('file' => 'archive.php', 'content' => $archive)
                );
            foreach( $theme_array as $page ){
                $fp = fopen( WFC_PT.$page['file'], "w" );
                fwrite( $fp, $page['content'] );
                fclose( $fp );
            }
        }
        ?>

        <form method="post">
            <p class="submit">
                <input name="build" type="submit" value="Build Out Theme"/>
            </p>
        </form>
    <?php
    }

    if( wfc_is_dev() ){
        add_action( 'admin_menu', 'Wfc_Build_Theme_Page' );
    }