$(function () {
    $(".select2").select2({
        language: 'es',
        placeholder: "Seleccione...",
        allowClear: true
    });

    $('#empresas').change(function () {
        var empresa = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#campanias").empty();
        $("#ofertas").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/proveedor-ofertas-premios/getDataEmpresa',
            data: {id: empresa, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#campanias").select2({
                        language: 'es',
                        data: data.campanias,
                        placeholder: "Seleccione..."
                    }).trigger("change");

                    $("#ofertas").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione..."
                    });
                } else {
                    $("#campanias").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione..."
                    });

                    $("#ofertas").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione..."
                    });
                }
            }
        });
    });

    $('#campanias').change(function () {
        var campania = $(this).val();
        var empresa_id = $('#empresas').val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#ofertas").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/proveedor-ofertas-premios/getDataOfertas',
            data: {id: campania, empresa_id: empresa_id, csrf: csrf},
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#ofertas").select2({
                        language: 'es',
                        data: data.ofertas,
                        placeholder: "Seleccione..."
                    });
                } else {
                    $("#ofertas").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione..."
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