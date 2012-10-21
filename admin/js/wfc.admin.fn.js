/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */

jQuery(function($){
    $('.wfc-meta-control .description').hide();

    $('.wfc-meta-control .switch').on('click',function(){
        $(this).next('.description').toggle();
        return false;
    });

    $('#upload_image_button1').click(function(){
        window.send_to_editor = function(html){
            imgurl = jQuery(html).attr('href');
            jQuery('#upload_image1').val(imgurl);
            tb_remove();
        }
        tb_show('', 'media-upload.php?post_id=1&amp;type=image&amp;TB_iframe=true');
        return false;
    });


    /**
     *
     * @package scf-framework
     * @author Steve (10/21/2012)
     * @version 2.1
     *
     *  ADDED HOVER OVER COLOR AND MAKE ENTIRE ROW CLICKABLE FOR ALL POST TYPES LIST VIEW
     */
    $('.campaign.type-campaign, .page.type-page, .homeboxes.type-homeboxes, .news.type-news, .subpagebanner.type-subpagebanner, .post.type-post').click(function(e){
        if(e.target.nodeName == 'A') return; // best way to exlude any native anchor tags ie quick edit, delete
        //console.log($(this));
        url = $('.row-actions .edit a', $(this) ).attr('href');
        //console.log(url);
        window.location.href = url;
    });

    $('.campaign.type-campaign, .page.type-page, .homeboxes.type-homeboxes, .news.type-news, .subpagebanner.type-subpagebanner, .post.type-post').hover(
        function () { $(this).toggleClass('wfc-mouse-on-post');
        },
        function () { $(this).toggleClass('wfc-mouse-on-post');
        }
    );
});
