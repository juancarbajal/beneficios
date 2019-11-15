$(document).ready(function () {
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    $('.modal-referido').click(function () {
        var referido = $(this).closest('tr').data('id');
        var csrf = $('input:hidden[name=csrf]').val();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/referido/getReferidoPor',
            data: {id: referido, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $('#referido-documento').html(data.referido.Documento);
                    $('#referido-nombre').html(data.referido.Nombres_Apellidos);
                    $('#referido-email').html(data.referido.Email);
                    $('#referido-telefono').html(data.referido.Telefonos);
                    $('#referido-especialista').html(data.referido.Especialista);
                }
            }
        });
    });
});