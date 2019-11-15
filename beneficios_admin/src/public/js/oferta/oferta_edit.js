var valorAnt;
$(function () {
    bPreguntar = true;
    $('#copyButton').hide();

    $('#empresa-prov').change(function () {
        dato_descarga = 0;
        dato_presencia = 0;
        dato_lead = 0;
        anterior_descarga = true;
        anterior_presencia = true;
        anterior_lead = true;
    });
});

$(document).on('keydown', '.atributos', function (e) {
    if(e.which == 13) {
        return false;
    }
});

$(document).on('change', '.atributos', function (e) {
    var element = $(this);
    var value = element.val();
    if(value.indexOf('\n') > -1) {
        element.val('');
        alert('no se acepta saltos de linea en el campo');
    }
});

$(document).on('change', '.dato_beneficio', function (e) {
    var tipo = $('#BNF_TipoBeneficio_id').val();
    if(tipo == 1 || tipo == 2) {
        if(!/^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test($(this).val())){
            $(this).val('');
            alert('En Tipo de Beneficio porcentual o efectivo, solo se aceptan números.')
        }
    }
});

$(document).on('change', '#DatoBeneficio', function (e) {
    var tipo = $('#BNF_TipoBeneficio_id').val();
    if(tipo == 1 || tipo == 2) {
        if(!/^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test($(this).val())){
            $(this).val('');
            alert('En Tipo de Beneficio porcentual o efectivo, solo se aceptan números.')
        }
    }
});

$(document).on('change','#tipoOferta', function () {
    var aproved = false;
    var stock_1 = 0;
    var stock_2 = 0;
    var stock_base = 0;

    if(tipoEspecial == 1) {
        $('.stocks').prop('readonly', true);
        $('.addfields').prop('disabled', true);
        $('.delfields').prop('disabled', true);
        $('#stock').prop('readonly', true);
        $('#tipo').prop('disabled', true);
        $(this).prop('disabled', true);
    } else {

        var valor = $("#tipo").find("option:selected").text();
        $("input.stocks").each(function (index, value) {
            stock_base = stock_base + Number($(value).val());
        });

        if ($(this).is(":checked")) {
            if (check_ant == false) {
                stock_2 = -1 * stock_base;
                stock_1 += Number($('#stock').val());
                aproved = true;
                $('#stock').val('');
            }
            check_ant = true;
        } else {
            if (check_ant == true) {
                stock_1 = stock_base;
                stock_2 -= Number($('#stock').val());
                aproved = true;
                $('.stocks').val('');
            }
            check_ant = false;
        }

        if (aproved) {
            var resultado = 0;
            if (valor == "Descarga") {
                var descargas = $('input[name="Descarga"]');
                dato_descarga = parseInt(descargas.val());
                resultado = (dato_descarga + stock_1) + stock_2;
                if (resultado >= 0) {
                    descargas.val(resultado);
                    dato_descarga = resultado;
                } else {
                    alert('La cantidad de stock ingresada supera a la de la bolsa')
                    $(this).val('');
                }
            } else if (valor == "Presencia") {
                var presencia = $('input[name="Presencia"]');
                dato_presencia = parseInt(presencia.val());
                resultado = (dato_presencia + stock_1) + stock_2;
                if (resultado >= 0) {
                    presencia.val(resultado);
                } else {
                    alert('La cantidad de stock ingresada supera a la de la bolsa')
                    $(this).val('');
                }
            } else if (valor == "Lead") {
                var lead = $('input[name="Lead"]');
                dato_lead = parseInt(lead.val());
                resultado = (dato_lead + stock_1) + stock_2;
                if (resultado >= 0) {
                    lead.val(resultado);
                } else {
                    alert('La cantidad de stock ingresada supera a la de la bolsa')
                    $(this).val('');
                }
            }
        }
    }
});

$(document).on('change','#tipo', function () {
    var aproved = false;
    var stock_1 = 0;
    var stock_base = 0;

    var valor = $(this).find("option:selected").text();
    $("input.stocks").each(function (index, value) {
        stock_base = stock_base + Number($(value).val());
    });

    if(valorAnt != undefined && valor != valorAnt) {
        if ($('#tipoOferta').is(":checked")) {
            stock_1 = stock_base;
            aproved = true;
            $('.stocks').val('');
        } else {
            stock_1 += Number($('#stock').val());
            aproved = true;
            $('#stock').val('');
        }
    }

    if(aproved) {
        var resultado = 0;
        if (valorAnt == "Descarga") {
            var descargas = $('input[name="Descarga"]');
            dato_descarga = parseInt(descargas.val());
            resultado = (dato_descarga + stock_1);
            if (resultado >= 0) {
                descargas.val(resultado);
                dato_descarga = resultado;
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa')
                $(this).val('');
            }
        } else if (valorAnt == "Presencia") {
            var presencia = $('input[name="Presencia"]');
            dato_presencia = parseInt(presencia.val());
            resultado = (dato_presencia + stock_1);
            if (resultado >= 0) {
                presencia.val(resultado);
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa')
                $(this).val('');
            }
        } else if (valorAnt == "Lead") {
            var lead = $('input[name="Lead"]');
            dato_lead = parseInt(lead.val());
            resultado = (dato_lead + stock_1);
            if (resultado >= 0) {
                lead.val(resultado);
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa')
                $(this).val('');
            }
        }
    }
    valorAnt = valor;
});