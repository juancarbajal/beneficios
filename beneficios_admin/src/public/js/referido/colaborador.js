$(document).ready(function () {
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    $('.modal-colaborador').click(function () {
        var colaborador = $(this).closest('tr').data('id');
        var csrf = $('input:hidden[name=csrf]').val();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/cliente-landing/getReferidos',
            data: {id: colaborador, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $('.lista-referidos').empty();
                    $.each(data.referido, function (index, value) {
                        var contenido = $('<ul>');
                        contenido.append('<li><label>Nombres y Apellidos: </label>' + value.Nombres_Apellidos + '</li>');
                        contenido.append('<li><label>Telefonos: </label>' + value.Telefonos + '</li>');
                        $('.lista-referidos').append(contenido);
                    });
                }
            }
        });
    });
});