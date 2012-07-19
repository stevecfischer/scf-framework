<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */
 
class WFC_Widget_Tax extends WP_Widget {

   function __construct() {
      $widget_ops = array( 'classname' => 'widget_categories', 'description' => __( "WFC Taxonomy Widget" ) );
      parent::__construct('categories', __('WFC Tax Widget'), $widget_ops);
   }

   function widget( $args, $instance ) {
      extract( $args );

      $title = apply_filters('widget_title', empty( $instance['title'] ) ? __( '' ) : $instance['title'], $instance, $this->id_base);
      $c = ! empty( $instance['count'] ) ? '1' : '0';
      $h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
      $d = ! empty( $instance['dropdown'] ) ? '1' : '0';

      echo $before_widget;
      echo '<div id="wfc-tax-term-widget">';
      if ( $title )
         echo $before_title . $title . $after_title;

         $terms = get_terms("topics");
         $count = count($terms);
         if ( $count > 0 ){
            echo "<ul class='navigation_menu_news'>";
               foreach ( $terms as $term ) {
                  echo "<li><a href='/topics/".$term->slug."/'>" . $term->name . "</a></li>";
               }
            echo "</ul>";
         }

      echo '</div>';
      echo $after_widget;
   }

   function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
      $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
      $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

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

add_action('widgets_init', create_function('', 'return register_widget("WFC_Widget_Tax");'));
