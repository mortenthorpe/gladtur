$(document).ready(function(){
    if($('#locationCategory_isTopcategory').prop('checked')){
       $('#locationCategory_parentCategory').prop('disabled', 'disabled');
    }
    else{
        $('#locationCategory_parentCategory').prop('disabled', false);
    }
});

var refSelectedVal;
$(document).ready(function(){
$('#locationCategory_parentCategory').change(function(){
    if(!$(this).val()){
        $('#locationCategory_isTopcategory').prop('checked', true);
    }
    else{
        $('#locationCategory_isTopcategory').prop('checked', false);
    }
});
  $('#locationCategory_isTopcategory').change(function(){
      if($('#locationCategory_isTopcategory').prop('checked')){
          refSelectedVal = $('#locationCategory_parentCategory').val();
          $('#locationCategory_parentCategory').val(null);
          $('#locationCategory_parentCategory').prop('disabled', 'disabled');
      }
      else{
          $('#locationCategory_parentCategory').prop('disabled', false);
          $('#locationCategory_parentCategory').val(refSelectedVal);
      }
  });
});