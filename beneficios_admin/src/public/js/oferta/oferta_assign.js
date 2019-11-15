$(function () {

    $(".select2").select2();

    $('.assign_special').click(function () {
        var idOferta = $("select[name=Oferta] option:selected").val();
        var row = $(this).parents('tr');
        var idVal = row.data('id');

        $.post("/oferta/loademp", {
            id: idVal,
            ofert: idOferta
        }, function (data) {
            if (data.response == true) {
                console.log(data.message);
                console.log(data.active);

                $('#idempresa').val(data.empresa['id']);
                $('#empresa').val(data.empresa['nombre']);
                $('#tipo').val(data.empresa['tipo']);

                var sub = $("#subgrupos");

                sub.empty();
                $.each(data.subgrupos, function (index, value) {
                    var dato = false;
                    $.each(data.active, function (indexa, valuea) {
                        if (index == valuea) {
                            sub.append('<div class="col-md-5"><input class="checkbox-inline" value="' +
                                index + '" type="checkbox" checked="checked">' + value + '</div>');
                            dato = true;
                        }
                    });
                    if (dato == false) {
                        sub.append('<div class="col-md-5"><input class="checkbox-inline" value="' +
                            index + '" type="checkbox">' + value + '</div>');
                    }
                });

                if (idOferta == '') {
                    $('#enviar').attr('disabled', 'disabled').hide();
                } else {
                    $('#enviar').removeAttr('disabled').show();
                }

                $("#btn_modal").trigger("click");
            } else {
                console.log(data.message);
            }
        }, 'json');
    });

    $('.select_normal label input').on('change', function (e) {
        var tabla = $('#EmpNorm').find('tbody tr');
        var idVal = $('input[name=normal]:checked').attr("id");
        var idOferta = $("select[name=Oferta] option:selected").val();

        if (idOferta != "") {
            var mensaje = $.trim($("label[for='" + idVal + "']").text());
            var r = confirm('¿Desea Aplicar Esta Acción: "' + mensaje + ' LAS EMPRESAS NORMALES"?');

            if (r == true) {
                var opt = $('input[name=normal]:checked', '.select_normal').val();

                var empresas = [];
                tabla.each(function () {
                    empresas.push($(this).attr("data-id"));
                });

                $.post("/oferta/assignnormaltodos", {
                    id: idOferta,
                    opt: opt,
                    emp: empresas
                }, function (data) {
                    if (data.response == true) {
                        console.log(data.message);
                        var resultado = data.values;
                        opt = opt.substring(0, 4);
                        var fila = 4;
                        loadAssigns(tabla, resultado, fila, opt);
                    } else {
                        console.log(data.message);
                    }
                }, 'json');
                e.preventDefault();
            } else {
                $(this).removeAttr("checked");
            }
        } else {
            $(this).removeAttr("checked");
            alert("Debe seleccionar una Oferta.");
        }
    });

    $('.select_especial label input').on('change', function () {
        var tabla = $('#EmpEsp').find('tbody  tr');
        var idVal = $('input[name=especial]:checked').attr("id");
        var idOferta = $("select[name=Oferta] option:selected").val();

        if (idOferta != "") {
            var mensaje = $.trim($("label[for='" + idVal + "']").text());
            var r = confirm('¿Desea Aplicar Esta Acción: "' + mensaje + ' LAS EMPRESAS ESPECIALES"?');
            if (r == true) {
                var opt = $('input[name=especial]:checked', '.select_especial').val();

                var empresas = [];
                tabla.each(function () {
                    empresas.push($(this).attr("data-id"));
                });

                $.post("/oferta/assignespecialtotal", {
                    id: idOferta,
                    opt: opt,
                    emp: empresas
                }, function (data) {
                    if (data.response == true) {
                        console.log(data.message);
                        var resultado = data.values;
                        opt = opt.substring(0, 4);
                        var fila = 5;
                        loadAssigns(tabla, resultado, fila, opt);
                    } else {
                        console.log(data.message);
                    }
                }, 'json');
            } else {
                $(this).removeAttr("checked");
            }
        } else {
            $(this).removeAttr("checked");
            alert("Debe seleccionar una Oferta.");
        }
    });

    $('.assignNO').on('click', function () {
        var row = $(this).parents('tr');
        var idVal = [row.data('id')];
        var opt = "";
        var tabla = $('#EmpNorm').find('tbody tr');

        var idOferta = $("select[name=Oferta] option:selected").val();

        if (idOferta != "") {
            if(! $(this).is(':checked')) {
                var r = confirm('¿Desea Cambiar el estado de la Asignación?');
                if (r == true) {
                    if ($(this).attr('checked')) {
                        opt = "quitN";
                        $.post("/oferta/deletenormal", {
                            id: idOferta,
                            opt: opt,
                            emp: idVal
                        }, function (data) {
                            if (data.response == true) {
                                console.log(data.message);
                                var resultado = data.values;
                                opt = opt.substring(0, 4);
                                var fila = 4;
                                loadAssigns(tabla, resultado, fila, opt);
                            } else {
                                console.log(data.message);
                            }
                        }, 'json');

                        $(this).removeAttr("checked");
                        $(this).prop("checked", false);
                    }
                } else {
                    if ($(this).attr('checked')) {
                        $(this).attr("checked", "checked");
                        $(this).prop("checked", true);
                    } else {
                        $(this).removeAttr("checked");
                        $(this).prop("checked", false);
                    }
                }
            }
        } else {
            $(this).removeAttr("checked");
            $(this).prop("checked", false);
            alert("Debe seleccionar una Oferta.");
        }
    });

    $('.assignEO').on('click', function () {
        var row = $(this).parents('tr');
        var idVal = [row.data('id')];
        var opt = "";
        var tabla = $('#EmpEsp').find('tbody tr');

        var idOferta = $("select[name=Oferta] option:selected").val();

        if (idOferta != "") {
            if(! $(this).is(':checked')) {
                var r = confirm('¿Desea Cambiar el estado de la Asignación?');
                if (r == true) {
                    if ($(this).attr('checked')) {
                        opt = "quitE";
                        $.post("/oferta/deleteespecial", {
                            id: idOferta,
                            opt: opt,
                            emp: idVal
                        }, function (data) {
                            if (data.response == true) {
                                console.log(data.message);
                                var resultado = data.values;
                                opt = opt.substring(0, 4);
                                var fila = 5;
                                loadAssigns(tabla, resultado, fila, opt);
                            } else {
                                console.log(data.message);
                            }
                        }, 'json');

                        $(this).removeAttr("checked");
                        $(this).prop("checked", false);
                    }
                } else {
                    if ($(this).attr('checked')) {
                        $(this).attr("checked", "checked");
                        $(this).prop("checked", true);
                    } else {
                        $(this).removeAttr("checked");
                        $(this).prop("checked", false);
                    }
                }
            }
        } else {
            $(this).removeAttr("checked");
            $(this).prop("checked", false);
            alert("Debe seleccionar una Oferta.");
        }
    });

    $('.assignSub').on('click', function () {
        var tabla = $('#EmpEsp').find('tbody tr');
        var div = $('#subgrupos').find('input');
        var idOferta = $("select[name=Oferta] option:selected").val();
        var idVal = $('#idempresa').val();
        var subgruos = [];
        var opt = "";
        var count = 0;

        div.each(function () {
            if ($(this).prop("checked")) {
                subgruos[$(this).val()] = 0;
                count = count + 1;
            } else {
                subgruos[$(this).val()] = 1;
            }
        });

        if (count > 0) {
            if (! $(this).is(':checked')) {
                var r = confirm('¿Desea Cambiar el estado de la Asignación?');
                if (r == true) {
                    opt = "asigE";
                    $.post("/oferta/assignespecialsub", {
                        id: idOferta,
                        emp: idVal,
                        sub: subgruos
                    }, function (data) {
                        if (data.response == true) {
                            console.log(data.message);
                            var resultado = data.values;
                            opt = opt.substring(0, 4);
                            var fila = 5;
                            loadAssigns(tabla, resultado, fila, opt);
                            $("#btn_close_modal").trigger("click");
                        } else {
                            console.log(data.message);
                        }
                    }, 'json');
                }
            } else {
                alert("Debe Elegir por lo menos un Subgrupo.");
            }
        }
    });

    $('#oferta').change(function () {
        $('.select_normal label input[name="normal"]').removeAttr('checked');
        $('.select_especial label input[name="especial"]').removeAttr('checked');

        var tabla = $('#EmpNorm').find('tbody  tr');
        cleanAssigns(tabla, 4);
        var tabla2 = $('#EmpEsp').find('tbody  tr');
        cleanAssigns(tabla2, 5);
        var idOferta = $('select[name="Oferta"] option:selected').val();
        $('input[name="oferta"]').val(idOferta);
        $.post("/oferta/loadassign", {
            id: idOferta
        }, function (data) {
            if (data.response == true) {
                console.log(data.message);
                var opt = "load";
                var resultado = data.normal;
                var fila = 4;
                loadAssigns(tabla, resultado, fila, opt);
                var resultado2 = data.especial;
                var fila2 = 5;
                loadAssigns(tabla2, resultado2, fila2, opt);
            } else {
                console.log(data.message);
            }
        }, 'json');
    });
});

function loadAssigns(tabla, resultados, fila, opt) {
    tabla.each(function () {
        var id = $(this).attr("data-id");
        var row = $(this);
        $.each(resultados, function (index, value) {
            if (value == id) {
                row.find("td").each(function (index2) {
                    if (index2 == fila) {
                        if (opt === "quit") {
                            $(this).find("input").removeAttr("checked");
                            $(this).find("input").prop("checked", false);
                        } else {
                            $(this).find("input").attr("checked", "checked");
                            $(this).find("input").prop("checked", true);
                        }
                    }
                });
            }
        });
    });
}

function cleanAssigns(tabla, fila) {
    tabla.each(function () {
        var id = $(this).attr("data-id");
        var row = $(this);
        row.find("td").each(function (index2) {
            if (index2 == fila) {
                $(this).find("input").removeAttr("checked");
                $(this).find("input").prop("checked", false);
            }
        });
    });
}

function verifyData(tabla, fila) {
    var count = 0;
    tabla.each(function () {
        var id = $(this).attr("data-id");
        var row = $(this);
        row.find("td").each(function (index2) {
            if (index2 == fila) {
                if ($(this).find("input").is(':checked')) {
                    count = count + 1;
                }
            }
        });
    });

    if (count <= 0) {
        alert("No hay empresas seleccionadas.");
        return false;
    } else {
        return true;
    }
}
