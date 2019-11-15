/**
 * Created by marlo on 02/09/15.
 */
$(function () {
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd'
    });
    $(".select2").select2();
});

$('.elim').click(function () {
    var row = $(this).parents('tr');
    var id = row.data('id');
    var val = "";
    var id_tag = '#elim' + id;
    var csrf = $('input:hidden[name=csrf]').val();
    var elim_id = $(id_tag);

    if (elim_id.attr('checked') === 'checked' ) {
        val = '1';
    } else {
        val = '0';
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/paquete/delete',
        data: {id: id, val: val,csrf:csrf},
        success: function (data) {
            $('input:hidden[name=csrf]').val(data.csrf);
            if (!data.response){
                if (val == "0"){
                    elim_id.removeAttr("checked");
                    elim_id.prop("checked", false);
                }else {
                    elim_id.attr("checked", "checked");
                    elim_id.prop("checked", true);
                }
            } else {
                if (val == "0"){
                    elim_id.attr("checked", "checked");
                    elim_id.prop("checked", true);
                }else {
                    elim_id.removeAttr("checked");
                    elim_id.prop("checked", false);
                }
            }
        }
    });
});

$('.elima').click(function () {
    var row = $(this).parents('tr');
    var id = row.data('id');
    var val = "";
    var id_tag = '#elima' + id;
    var elim_id = $(id_tag);

    if (elim_id.attr("name") == 1) {
        elim_id.attr("name", 0);
        val = '0';
    } else {
        elim_id.attr("name", 1);
        val = '1';
    }

    $.post("/paquete/deleteassing", {
        id: id,
        val: val
    }, function (data) {
        if (data.type == 1) {
            alert(data.message);
        } else if (data.type == 2) {
            //$('#b'+id).text(data.bolsa);
        }
    }, 'json');
});
