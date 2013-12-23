<?php
/**
 * Displays a pagination
 * To be used in template
 *
 */
function Wfc_Pagination(){
    echo '<div class="pagination">';
    global $wp_query;
    $big = 999999999; // need an unlikely integer
    echo paginate_links(
        array(
             'base'    => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
             'format'  => '?paged=%#%',
             'current' => max( 1, get_query_var( 'paged' ) ),
             'total'   => $wp_query->max_num_pages
        ) );
    echo '<div class="clear-both"></div></div>';
}

/**
* Customized excerpt lenght
*
* @param int $length excerpt lenght in chars
*/
function Wfc_Print_Excerpt( $length = 55 ){
    global $post;
    $text = $post->post_excerpt;
    if( '' == $text ){
        $text = get_the_content( '' );
        $text = apply_filters( 'the_content', $text );
        $text = str_replace( ']]>', ']]>', $text );
    }
    $text = strip_shortcodes( $text );
    $text =
        strip_tags( $text, '<a>' ); // use ' $text = strip_tags($text,'<p><a>'); ' if you want to keep some tags
    if( strlen( $text ) > $length ){
        $text = substr( $text, 0, $length ).'...';
    } else{
        $text = substr( $text, 0, $length );
    }
    $excerpt = Wfc_Reverse_Strrchr( $text, '.', 1 );
    if( $excerpt ){
        echo apply_filters( 'the_excerpt', $excerpt );
    } else{
        echo apply_filters( 'the_excerpt', $text );
    }
}

/**
 * Searches the last position of $needle in $haystack
 * Return $haystack from char 0 to last postion+$trail
 *
 * @param string $haystack string to search in
 * @param string $needle string we search for
 * @param int $trail numbers of caracters we want after ($needle - 1)
 * @return string $str shortened haystack
 */
function Wfc_Reverse_Strrchr( $haystack, $needle, $trail ){
    return strrpos( $haystack, $needle ) ? substr( $haystack, 0, strrpos( $haystack, $needle ) + $trail ) : false;
}

/**
 * Filters the excerpt to return only 56 chars
 *
 * @param string $content content before
 * @return string $excerpt content after
 */
function Wfc_Limit_Excerpt( $content ){
    global $post;
    if( strlen( $content ) >= 55 ){
        return substr( $content, 0, 55 ).'...';
    }
    return $content;
}
add_filter( 'the_excerpt', 'Wfc_Limit_Excerpt' );

/**
 * Changes the excerpt length
 *
 * @param int $length new length
 * @return int $length new length
 */
function wfc_excerpt_length( $length ){
    return $length;
}
add_filter( 'excerpt_length', 'wfc_excerpt_length', 999 );

/**
 * Limits the title length :
 * - 33 chars for news
 * - 30 chars for spotlight
 *
 * @param string $title title before
 * @return string $title title after
 */
function Wfc_Limit_Title( $title ){
    global $post;
    if( strlen( $title ) >= 32 && $post->post_type == 'news' ){
        return substr( $title, 0, 32 ).'...';
    }
    if( strlen( $title ) >= 29 && $post->post_type == 'spotlight' ){
        return substr( $title, 0, 29 ).'...';
    }
    return $title;
}
add_filter( 'the_title', 'Wfc_Limit_Title' );

/**
 * If a page has no content
 * Displays a default one
 *
 * @package scf-framework
 * @author Steve (12/10/2012)
 * @param string $content content before
 * @return string $content content after
 */
function Wfc_Auto_Content( $content ){
    if( is_page() ){
        if( $content == "" &&  get_option('wfc_default_content')===false){
            $content = '<div id="container">

        <div id="wfcContent">

            <h1>HTML Ipsum Presents</h1>

            <p>
                <strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.
            </p>

            <h2>Header Level 2</h2>

            <ol>
                <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
                <li>Aliquam tincidunt mauris eu risus.</li>
            </ol>

            <p>
                <strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.
            </p>

            <blockquote>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.
                </p>
            </blockquote>

            <p>
                <strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.
            </p>

            <h3>Header Level 3</h3>

            <ul>
                <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
                <li>Aliquam tincidunt mauris eu risus.</li>
            </ul>

            <img class="aligncenter" src="'.WFC_IMG_URI.'/box-1-5.jpg" alt="" />

             <p>
                <strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.
            </p>

            <img class="aligncenter" src="'.WFC_IMG_URI.'/box-1-5.jpg" alt="" />

        </div><!--end #wfcContent-->
    </div><!--end #container-->';
        }
    }
    return $content;
}
add_filter( 'the_content', 'Wfc_Auto_Content' );

/**
 * ENABLE SHORTCODES INSIDE TEXT WIDGETS
 */
add_filter( 'widget_text', 'do_shortcode' );

/**
 * WFC IMAGE PATH IN SHORTCODE FORM. HELPFUL IN TEXT WIDGETS
 * @return string $url wfc images url
 */
function wfc_img_url(){
    return WFC_IMG_URI;
}
add_shortcode( 'wfcimg', 'wfc_img_url' );

/**a
 * Filter to add edit link on pages/posts when connected
 *
 * @param string $content content before
 * @return string $content content after
 * @since 5.2
 */
function add_edit_link($content)
{
    global $wfc_admin;
    $wfc_admin->_wfc_deprecated_argument( __FUNCTION__, '5.3.7', 'Current method to use: edit_post_link() directly in template file' );
}
add_filter('the_content','add_edit_link');