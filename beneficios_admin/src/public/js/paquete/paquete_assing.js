/**
 * Created by marlo on 22/09/15.
 */
document.write("<script type='text/javascript' src='../../js/validations.js'></script>");

$(function () {
    var ctrlDown = false;
    var ctrlKey = 17, vKey = 86, cKey = 67, xKey = 88;

    $(document).keydown(function (e) {
        if (e.keyCode == ctrlKey) ctrlDown = true;
    }).keyup(function (e) {
        if (e.keyCode == ctrlKey) ctrlDown = false;
    });
    //Initialize Select2 Elements
    $(".select2").select2();
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd'
    });

    var mdias = $("input[name=MaximoLeads]");
    var cos = $("input[name=CostoPorLead]");

    cos.numeric({decimalPlaces: 2});

    mdias.on("keydown", function (event) {
        if (ctrlDown && (event.keyCode == vKey || event.keyCode == cKey || event.keyCode == xKey)) {
            return true;
        } else {
            onlyNumbers(event);
        }
    });

    /*mdias.on("keyup", function () {
        maxInput($(this), 4);
        copyPage($(this));
    });*/

    cos.on("keyup", function () {
        maxInput($(this), 11);
        copyPage($(this));
    });

});
