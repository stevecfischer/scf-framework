<?php

    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 12/23/13
     * @version 5.2
     */
    class Wfc_Custom_Nav_Walker
        extends Walker_page
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
            $short_cut = get_post_meta( $page->ID, 'wfc_page_type_shortcut', true );
            if( $short_cut != 'none' ){
                switch( $short_cut ){
                    case 'Page':
                        $short_cut_destination_object    = get_page_by_title( get_post_meta( $page->ID, 'wfc_page_existing_pages', true ) );
                        $short_cut_destination_permalink = get_permalink( ($short_cut_destination_object->ID) );
                        break;
                    case 'External Link':
                        $short_cut_destination_permalink = get_post_meta( $page->ID, 'wfc_page_external_link', true );
                        break;
                    case 'PDF':
                        //returns ID instead of title.
                        $a                               = get_post_meta( $page->ID, 'wfc_page_existing_pdfs', true );
                        $short_cut_destination_permalink = wp_get_attachment_url( $a );
                        break;
                    default:
                        unset($short_cut);
                        break;
                }
            } else{
                unset($short_cut);
            }
            if( isset($short_cut) && !empty($short_cut) ){
                $short_new_tab = get_post_meta( $page->ID, 'wfc_page_new_tab_option', true );
                if( isset($short_new_tab) && !empty($short_new_tab) ){
                    $output .=
                        $indent.'<li class="'.$css_class.'"><a target="_blank" href="'.$short_cut_destination_permalink.'">'.$link_before.
                        apply_filters( 'the_title', $page->post_title, $page->ID ).$link_after.'</a>';
                } else{
                    $output .= $indent.'<li class="'.$css_class.'"><a href="'.$short_cut_destination_permalink.'">'.$link_before.
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
