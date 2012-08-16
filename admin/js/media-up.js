function open_media_uploader(id){
   tb_show('', 'media-upload.php?post_id=1&type=image&amp;TB_iframe=true');
   window.send_to_editor = function(html) {
      url = jQuery(html).attr('href');
      jQuery('#'+id).val(url);
      tb_remove();
   };
   return false;
}


function remove_field(id){
   jQuery('#'+id).remove();
}

( function( $ ) {

   $(document).ready(

      function()
      {

         $('#add_field').click(
            function(event)
            {
               var rowcount = $('#form tr').length + 1;
               event.preventDefault();
               if(rowcount != 1)
               $('#form tr:last').after('<tr valign="top" id="row_'+rowcount+'"><td scope="row"><label for="image_'+rowcount+'">Picture </label></td><td><input type="text" name="image_urls[]"  id="image_'+rowcount+'" size="70" value="" /><input id="image_'+rowcount+'" onclick="open_media_uploader(this.id)" type="button" value="Upload Image" class="rttheme_upload_button button" /><input id="row_'+rowcount+'" onclick="remove_field(this.id)" type="button" value="Remove picture"  /><br/><label for="caption_'+rowcount+'">Caption : </label><input type="text" name="image_captions[]"  id="caption_'+rowcount+'" size="59" value="" /></td></tr>');
               else
               $('#form ').append('<tr valign="top" id="row_'+rowcount+'"><td scope="row"><label for="image_'+rowcount+'">Picture </label></td><td><input type="text" name="image_urls[]"  id="image_'+rowcount+'" size="70" value="" /><input id="image_'+rowcount+'" onclick="open_media_uploader(this.id)" type="button" value="Upload Image" class="rttheme_upload_button button" /><input id="row_'+rowcount+'" onclick="remove_field(this.id)" type="button" value="Remove picture"  /><br/><label for="caption_'+rowcount+'">Caption : </label><input type="text" name="image_captions[]"  id="caption_'+rowcount+'" size="59" value="" /></td></tr>');
               init_table();
         }
         );

            $('#add_field_swatches').click(
            function(event)
            {
               var rowcount = $('#form_swatches tr').length + 1;
               event.preventDefault();
               if(rowcount != 1)
               $('#form_swatches tr:last').after('<tr valign="top" id="row_swatches_'+rowcount+'"><td scope="row"><label for="image_swatches_'+rowcount+'">Picture </label></td><td><input type="text" name="image_urls_swatches[]"  id="image_swatches_'+rowcount+'" size="70" value="" /><input id="image_swatches_'+rowcount+'" onclick="open_media_uploader(this.id)" type="button" value="Upload Image" class="rttheme_upload_button button" /><input id="row_swatches_'+rowcount+'" onclick="remove_field(this.id)" type="button" value="Remove picture"  /><br/><label for="caption_swatches_'+rowcount+'">Caption : </label><input type="text" name="image_captions_swatches[]"  id="caption_swatches_'+rowcount+'" size="59" value="" /></td></tr>');
               else
               $('#form_swatches ').append('<tr valign="top" id="row_swatches_'+rowcount+'"><td scope="row"><label for="image_swatches_'+rowcount+'">Picture </label></td><td><input type="text" name="image_urls_swatches[]"  id="image_swatches_'+rowcount+'" size="70" value="" /><input id="image_swatches_'+rowcount+'" onclick="open_media_uploader(this.id)" type="button" value="Upload Image" class="rttheme_upload_button button" /><input id="row_swatches_'+rowcount+'" onclick="remove_field(this.id)" type="button" value="Remove picture"  /><br/><label for="caption_swatches_'+rowcount+'">Caption : </label><input type="text" name="image_captions_swatches[]"  id="caption_swatches_'+rowcount+'" size="59" value="" /></td></tr>');
               init_table();
         }
         );
      }

   );
} ) ( jQuery );


// Make the table use the Drag & drop functionality
jQuery(document).ready(function() {
      // Initialise the first table (as before)
       init_table();
});

function init_table(){
   jQuery("#form").tableDnD({
        onDragClass: "myDragClass",
        onDrop: function(table, row) {
            var rows = table.tBodies[0].rows;
            var debugStr = "Row dropped was "+row.id+". New order: ";
            for (var i=0; i<rows.length; i++) {
                debugStr += rows[i].id+" ";
            }
            jQuery("#debugArea").html(debugStr);
        },
        onDragStart: function(table, row) {
            jQuery("#debugArea").html("Started dragging row "+row.id);
        }
      });

        jQuery("#form_swatches").tableDnD({
        onDragClass: "myDragClass",
        onDrop: function(table, row) {
            var rows = table.tBodies[0].rows;
            var debugStr = "Row dropped was "+row.id+". New order: ";
            for (var i=0; i<rows.length; i++) {
                debugStr += rows[i].id+" ";
            }
            jQuery("#debugArea").html(debugStr);
        },
        onDragStart: function(table, row) {
            jQuery("#debugArea").html("Started dragging row "+row.id);
        }
      });
}
