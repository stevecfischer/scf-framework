<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */ 
 
/*
** The Class
*/
 include_once( 'wfc_meta_box_cases.php' );
/*
**================================
**================================
*/
class wfcfw
{
   private $new_cpts = array();


   public function __construct($obj){
      $this->new_cpts[] = $obj;
      if( isset($obj['meta_box']) ) {
         $new_meta_box = new wfc_meta_box_class($obj);
      }
      $this->add_cpt_to_admin_menu();
   }

   function add_cpt_to_admin_menu(){
      $vars = $this->new_cpts;
      foreach($vars as $var){
         if( !empty($var['cpt']) ) {
            $cpt_labels = array(
               'name' => _x($var ['cpt'], 'post type general name'),
               'singular_name' => _x($var['cpt'], 'post type singular name'),
               'add_new' => _x('Add New ', $var['cpt']),
               'add_new_item' => __('Add New '. $var['cpt']),
               'edit_item' => __('Edit '. $var['cpt']),
               'new_item' => __('New '.$var['cpt']),
               'view_item' => __('View '.$var['cpt']),
               'search_items' => __('Search '.$var['cpt']),
               'not_found' =>  __('No '.$var['cpt'].' found'),
               'not_found_in_trash' => __('No '.$var['cpt'].' found in Trash'),
               'parent_item_colon' => '',
               'menu_name' => !empty($var['menu_name']) ? $var['menu_name'] : $var ['cpt']
               );
            register_post_type( strtolower( $var['cpt'] ),
               array(
                  'labels' => $cpt_labels,
                  'public' => true,
                  'menu_position' => 5,
                  'has_archive' => strtolower( $var['cpt'] ),
                  'rewrite' => array(
                     'slug' => strtolower( $var['cpt'] ),
                     'with_front' => false
                  ),
                  'hierarchical' => true,
                  'supports' => $var['supports']
                  )
            );
         }
         if( !empty($var['tax']) ) {
            foreach($var['tax'] as $single_tax ){
               $tax_labels = array(
                'name' => _x( !empty($single_tax['menu_name']) ? $single_tax['menu_name'] : $single_tax['tax_label'], 'taxonomy general name' ),
                'singular_name' => _x( $single_tax['tax_label'], 'taxonomy singular name' ),
                'search_items' =>  __( 'Search '. $single_tax['tax_label'] ),
                'all_items' => __( 'All '.$single_tax['tax_label'] ),
                'parent_item' => __( 'Parent '.$single_tax['tax_label'] ),
                'parent_item_colon' => __( 'Parent '.$single_tax['tax_label'].':' ),
                'edit_item' => __( 'Edit '.$single_tax['tax_label'] ),
                'update_item' => __( 'Update ' .$single_tax['tax_label']),
                'add_new_item' => __( 'Add New '.$single_tax['tax_label'] ),
                'new_item_name' => __( 'New Genre '.$single_tax['tax_label'] ),
                'menu_name' => !empty($single_tax['menu_name']) ? $single_tax['menu_name'] : $single_tax['tax_label']
               );

               /* added ability to 'share' custom taxonomies across post types */
               $obj_type =  isset( $var['object_type'] ) ? $var['object_type'] : strtolower( $var['cpt'] );
               register_taxonomy( strtolower( $single_tax['tax_label'] ),$obj_type, array(
                'hierarchical' => true,
                'labels' => $tax_labels,
                'show_ui' => true,
                'public' => true,
                'query_var' => true,
                'rewrite' => true,
               ));
            }
         }
      }
    }
}/*EOC*/


