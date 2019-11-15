/**
 * Created by luisvar on 25/01/16.
 */
$(function () {
    ////////////////////////////////////////
    $(".textarea").wysihtml5();
    $(".select2").select2();
    ////////////////////////////////////////
    $('#ofertas').change(function () {
        var id = $('select[name="Oferta"] option:selected').val();

        $.ajax({
                url: "/registrar-lead/getData",
                type: "POST",
                data: {id: id},
                dataType: "json"
            })
            .done(function (data, textStatus, jqXHR) {
                if (console && console.log) {
                    if (data.response == true) {
                        $('#Condiciones').closest('div').find('iframe').contents()
                            .find("body").html(data.data["Contenido"]);

                        if (data.data["Estado"] == "1") {
                            $('input[name=CondicionesEstado]').val(data.data["Estado"]);
                            $('#CondicionesEstado').attr("checked", "checked").prop("checked", true);
                        } else {
                            $('input[name=CondicionesEstado]').val(data.data["Estado"]);
                            $('#CondicionesEstado').removeAttr("checked").prop("checked", false);
                        }

                        $('#CondicionesTexto').val(data.data["Texto"]);

                        $('div.container-lead').empty();

                        $.each(data.form, function (index, value) {
                            createblocksrecover(index, value);
                        });

                        createblocksreset();
                    } else {
                        console.log(data.data);
                    }
                    console.log("La solicitud se ha completado correctamente.");
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                if (console && console.log) {
                    console.log("La solicitud a fallado: " + textStatus);
                }
            });
    });

    $('#CondicionesEstado').change(function () {
        if ($(this).is(':checked')) {
            $('input[name=CondicionesEstado]').val("1");
        } else {
            $('input[name=CondicionesEstado]').val("0");
        }
    });

    $(".add-block").click(function () {
        createblocks();
    });

    $('.details').change(function () {
        disableinput(this);
    });

    $('#registrar-oferta').submit(function (e) {
        var count = 0;
        var countdetail = 0;
        $(".name-control").each(function () {
            if ($(this).val() === "") {
                $(this).closest('div').addClass('has-error').find('ul').show();
                $(this).addClass("input-error");
                count = count + 1;
            } else {
                $(this).closest('div').removeClass('has-error').find('ul').hide();
                $(this).removeClass("input-error");
            }
        });

        $(".detail-control").each(function () {
            if ($(this).val() === "" && !$(this).prop("disabled")) {
                $(this).closest('div').addClass('has-error').find('ul').show();
                $(this).addClass("input-error");
                countdetail = countdetail + 1;
            } else {
                $(this).closest('div').removeClass('has-error').find('ul').hide();
                $(this).removeClass("input-error");
            }
        });

        if (count !== 0 || countdetail !== 0) e.preventDefault();
    });
});

function disableinput(e) {
    var valor = parseInt(e.value);
    if (valor === 1) {
        $(e).closest('div.children').find('div.detail-container').find(':input').attr('disabled', true);
    } else {
        $(e).closest('div.children').find('div.detail-container').find(':input').removeAttr('disabled');
    }
}

function removediv(e) {
    var r = confirm("¿Esta usted seguro de eliminar este campo?");
    if (r == true) {
        $(e).closest('div.children').remove();
    }
}

function removedivrecovered(e) {
    var r = confirm("¿Esta usted seguro de eliminar este campo?");
    if (r == true) {
        var content = $(e).closest('div.children');
        var hval = parseInt(content.find('input:hidden').val());
        if (hval !== 0) {
            $.ajax({
                    url: "/registrar-lead/deleteData",
                    type: "POST",
                    data: {id: hval},
                    dataType: "json"
                })
                .done(function (data, textStatus, jqXHR) {
                    if (console && console.log) {
                        if (data.response == true) {
                            content.remove();
                        } else {
                            console.log("no se encontro el campo");
                        }
                        console.log("La solicitud se ha completado correctamente.");
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    if (console && console.log) {
                        console.log("La solicitud a fallado: " + textStatus);
                    }
                });
        }
    }
}

function createblocks() {
    var id = Math.round(Math.random() * 1000);

    var requerido = '<div class="col-md-4">' +
        '<div class="col-md-6">' +
        '<label>' +
        '<input type="checkbox" name="obligatorio[' + id + ']">Obligatorio' +
        '</label>' +
        '</div>' +
        '<div class="col-md-6">' +
        '<label>' +
        '<input type="checkbox" name="activo[' + id + ']" checked="checked">Activo' +
        '</label>' +
        '</div>' +
        '</div>';

    var tipo = '<div class="form-group">' +
        '<div class="col-md-1"><label>Tipo:</label></div>' +
        '<div class="col-md-4">' +
        '<div class="col-md-5">' +
        '<label><input type="radio" class="details" onchange="disableinput(this);" ' +
        'checked="checked" name="tipo[' + id + ']" value="0">Combo</label>' +
        '</div>' +
        '<div class="col-md-5">' +
        '<label><input type="radio" class="details" onchange="disableinput(this);" ' +
        'name="tipo[' + id + ']" value="1">Texto</label>' +
        '</div>' +
        '</div>' +
        requerido +
        '</div>';

    var nombre = '<div class="col-md-1"><label>Nombre:</label></div>' +
        '<div class="col-md-3">' +
        '<label>' +
        '<input type="text" class="form-control name-control" name="nombre[' + id + ']">' +
        '</label>' +
        '<ul style="display:none;color:red;"> ' +
        '<li>El Nombre es requerido y no puede quedar vacío.</li>' +
        '</ul>' +
        '</div>';

    var detalle = '<div class="detail-container">' +
        '<div class="col-md-1"><label>Detalle:</label></div>' +
        '<div class="col-md-5">' +
        '<label>' +
        '<textarea rows="2" cols="25" class="form-control detail-control" name="detalle[' + id + ']"></textarea>' +
        '<span>Separar por “;” cada item.</span>' +
        '</label>' +
        '<ul style="display:none;color:red;">' +
        '<li>El detalle es necesario para el contenido del combobox, no puede quedar vacío.</li></ul>' +
        '</div>' +
        '</div>';

    var boton = '<div class="col-md-2">' +
        '<button type="button" class="btn btn-block btn-default" onclick="removediv(this);">' +
        '<i class="fa fa-minus"></i>' +
        '</button>' +
        '</div>';


    var contenido = '<div class="col-md-12 children"><hr>' +
        '<div class="form-group">'
        + tipo +
        '</div>' +
        '<br><br>' +
        '<div class="form-group">' +
        nombre + detalle + boton +
        '</div>' +
        '</div>';
    $('.container-lead').append(contenido);
}

function createblocksreset() {
    var boton = '<div class="col-md-2 col-lg-offset-10">' +
        '<button type="button" class="btn btn-block btn-default" onclick="createblocks()">' +
        '<i class="fa fa-plus"></i>' +
        '</button>' +
        '</div>';

    var contenido = '<div class="col-md-12 children">' + boton + '</div>';
    $('.container-lead').before(contenido);
}

function createblocksrecover(id, value) {

    var idvalue = '<input type="hidden" name="id[' + id + ']" value="' + id + '">';
    var tipo = "";

    var obligratoriocheck = (value["Requerido"] === "1") ? 'checked="checked" ' : '';

    var activocheck = (value["Activo"] === "1") ? 'checked="checked" ' : '';

    var requerido = '<div class="col-md-4">' +
        '<div class="col-md-6">' +
        '<label>' +
        '<input type="checkbox" ' + obligratoriocheck + ' name="obligatorio[' + id + ']">Obligatorio' +
        '</label>' +
        '</div>' +
        '<div class="col-md-6">' +
        '<label>' +
        '<input type="checkbox" ' + activocheck + ' name="activo[' + id + ']">Activo' +
        '</label>' +
        '</div>' +
        '</div>';

    if (value["Tipo_Campo"] == "1") {
        tipo = '<div class="form-group">' +
            '<div class="col-md-1"><label>Tipo:</label></div>' +
            '<div class="col-md-4">' +
            '<div class="col-md-5">' +
            '<label><input type="radio" class="details" onchange="disableinput(this);" ' +
            'name="tipo[' + id + ']" value="0">Combo</label>' +
            '</div>' +
            '<div class="col-md-5">' +
            '<label><input type="radio" class="details" onchange="disableinput(this);" ' +
            'checked="checked" name="tipo[' + id + ']" value="1">Texto</label>' +
            '</div>' +
            '</div>' +
            requerido +
            '</div>';
    } else {
        tipo = '<div class="form-group">' +
            '<div class="col-md-1"><label>Tipo:</label></div>' +
            '<div class="col-md-4">' +
            '<div class="col-md-5">' +
            '<label><input type="radio" class="details" onchange="disableinput(this);" ' +
            'checked="checked" name="tipo[' + id + ']" value="0">Combo</label>' +
            '</div>' +
            '<div class="col-md-5">' +
            '<label><input type="radio" class="details" onchange="disableinput(this);" ' +
            'name="tipo[' + id + ']" value="1">Texto</label>' +
            '</div>' +
            '</div>' +
            requerido +
            '</div>';
    }

    var nombrevalue = (value["Nombre_Campo"] !== "") ? 'value="' + value["Nombre_Campo"] + '"' : '';

    var nombre = '<div class="col-md-1"><label>Nombre:</label></div>' +
        '<div class="col-md-3">' +
        '<label>' +
        '<input type="text" ' + nombrevalue + ' class="form-control name-control" name="nombre[' + id + ']">' +
        '</label>' +
        '<ul style="display:none;color:red;"> ' +
        '<li>El Nombre es requerido y no puede quedar vacío.</li>' +
        '</ul>' +
        '</div>';

    var detallevalue = (value["Detalle"] !== null) ? value["Detalle"] : '';
    var detalledis = (value["Tipo_Campo"] == "1") ? 'disabled="disabled"' : '';

    var detalle = '<div class="detail-container">' +
        '<div class="col-md-1"><label>Detalle:</label></div>' +
        '<div class="col-md-5">' +
        '<label>' +
        '<textarea rows="2" cols="25" class="form-control detail-control" name="detalle[' + id + ']" ' + detalledis + '>' +
        detallevalue + '</textarea><span>Separar por “;” cada item.</span>' +
        '</label>' +
        '<ul style="display:none;color:red;">' +
        '<li>El detalle es necesario para el contenido del combobox, no puede quedar vacío.</li></ul>' +
        '</div>' +
        '</div>';

    var boton = '<div class="col-md-2">' +
        '<button type="button" class="btn btn-block btn-default" onclick="removedivrecovered(this);">' +
        '<i class="fa fa-minus"></i>' +
        '</button>' +
        '</div>';

    var contenido = '<div class="col-md-12 children"><hr>' +
        idvalue +
        '<div class="form-group">'
        + tipo +
        '</div>' +
        '<br><br>' +
        '<div class="form-group">' +
        nombre + detalle + boton +
        '</div>' +
        '</div>';

    $('.container-lead').append(contenido);
}