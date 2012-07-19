<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */
 
class WFC_Widget_Recent_Posts extends WP_Widget {

   function __construct() {
      $widget_ops = array( 'classname' => 'wfc_widget_recent_posts', 'description' => __( "WFC Most Recent Posts" ) );
      parent::__construct('wfc-most-recent-posts', __('WFC Recent Posts'), $widget_ops);
   }

   function widget( $args, $instance ) {
      extract($args);

      $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
      $title_only = $instance['title_only'];
      if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
         $number = 5;

      $r = new WP_Query(array('posts_per_page' => $number, 'post_status' => 'publish', 'post_type' => $wfc_recent_post_type));
      if ($r->have_posts()) :
?>
      <?php echo $before_widget; ?>
      <ul class="navigation_menu_news">
      <?php  while ($r->have_posts()) : $r->the_post(); ?>
      <?php
         if( $title_only ) { ?>
            <li class="<?php echo 'article post-'.get_the_ID(); ?>">
            <p>
            <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
            <?php echo get_the_title(); ?><br />
            </a>
            </p>
            </li>
         <?php }else{ ?>
            <li class="<?php echo 'article post-'.get_the_ID(); ?>"><p><?php echo get_the_title(); ?><br /></p><a class="read_more" href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">read more &raquo;</a></li>
         <?php } ?>
      <?php endwhile; ?>
      </ul>
      <?php echo $after_widget; ?>
<?php
      // Reset the global $the_post as this query will have stomped on it
      wp_reset_postdata();

      endif;
   }

   function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['title_only'] = !empty($new_instance['title_only']) ? 1 : 0;

      return $instance;
   }

   function form( $instance ) {
      //Defaults
      $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
      $title = esc_attr( $instance['title'] );
      $title_only = esc_attr( $instance['title_only'] );
      $count = isset($instance['count']) ? (bool) $instance['count'] :false;
      $hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
      $dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

      <p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('title_only'); ?>" name="<?php echo $this->get_field_name('title_only'); ?>"<?php checked( $title_only ); ?> />
      <label for="<?php echo $this->get_field_id('title_only'); ?>"><?php _e( 'Display title only' ); ?></label><br />
<?php
   }
}

add_action('widgets_init', create_function('', 'return register_widget("WFC_Widget_Recent_Posts");'));

