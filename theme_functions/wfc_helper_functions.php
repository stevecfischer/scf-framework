<?php
    function Wfc_Pagination(){
        ?>
    <div class="pagination">
        <?php
        global $wp_query;
        $big = 999999999; // need an unlikely integer
        echo paginate_links(
            array(
                 'base'    => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
                 'format'  => '?paged=%#%',
                 'current' => max( 1, get_query_var( 'paged' ) ),
                 'total'   => $wp_query->max_num_pages
            ) );
        ?>
        <div style="clear:both;"></div>
    </div>
    <?php
    }

    /*
    ===============================
    CUSTOM EXCERPT LENGTH
    ===============================
    */
    function Wfc_Print_Excerpt( $length = 55 ){
        global $post;
        $text = $post->post_excerpt;
        if( '' == $text ){
            $text = get_the_content( '' );
            $text = apply_filters( 'the_content', $text );
            $text = str_replace( ']]>', ']]>', $text );
        }
        $text    = strip_shortcodes( $text );
        $text    =
            strip_tags( $text, '<a>' ); // use ' $text = strip_tags($text,'<p><a>'); ' if you want to keep some tags
        if(strlen($text) > $length){
            $text    = substr( $text, 0, $length ).'...';
        }else{
            $text    = substr( $text, 0, $length );
        }
        $excerpt = Wfc_Reverse_Strrchr( $text, '.', 1 );
        if( $excerpt ){
            echo apply_filters( 'the_excerpt', $excerpt );
        } else{
            echo apply_filters( 'the_excerpt', $text );
        }
    }

    function Wfc_Reverse_Strrchr( $haystack, $needle, $trail ){
        return strrpos( $haystack, $needle ) ? substr( $haystack, 0, strrpos( $haystack, $needle ) + $trail ) : false;
    }

    /**
     * @param $content
     *
     * @return string
     */
    function Wfc_Limit_Excerpt( $content ){
        global $post;
        if( $post->post_type == 'spotlight' ){
            return $content;
        }
        if( strlen( $content ) >= 55 ){
            return substr( $content, 0, 55 ).'...';
        }
        return $content;
    }

    add_filter( 'the_excerpt', 'Wfc_Limit_Excerpt' );
    /**
     * @param $title
     *
     * @return string
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
    function Wfc_Client_News_Feed_Widget(){
        echo '<iframe src="http://69.72.236.85/scf_framework_iframe.html" sandbox=" "></iframe>';
    }

    function Add_Wfc_Client_News_Feed_Widget(){
        wp_add_dashboard_widget( "Wfc_Client_News_Feed_Widget", __( "WFC Client News Feed!" ), "Wfc_Client_News_Feed_Widget" );
    }

    add_action( "wp_dashboard_setup", "Add_Wfc_Client_News_Feed_Widget" );
    /**
     *
     * @package scf-framework
     * @author Steve (12/10/2012)
     * @version 2.3
     * @description: content will be displayed if a page has not content entered.
     */
    function Wfc_Auto_Content( $content ){
        if( is_page() ){
            if( $content == "" ){
                $content = '<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. Donec non enim in turpis pulvinar facilisis. Ut felis. Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus</p>
                <h2>Header Level 2</h2>
                <ol>
                    <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
                    <li>Aliquam tincidunt mauris eu risus.</li>
                </ol>
                <blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>
                <h3>Header Level 3</h3>
                <ul>
                    <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
                    <li>Aliquam tincidunt mauris eu risus.</li>
                </ul>
                <div class="clearboth"></div>';
            }
        }
        return $content;
    }

    add_filter( 'the_content', 'Wfc_Auto_Content' );