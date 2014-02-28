<?php

    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     */
    class WFC_Widget_Recent_Posts
        extends WP_Widget
    {

        function __construct(){
            $widget_ops =
                array('classname' => 'wfc_widget_recent_posts', 'description' => __( "WFC Most Recent Posts" ));
            parent::__construct( 'wfc-most-recent-posts', __( 'WFC Recent Posts' ), $widget_ops );
        }

        function widget( $args, $instance ){
            extract( $args );
            $wfc_title            = apply_filters(
                'widget_title', empty($instance['title']) ? __( 'Recent Posts' ) :
                $instance['title'], $instance, $this->id_base );
            $wfc_title_only       = $instance['title_only'];
            $wfc_number           = isset($instance['number']) ? absint( $instance['number'] ) : 5;
            $wfc_recent_post_type = isset($instance['post_type']) ? trim( $instance['post_type'] ) : 'news';
            $r                    = new WP_Query(array(
                'posts_per_page' => $wfc_number,
                'post_status'    => 'publish',
                'post_type'      => $wfc_recent_post_type
            ));
            if( $r->have_posts() ) :

                echo $before_widget;
                echo $before_title.$wfc_title.$after_title;
                ?>
                <ul id="wfc-recent-posts-widget" class="navigation_menu_news">
                    <?php while( $r->have_posts() ) : $r->the_post(); ?>
                        <?php
                        if( !$wfc_title_only ){
                            ?>
                            <li class="<?php echo 'article post-'.get_the_ID(); ?>">
                            <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(
                                get_the_title() ? get_the_title() : get_the_ID() ); ?>">
                                <?php the_post_thumbnail( 'thumbnail' ); ?>
                                <div class="post-widget-article">
                                    <span class="post-widget-title"><?php echo get_the_title(); ?></span>
                                    <?php the_excerpt(); ?>
                                </div>
                            </a>
                            </li>
                        <?php } else{ ?>
                            <li class="<?php echo 'article post-'.get_the_ID(); ?>"><p><?php echo get_the_title(); ?>
                                <br/></p>
                            <a class="read_more" href="<?php the_permalink() ?>" title="<?php echo esc_attr(
                                get_the_title() ? get_the_title() : get_the_ID() ); ?>">read more &raquo;</a>
                            </li>
                        <?php } ?>
                    <?php endwhile; ?>
                </ul>
                <?php echo $after_widget;
                wp_reset_postdata();
            else:
                echo 'No Posts for post type '.$wfc_recent_post_type;
            endif;
        }

        function update( $new_instance, $old_instance ){
            $instance               = $old_instance;
            $instance['title']      = strip_tags( $new_instance['title'] );
            $instance['number']     = strip_tags( $new_instance['number'] );
            $instance['post_type']  = strip_tags( $new_instance['post_type'] );
            $instance['title_only'] = !empty($new_instance['title_only']) ? 1 : 0;
            return $instance;
        }

        function form( $instance ){
            //Defaults
            $instance             = wp_parse_args( (array)$instance, array('title' => '') );
            $wfc_title            = esc_attr( $instance['title'] );
            $wfc_title_only       = esc_attr( $instance['title_only'] );
            $wfc_number           = esc_attr( $instance['number'] );
            $wfc_recent_post_type = esc_attr( $instance['post_type'] );

            ?>
            <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $wfc_title; ?>"/>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of Posts to Show:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $wfc_number; ?>"/>
            </p>

            <p>
                <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'title_only' ); ?>" name="<?php echo $this->get_field_name( 'title_only' ); ?>"<?php checked( $wfc_title_only ); ?> />
                <label for="<?php echo $this->get_field_id( 'title_only' ); ?>"><?php _e( 'Display title only' ); ?></label><br/>

            <p><label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:' ); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
                    <option value="">All</option>
                    <?php
                        $args = array(
                            'public'   => true,
                            '_builtin' => false
                        );
                        $post_types = get_post_types( $args, 'objects', 'and' );
                        foreach( $post_types as $post_type ){
                            echo '<option value="'.$post_type->query_var.'"'
                                .($post_type->query_var == $wfc_recent_post_type ? ' selected="selected"' : '')
                                .'>'.$post_type->labels->menu_name."</option>\n";
                        }
                    ?>
                </select>
            </p>
        <?php
        }
    }

    register_widget( 'WFC_Widget_Recent_Posts' );

