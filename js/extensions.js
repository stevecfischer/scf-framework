/**
 * A TO Z INDEX FUNCTIONS
 */
;
(function ($) {
    $.fn.wfc_atoz_index = function (el, options) {
        var plugin = this;
        var scf_ps = plugin.selector;
        var defaults = {
            'initial_tab': $(scf_ps + " > li > :first-child").attr('id')
        }
        plugin.settings = {}
        var init = function () {
            plugin.settings = $.extend({}, defaults, options);
            plugin.el = el;
            $("#"+plugin.settings.initial_tab).parent().addClass('current');
            $('ul#'+plugin.settings.initial_tab).addClass('active').removeClass('inactive');
            $(scf_ps + " li a").click(function (e) {
                e.preventDefault();
                $(scf_ps + ' li').removeClass('current');
                $(this).parent().addClass('current');
                $('#atoz ul').removeClass('active').addClass('inactive');
                var actObj = $(this).attr('id');
                $('#atoz ul#' + actObj).removeClass('inactive').addClass('active');
            });
        }
        init();
    }
})(jQuery);
jQuery(function ($) {
    $('#az_tabs').wfc_atoz_index({},{
    /*'initial_tab':'list_A'*/
    });
});
// END A TO Z INDEX FUNCTIONS