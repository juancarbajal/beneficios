$(function () {
    $(".select2").select2({
        language: 'es',
        placeholder: "Todas",
        allowClear: true
    });
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        startDate: '2016-05-01'
    });

    $('#empresas').change(function () {
        var empresa = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#campanias").empty();
        $("#segmentos").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/reporte-puntos/getDataEmpresa',
            data: {id: empresa, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#campanias").select2({
                        language: 'es',
                        data: data.campanias,
                        placeholder: "Todas",
                        allowClear: true
                    }).val("").trigger("change");

                    $("#segmentos").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Todas",
                        allowClear: true
                    });
                } else {
                    $("#campanias").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Todas",
                        allowClear: true
                    });
                    $("#segmentos").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Todas",
                        allowClear: true
                    });
                }
            }
        });
    });

});

$(document).ready(function () {
    var empresa = $('#empresas');
    if (empresa.val() !== "") {
        empresa.trigger('change');
    }

    $('input[name="FechaFin"]').on('change', function () {
        var f1 = new Date($(this).datepicker('getDate'));
        var f3 = new Date($('input[name="FechaInicio"]').datepicker('getDate'));
        if (f1 < f3 && $(this).val() != '') {
            alert('La fecha hasta debe de ser mayor a la de Inicio');
            $(this).val('');
        }
    });

    $('input[name="FechaInicio"]').on('change', function () {
        var f1 = new Date($(this).datepicker('getDate'));
        var f3 = new Date($('input[name="FechaFin"]').datepicker('getDate'));
        if (f3 < f1 && $(this).val() != '') {
            alert('La fecha Inicio debe de ser menor a la de Fin');
            $(this).val('');
        }
    });
});