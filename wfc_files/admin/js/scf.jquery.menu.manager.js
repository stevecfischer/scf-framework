jQuery(function ($) {
    $("td.scf_shortcut_link:not(:empty)").parents("tr").css("background", "rgb(255,220,200)");
    var base_link=location.protocol + '//' + location.hostname;
    var href_link=location.href;
    href_link=href_link.replace(base_link,'');
    href_link=href_link.substr(0,href_link.lastIndexOf('/'));
    var newBtn = '<a class="add-new-h2" href="'+href_link+'/post-new.php?post_type=page&shortcut=true">Add Shortcut</a>';
    $(newBtn).appendTo($('.add-new-h2').parent());

    $('#wfc_page_existing_pages').hide(600);
    $('#wfc_page_existing_posts').hide(600);
    $('#wfc_page_external_link').hide(600);
    $('#wfc_page_existing_images').hide(600);
    $('#wfc_page_existing_pdfs').hide(600);
    $('#wfc_page_type_shortcut').change(function () {
        var s=parseInt($('#wfc_page_type_shortcut option:selected').val());
        switch(s)
        {
            case 1:
                $('#wfc_page_existing_pages').show(600);
                $('#wfc_page_existing_posts').hide(600);
                $('#wfc_page_external_link').hide(600);
                $('#wfc_page_existing_images').hide(600);
                $('#wfc_page_existing_pdfs').hide(600);
            break;
            case 2:
                $('#wfc_page_existing_pages').hide(600);
                $('#wfc_page_existing_posts').hide(600);
                $('#wfc_page_external_link').show(600);
                $('#wfc_page_existing_images').hide(600);
                $('#wfc_page_existing_pdfs').hide(600);
            break;
            case 3:
                $('#wfc_page_existing_pages').hide(600);
                $('#wfc_page_existing_posts').hide(600);
                $('#wfc_page_external_link').hide(600);
                $('#wfc_page_existing_images').show(600);
                $('#wfc_page_existing_pdfs').hide(600);
            break;
            case 4:
                $('#wfc_page_existing_pages').hide(600);
                $('#wfc_page_existing_posts').hide(600);
                $('#wfc_page_external_link').hide(600);
                $('#wfc_page_existing_images').hide(600);
                $('#wfc_page_existing_pdfs').show(600);
            break;
            case 5:
                $('#wfc_page_existing_pages').hide(600);
                $('#wfc_page_existing_posts').show(600);
                $('#wfc_page_external_link').hide(600);
                $('#wfc_page_existing_images').hide(600);
                $('#wfc_page_existing_pdfs').hide(600);
            break;
            default:
                $('#wfc_page_existing_pages').hide(600);
                $('#wfc_page_existing_posts').hide(600);
                $('#wfc_page_external_link').hide(600);
                $('#wfc_page_existing_images').hide(600);
                $('#wfc_page_existing_pdfs').hide(600);
            break;
        }
    });
    $('#wfc_page_type_shortcut').trigger('change');
});

