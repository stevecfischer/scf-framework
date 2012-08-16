/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */

jQuery(function($){
   $('.wfc-meta-control .description').hide();

   $('.wfc-meta-control .switch').on('click',function(){
       $(this).next('.description').toggle();
      return false;
   });

   $('#upload_image_button1').click(function(){
   window.send_to_editor = function(html){
   imgurl = jQuery(html).attr('href');
   jQuery('#upload_image1').val(imgurl);
   tb_remove();
   }
   tb_show('', 'media-upload.php?post_id=1&amp;type=image&amp;TB_iframe=true');
   return false;
   });
});
