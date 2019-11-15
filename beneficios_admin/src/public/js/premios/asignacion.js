$(document).ready(function () {
    $('#empresa-cli').change(function () {
        var empresa = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#campanias").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/asignaciones-premios/getDataEmpresa',
            data: {id: empresa, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#campanias").select2({
                        language: 'es',
                        data: data.campanias
                    })
                } else {
                    $("#campanias").select2({
                        language: 'es',
                        data: []
                    });
                }
            }
        });
    });
});

$(function () {
    $(".select2").select2({
        language: 'es'
    });
});
