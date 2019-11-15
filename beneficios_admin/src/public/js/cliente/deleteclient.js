/**
 * Created by luisvar on 02/09/15.
 */

$(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    }
);
$('.elim').click(function () {
    var row = $(this).parents('tr');
    var id = $(this).attr('rel');
    var company = $(this).attr('company');
    var val = 0;
    if ($(this).is(':checked')) {
        val = 1;
    }
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/cliente/active',
        data: {id: id, val: val, company: company},
        success: function (data) {
            console.log(data);
        }
    });
});