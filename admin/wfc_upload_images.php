<?php
function scf_admin_scripts() {
?>
    <script type="text/javascript" src="<?php echo WFC_ADM_JS_URI;?>/media-up.js"></script>
    <script type="text/javascript" src="<?php echo WFC_ADM_JS_URI;?>/jquery.tablednd.0.7.min.js"></script>
    <link rel='stylesheet'  href='<?php echo WFC_ADM_CSS_URI;?>/dragdrop.css' type='text/css' media='all' />
<?php
}
add_action('admin_head', 'scf_admin_scripts');



// Add custom metabox
function add_product_meta_boxes() {
   add_meta_box('product-meta-box', 'Gallery', 'show_fields', 'portfolio', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_product_meta_boxes');

function show_fields($post) {
   // Get saved metabox data
   $images = get_post_meta($post->ID, 'image_urls',true);
   $captions = get_post_meta($post->ID, 'image_captions',true);

   if($images != "") {
   ?>
   <!-- The form with data-->
   <table class="form-table" id="form">
   <?php for( $i = 0; $i < count($images); $i++ ) : ?>
      <tr valign="top" id="row_<?php echo $i+1; ?>">
         <td scope="row">
            <label for="image_<?php echo $i+1; ?>">Picture</label><br/>
         </td>
         <td>
            <input type="text" name="image_urls[]"  id="image_<?php echo $i+1; ?>" size="70" value="<?php echo  $images[$i]; ?>" />
            <input id="image_<?php echo $i+1; ?>" onclick="open_media_uploader(this.id)" type="button" value="Upload Image" class="rttheme_upload_button button" />
            <input  onclick="remove_field(this.id)" id="row_<?php echo $i+1; ?>" type="button" value="Remove picture"  /><br/>
            <label for="caption_<?php echo $i+1; ?>">Caption : </label>
            <input type="text" name="image_captions[]"  id="caption_<?php echo $i+1; ?>" size="59" value="<?php echo  $captions[$i]; ?>" />

         </td>
      </tr>
   <?php endfor;
   }else{ ?>
      <!-- The form without data-->
      <table class="form-table" id="form">
      <tr valign="top" id="row_1">
         <td scope="row">
            <label for="image_1">Picture</label>
         </td>
         <td>
            <input type="text" name="image_urls[]"  id="image_1" size="70" value="" />
            <input id="image_1" onclick="open_media_uploader(this.id)" type="button" value="Upload Image" class="rttheme_upload_button button" /><br/>
            <label for="caption_1">Caption : </label>
            <input type="text" name="image_captions[]"  id="caption_1" size="59" value="" />
            <td><a onclick="remove_field(this.id)" id="row_1" href="#">Remove picture</a></td>
         </td>
      </tr>
   <?php } ?>
   </table>
   <br/>&nbsp;&nbsp;&nbsp;<a id="add_field" href="">Add an other picture</a>

<?php
// Enqueue some admin scripts
function admin_scripts(){
   wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
}

function admin_styles(){
   wp_enqueue_style('thickbox');
}

add_action('admin_print_scripts', 'admin_scripts');
add_action('admin_print_styles', 'admin_styles');
}

function save_my_meta_box($post_id) {

   //exit on autosave
   if (defined('DOING_AUTOSAVE')  == DOING_AUTOSAVE) {
      return $post_id;
   }

   // Update and Delete
   if(isset($_POST['image_urls'])) {
      update_post_meta($post_id, 'image_urls', $_POST['image_urls']);
      update_post_meta($post_id, 'image_captions', $_POST['image_captions']);
   } else {
      delete_post_meta($post_id, 'image_urls');
      delete_post_meta($post_id, 'image_captions');
   }

   return $post_id;
}
add_action('save_post', 'save_my_meta_box');


/**
 * SCF IDEA GALLERY ADDITIONS
 *
*/






function scf_load_scripts(){
     wp_enqueue_script('jquery-scf-ideagallery', WFC_JS_URI  . '/jquery.ideagallery.1.1.js', array('jquery', 'jquery-scf-lightbox') );
     wp_enqueue_script('jquery-scf-lightbox', WFC_JS_URI  . '/lightbox.js', array('jquery') );
     wp_enqueue_script('jquery-scf-easing', WFC_JS_URI  . '/jquery.easing.1.3.js', array('jquery') );

}
add_action('init', 'scf_load_scripts');


function scf_load_styles(){
     wp_register_style('css-scf-ideagallery',WFC_CSS_URI . '/style.css', 2 , false, 'all');
     wp_enqueue_style('css-scf-ideagallery');
}
add_action('init', 'scf_load_styles');




function scf_imgurl_to_postid($image_src,$posttype = false) {
     global $wpdb;
     $new_img_src = explode('uploads/',$image_src);


     $query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value ='$new_img_src[1]'";
     $id = $wpdb->get_var($query);

     if($posttype == 'products'){
        $img_thumb = wp_get_attachment_image_src( $id, 'product-thumb');
        $img_frame = wp_get_attachment_image_src( $id, 'product-frame');
        $img_max_size = wp_get_attachment_image_src( $id, 'max-size-lightbox');
        $img_swatch_thumb = wp_get_attachment_image_src( $id, 'product-swatch-thumb');
        $arr = array('thumb'=>$img_thumb,'frame'=>$img_frame,'swatch'=>$img_swatch_thumb,'max_size'=>$img_max_size);
     }else{
        $img_thumb = wp_get_attachment_image_src( $id, 'idea-gallery-thumb');
        $img_frame = wp_get_attachment_image_src( $id, 'idea-gallery-frame');
        $img_max_size = wp_get_attachment_image_src( $id, 'max-size-lightbox');
        $arr = array('thumb'=>$img_thumb,'frame'=>$img_frame,'max_size'=>$img_max_size);
     }


     return $arr;
}



//include_once 'wfc-order-custom-posts.php';


