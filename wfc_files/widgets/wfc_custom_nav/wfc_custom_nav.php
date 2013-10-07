<?php
    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     */
    class WFC_Custom_Nav_Widget
        extends WP_Widget
    {

        function WFC_Custom_Nav_Widget(){
            $widget_ops =
                array('classname' => 'wfc_custom_nav', 'description' => __( "WFC Custom Sidebar Navigation" ));
            $this->WP_Widget( 'wfc_custom_nav', __( 'WFC Custom Navigation' ), $widget_ops );
        }

        function widget( $argsw, $instance ){
            extract( $argsw );
            global $post;
            $exclude_list   = '';
            $post_ancestors = (isset($post->ancestors)) ? $post->ancestors :
                get_post_ancestors( $post ); //get the current page's ancestors either from existing value or by executing function
            $top_page       = $post_ancestors ? end( $post_ancestors ) : $post->ID; //get the top page id
            $thedepth       = 0; //initialize default variable"<h2>"s
            $ancestors_me   = implode( ',', $post_ancestors ).','.$post->ID;
            //exclude pages not in direct hierarchy
            foreach( $post_ancestors as $anc_id ){
                $pageset = get_pages( array('child_of' => $anc_id, 'parent' => $anc_id, 'exclude' => $ancestors_me) );
                foreach( $pageset as $page ){
                    $excludeset = get_pages( array('child_of' => $page->ID, 'parent' => $page->ID) );
                    foreach( $excludeset as $expage ){
                        $exclude_list .= ','.$expage->ID;
                    }
                }
            }
            $thedepth = count( $post_ancestors ) + 1; //prevents improper grandchildren from showing
            if( $thedepth != 1 ){ //only if the page is not the top of the hierarchy
                $top_page = $post->post_parent;
            }
            // show parent title
            echo $before_widget;
            $wfc_custom_nav                = array();
            $wfc_custom_nav['before_menu'] = '<ul class="nav nav-list affix-top">';
            $wfc_custom_nav['the_menu']    =
                '<li class="previous"><a class="return" href="'.get_permalink( $post->post_parent ).'">'.
                get_the_title( $post->post_parent ).'</a></li>';
            $wfc_custom_nav['the_menu'] .= wp_list_pages(
                array(
                     'title_li' => '',
                     'echo'     => 0,
                     'depth'    => $thedepth,
                     'child_of' => $top_page,
                     'exclude'  => $exclude_list,
                     'walker'   => new Wfc_Custom_Nav_Walker
                ) );
            $wfc_custom_nav['after_menu'] = '</ul>';
            $wfc_menu                     = apply_filters( 'wfc_before_menu', $wfc_custom_nav );
            echo $wfc_menu['before_menu'].$wfc_menu['the_menu'].$wfc_menu['after_menu'];
            echo $after_widget;
        }

        function update( $new_instance, $old_instance ){
            $instance         = $old_instance;
            $instance['side'] =
                (in_array( $new_instance['side'], array('left', 'right') )) ? $new_instance['side'] : 'left';
            return $instance;
        }

        function form( $instance ){
            $instance = wp_parse_args( (array)$instance, array('side' => false) );
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'side' ); ?>">
                    <?php _e( 'Menu Orientation:' ); ?>
                </label>
            </p>
        <?php
        }
    }

    //EOC
    add_action( 'widgets_init', create_function( '', 'return register_widget("WFC_Custom_Nav_Widget");' ) );
    class Wfc_Custom_Nav_Walker
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
            $css_class     =
                implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );
            $short_cut     = get_post_meta( $page->ID, 'wfc_page_shortcut_url', true );
            $short_new_tab = get_post_meta( $page->ID, 'wfc_page_new_tab_option', false );
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
