<?php
class WFC_Widget_Recent_Posts extends WP_Widget {

   function __construct() {
      $widget_ops = array( 'classname' => 'wfc_widget_recent_posts', 'description' => __( "WFC Most Recent Posts" ) );
      parent::__construct('wfc-most-recent-posts', __('WFC Recent Posts'), $widget_ops);
   }

   function widget( $args, $instance ) {
      extract($args);

      $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
      $wfc_recent_post_type = $instance['wfc_recent_post_type'] ? $instance['wfc_recent_post_type'] : 'news';
      if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
         $number = 5;

      $r = new WP_Query(array('posts_per_page' => $number, 'post_status' => 'publish', 'post_type' => $wfc_recent_post_type));
      if ($r->have_posts()) :
?>
      <?php echo $before_widget; ?>
      <ul class="navigation_menu_news">
      <?php  while ($r->have_posts()) : $r->the_post(); ?>
      <li class="<?php echo 'article post-'.get_the_ID(); ?>"><p><?php echo get_the_title(); ?><br /></p><a class="read_more" href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">read more &raquo;</a></li>
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
      $instance['title_only'] = strip_tags($new_instance['title_only']);
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['wfc_recent_post_type'] = strip_tags($new_instance['wfc_recent_post_type']);
      $instance['number'] = (int) $new_instance['number'];

      return $instance;
   }

   function form( $instance ) {
      //Defaults
      $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
      $title = esc_attr( $instance['title'] );
      $count = isset($instance['count']) ? (bool) $instance['count'] :false;
      $hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
      $dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

      <p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
      <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Display as dropdown' ); ?></label><br />

      <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
      <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />

      <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
      <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' ); ?></label></p>
<?php
   }
}

add_action('widgets_init', create_function('', 'return register_widget("WFC_Widget_Recent_Posts");'));

