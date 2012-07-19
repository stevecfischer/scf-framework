<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */

class  wfc_meta_box_class{
   private $new_meta_boxes = array();
   public function __construct($obj){
      $this->new_meta_boxes[] = $obj;
      add_action( 'add_meta_boxes', array( &$this, 'register_meta_box' ) );
      add_action('save_post', array( &$this, 'save_meta_box' ), 10, 2 );
   }
   public function types_meta_box($var){
      global $post;
      wp_nonce_field( 'wfc_meta_box_nonce', 'meta_box_nonce' );
         if( is_array($var['meta_box']['new_boxes']) ) {
            foreach ($var['meta_box']['new_boxes'] as $field) {

               $field_id_cleaning = preg_replace("/[^A-Za-z0-9 ]/", '', $field['field_title']);
               $field_id_cleaning = strtolower( str_replace(" ", "_", $field_id_cleaning ));
               $field['id'] = $var['cpt'].'_'.$field_id_cleaning;

               $meta = get_post_meta( $post->ID, $field['id'], true );
               echo '<tr id="row'.$field['id'].'">';
               echo '<th style="width:20%"><label>'. $field['field_title']. '</label></th><td>';
               switch ($field['type_of_box']) {
                  case 'text':
                     if( !is_array($meta) ) {
                        echo '<input type="text" name="'. $field['id']. '" id="'. $field['id']. '"
                           value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />';
                     echo '<br />'. $field['desc'];
                     } else{
                        foreach($meta as $meta_value){
                           echo '<input type="text" name="'. $field['id']. '" id="'. $field['id']. '"
                              value="'.$meta_value.'" size="30" style="width:97%" />';
                        }
                     echo '<br />'. $field['desc'];
                     }
                  break;
                  case 'textarea':
                     echo '<textarea name="'. $field['id']. '" id="'. $field['id']. '" cols="60? rows="4? style="width:97%">
                        ', $meta ? $meta : $field['std'], '</textarea>';
                     echo '<br />'. $field['desc'];
                  break;
                  case 'select':
                     echo '<select name="'. $field['id']. '" id="'. $field['id']. '">';
					 echo '<option value="none" style="'. $option['style']. '>None</option>';
                     foreach ($field['options'] as $option) {
                        if( is_array($option) ) {
                           if( $meta == $option['value'] ) {
                              echo '<option value="'. $option['value']. '" style="'. $option['style']. ' " selected="selected">'. $option['name']. '</option>';
                           }else{
                              echo '<option value="'. $option['value']. ' " style="'. $option['style']. ' ">'. $option['name']. '</option>';
                           }
                        }else{
                           if( $meta == $option ) {
                              echo '<option value="'. $option. '" selected="selected">'. $option. '</option>';
                           }else{
                              echo '<option value="'. $option. '">'. $option. '</option>';
                           }
                        }
                     }
                     echo '</select>&nbsp;&nbsp;&nbsp;<a id="preview_shortcut_link" style="display:none;" >Preview</a>';
                  break;
                  case 'radio':
                     foreach ($field['options'] as $option) {
                        echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                     }
                  break;
                  case 'checkbox':
                     if( is_array($field['options']) ) {
                        foreach ($field['options'] as $option) {
                           echo '<div class="wfc-meta-chkbox-block">';
                           if( $meta == '' ) {
                              echo '<div class="wfc-meta-chkbox"><input type="checkbox" name="'. $field['id']. '[]" value="'. $option. '" /></div><div>'. $option .'</div>';
                           }elseif( in_array($option, $meta ) ){
                              echo '<div class="wfc-meta-chkbox"><input type="checkbox" name="'. $field['id']. '[]" value="'. $option. '" checked="checked" /></div><div>'. $option .'</div>';
                           }else{
                              echo '<div class="wfc-meta-chkbox"><input type="checkbox" name="'. $field['id']. '[]" value="'. $option. '" /></div><div>'. $option .'</div>';
                           }
                           echo '</div>';
                        }
                     }else{
                        if( $meta == $field['id'] ) {
                          echo '<div class="wfc-meta-chkbox"><input type="checkbox" name="'. $field['id']. '" value="'. $field['id']. '" checked="checked"/></div><div>'.$field['field_title'].'</div>';
                        }else{
                          echo '<div class="wfc-meta-chkbox"><input type="checkbox" name="'. $field['id']. '" value="'. $field['id']. '" /></div><div>'.$field['field_title'].'</div>';
                        }
                     }

                  break;
               }
               echo '<td></tr>';
            }
         }
      echo '</table>';
   }//EOF
   public function register_meta_box(){
      $vars = $this->new_meta_boxes;
      foreach($vars as $var){
        add_meta_box(
           $var['cpt'].'_metabox',
           $var['meta_box']['title'],
           array( &$this, 'display_meta_box_content' ),
           $var['cpt'],
           'advanced',
           'high'
        );
      }
    }//EOF
    public function display_meta_box_content($post_obj){
      $vars = $this->new_meta_boxes;
      $current_post_type = $post_obj->post_type;
      foreach($vars as $var){
         if( strtolower($var['cpt']) == $current_post_type ){
             $meta_box = $var['meta_box']['new_boxes'];
             $this->types_meta_box($var);
          }
       }
    }//EOF
   public function save_meta_box() {
      global $post;
      $post_id = $post->ID;
      $vars = $this->new_meta_boxes;
      foreach($vars as $var){
         $meta_box = $var['meta_box'];
      }
      // verify nonce
      if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'wfc_meta_box_nonce' ) ){
         return $post_id;
      }
      // custom meta boxes are immune to auto saving for some reason
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
         return $post_id;
      }
      // check permissions
      if (!current_user_can('edit_page', $post_id)) {
         return $post_id;
      }
      foreach ($meta_box['new_boxes'] as $field) {

         $field_id_cleaning = preg_replace("/[^A-Za-z0-9 ]/", '', $field['field_title']);
         $field_id_cleaning = strtolower( str_replace(" ", "_", $field_id_cleaning ));
         $field['id'] = $var['cpt'].'_'.$field_id_cleaning;

         $old = get_post_meta($post_id, $field['id'], true);
         $trim_fields = preg_replace('/\[\]/', '', $field['id'] );
         $new = $_POST[$trim_fields];
         if ($new && $new != $old && $field['type_of_box'] != 'checkbox') {
            update_post_meta($post_id, $field['id'], $new);
         } elseif ( $field['type_of_box'] == 'checkbox' ){

            update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
         } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
         }
      }
   }
}//EOC
