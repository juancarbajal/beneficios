/**
 * Created by luisvar on 17/06/16.
 */
$(function () {
    $(".select2").select2({
        language: 'es'
    });
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    $(".textarea").wysihtml5();
});
