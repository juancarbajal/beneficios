/**
 * Created by luisvar on 19/11/15.
 */
document.write("<script type='text/javascript' src='../../js/validations.js'></script>");

$(function () {
    $(".select2").select2();
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd'
    });

    $('.popup').click(function () {
        var row = $(this).parents('tr');
        var id = row.data('id');

        $.post("/paquetes-comprados/getdetalle", {
            id: id
        }, function (data) {
            if (data.response == true) {
                var tipo = data.results['TipoPaquete'];
                if (tipo == 1) {
                    $('#paquete').text(data.results['NombrePaquete']);
                    $('#precio-descarga').val(data.results['PrecioUnitarioDescarga']);
                    $('#bonificacion').val(data.results['Bonificacion']);
                    $('#precio-bonificacion').val(data.results['PrecioUnitarioBonificacion']);
                    $('#costo').val('');
                    $('.presencia').css('display','none');
                    $('.descarga').css('display','block');
                } else if (tipo == 2) {
                    $('#paquete').text(data.results['NombrePaquete']);
                    $('#precio-descarga').val('');
                    $('#bonificacion').val('');
                    $('#precio-bonificacion').val('');
                    $('#costo').val(data.results['CostoDia']);
                    $('.presencia').css('display','block');
                    $('.descarga').css('display','none');
                }
                $("#btn_modal").trigger("click");
            } else {
                console.log(data.message);
            }
        }, 'json');
    });
});
