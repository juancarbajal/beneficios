/**
 * Created by luisvar on 23/11/15.
 */
$(function () {

    $(".textarea").wysihtml5();
    $(".select2").select2();
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });

});
