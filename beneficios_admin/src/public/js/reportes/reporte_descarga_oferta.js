/**
 * Created by marlo on 20/07/16.
 */
$(function () {
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        startDate: '2016-01-01'
    });
    $(".select2").select2();

    $('input[name="FechaFin2"]').on('change', function () {
        var f1 = new Date($(this).datepicker('getDate'));
        var f3 = new Date($('input[name="FechaInicio2"]').datepicker('getDate'));
        if (f1 < f3 && $(this).val() != '') {
            alert('La fecha hasta debe de ser mayor a la de Inicio');
            $(this).val('');
        }
    });

    $('input[name="FechaInicio2"]').on('change', function () {
        var f1 = new Date($(this).datepicker('getDate'));
        var f3 = new Date($('input[name="FechaFin2"]').datepicker('getDate'));
        if (f3 < f1 && $(this).val() != '') {
            alert('La fecha Inicio debe de ser menor a la de Fin');
            $(this).val('');
        }
    });
});