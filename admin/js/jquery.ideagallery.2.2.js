/*
 * jQuery Idea Gallery Slideshow v2.2
 *
 */
;
(function ($) {
    $.fn.scf_slider = function (el, options) {
        //plugin std variables
        var defaults = {}
        var plugin = this;
        plugin.settings = {}
        //option variables
        var gallerySize = 7,
            current = 0,
            anim = false,
            frameEasing = 'easeOutCirc', /* easeOutSine, easeOutQuart, easeInOutSine, easeInOutBack */
            thumbEasing = 'easeOutBack',
            currentPage = 1,
            realImageSize = 670,
            slideSpeed = 750;
        //gallery and frame variables
        var $scfGallery = $('#gallery'),
            $scfFrame = $scfGallery.find('div#frame'),
            $wrapper = $('#thumb-wrapper'),
            $navPrev = $scfGallery.find('span#prev'),
            $navNext = $scfGallery.find('span#next')
        adjustedImageSize = realImageSize + 10;
        //thumbnail variables
        var $scfThumbList = $('#thumbs'),
            $scfThumbsItems = $scfGallery.find('ul > li'),
            singleWidth = $scfThumbsItems.filter(':first').outerWidth(),
            visible = Math.ceil($wrapper.innerWidth() / singleWidth),
            $scfThumbImgs = $('#gallery img'),
            thumbsCount = $scfThumbsItems.length;
        //thumbnail equations
        $thumbGalleryWidth = singleWidth * gallerySize;
        $scfThumbListWidth = (thumbsCount * singleWidth) + 6;// add 6 pixels active thumbnail border
        $wrapper.css('width', $thumbGalleryWidth);
        $scfThumbList.css('width', $scfThumbListWidth);
        var pages = Math.ceil(thumbsCount / gallerySize);
        _init = function () {
            plugin.settings = $.extend({}, defaults, options);
            plugin.el = el;
            if (thumbsCount < gallerySize) {
                $('#thumb-controls').empty(); // remove thumbnail arrows if there arn't enough to use pagination
            }
            if (thumbsCount == 1) {
                $('.frame-arrows').remove(); // remove frame arrows if there are no other images
            }
            $('.frame-arrows, .thumb-arrows').hide(); // hide all arrows. only show on hover
            _addImageWrapper();
            _addThumbWrapper();
            _lightBox();
            $('#frame a').click(function () {
                //return false;
            });
        }, _addImageWrapper = function () {
            $("#gallery").hover(
                function () {
                    $('.frame-arrows, .thumb-arrows').show();
                },
                function () {
                    $('.frame-arrows, .thumb-arrows').hide();
                }
            );
            $navPrev.on('click', $scfGallery, function (event) {
                _navigate('left');
                return false;
            });
            $navNext.on('click', $scfGallery, function (event) {
                _navigate('right');
                return false;
            });
        }, _addThumbWrapper = function () {
            var $navPrev = $scfGallery.find('span#thumb-prev'),
                $navNext = $scfGallery.find('span#thumb-next'),
                allThumbs = $('#thumbs li');
            allThumbs.on('click', $scfGallery, function (event) {
                current = $(this).index();
                _imgSwap($scfThumbsItems.eq($(this).index()));
                return false;
            });
            $navPrev.on('click', $scfGallery, function (event) {
                _thumbScroll('left');
                return false;
            });
            $navNext.on('click', $scfGallery, function (event) {
                _thumbScroll('right');
                return false;
            });
        }, _navigate = function (dir) {
            if (anim) {
                return false;
            }
            if (dir === 'right') {
                if (current + 1 >= thumbsCount) {
                    current = 0;
                }
                else {
                    ++current;
                }
            } else if (dir === 'left') {
                if (current - 1 < 0) {
                    current = thumbsCount - 1;
                }
                else {
                    --current;
                }
            }
            _imgSwap($scfThumbsItems.eq(current), dir);
            if (!_pagination(current)) {
                _thumbScroll(dir);
            }
        }, _imgSwap = function ($itemThumb, dir) {
            if (anim) {
                return false;
            }
            anim = true;
            var $thumb = $itemThumb.find('img'),
                largesrc = $thumb.data('large'),
                fullsrc = $thumb.data('full'),
                title = $thumb.data('description');
            $scfThumbImgs.removeClass('selected');
            $($thumb).addClass('selected');
            if (dir == 'left') {
                $scfFrame.animate(
                    {left:'-=' + adjustedImageSize},
                    {duration:slideSpeed, easing:frameEasing, complete:function () {
                        $(this).empty().delay(300).css('left', adjustedImageSize).animate(
                            {left:0},
                            {duration:slideSpeed, easing:frameEasing})
                            .append('<a href="#"><img data-large="' + largesrc + '" data-full="' + fullsrc + '" data-description="' + title + '" src="' + largesrc + '" /></a>');
                    }
                    });
            } else {
                $scfFrame.animate(
                    {left:'+=' + adjustedImageSize},
                    {duration:slideSpeed, easing:frameEasing, complete:function () {
                        $(this).empty().delay(300).css('left', -adjustedImageSize).animate(
                            {left:0},
                            {duration:slideSpeed, easing:frameEasing})
                            .append('<a href="#"><img data-large="' + largesrc + '" data-full="' + fullsrc + '" data-description="' + title + '" src="' + largesrc + '" /></a>');
                    }
                    });
            }
            if (title) {
                $scfGallery.find('div#img-caption').show().children('p').empty().text(title);
            }
            anim = false;
        }, _lightBox = function () {
            $expandBtn = $scfGallery.find('span#blow-up');
            $expandBtn.on('click', $expandBtn, function (event) {
                var $frame = $scfGallery.find('div#frame'),
                    $img = $('img', $frame);
                fullsrc = $img.data('full'),
                    title = $img.data('description'),
                    $anchor = $('a', $frame);
                $anchor.attr('rel', 'lightbox[ideagallery]').attr('href', fullsrc);
                $('a', $scfThumbsItems).attr('rel', 'lightbox[ideagallery]');
                $('ul#thumbs li:first a').attr('rel', '');
                $anchor.trigger('click');
                return false;
            });
        }, _pagination = function (current) {
            start_from = (currentPage - 1) * gallerySize;
            end_on = start_from + gallerySize;
            if (current < end_on && current >= start_from) {
                return true;
            } else {
                return false;
            }
        }, _thumbScroll = function (dir) {
            //***~~~~~~~~
            // CHECK IF WE ARE ON THE FIRST OR THE LAST PAGE
            if (currentPage == 1 && dir == 'left') {
                $moveMe = $thumbGalleryWidth * -( pages - 1);
                $('#thumbs').filter(':not(li:animated)').animate({
                    left:$moveMe + 'px'
                }, 500, function () {
                    currentPage = pages;
                });
                return;
            }
            if (currentPage == pages && dir == 'right') {
                $moveMe = $thumbGalleryWidth * -( pages - 1);
                $('#thumbs').filter(':not(li:animated)').animate({
                    left:'0px'
                }, 500, function () {
                    currentPage = 1;
                });
                return;
            }
            //***~~~~~~~~
            //***~~~~~~~~
            if (dir == 'right') {
                $moveMe = $thumbGalleryWidth * -1;
                ++currentPage;
            } else {
                $moveMe = $thumbGalleryWidth * 1;
                --currentPage;
            }
            $('#thumbs').filter(':not(li:animated)').animate(
                {left:'+=' + $moveMe},
                {duration:500, easing:thumbEasing}
            );
            _pagination(currentPage);
        }
        _init();
    }
})(jQuery);
jQuery(function ($) {
    $('body.single-portfolio').scf_slider();
});
;
(function ($) {
    var cache = [];
    // Arguments are image paths relative to the current page.
    $.preLoadImages = function () {
        var args_len = arguments.length;
        for (var i = args_len; i--;) {
            var cacheImage = document.createElement('img');
            cacheImage.src = arguments[i];
            cache.push(cacheImage);
        }
    }
})(jQuery)
