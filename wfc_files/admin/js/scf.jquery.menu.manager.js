jQuery(function ($) {
    $("td.scf_shortcut_link:not(:empty)").parents("tr").css("background", "rgb(255,220,200)");
    var base_link=location.protocol + '//' + location.hostname;
    var href_link=location.href;
    href_link=href_link.replace(base_link,'');
    href_link=href_link.substr(0,href_link.lastIndexOf('/'));
    var newBtn = '<a class="add-new-h2" href="'+href_link+'/post-new.php?post_type=page&shortcut=true">Add Shortcut</a>';
    $(newBtn).appendTo($('.add-new-h2').parent());

    var s=parseInt($('#wfc_page_type_shortcut option:selected').val());
    $('#wfc_page_existing_pages').hide(600);
    $('#wfc_page_external_link').hide(600);
    $('#wfc_page_existing_pdfs').hide(600);
    switch(s)
    {
        case 1:
            $('#wfc_page_existing_pages').show(600);
        break;
        case 2:
             $('#wfc_page_external_link').show(600);
        break;
        case 3:
            $('#wfc_page_existing_pdfs').show(600);
        break;
    }

    $('#wfc_page_type_shortcut').change(function () {
        var s=$('#wfc_page_type_shortcut option:selected').val();
        switch(s)
        {
            case 'Page':
                $('#wfc_page_existing_pages').show(600);
                $('#wfc_page_external_link').hide(600);
                $('#wfc_page_existing_pdfs').hide(600);
            break;
            case 'External Link':
                $('#wfc_page_existing_pages').hide(600);
                $('#wfc_page_external_link').show(600);
                $('#wfc_page_existing_pdfs').hide(600);
                $('wfc_page_new_tab_option[]').prop('checked', true);
            break;
            case 'PDF':
                $('#wfc_page_existing_pages').hide(600);
                $('#wfc_page_external_link').hide(600);
                $('#wfc_page_existing_pdfs').show(600);
                $('wfc_page_new_tab_option[]').prop('checked', true);
            break;
            default:
                $('#wfc_page_existing_pages').hide(600);
                $('#wfc_page_external_link').hide(600);
                $('#wfc_page_existing_pdfs').hide(600);
            break;
        }
        $('#wfc_page_existing_pages').css('background-color', 'rgba(235,185,35,0)').animate({'opacity': 1}, 500);
        $('#wfc_page_external_link').css('background-color', 'rgba(235,185,35,0)').animate({'opacity': 1}, 500);
        $('#wfc_page_existing_pdfs').css('background-color', 'rgba(235,185,35,0)').animate({'opacity': 1}, 500);
    });
});

