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
            $wfc_custom_nav['before_menu'] = '<ul class=" '.$instance['menu_class'].' nav nav-list affix-top">';
            $wfc_custom_nav['the_menu']    =
                '<li class="previous"><a href="'.get_permalink( $post->post_parent ).'">'.
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
            $instance               = $old_instance;
            $instance['menu_class'] = strip_tags( $new_instance['menu_class'] );
            return $instance;
        }

        function form( $instance ){
            $instance       = wp_parse_args( (array)$instance, array('menu_class' => '') );
            $wfc_menu_class = esc_attr( $instance['menu_class'] );
            ?>
            <p><label for="<?php echo $this->get_field_id( 'menu_class' ); ?>"><?php _e( 'Menu Class:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'menu_class' ); ?>" name="<?php echo $this->get_field_name( 'menu_class' ); ?>" type="text" value="<?php echo $wfc_menu_class; ?>"/>
            </p>
        <?php
        }
    }

    register_widget( 'WFC_Custom_Nav_Widget' );