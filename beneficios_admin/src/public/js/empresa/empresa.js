
$(function () {
    var ctrlDown = false;
    var ctrlKey = 17, vKey = 86, cKey = 67,xKey=88;

    $(document).keydown(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = true;
    }).keyup(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = false;
    });

    $(".select2").select2();

    var ruc = $("input[name=Ruc]");

    ruc.on("keydown", function (event) {
        if (ctrlDown && (event.keyCode == vKey || event.keyCode == cKey || event.keyCode == xKey)) {
            return true;
        }else{
            onlyNumbers(event);
        }
    });

    ruc.on("keyup", function () {
        maxInput($(this), 11);
        copyPage($(this));
    });
});



$('.elim').click(function () {
    var row = $(this).parents('tr');
    var id = row.data('id');
    var tipo = row.data('tipo');
    var val = "";
    var id_tag = '#elim' + id + tipo;
    var elim_id = $(id_tag);
    var csrf = $('input:hidden[name=csrf]').val();
    var message='';
    if (elim_id.attr('checked') === 'checked' ) {
        val = '1';
        message = 'Desactivar';
    } else {
        val = '0';
        message = 'Activar';
    }
    var r = confirm('¿Desea '+message+' la Empresa?');
    if (r == true) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/empresa/delete',
            data: {id: id, val: val, tipo: tipo, csrf:csrf },
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
    }  else {
        if ($(this).attr('checked')) {
            $(this).attr("checked", "checked");
            $(this).prop("checked", true);
        } else {
            $(this).removeAttr("checked");
            $(this).prop("checked", false);
        }
    }
});

function onlyNumbers(event) {
    if (event.shiftKey) {
        event.preventDefault();
    }

    if (!(event.keyCode == 46 || event.keyCode == 17
        || event.keyCode == 8 || event.keyCode == 9
        || event.keyCode == 35 || event.keyCode == 36
        || event.keyCode == 37 || event.keyCode == 39)) {


        if (event.keyCode >= 95) {
            if (event.keyCode < 96 || event.keyCode > 105) {
                event.preventDefault();
            }
        }
        else {
            if (event.keyCode < 48 || event.keyCode > 57) {
                event.preventDefault();
            }
        }
    }
}

function copyPage(input){
    var value = input.val();
    if(! $.isNumeric( value )){
        input.val('');
    }
}

function maxInput(input, limit) {
    var value = input.val();
    var current = value.length;
    if (limit < current) {
        input.val(value.substring(0, limit));
    }
}

function onlyLeters(key) {
    if ((key.keyCode < 65 || key.keyCode > 122) //letras minusculas
        && (key.which != 8) //retroceso
        && (key.which != 9) //tab
        && (key.keyCode != 17) //ctrl
        && (key.keyCode != 16) //shift
        && (key.keyCode != 35) //inicio
        && (key.keyCode != 36) //fin
        && (key.keyCode != 46) //suprim
        && (key.keyCode != 37) //felcha izq
        && (key.keyCode != 39) //felcha der
        && (key.keyCode != 241) //ñ
        && (key.keyCode != 209) //Ñ
        && (key.keyCode != 32) //espacio
        && (key.keyCode != 225) //á
        && (key.keyCode != 233) //é
        && (key.keyCode != 237) //í
        && (key.keyCode != 243) //ó
        && (key.keyCode != 250) //ú
        && (key.keyCode != 193) //Á
        && (key.keyCode != 201) //É
        && (key.keyCode != 205) //Í
        && (key.keyCode != 211) //Ó
        && (key.keyCode != 218) //Ú
    )
        key.preventDefault();
}
