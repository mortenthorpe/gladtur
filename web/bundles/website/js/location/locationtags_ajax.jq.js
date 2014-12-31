$(document).ready(function(){
    $('#pid').on('change',function(){
        $('#data_saved').html('');
        ajaxreplace($(this).val());

    });

    $('.locationtag').on('change', function(){
        $('#data_saved').html('');
    });

    var frm = $('#locationtagsforprofile');
    frm.submit(function (ev) {
        $.ajax({
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: frm.serialize(),
            success: function (data) {
                $('#data_saved').html(data);
            }
        });

        ev.preventDefault();
    });

});