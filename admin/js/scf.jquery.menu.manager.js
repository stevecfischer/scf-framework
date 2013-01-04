jQuery(function($){
   $("td.scf_shortcut_link:not(:empty)").parents("tr").css("background", "rgb(255,220,200)");
   var newBtn = '<a class="add-new-h2" href="/cms-wfc/wp-admin/post-new.php?post_type=page&shortcut=true">Add Shortcut</a>';
   $(newBtn).appendTo($('.add-new-h2').parent());


   $('select#wfc_page_existing_pages').change(function(){
       var wfc_href = $(this).val();
       if(wfc_href == 'none'){
           $('#wfc_page_shortcut_url input').val('');
       }else{
           $('#wfc_page_shortcut_url input').val($(this).val());
       }

      $('a[href*="pdf"]').removeAttr("style");
   });


   $('#post').submit(function() {
      var patt=/http/g;
      var result=patt.test($('#wfc_page_shortcut_url').val());

      if ($('#wfc_page_shortcut_url').val()==""){
         return true;
      }else if(!result){
         var $elem = $('#post-body');
         $('#publish').attr('class', 'button-primary');
         $('#ajax-loading').css('visibility', 'hidden');
         $('#error_message').css('display', 'block');
         $('html, body').animate({scrollTop: $elem.height()}, 400);
        return false;
         }
        return true;
    });
});