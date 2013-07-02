<?php
    /**
     *
     * @package scf-framework
     * @author Steve (08/16/2012)
     * @added since 2.3
     */
    function Wfc_fix_sitemap_url( $ancestor_id ){
        $shortCutUrl = get_post_meta( $ancestor_id, 'wfc_page_shortcut_url', true );
        if( $shortCutUrl == '' || empty($shortCutUrl) ){
            return get_permalink( $ancestor_id );
        } else{
            return $shortCutUrl;
        }
    }

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
                     'exclude' => ''
                ), $atts ) );
        $exclude_pages     = $exclude;
        $exclude_pages_Arr = explode( ',', $exclude_pages );
        $result            = wfc_get_ancestors();
        $return            = '';
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
    class Wfc_Sitemap_Walker
        extends Walker_page
    {
        function start_el( &$output, $page, $depth, $args, $current_page ){
            if( $depth ){
                $indent = str_repeat( "\t", $depth );
            } else{
                $indent = '';
            }
            extract( $args, EXTR_SKIP );
            $css_class = array('page_item', 'page-item-'.$page->ID);
            if( !empty($current_page) ){
                $_current_page = get_page( $current_page );
                _get_post_ancestors( $_current_page );
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
            $short_cut = get_post_meta( $page->ID, 'wfc_page_shortcut_url', true );
            // new tab comes out as an array -- need to fix this
            $short_new_tab = get_post_meta( $page->ID, 'wfc_page_new_tab_option', true );
            if( isset($short_cut) && !empty($short_cut) ){
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