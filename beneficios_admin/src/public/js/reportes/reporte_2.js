/**
 * Created by marlo on 06/01/16.
 */
$(function () {
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd'
    });
    $(".select2").select2();
});

$('#submitbutton').click(function (e) {
    var publicar = true;
    var costo = $('#costo').val();
    var meta = $('#meta').val();
    var menssage = 'Si no Ingresa Costo por Descarga y Meta en Dinero no se generara la segunda parte del reporte' +
        ' esta seguro de Seguir?\n';

    if (meta == '' && costo == '') {
        publicar = confirm(menssage);
    }

    if (publicar == true) {
        $('.form-reporte2').submit();
    } else {
        e.preventDefault();
    }
});