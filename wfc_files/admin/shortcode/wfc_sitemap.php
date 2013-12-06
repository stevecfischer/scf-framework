<?php
    /**
     * [wfc_sitemap] shortcode
     *
     * @package scf-framework
     * @author Steve (08/16/2012)
     * @since since 2.3
     */

    /**
     * Fix url to include shortcuts
     *
     * @author Thibault Miclo
     * @since 5.2
     * @param integer $id id of the element
     * @return string $url correct url
     */
    function Wfc_fix_sitemap_url( $ancestor_id ){
        $short_cut     = get_post_meta( $ancestor_id, 'wfc_page_type_shortcut', true );
        if(intval($short_cut)>0)
        {
            switch($short_cut)
            {
                case 1:
                    $short_cut =get_permalink(get_post_meta( $ancestor_id, 'wfc_page_existing_pages', true ));
                break;
                case 2:
                    $short_cut=get_post_meta( $ancestor_id, 'wfc_page_external_link', true );
                break;
                case 3:
                     $short_cut=wp_get_attachment_url(get_post_meta( $ancestor_id, 'wfc_page_existing_pdfs', true ));
                break;
            }
            return $short_cut;
        }
        else
            return get_permalink( $ancestor_id );
    }

    /**
     * get all ancestor pages, pages with no post parent from wordpress database
     *
     * @global $wpdb
     * @return object $result a wordpress sql result
     */
    function wfc_get_ancestors(){
        global $wpdb;
        $result =
            $wpdb->get_results( "SELECT ID, post_title, guid FROM $wpdb->posts WHERE post_type = 'page' AND post_parent = 0 AND post_status = 'publish' ORDER BY menu_order " );
        return $result;
    }

    /**
     * Build the HTML for the sitemap
     *
     * @param array $atts attributes sent through the shortcode
     * @return string $html html to be displayed instead of the shortcode
     */
    function wfc_get_sitemap( $atts ){
        extract(
            shortcode_atts(
                array(
                     'exclude' => ''
                ), $atts ) );
        if( $exclude != '' && !empty($exclude) ){
            $exclude_pages     = $exclude;
            $exclude_pages_Arr = explode( ',', $exclude_pages );
        } else{
            $exclude_pages     = get_option( 'wfc_exclude_sitemap' );
            $exclude_pages_Arr = explode( ',', $exclude_pages );
        }
        $result = wfc_get_ancestors();
        $return = '';
        if( $result ){
            $return .= '<div class="sitemap_without_child">';
            $return .= '<ul>';
            foreach( $result as $res ){
                $ancestor_id = $res->ID;
                $args        = array(
                    'child_of' => $ancestor_id,
                    'exclude'  => $exclude_pages,
                    'title_li' => '',
                    'echo'     => 0,
                    'walker'   => new Wfc_Sitemap_Walker
                );
                $children    = wp_list_pages( $args );
                if( !$children ){
                    if( !in_array( $res->ID, $exclude_pages_Arr ) ){
                        //$link = get_permalink( $ancestor_id );
                        $link = Wfc_fix_sitemap_url( $ancestor_id );
                        $return .= "<li><a href='$link'>$res->post_title</a></li>";
                    }
                }
            }
            $return .= '</ul>';
            $return .= '</div>';
            foreach( $result as $res ){
                $ancestor_id = $res->ID;
                $args        = array(
                    'child_of' => $ancestor_id,
                    'exclude'  => $exclude_pages,
                    'title_li' => '',
                    'echo'     => 0,
                    'walker'   => new Wfc_Sitemap_Walker
                );
                $children    = wp_list_pages( $args );
                if( $children ){
                    $return .= '<div class="sitemap_with_child">';
                    if( !in_array( $res->ID, $exclude_pages_Arr ) ){
                        //$link = get_permalink( $ancestor_id );
                        $link = Wfc_fix_sitemap_url( $ancestor_id );
                        $return .= "<a dave href='$link'>$res->post_title</a><ul>";
                        $return .= $children;
                        $return .= '</ul></div>';
                    } else{
                        $return .= '<ul>';
                        $return .= $children;
                        $return .= '</ul></div>';
                    }
                }
            }
        }
        return $return;
    }
    add_shortcode( 'wfc_sitemap', 'wfc_get_sitemap' );

    /**
     * Class to walk through each page and display them in a list
     *
     * @param $output
     * @param $page
     * @param $depth
     * @param $args
     * @param $current_page
     * @return string $html $output + additional html
     */
    class Wfc_Sitemap_Walker extends Walker_page
    {
        function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ){
            if( $depth ){
                $indent = str_repeat( "\t", $depth );
            } else{
                $indent = '';
            }
            extract( $args, EXTR_SKIP );
            $css_class = array('page_item', 'page-item-'.$page->ID);
            if( !empty($current_page) ){
                $_current_page = get_post( $current_page );
                get_post_ancestors( $_current_page );
                if( isset($_current_page->ancestors) && in_array( $page->ID, (array)$_current_page->ancestors ) ){
                    $css_class[] = 'current_page_ancestor';
                }
                if( $page->ID == $current_page ){
                    $css_class[] = 'current_page_item';
                } elseif( $_current_page && $page->ID == $_current_page->post_parent ){
                    $css_class[] = 'current_page_parent';
                }
            } elseif( $page->ID == get_option( 'page_for_posts' ) ){
                $css_class[] = 'current_page_parent';
            }
            $css_class =
                implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );
             $short_cut     = get_post_meta( $page->ID, 'wfc_page_type_shortcut', true );
            if($short_cut[0]!='none')
            {
                switch($short_cut[0])
                {
                    case 1:
                        $a=get_post_meta( $page->ID, 'wfc_page_existing_pages', true );
                        $short_cut =get_permalink($a[0]);
                    break;
                    case 2:
                        $short_cut=get_post_meta( $page->ID, 'wfc_page_external_link', true );
                    break;
                    case 3:
                        $a=get_post_meta( $page->ID, 'wfc_page_existing_pdfs', true );
                        $short_cut=wp_get_attachment_url($a[0]);
                    break;

                    default:
                        unset($short_cut);
                    break;
                }
            }
            else
                unset($short_cut);
            if( isset($short_cut) && !empty($short_cut) )
                {$short_new_tab = get_post_meta( $page->ID, 'wfc_page_new_tab_option', true );
                if( isset($short_new_tab) && !empty($short_new_tab) ){
                    $output .=
                        $indent.'<li class="'.$css_class.'"><a target="_blank" href="'.$short_cut.'">'.$link_before.
                        apply_filters( 'the_title', $page->post_title, $page->ID ).$link_after.'</a>';
                } else{
                    $output .= $indent.'<li class="'.$css_class.'"><a href="'.$short_cut.'">'.$link_before.
                        apply_filters( 'the_title', $page->post_title, $page->ID ).$link_after.'</a>';
                }
            } else{
                $output .= $indent.'<li class="'.$css_class.'"><a href="'.get_permalink( $page->ID ).'">'.$link_before.
                    apply_filters( 'the_title', $page->post_title, $page->ID ).$link_after.'</a>';
            }
            if( !empty($show_date) ){
                if( 'modified' == $show_date ){
                    $time = $page->post_modified;
                } else{
                    $time = $page->post_date;
                }
                $output .= " ".mysql2date( $date_format, $time );
            }
        }
    }