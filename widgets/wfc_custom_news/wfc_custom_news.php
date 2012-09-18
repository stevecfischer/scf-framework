<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 */

class WFC_Widget_Recent_News extends WP_Widget {

   function __construct() {
      $widget_ops = array('classname' => 'wfc_widget_recent_entries', 'description' => __( "WFC Recent Posts") );
      parent::__construct('wfc-recent-posts', __('WFC Recent Posts'), $widget_ops);
      $this->alt_option_name = 'widget_recent_entries';

      add_action( 'save_post', array(&$this, 'flush_widget_cache') );
      add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
      add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
   }

   function scf_get_news_tax($postID){
      $r = get_post_meta($postID, '_page_select_news_cats',true);
      return $r;
   }

   function widget($args, $instance) {
      $cache = wp_cache_get('wfc_widget_recent_news', 'widget');

      if ( !is_array($cache) )
         $cache = array();

      if ( ! isset( $args['widget_id'] ) )
         $args['widget_id'] = $this->id;

      if ( isset( $cache[ $args['widget_id'] ] ) ) {
         echo $cache[ $args['widget_id'] ];
         return;
      }

      ob_start();
      extract($args);

      $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
      global $post;

      $wfc_recent_news_posts = $this->scf_get_news_tax($post->ID);

      if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
         $number = 5;

      $news_args = array(
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'post_type' => 'news',
            'tax_query' => array(
               array(
                  'taxonomy' => 'topics',
                  'field' => 'slug',
                  'terms' => $wfc_recent_news_posts
               ),
            ),
         );
      $r = new WP_Query($news_args);
      if ($r->have_posts()) :
?>
      <?php echo $before_widget; ?>
      <?php if ( $title ) echo $before_title . $title . $after_title; ?>
      <ul class="navigation_menu_news">
      <?php  while ($r->have_posts()) : $r->the_post(); ?>
      <li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
      <?php endwhile; ?>
      </ul>
      <?php echo $after_widget; ?>
<?php
      // Reset the global $the_post as this query will have stomped on it
      wp_reset_postdata();

      endif;

      $cache[$args['widget_id']] = ob_get_flush();
      wp_cache_set('wfc_widget_recent_news', $cache, 'widget');
   }

   function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['number'] = (int) $new_instance['number'];
      $this->flush_widget_cache();

      $alloptions = wp_cache_get( 'alloptions', 'options' );
      if ( isset($alloptions['widget_recent_entries']) )
         delete_option('widget_recent_entries');

      return $instance;
   }

   function flush_widget_cache() {
      wp_cache_delete('wfc_widget_recent_news', 'widget');
   }

   function form( $instance ) {
      $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
      $number = isset($instance['number']) ? absint($instance['number']) : 5;
      $wfc_recent_post_type = $instance['wfc_recent_post_type'];
?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

      <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
      <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
   }
}
add_action('widgets_init', create_function('', 'return register_widget("WFC_Widget_Recent_News");'));
