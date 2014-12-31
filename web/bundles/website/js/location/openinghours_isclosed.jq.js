/**
 * Created by mortenthorpe on 22/01/14.
 */
$(document).ready(function(){
    $('.isclosed').each(function(){
        // Locate the corresponding textfield with the opening hours text inside it
        var myTimestxtFieldId = $(this).attr('id').replace('isclosed', 'timesTxt');
        var myTimestxtField = $(myTimestxtFieldId);
        if($(this).is(':checked')){
            myTimestxtField.val('LUKKET');
            myTimestxtField.attr('disabled','disabled');
        }
        else{
            myTimestxtField.val('');
            myTimestxtField.attr('disabled',false);
        }
    });
    $('.isclosed').bind('click', function(){
        // Locate the corresponding textfield with the opening hours text inside it
        var myTimestxtFieldId = $(this).attr('id').replace('isclosed', 'timesTxt');
        var myTimestxtField = $('#'+myTimestxtFieldId);
        if($(this).is(':checked')){
            myTimestxtField.val('LUKKET');
            myTimestxtField.attr('disabled','disabled');
        }
        else{
            myTimestxtField.val(myTimestxtField.attr('value_default'));
            myTimestxtField.attr('disabled',false);
        }
    });
});