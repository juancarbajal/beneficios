/**
 * Created by luisvar on 11/07/16.
 */
$(document).ready(function () {
    $('#submitButton').prop("disabled", true).hide();
    $('#asignacion-seccion').hide();

    $('#asignar-button').on('click', function () {
        $('#asignacion-seccion').show();
        $('#submitButton').prop("disabled", false).show();
        $('#delete-button').hide();
        $(this).hide();
    });
});


