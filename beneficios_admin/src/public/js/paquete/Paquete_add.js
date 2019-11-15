/**
 * Created by marlo on 02/09/15.
 */
document.write("<script type='text/javascript' src='../../js/validations.js'></script>");

$('#tipopaq').change(function () {
    var val = $('#tipopaq').val();
    var des = $('.des');
    var pre = $('.pre');
    var lead = $('.lead');
    if (val == 2) {
        des.addClass('hidden');
        pre.removeClass('hidden');
        lead.removeClass('hidden');
        des.val(null);
    }
    else if (val == 3) {
        pre.addClass('hidden');
        des.addClass('hidden');
        lead.addClass('hidden');
        pre.val(null);
        des.val(null);
        lead.val(null);
    }
    else {
        pre.addClass('hidden');
        des.removeClass('hidden');
        lead.removeClass('hidden');
        pre.val(null);
    }
});

$(function () {
    var ctrlDown = false;
    var ctrlKey = 17, vKey = 86, cKey = 67, xKey = 88;

    $(document).keydown(function (e) {
        if (e.keyCode == ctrlKey) ctrlDown = true;
    }).keyup(function (e) {
        if (e.keyCode == ctrlKey) ctrlDown = false;
    });

    var pre = $("input[name=Precio]");
    var predes = $("input[name=PrecioUnitarioDescarga]");
    var prebon = $("input[name=PrecioUnitarioBonificacion]");
    var predia = $("input[name=CostoDia]");

    pre.numeric({decimalPlaces: 2});
    predes.numeric({decimalPlaces: 2});
    prebon.numeric({decimalPlaces: 2});
    predia.numeric({decimalPlaces: 2});

    var bon = $("input[name=Bonificacion]");
    var ndias = $("input[name=NumeroDias]");
    var cdes = $("input[name=CantidadDescargas]");


    ndias.on("keydown", function (event) {
        if (ctrlDown && (event.keyCode == vKey || event.keyCode == cKey || event.keyCode == xKey)) {
            return true;
        } else {
            onlyNumbers(event);
        }
    });

    ndias.on("keyup", function () {
        maxInput($(this), 4);
        copyPage($(this));
    });

    cdes.on("keydown", function (event) {
        if (ctrlDown && (event.keyCode == vKey || event.keyCode == cKey || event.keyCode == xKey)) {
            return true;
        } else {
            onlyNumbers(event);
        }
    });

    cdes.on("keyup", function () {
        maxInput($(this), 4);
        copyPage($(this));
    });

    bon.on("keydown", function (event) {
        if (ctrlDown && (event.keyCode == vKey || event.keyCode == cKey || event.keyCode == xKey)) {
            return true;
        } else {
            onlyNumbers(event);
        }
    });

    bon.on("keyup", function () {
        maxInput($(this), 11);
        copyPage($(this));
    });

    pre.on("keyup", function () {
        maxInput($(this), 11);
        copyPage($(this));
    });
    predes.on("keyup", function () {
        maxInput($(this), 11);
        copyPage($(this));
    });
    prebon.on("keyup", function () {
        maxInput($(this), 11);
        copyPage($(this));
    });
    predia.on("keyup", function () {
        maxInput($(this), 11);
        copyPage($(this));
    });
});
