<?php
    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 7/20/13
     */

    /*
     * @sftodo: We should really integrate this with post types, meta boxes. Ex user enters custom post type like staff in form field and this class will build required files.  like staff-archive.php, dept-taxonomy.php, staff-single.php etc etc.
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
            die("Theme already built.  Go Fish.");
        }
        if( isset($_REQUEST['build']) && $_REQUEST['build'] ){
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
            $footer       = '<!-- FOOTER -->
                <footer>
                    <p class="pull-right">
                        <a href="#">Back to top</a>
                    </p>
                    <div id="wfc-footer-links">
                        <p>
                            <a target="_blank" href="http://www.webfullcircle.com">Internet Marketing</a>
                            |
                            <a target="_blank" href="http://www.webfullcircle.com">Website Design</a>
                        </p>
                    </div>
                </footer>
                </div>
                <!-- /.container -->
                <!-- Bootstrap core JavaScript
                ================================================== -->
                <!-- Placed at the end of the document so the pages load faster -->
                <?php wp_footer(); ?>
                </body>
                </html';
            $page         = '
                <?php get_header(); ?>
                <?php Wfc_Core_Page_Loop(); ?>
                <?php get_footer(); ?>';
            $frontpage    = '
                <?php get_header(); ?>
                <?php Wfc_Core_Home_Page_Loop(); ?>
                <?php get_footer(); ?>';
            $search       = '
                <?php get_header(); ?>
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
            //Replace _ by . in Request variable
            $keys                   = implode( ',', array_keys( $_REQUEST ) );
            $keys                   = str_replace( '_', '.', $keys );
            $_REQUEST               = array_combine( explode( ',', $keys ), array_values( $_REQUEST ) );
            $_REQUEST['header.php'] = true;
            $_REQUEST['footer.php'] = true;
            foreach( $theme_array as $page ){
                if( $_REQUEST[$page['file']] ){
                    echo WFC_PT.'../'.$page['file'].' - Created<br />';
                    $fp = fopen( WFC_PT.'../'.$page['file'], "w" );
                    fwrite( $fp, $page['content'] );
                    fclose( $fp );
                }
            }
            echo 'Theme built successfully.';
        } else{
            ?>

            <form method="post">
                <p class="choices">
                    Page.php : <input type="checkbox" name="page.php"/><br/>
                    Front-page.php : <input type="checkbox" name="front-page.php"/><br/>
                    Search.php : <input type="checkbox" name="search.php"/><br/>
                    404.php : <input type="checkbox" name="404.php"/><br/>
                    Archive.php : <input type="checkbox" name="archive.php"/><br/>
                    Single.php : <input type="checkbox" name="single.php"/><br/>
                    Editor-Style.css : <input type="checkbox" name="editor-style.css"/><br/>
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