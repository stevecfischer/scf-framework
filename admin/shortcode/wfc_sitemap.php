<?php
    /**
     *
     * @package scf-framework
     * @author Steve (08/16/2012)
     * @added since 2.3
     * @version 2.3


    ==== @TODO: need to see about getting Custom Tax terms and maybe their respective posts to show up ie products, portfolio, staff

     */
    function wfc_get_ancestors(){
        //get all ancestor pages, pages with no post parent from wordpress database
        global $wpdb;
        $result =
            $wpdb->get_results( "SELECT ID, post_title, guid FROM $wpdb->posts WHERE post_type = 'page' AND post_parent = 0 AND post_status = 'publish' ORDER BY menu_order " );
        return $result;
    }

    function wfc_get_sitemap( $atts ){
        extract(
            shortcode_atts(
                array(
                     'exclude' => 555
                ), $atts ) );
        $exclude_pages     = $exclude;
        $exclude_pages_Arr = explode( ',', $exclude_pages );
        $result            = wfc_get_ancestors();
        $return            = '';
        if( $result ){
            foreach( $result as $res ){
                $ancestor_id = $res->ID;
                $children    = wp_list_pages( "title_li=&child_of=$ancestor_id&echo=0&exclude=$exclude_pages" );
                if( $children ){
                    $return .= '<div class="sitemap_with_child">';
                    if( !in_array( $res->ID, $exclude_pages_Arr ) ){
                        $link = get_permalink( $ancestor_id );
                        $return .= "<a href='$link'>$res->post_title</a><ul>";
                        $return .= $children;
                        $return .= '</ul></div>';
                    } else{
                        $return .= '<ul>';
                        $return .= $children;
                        $return .= '</ul></div>';
                    }
                }
            }
            $return .= '<div class="sitemap_without_child">';
            $return .= '<ul>';
            foreach( $result as $res ){
                $ancestor_id = $res->ID;
                $children    = wp_list_pages( "title_li=&child_of=$ancestor_id&echo=0&exclude=$exclude_pages" );
                if( !$children ){
                    if( !in_array( $res->ID, $exclude_pages_Arr ) ){
                        $link = get_permalink( $ancestor_id );
                        $return .= "<li><a href='$link'>$res->post_title</a></li>";
                    }
                }
            }
            $return .= '</ul>';
            $return .= '</div>';
        }
        return $return;
    }

    add_shortcode( 'wfc_sitemap', 'wfc_get_sitemap' );


