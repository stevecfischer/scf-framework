jQuery(function ($) {
    $("td.scf_shortcut_link:not(:empty)").parents("tr").css("background", "rgb(255,220,200)");
    var base_link=location.protocol + '//' + location.hostname;
    var href_link=location.href;
    href_link=href_link.replace(base_link,'');
    href_link=href_link.substr(0,href_link.lastIndexOf('/'));
    var newBtn = '<a class="add-new-h2" href="'+href_link+'post-new.php?post_type=page&shortcut=true">Add Shortcut</a>';
    $(newBtn).appendTo($('.add-new-h2').parent());
    var wfc_url = $('#wfc_page_shortcut_url input');
    $('select#wfc_page_existing_pages').change(function () {
        var wfc_href = $(this).val();

        if (wfc_href == 'none') {
            $(wfc_url).val('');
        } else {
            $(wfc_url).val($(this).val());
        }
        $('a[href*="pdf"]').removeAttr("style");
    });
    $('#post').submit(function () {
        /*!
        @todo: -SCF- all this needs to be reviewed!
         */
        var patt = /http/g;
        var result = patt.test($(wfc_url).val());
        if ($(wfc_url).val() == "") {
            return true;
        } else if (!result) {
            var $elem = $('#post-body');
            $('#publish').attr('class', 'button-primary');
            $('#ajax-loading').css('visibility', 'hidden');
            $('#error_message').css('display', 'block');
            $('html, body').animate({scrollTop:$elem.height()}, 400);
            return false;
        }
        return true;
    });
});