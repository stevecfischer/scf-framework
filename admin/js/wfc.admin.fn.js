/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 */
jQuery(function($){
    if($('#page_template').val() == 'template-3-col.php'){
            $('#_additional_page_news_widget_options').show();
        }else{
            $('#_additional_page_news_widget_options').hide();
        }
    $('#page_template').change(function(){
        if($('#page_template').val() == 'template-3-col.php'){
            $('#_additional_page_news_widget_options').show();
        }else{
            $('#_additional_page_news_widget_options').hide();
        }
    });
});
