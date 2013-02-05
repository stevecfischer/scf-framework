jQuery(function ($) {
    $('#add_field').click(function (event) {
        rowcount = $('#form tr').length;
        newRowCount = rowcount + 1;
        event.preventDefault();
        newRow = $('table#form tr:first-child').clone();
        $(newRow).each(function (index, value) {
            $('input[type="text"]', value).val(' ');
            $('input:nth-child(2)', value).attr('id', 'image_' + newRowCount);
            $('input:last-child', value).attr('id', 'caption_' + newRowCount);
        });
        $('#form tr:last').after(newRow);
    });
    $('#poststuff').on('click', '.wfc_upload_image', function () {
        var postID = $('#current_post_id').val();
        var id = $(this).prev().attr('id');
        tb_show('', 'media-upload.php?post_id=' + postID + '&type=image&TB_iframe=true');
        window.send_to_editor = function (html) {
            url = jQuery(html).attr('href');
            jQuery('#' + id).val(url);
            tb_remove();
        };
        return false;
    });
    $('#poststuff').on('click', '.wfc_remove_image', function () {
        var selectedRow = $(this).closest('tr').remove();
        return false;
    });
    //$("table tbody.wfc-image-gallery-table").sortable({handle: 'tr:first'});
    //$("table tbody.wfc-image-gallery-table").disableSelection();
});
// Make the table use the Drag & drop functionality
jQuery(document).ready(function () {
    // Initialise the first table (as before)
    //init_table();
});
function init_table() {
    jQuery("#form").tableDnD({
        onDragClass:"myDragClass",
        onDrop     :function (table, row) {
            var rows = table.tBodies[0].rows;
            var debugStr = "Row dropped was " + row.id + ". New order: ";
            for (var i = 0; i < rows.length; i++) {
                debugStr += rows[i].id + " ";
            }
            jQuery("#debugArea").html(debugStr);
        },
        onDragStart:function (table, row) {
            jQuery("#debugArea").html("Started dragging row " + row.id);
        }
    });
    jQuery("#form_swatches").tableDnD({
        onDragClass:"myDragClass",
        onDrop     :function (table, row) {
            var rows = table.tBodies[0].rows;
            var debugStr = "Row dropped was " + row.id + ". New order: ";
            for (var i = 0; i < rows.length; i++) {
                debugStr += rows[i].id + " ";
            }
            jQuery("#debugArea").html(debugStr);
        },
        onDragStart:function (table, row) {
            jQuery("#debugArea").html("Started dragging row " + row.id);
        }
    });
}
