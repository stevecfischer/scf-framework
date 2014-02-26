<?php
class wfc_spotlight
    extends WP_Widget
{
    function wfc_spotlight(){
        parent::WP_Widget( 'wfc_spotlight', 'WFC Spotlight', array('description' => 'WFC Spotlight') );
    }

    function form( $instance ){
        if( $instance ){
            $title = $instance['wfc_title'];
        } else{
            $title = 'New title';
        } ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'wfc_title' ); ?>"><?php _e( 'Title:' ); ?></label> <input
                class="widefat"
                id="<?php echo $this->get_field_id( 'wfc_title' ); ?>"
                name="<?php echo $this->get_field_name( 'wfc_title' ); ?>"
                type="text"
                value="<?php echo $title; ?>"
                />
        </p>
    <?php
    }

    function update( $new_instance, $old_instance ){
        $instance              = $old_instance;
        $instance['wfc_title'] = strip_tags( $new_instance['wfc_title'] );
        return $instance;
    }

    function widget( $args, $instance ){
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['wfc_title'] );
        echo $before_widget;
        echo $before_title;
        echo '<a href="/spotlight/">'.$title.'</a>';
        echo $after_title;
        echo '<div class="widget_content">';
        echo '<ul class="spotlight-list">';
        $queryArgs =
            array('post_type' => 'spotlight', 'posts_per_page' => -1, 'order' => 'ASC');
        query_posts( $queryArgs );
        if( have_posts() ) : while( have_posts() ) : the_post();
            ?>
            <li>
            <div id="spotlight_slider_bg">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail( 'spotlight-thumb' ); ?>
                </a>
                <span class="spotlight-mask"></span>
            </div>
            <!-- / .spotlight slider-->
            <h3 class="spotlight_title">
                <a href="<?php the_permalink(); ?>"><?php the_title();?></a>
            </h3>
            <?php the_content(); ?>
            </li>
        <?php
        endwhile; endif;
        echo '</ul>';
        echo '<div class="spotlight_nav">
                    <ul>
                        <li>
                            <a class="prev" href="#">1</a>
                        </li>
                        <li>
                            <a class="next" href="#">2</a>
                        </li>
                    </ul>
                </div>';
        echo '</div><!-- / .widget_content-->';
        echo $after_widget;
    }
}

register_widget( 'wfc_spotlight' );