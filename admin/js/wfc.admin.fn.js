/**
 *
 * @package scf-framework
 * @author Steve (10/21/2012)
 * @version 2.1
 *
 *  ADDED HOVER OVER COLOR AND MAKE ENTIRE ROW CLICKABLE FOR ALL POST TYPES LIST VIEW
 *  1/5/12 --SCF: converted into jquery plugin
 */
(function ($) {
    if (!$.Wfc) {
        $.Wfc = new Object();
    }
    ;
    $.Wfc.AdminTools = function (el, cptHover, updateAndDone, options) {
        var base = this;
        base.$el = $(el);
        base.el = el;
        base.$el.data("Wfc.AdminTools", base);
        base.init = function () {
            base.cptHover = cptHover;
            base.updateAndDone = updateAndDone;
            base.options = $.extend({}, $.Wfc.AdminTools.defaultOptions, options);
            base.$el.click(function (e) {
                if (e.target.nodeName == 'A' || e.target.nodeName == 'INPUT') {
                    return;
                }
                url = $('.row-actions .edit a', $(this)).attr('href');
                window.location.href = url;
            });
            base.$el.hover(
                function () {
                    $(this).toggleClass('wfc-mouse-on-post');
                },
                function () {
                    $(this).toggleClass('wfc-mouse-on-post');
                }
            );
        };
        base.functionName = function (paramaters) {
        };
        base.init();
    };
    $.Wfc.AdminTools.defaultOptions = { };
    $.fn.wfc_AdminTools = function (cptHover, updateAndDone, options) {
        return this.each(function () {
            (new $.Wfc.AdminTools(this, cptHover, updateAndDone, options));
        });
    };
})(jQuery);
/**
 *
 * @package scf-framework
 * @author Steve (3/4/13)
 * @version 2.3
 *
 * Used in Site Options Panel
 */
jQuery(document).ready(function () {
    jQuery('.rm_options').slideUp();
    jQuery('.rm_section h3').click(function () {
        if (jQuery(this).parent().next('.rm_options').css('display') == 'none') {
            jQuery(this).removeClass('inactive');
            jQuery(this).addClass('active');
            jQuery(this).children('img').removeClass('inactive');
            jQuery(this).children('img').addClass('active');
        } else {
            jQuery(this).removeClass('active');
            jQuery(this).addClass('inactive');
            jQuery(this).children('img').removeClass('active');
            jQuery(this).children('img').addClass('inactive');
        }
        jQuery(this).parent().next('.rm_options').slideToggle('slow');
    });
});