$(document).ready(function(){
    $('#accessibilities_listitems li').each(function(){
       var labelElems = $(this).find('label.label_icon');
        $(labelElems).each(function(){
           if($(this).next().is(':checked')){
               $(this).addClass('active');
           }
        });
    });

    $(document.body).on('change', '#accessibilities_listitems li input', function(){
        $(this).parents('li').find('label.label_icon').removeClass('active');
        if($(this).is(':checked')){
          $(this).prev('label.label_icon').addClass('active');
        }
    });

    /* Code below is not for toggling properties, but for disabling static input in forms! */
    /*if($('#gladturlocation_location_top_category').val()){
      $('#gladturlocation_location_top_category').wrap('<div id="topcategory_disabled" style="display:none"/>');
      $('#topcategory_disabled').before('<strong class="value-static">' + $('#gladturlocation_location_top_category option:selected').text() + '</strong>');
    }
    $(":text").each(function(){
        var attr = $(this).attr('readonly');
        if (typeof attr !== typeof undefined && attr !== false) {
            $(this).wrap('<div class="disabled_text" style="display:none"/>');
            $(this).parent().before('<strong class="value-static">' + $(this).val() + '</strong>');
        }
    });*/
});