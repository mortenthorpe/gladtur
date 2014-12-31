$(document).ready(function(){
    $( "#form_query" ).keypress(function( event ) {
        if ( event.which == 13 ) {
            event.preventDefault();
            $(this).parents('form').trigger('submit');
        }
    });

    $('fieldset legend').bind('click', function(){
        $(this).parent().find('.willshowonhide').slideToggle();
        $(this).parent().find('.willhide').slideToggle();
    });

    /*$('input:text, textarea').each(function(){
        $(this).attr('value_default', $(this).val());
    });

    $('input:text, textarea').click(function(){
        if($(this).val() == $(this).attr('value_default')){
            $(this).val('');
        }
    });
    $('input:text, textarea').focusout(function(){
        if($(this).val() == ''){
            $(this).val($(this).attr('value_default'));
        }
    });
    */

    $('.searchform #form_category .btn_label label').click(function(){
        var labelTitle = $(this).find('.label_title').text();
        if(labelTitle.length>25){
            labelTitle = labelTitle.substr(0, 22)+'...';
        }
        $('#op_category_search').val('Søg '+labelTitle);
    });

    //http://stackoverflow.com/questions/5939801/passing-extra-parameters-to-source-using-jquery-ui-autocomplete
    var xhr;
    var isDevice = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
    if(isDevice){
        $('body').addClass('mobile');
    }
    if(!isDevice){
        $('body').addClass('desktop');
        $('.searchform #form_query').autocomplete({
            minLength: 4,
            source: function( request, response ) {
                var regex = new RegExp(request.term, 'i');
                if(xhr){
                    xhr.abort();
                }
                xhr = $.ajax({
                    url: "/ajaxsearch",
                    dataType: "json",
                    data: {
                        name: request.term,
                    },
                    cache: false,
                    success: function(data) {
                        response($.map(data, function(item) {
                            if(regex.test(item.label)){
                                return {
                                    label: item.label,
                                    slug: item.slug
                                };
                            }
                        }));
                    }
                });
            },
            select: function( event, ui ) {
                $('.searchform #form_query').val( ui.item.label);
                window.location.href='/sted/' + ui.item.slug;
                return false;
            }
        });
    }

});