$(function () {
    $(".select2").select2({
        language: 'es',
        placeholder: "Todas",
        allowClear: true
    });

    $('#empresas').change(function () {
        var empresa = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#campanias").empty();
        $("#segmentos").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/reporte-premios/getDataEmpresa',
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

    $('#campanias').change(function () {
        var campania = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#segmentos").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/reporte-premios/getDataSegmentos',
            data: {id: campania, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#segmentos").select2({
                        language: 'es',
                        data: data.segmentos,
                        placeholder: "Todas",
                        allowClear: true
                    }).val("").trigger("change")
                } else {
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
});