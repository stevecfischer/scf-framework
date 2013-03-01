/**
 * A TO Z INDEX FUNCTIONS
 */
;
(function ($) {
    $.fn.wfc_atoz_index = function (el, options) {
        var defaults = {}
        var plugin = this;
        plugin.settings = {}
        var init = function () {
            plugin.settings = $.extend({}, defaults, options);
            plugin.el = el;
            $('#list_A').parent().addClass('current');
            $('ul#list_A').addClass('active').removeClass('inactive');
            $('#az_tabs li a').click(function (e) {
                e.preventDefault();
                console.log(plugin);
                $('#az_tabs li').removeClass('current');
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
    $('#list_A').wfc_atoz_index();
});
// END A TO Z INDEX FUNCTIONS