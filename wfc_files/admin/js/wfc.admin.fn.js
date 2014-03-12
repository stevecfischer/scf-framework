/**
 *
 * @package scf-framework
 * @author Steve (10/21/2012)
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
jQuery(function ($) {
    // plus one button text repeater fields cmb's
    $('.wfc-meta-field-grid').on('click', '.wfc-plus-one', function () {
        var newRow = $('.wfc-text-repeater-row:last')
            .clone()
            .find("input")
            .val("")
            .end();
        $(newRow).appendTo('.wfc-meta-field-grid');
    });
    function removeMinus(numRows, minusButton) {
        if (numRows == 1) {
            $('.wfc-minus-one:first').remove();
        }
    }

    var undoSet = false;

    function setUndo() {
        var tempUndo = $('.wfc-meta-field-grid').clone();
        $('<span class="blink_me wfc-undo">Undo</span>')
            .appendTo(".wfc-input-text_repeater .wfc-meta-label")
            .fadeIn("slow");
        $('.wfc-undo').on('click', function () {
            $('.wfc-meta-field-grid').html(tempUndo.html());
            resetUndo();
        });
    }

    function resetUndo() {
        $('.blink_me.wfc-undo').remove();
        undoSet = false;
    }

    $('.wfc-meta-field-grid').on('click', '.wfc-minus-one', function () {
        /*
         @scftodo: this is too buggy to release
         if(undoSet == false){
         setUndo();
         undoSet = true;
         }*/
        var scfThis = $(this);
        var wfcTextRepeaterRows = $('.wfc-text-repeater-row');
        if (wfcTextRepeaterRows.length <= 1) {
            return;
        }
        $.when($(this).closest('.wfc-text-repeater-row').remove()).then(
            removeMinus(wfcTextRepeaterRows.length - 1, scfThis)
        );
    });
});
/**
 *
 * @package scf-framework
 * @author Steve (3/4/13)

 *
 * Used in Site Options Panel
 */
jQuery(function ($) {
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
    $('.wfc-meta-block .description').hide();
    $('.wfc-meta-block .switch').on('click', function () {
        $(this).next('.description').toggle();
        return false;
    });
    $('#develop_checklist,  #seo_checklist').find($('.good, .bad')).each(function () {
        $('input', this).hide();
    });
    $('.wfc-color-picker').wpColorPicker();
});