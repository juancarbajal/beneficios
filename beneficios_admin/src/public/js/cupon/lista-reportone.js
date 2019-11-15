$(function () {
    $(".select2").select2({
        language: 'es',
        placeholder: "Seleccione...",
        allowClear: true
    });

    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    $(".enviar").prop("disabled", true);
});

$(document).ready(function () {
    var por_pagar = $('.por_pagar_check');
    var pagados = $('.pagado_check');

    $('#allCheck').change(function () {
        if ($(this).is(":checked")) {
            if (por_pagar.length > 0) {
                por_pagar.each(function () {
                    $(this).prop("checked", true);
                });
            } else if (pagados.length > 0) {
                pagados.each(function () {
                    $(this).prop("checked", true);
                });
            }
            $(".enviar").prop("disabled", false);
        } else {
            if (por_pagar.length > 0) {
                por_pagar.each(function () {
                    $(this).prop("checked", false);
                });
            } else if (pagados.length > 0) {
                pagados.each(function () {
                    $(this).prop("checked", false);
                });
            }
            $(".enviar").prop("disabled", true);
        }
    });

    por_pagar.click(function () {
        if ($(this).is(":checked")) {
            $(this).prop("checked", true);
            $(".enviar").prop("disabled", false);
        } else {
            var count = 0;

            $(this).prop("checked", false);
            por_pagar.each(function () {
                if ($(this).is(":checked")) {
                    count++;
                }
            });

            if (count == 0) {
                $(".enviar").prop("disabled", true);
            } else {
                $(".enviar").prop("disabled", false);
            }
        }
    });

    pagados.click(function () {
        if ($(this).is(":checked")) {
            $(this).prop("checked", true);
            $(".enviar").prop("disabled", false);
        } else {
            var count = 0;

            $(this).prop("checked", false);
            pagados.each(function () {
                if ($(this).is(":checked")) {
                    count++;
                }
            });

            if (count == 0) {
                $(".enviar").prop("disabled", true);
            } else {
                $(".enviar").prop("disabled", false);
            }
        }
    });

    $('#empresa-prov').change(function () {
        var empresa = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#campanias").empty();
        $("#ofertas").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/cupon-puntos/getDataEmpresa',
            data: {id: empresa, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#campanias").select2({
                        language: 'es',
                        data: data.campanias,
                        placeholder: "Seleccione...",
                        allowClear: true
                    }).val("").trigger("change");
                } else {
                    $("#campanias").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione...",
                        allowClear: true
                    });
                }
            }
        });
    });

    $('#campanias').change(function () {
        var campania = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#ofertas").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/cupon-puntos/getDataCampania',
            data: {id: campania, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#ofertas").select2({
                        language: 'es',
                        data: data.ofertas,
                        placeholder: "Seleccione...",
                        allowClear: true
                    }).val("").trigger("change");
                } else {
                    $("#ofertas").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione...",
                        allowClear: true
                    });
                }
            }
        });
    });

    $(".enviar").click(function () {
        var form = $(this).closest('form');
        var postData = form.serializeArray();
        var formURL = form.attr("action");
        var confirmar = confirm("¿Esta usted seguro de realizar este cambio?");
        if (confirmar) {
            $.ajax({
                url: formURL,
                type: "POST",
                data: postData,
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        alert("Operación completada");
                        window.location = json.direccion;
                        return false;
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("Status: " + textStatus);
                    console.log("Error: " + errorThrown);
                }
            });
        }
    });
});