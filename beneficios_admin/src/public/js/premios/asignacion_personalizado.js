/**
 * Created by luisvar on 12/07/16.
 */
$(document).on('click', '.add-content', function () {
    agregarCampos();
});

$(document).on('click', '.delete-content', function () {
    $(this).closest('div.form-group').remove();
});

$(document).on('keyup', '.premios-control', function () {
    var presupuesto = $('input:hidden[name="presupuesto"]').val();

    var premios = 0;
    $("input.premios-control").each(function (index, value) {
        var valor = isNaN(Number($(value).val())) ? 0 : Number($(value).val());
        premios = premios + valor;
    });

    var disponible = presupuesto - premios;

    $('#total-asignado').html(premios);
    $('#total-disponible').html(disponible);
});

$(document).on('change', '.estado_asignado', function () {
    var count = 0;
    $('.data-content input:checked').each(function () {
        count++;
    });

    if (count != 0) {
        $('#error-found').html('');
    }
});

$(document).ready(function () {
    $('#submitButton').prop("disabled", true).hide();
    $('#content-premios').hide();
    $('#asignacion-seccion').hide();

    $('#asignar-button').on('click', function () {
        $('#content-premios').show();
        $('#submitButton').prop("disabled", false).show();
        $('#delete-button').hide();
        $('#asignar-button-archivo').remove();
        $('input:hidden[name=action]').val('input');
        $(this).hide();
    });

    $('#asignar-button-archivo').on('click', function () {
        $('#asignacion-seccion').show();
        $('#submitButton').prop("disabled", false).show();
        $('#delete-button').hide();
        $('#asignar-button').remove();
        $('input:hidden[name=action]').val('file');
        $(this).hide();
    });

    $('#delete-button').click(function () {
        var id = $('input:hidden[name="idSegmento"]').val();
        var csrf = $('input:hidden[name="csrf"]').val();

        var selected = {};
        $('.data-content input:checked').each(function () {
            var position = $(this).attr('name').replace('Estado[', '').replace(']', '');
            selected[position] = $(this).val();
        });


        if (!jQuery.isEmptyObject(selected)) {
            $('#load-image').show();
            $('#submitButton').prop("disabled", true);
            $(this).prop("disabled", true);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '/asignaciones-premios/deletePersonalizado',
                data: {id: id, csrf: csrf, estado: selected},
                success: function (data) {
                    $('input:hidden[name=csrf]').val(data.csrf);
                    if (data.response) {
                        window.location.replace(data.url);
                    }
                },
                error: function () {
                    console.log('error');
                }
            });
        } else {
            $('#error-found').html('No hay asignaciones seleccionadas.');
        }
    });
});

function agregarCampos() {
    var count = Math.round(Math.random() * 100000);
    var contenido = '<div class="form-group">' +
        '<div class="col-md-2"><input type="text" name="numeroDocumento[' + count + ']" class="documento-control" title="documento">' +
        '<div class="numeroDocumento error"></div></div>' +
        '<div class="col-md-2"><div style="text-align: center"><label class="asignados-control"></label></div></div>' +
        '<div class="col-md-2"><input type="text" name="sumaPremios[' + count + ']" class="sumaPremios" title="suma">' +
        '<div class="sumaPremios error"></div></div>' +
        '<div class="col-md-2"><input type="text" name="restaPremios[' + count + ']" class="restaPremios" title="resta">' +
        '<div class="restaPremios error"></div></div>' +
        '<div class="col-md-2">' +
        '<button class="btn btn-default add-content" type="button">&#43;</button>' +
        '<button class="btn btn-default delete-content" type="button">&#45;</button>' +
        '</div></div>';
    $('#data-content').append(contenido);
}
