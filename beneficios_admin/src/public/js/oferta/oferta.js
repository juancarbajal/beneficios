var bPreguntar = false;
var imagenes = [];
var fechaAnterior;

var dato_descarga = 0;
var dato_presencia = 0;
var dato_lead = 0;
var anterior_descarga = true;
var anterior_presencia = true;
var anterior_lead = true;
var check_ant;
var stockAnt;
var eject = false;

$(window).on('beforeunload', function () {
    if (bPreguntar) {
        return "¿Seguro que quieres salir?";
    }
});

$(function () {
    $(".textarea").wysihtml5();
    $(".select2").select2();
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });
    $("a#single_image").fancybox();

    Inicio();

    //Genera los Tipos de Ofertas segun la bolsa de la Empresa
    $('#empresa-prov').change(function () {
        cargarBolsa('undefined', $(this).val());
    });

    //Obtenemos el valor seleccionado y establecemos un limite de ingreso en stock
    $('#tipo').on('change', null, function () {
        var valor = $("#tipo").find("option:selected").text();
        var fin = $("input[name=FechaFinPublicacion]");

        if (!$('#tipoOferta').is(':checked')) {
            $('#stock').attr('min', 0).trigger("change");
        } else {
            $('.stocks').trigger("change");
        }

        if (valor == "Descarga") {
            fin.removeAttr('disabled', 'disabled');
            $('#form-config').addClass('hidden');
            $('.vigencia').removeClass('hidden');
        } else if (valor == "Presencia") {
            fin.attr('disabled', 'disabled');
            calcularFecha($('#datepicker'));
            $('#form-config').addClass('hidden');
            $('.vigencia').removeClass('hidden');
        } else if (valor == "Lead") {
            fin.removeAttr('disabled', 'disabled');
            $('#form-config').removeClass('hidden');
            $('.vigencia').addClass('hidden');
        }
    });

    //Despliega todos los departamentos
    $('#pais').on('change', null, function (event, value) {
        var $pais = $(this);
        var $depa = $(".listdep");
        var $cate = $(".listcat");
        var $camp = $(".listcam");
        var get_val = $pais.val();

        $.post("/oferta/getdepa", {
            id: get_val
        }, function (data) {
            if (data.response == true) {

                var messagedep = $(".listdep ul");
                var chekadosdep = $(".listdep label input[name='Departamento[]']:checked");
                recargasChecks($depa, data.depa, messagedep, chekadosdep, "Departamento");

                var messagecat = $(".listcat ul");
                var chekadoscat = $(".listcat label input[name='Categoria[]']:checked");
                recargasChecks($cate, data.cate, messagecat, chekadoscat, "Categoria");

                var messagecam = $(".listcam ul");
                var chekadoscam = $(".listcam label input[name='Campania[]']:checked");
                recargasChecks($camp, data.camp, messagecam, chekadoscam, "Campania");
            } else {
                $depa.empty();
                $cate.empty();
                $camp.empty();
            }
        }, 'json');

    });

    $(document).on('change', '.vigencias', function () {
        var f1 = new Date($('input[name="FechaFinPublicacion"]').datepicker('getDate'));
        var f2 = new Date($(this).datepicker('getDate'));
        if (f1 > f2 && $(this).val() != '') {
            alert('La fecha seleccionada debe de ser mayor o igual a la de Fin de Publicacion');
            $(this).val('');
        }
        f1 = new Date($('input[name="FechaInicioPublicacion"]').datepicker('getDate'));
        if (f1 > f2 && $(this).val() != '') {
            alert('La fecha seleccionada debe de ser mayor a la de Inicio de Publicacion');
            $(this).val('');
        }
    });

    $('input[name="FechaFinVigencia"]').on('change', function () {
        var f1 = new Date($('input[name="FechaFinPublicacion"]').datepicker('getDate'));
        var f2 = new Date($(this).datepicker('getDate'));
        if (f1 > f2 && $(this).val() != '') {
            alert('La fecha seleccionada debe de ser mayor o igual a la de Fin de Publicacion');
            $(this).val('');
        }
    });

    $('input[name="FechaFinPublicacion"]').on('change', function () {
        var f1 = new Date($(this).datepicker('getDate'));
        var f3 = new Date($('input[name="FechaInicioPublicacion"]').datepicker('getDate'));

        if (f1 <= f3 && $(this).val() != '') {
            alert('La fecha fin de publicación debe de ser mayor a la de Inicio');
            $(this).val('');
        }

        if($('#tipoOferta').is(":checked")) {
            var error = false;
            $("input.vigencias").each(function (index, value) {
                var vigencia = $(value);
                var f2 = new Date(vigencia.val() + ' 00:00:00');
                if (f1 > f2 && vigencia.val() != '') {
                    error = true;
                    vigencia.val('');
                }
            });
            if(error) {
                alert('La fecha de Vigencia del Atributo(s) debe de ser mayor o igual a la de Fin de Publicacion');
            }
        } else {
            var vigencia = $('input[name="FechaFinVigencia"]');
            var f2 = new Date(vigencia.val() + ' 00:00:00');
            if (f1 > f2 && vigencia.val() != '') {
                alert('La fecha de Vigencia debe de ser mayor o igual a la de Fin de Publicacion');
                vigencia.val('');
            }
        }
    });

    //cahngeData se us para que no se triplique el evento por el datapicker
    $('input[name="FechaInicioPublicacion"]').on('changeDate', function () {
        calcularFecha($(this));
        var hasta = $('input[name="FechaFinPublicacion"]');
        var f1 = new Date($(this).datepicker('getDate'));
        var f2 = new Date(hasta.datepicker('getDate'));
        if (f1 >= f2 && $(this).val() != '') {
            alert('La fecha fin de publicación debe de ser mayor a la de Inicio');
            hasta.val('');
        }

        if($('#tipoOferta').is(":checked")) {
            var error = false;
            $("input.vigencias").each(function (index, value) {
                var vigencia = $(value);
                var f3 = new Date(vigencia.val() + ' 00:00:00');
                if (f1 >= f3 && vigencia.val() != '') {
                    error = true;
                    vigencia.val('');
                }
            });
            if(error) {
                alert('La fecha de Vigencia del Atributo(s) debe de ser mayor a la de Inicio de Publicacion');
            }
        } else {
            var vigencia = $('input[name="FechaFinVigencia"]');
            var f3 = new Date(vigencia.val() + ' 00:00:00');
            if (f1 >= f3 && vigencia.val() != '') {
                alert('La fecha de Vigencia debe de ser mayor a la de Inicio de Publicacion');
                vigencia.val('');
            }
        }
    });

    $('#submitButton').click(function (e) {
        $('input:hidden[name=action]').val('');
        var publicar = true;
        var menssage = '';
        var estado = $('select[name="Estado"]').val();
        var fechaactual = new Date();
        var FA = new Date();
        var fa = FA.getFullYear() + '-' + (FA.getMonth() + 1) + '-' + FA.getDate() + ' 00:00:00';
        var fechainicio = $('input[name="FechaInicioPublicacion"]');
        var fechafin = $('input[name="FechaFinPublicacion"]');
        var fechavigencia = $('input[name="FechaFinVigencia"]');
        var tipo_oferta = $('#tipoOferta').is(':checked');

        //validar fechas
        var f1 = new Date(fechainicio.datepicker('getDate'));
        var f2 = new Date(fechafin.datepicker('getDate'));
        if (f1 >= f2 && fechafin.val() != '') {
            alert('La fecha fin de publicación debe de ser mayor a la de Inicio');
            fechafin.val('');
            publicar = false;
        }

        var mensaje = 'Fin';
        if (fechafin.val() == '') {
            f2 = f1;
            mensaje = 'Inicio';
        }

        if (tipo_oferta) {
            var error = false;
            $("input.vigencias").each(function (index, value) {
                var vigencia = $(value);
                var f3 = new Date(vigencia.val() + ' 00:00:00');
                if (f2 > f3) {
                    error = true;
                    vigencia.val('');
                }
            });
            if (error) {
                alert('La fecha de Vigencia del Atributo(s) debe de ser mayor o igual a la de ' + mensaje + ' de Publicacion');
                publicar = false;
            }
        } else {
            var f3 = new Date(fechavigencia.val() + ' 00:00:00');
            if (f2 > f3 && fechavigencia.val() == '') {
                alert('La fecha de Vigencia debe de ser mayor o igual a la de ' + mensaje + ' de Publicacion');
                fechavigencia.val('');
                publicar = false;
            }
        }
        ///

        if (publicar == true) {
            var x = $("[name='id']").val();
            if (x == undefined) {
                if (new Date(fechainicio.val() + ' 00:00:00') < new Date(fa)) {
                    alert('La fecha Inicio de publicación debe de ser mayor o igual a la Fecha Actual');
                    fechainicio.val('');
                    publicar = false;
                }
            }
        }

        if (publicar == true) {
            if (estado == 'Publicado') {
                var stock = 0;
                if (tipo_oferta) {
                    $("input.stocks").each(function (index, value) {
                        stock = stock + Number($(value).val());
                    });
                    if (stock == '' || stock == 0) {
                        menssage += '- La oferta no tiene Stock, desea Publicar?\n';
                    }
                } else {
                    stock = $('input[name="Stock"]').val();
                    if (stock == '' || stock == 0) {
                        menssage += '- La oferta no tiene Stock, desea Publicar?\n';
                    }
                }

                if (fechainicio.val() == '' || fechafin.val() == '') {
                    menssage += '- La oferta no tiene fechas de Publicacion, desea Publicar?\n';
                }

                if (fechavigencia.val() == '' && tipo_oferta === false) {
                    menssage += '- La oferta no tiene fecha de vigencia, desea Publicar?\n';
                }

                fechainicio = fechainicio.datepicker('getDate');

                //calculamos la fecha actual
                if (typeof fechaAnterior !== 'undefined') {
                    fechaactual = new Date(fechaAnterior);
                } else {
                    var dia = fechaactual.getDate();
                    var mes = fechaactual.getMonth() + 1;
                    var anio = fechaactual.getFullYear();
                    fechaactual = String(mes + "/" + dia + "/" + anio);
                    fechaactual = new Date(fechaactual);
                }

                if (fechainicio < fechaactual) {
                    $('#fecini_ul').removeAttr('hidden');
                    publicar = false;
                }
            } else if (estado == 'Caducado' || estado == 'Pendiente') {
                menssage += '- La oferta no se visualizara en la web porque se guardara con estado Caducado/Pendiente\n';
            }

            if (menssage != '') {
                publicar = confirm(menssage);
            }
        }

        if (publicar == true) {
            bPreguntar = false;
            $('.form-oferta').submit();
        } else {
            e.preventDefault();
        }
    });

    $('#copyButton').click(function () {
        var result = confirm("¿Esta seguro de guardar esta oferta como una nueva?");
        if (result) {
            $('input:hidden[name=action]').val('copy');
            bPreguntar = false;
            $('#editarForm').submit();
        } else {
            return false;
        }
    });

    ///oferta imagenes
    $('#btn-image').click(function () {
        var file = $("#input-file")[0].files[0];
        var fileName = file.name;
        var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        var fileSize = file.size;
        if (fileSize < 2097152) {
            if (isImage(fileExtension)) {
                $('#download').removeClass('hidden');
                $('#error-image').empty();
                var data = new FormData();
                data.append('val', file);
                data.append('ext', fileExtension);
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '/oferta/saveImage',
                    data: data,
                    //necesario para subir archivos via ajax
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.response) {
                            myCreateFunction(data.name);
                            imagenes.push(data.name);
                            $('#download').addClass('hidden');
                        }
                    },
                    error: function () {
                        console.log('error');
                    }
                });
            } else {
                $('#error-image').html(
                    '<li >El archivo ' + fileName
                    + ' tiene una extensión incorrecta. Solo se admiten imagenes jpg y png.</li>');
            }
        } else {
            $('#error-image').html('<li >El tamaño máximo permitido para el archivo es de 2MB.</li>');
        }
    });

    $('.delete-image').click(function () {
        var row = $(this).parents('tr');
        var id = row.data('id');

        if (confirm('!La imagen se eliminara inmediatamente¡. ¿esta seguro de seguir?')) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '/oferta/deleteImagen',
                data: {id: id},
                success: function (data) {
                    if (data.response) {
                        console.log(data.data);
                        row.fadeOut();
                    } else {
                        alert(data.message);
                        console.log('No Puede Eliminar Todas Las Imagenes');
                    }
                },
                error: function () {
                    console.log('error');
                }
            });
        }
    });

    $('input[name=principal]').on('click', function () {
        var row = $(this).parents('tr');
        var id = row.data('id');
        var oferta_id = $('#oferta_id').val();
        if ($('input[name=principal]').is(':checked')) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '/oferta/pricipalImage',
                data: {id: id, oferta_id: oferta_id},
                success: function (data) {
                    console.log(data.data);
                },
                error: function () {
                    console.log('error');
                }
            });
        } else {
            console.log(oferta_id, id);
        }
    });

    ///banner imagen
    $('#btn-banner').on('click', function () {
        var file = $("#input-banner")[0].files[0];
        var fileName = file.name;
        var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        var fileSize = file.size;
        if ($('#list-banner').find('tr').length == 1) {
            if (fileSize < 2097152) {
                if (isImage(fileExtension)) {
                    $('#download-banner').removeClass('hidden');
                    $('#error-image').empty();
                    var data = new FormData();
                    data.append('val', file);
                    data.append('ext', fileExtension);
                    data.append('id_oferta', $("input[name=id]").val());
                    data.append('id_formimg', $("input[name=banner_id]").val());
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '/oferta/saveBanner',
                        data: data,
                        //necesario para subir archivos via ajax
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            if (data.response) {
                                CreatelistaBanner(data.name);
                            } else {
                                $('#error-banner').html('<li >Ocurrio un Error, refresque la página y vuélvalo a intentarlo Gracias.</li>');
                            }
                            $('#download-banner').addClass('hidden');
                        },
                        error: function () {
                            console.log('error');
                        }
                    });
                } else {
                    $('#error-banner').html(
                        '<li >El archivo ' + fileName
                        + ' tiene una extensión incorrecta. Solo se admiten imagenes jpg y png.</li>');
                }
            } else {
                $('#error-banner').html('<li >El tamaño máximo permitido para el archivo es de 2MB.</li>');
            }
        }
    });

    $('#Departamento').on('click', function () {
        if (!$(this).is(":checked")) {
            $('#Provincia').attr('checked', false);
        }
    });

    $('#Provincia').on('click', function () {
        if ($(this).is(":checked")) {
            $('#Departamento').prop('checked', 'checked');
        }
    });

    $('.precio-content').hide();
    $('#tipoOfertaContent').hide();
});

$(document).ready(function () {
    $('.delfields').click(function () {
        deleteFields();
    });

    $('.addfields').click(function () {
        createAtributosFields(true);
    });

    $('.atributos').keypress(function () {
        caracteresRestantes(this);
    });

    $('#tipo').change(function () {
        var tipo = $(this).val();
        if (tipo === "1" || tipo === "2" || tipo === "3") {
            $('#tipoOfertaContent').attr("disabled", false).show();
            $('#tipoOferta').attr("disabled", false).show().trigger('change');
            $('input:hidden[name=action]').prop('disabled', false);
            $('#copyButton').show();
        } else {
            $('#tipoOfertaContent').attr("disabled", true).hide();
            $('#tipoOferta').attr("disabled", true).hide().trigger('change');
            $('input[name=FechaFinVigencia]').attr("disabled", false);
            $('input[name=Stock]').attr("disabled", false);
            $('input:hidden[name=action]').prop('disabled', false);
            $('#copyButton').show();
        }
    });

    $('#tipoOferta').change(function () {
        var tipo = $(this).is(':checked');
        if (tipo === true) {
            var tipoV = $("#tipo").val();
            if (tipoV === "1" || tipoV === "2") {
                $('.precio-content').show();
                $('.precio-content :input').attr("disabled", false);
                $('.required').hide();
                $('input[name=FechaFinVigencia]').attr("disabled", true);
                $('input[name=DatoBeneficio]').attr("disabled", true);
                $('.stock-principal').hide();
                $('input[name=Stock]').attr("disabled", true);
                $('.precio-content .label-vigencia-h').show();
                $('.precio-content .input-vigencia-h').show();
                $('.precio-content .input-vigencia-h :input').attr("disabled", false);
            }else if(tipoV === "3"){
                $('.precio-content').show();
                $('.precio-content :input').attr("disabled", false);
                $('.required').hide();
                $('input[name=FechaFinVigencia]').attr("disabled", true);
                $('input[name=DatoBeneficio]').attr("disabled", true);
                $('.stock-principal').hide();
                $('input[name=Stock]').attr("disabled", true);
                $('.precio-content .label-vigencia-h').hide();
                $('.precio-content .input-vigencia-h').hide();
                $('.precio-content .input-vigencia-h :input').attr("disabled", true);
            }
        } else {
            $('.precio-content').hide();
            $('.precio-content :input').attr("disabled", true);
            $('input[name=FechaFinVigencia]').attr("disabled", false);
            $('input[name=DatoBeneficio]').attr("disabled", false);
            $('.stock-principal').show();
            $('input[name=Stock]').attr("disabled", false);
            $('.required').show();
        }
    });
});

Date.prototype.toInputFormat = function () {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = this.getDate().toString();
    return yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]); // padding
};

function Inicio() {
    var x = $("[name='id']").val();
    if (x !== '') {
        var fechaactual = new Date();
        var dia = fechaactual.getDate();
        var mes = fechaactual.getMonth() + 1;
        var anio = fechaactual.getFullYear();
        fechaactual = new Date(String(mes + "/" + dia + "/" + anio));
        fechaAnterior = $('input[name="FechaInicioPublicacion"]').datepicker('getDate');
        if (fechaactual < fechaAnterior) {
            fechaAnterior = fechaactual;
        }
    }
}

function cargarBolsa(value, empresa_prov) {
    var tipo_bolsa = $("#tipo");
    var options = tipo_bolsa.find("option");
    var valor_seleccionado = "";
    var dato = false;
    var estado = "";
    var x = null;

    var get_val = empresa_prov,
        get_opt = "prov";

    if (typeof value !== 'undefined') {
        valor_seleccionado = value.name;
        estado = value.step;
        if (empresa_prov == "") {
            get_val = value.emp;
        }
    }

    options.each(function () {
        if (valor_seleccionado === this.value) {
            x = $(this);
            if (x.val() == 3) {
                $('#form-config').removeClass('hidden');
                $('.vigencia').addClass('hidden');
            } else {
                $('.vigencia').removeClass('hidden');
            }
        }
    });

    $.post("/oferta/getprov", {
        id: get_val,
        opt: get_opt
    }, function (data) {
        if (data.response == true) {

            $("input[name=Descarga]").val(data.valores[1]);
            $("input[name=Presencia]").val(data.valores[2]);
            $("input[name=Lead]").val(data.valores[3]);

            tipo_bolsa.empty();
            tipo_bolsa.append('<option value="">Selecione...</option>');

            $.each(data.tipos, function (index, value) {
                tipo_bolsa.append('<option value="' + index + '">' + value + '</option>');
            });

            tipo_bolsa.val(valor_seleccionado).change();

            tipo_bolsa.find('option').each(function (index, value) {
                if (valor_seleccionado === $(value).val()) {
                    dato = true;
                }
            });

            if (estado == "edit" && dato === false) {
                $("#tipo").append(x);
            }

            tipo_bolsa.trigger("change");

            var valor = $("#tipo").find("option:selected").text();
            var bolsa = $('#bolsa').val();
            if (parseInt(bolsa) > 0) {
                if ($('#tipoOferta').is(":checked")) {
                    var stock_base = 0;
                    $("input.stocks").each(function (index, value) {
                        stock_base = stock_base + Number($(value).val());
                    });
                    bolsa = bolsa - stock_base;
                } else {
                    bolsa = bolsa - Number($('#stock').val());
                }

                if (valor == "Descarga") {
                    var descargas = $('input[name="Descarga"]');
                    descargas.val(bolsa);
                    dato_descarga = bolsa;
                } else if (valor == "Presencia") {
                    var presencia = $('input[name="Presencia"]');
                    presencia.val(bolsa);
                    dato_presencia = bolsa;
                } else if (valor == "Lead") {
                    var lead = $('input[name="Lead"]');
                    lead.val(bolsa);
                    dato_lead = bolsa;
                }
            }
        }
    }, 'json');
}

function calcularFecha(picker) {
    var fecha_fin = $('input[name="FechaFinPublicacion"]');
    var fecha = new Date(picker.datepicker('getDate'));
    var stock = 0;
    var tipo_oferta = $('#tipoOferta').is(':checked');

    if (tipo_oferta === true) {
        $('input.stocks').each(function () {
            stock = stock + parseInt($(this).val(), 10);
        })
    } else {
        stock = parseInt($('input[name="Stock"]').val(), 10);
    }

    if (!isNaN(fecha) && stock > 0) {
        var tipo = $("#tipo").find("option:selected").text();

        if (tipo === 'Presencia') {
            fecha.setDate(fecha.getDate() + stock);
            fecha_fin.attr('value', fecha.toInputFormat());
            fecha_fin.datepicker("setDate", fecha.toInputFormat());
        }
    }
}

function recargasChecks(div, valores, mensaje, checkeds, name) {
    div.empty();
    div.append('<input type="hidden" value="" name="' + name + '">');
    var cont = 0;
    var cadena = null;
    $.each(valores, function (index, value) {
        var write = false;
        if (cont == 0) {
            cadena = '<tr>';
        }

        $.each(checkeds, function (indexc, valuec) {
            if (cont <= 1) {
                if (valuec.value === index) {
                    cont++;
                    cadena += '<td><label><input name="' + name + '[]" class="checkbox-inline" ' +
                        'style="margin-left: 2em;" ' +
                        'value="' + index + '" type="checkbox" checked>' + value + '</label></td>';
                    write = true;
                }
            }
        });

        if (!write) {
            if (cont <= 1) {
                cont++;
                cadena += '<td><label><input name="' + name + '[]" class="checkbox-inline" ' +
                    'style="margin-left: 2em;" ' +
                    'value="' + index + '" type="checkbox">' + value + '</label></td>';
            }
        }
        if (cont == 2) {
            cont = 0;
            cadena += '</tr>';
            div.append(cadena);
            cadena = null;
        }
    });
    if (cont == 1) {
        cont = 0;
        cadena += '</tr>';
        div.append(cadena);
        cadena = null;
    }
    div.append(mensaje);
}

function isImage(extension) {
    switch (extension.toLowerCase()) {
        case 'jpg':
        case 'png':
        case 'jpeg':
            return true;
            break;
        default:
            return false;
            break;
    }
}

function myCreateFunction(name) {
    var id = $('#oferta_id').val();
    var table = document.getElementById("list-image");
    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var index = Math.floor((Math.random() * 100) + 1);
    row.setAttribute('id', index);
    principalImage(index);
    cell1.innerHTML = '<a id="single_image" href="/elements/oferta/' + name + '"><img style="height: 50px;width: '
        + '50px;" src="/elements/oferta/' + name + '"></a>';
    cell2.innerHTML = name + '<input type="hidden" name="Imagen[' + index + ']" value="' + name + '">';
    name = "'" + name + "'";
    cell3.innerHTML = '<input onclick="principalImage(' + index + ')" type="radio" name="principal" checked>';
    cell4.innerHTML = '<a onclick="deleteElement(' + index + ',' + name + ')" class="btn btn-danger">Eliminar</a>';
}

function deleteElement(index, name) {
    var fileExtension = name.substring(name.lastIndexOf('.') + 1);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/oferta/deleteImage',
        data: {
            val: name,
            ext: fileExtension
        },
        success: function (data) {
            if (data.response) {
                $("#" + index).remove();
            }
        },
        error: function () {
            console.log('error');
        }
    });
}

function principalImage(index) {
    $('#principalimage').val(index);
}

function eliminar() {
    $.each(imagenes, function (index, value) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/oferta/deleteImage',
            data: {val: value},
            success: function (data) {
                if (data.response) {
                    console.log('ok');
                }
            },
            error: function () {
                console.log('error');
            }
        });
    });
}

function CreatelistaBanner(name) {
    var table = document.getElementById("list-banner");
    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var index = Math.floor((Math.random() * 100) + 1);
    row.setAttribute('id', index);
    cell1.innerHTML = '<a id="single_image" href="/elements/banners/' + name + '"><img style="height: 50px;width: '
        + '50px;" src="/elements/banners/' + name + '"></a>';
    cell2.innerHTML = name + '<input type="hidden" name="banner" value="' + name + '">';
    name = "'" + name + "'";
    cell3.innerHTML = '<a onclick="deleteBanner(' + index + ',' + name + ')" class="btn btn-danger">Eliminar</a>';
}

function deleteBanner(index, name) {
    $('#download-banner').removeClass('hidden');
    var fileExtension = name.substring(name.lastIndexOf('.') + 1);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/oferta/deleteBanner',
        data: {
            val: name,
            ext: fileExtension,
            id_oferta: $("input[name=id]").val()
        },
        success: function (data) {
            if (data.response) {
                $("#" + index).remove();
            } else {
                $('#error-banner').html('<li >Ocurrio un Error, refresque la página y vuélvalo a intentarlo Gracias.</li>');
            }
            $('#download-banner').addClass('hidden');
        },
        error: function () {
            console.log('error');
            $('#download-banner').addClass('hidden');
        }
    });
}

function confimacion(index, name) {
    if (confirm('!La imagen se eliminara inmediatamente¡. ¿esta seguro de seguir?')) {
        deleteBanner(index, name);
    }
}

function deleteFields(e) {
    var div = e.closest('div.data-list');
    var value = Number($(div).find("input.stocks").val());
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/oferta/getDescargasByAtributo',
        data: {
            id: Number($(div).find("input.atributosId").val())
        },
        success: function (data) {
            if (data.response) {
                if (data.cupon <= 0) {
                    var valor = $("#tipo").find("option:selected").text();
                    if (valor == "Descarga") {
                        var descargas = $('input[name="Descarga"]');
                        dato_descarga = Number(descargas.val());
                        dato_descarga = dato_descarga + value;
                        descargas.val(dato_descarga);
                    } else if (valor == "Presencia") {
                        var presencia = $('input[name="Presencia"]');
                        dato_presencia = Number(presencia.val());
                        dato_presencia = dato_presencia + value;
                        presencia.val(dato_presencia);
                    } else if (valor == "Lead") {
                        var lead = $('input[name="Lead"]');
                        dato_lead = Number(lead.val());
                        dato_lead = dato_lead + value;
                        lead.val(dato_lead);
                    }
                    $(e).closest('div.data-list').remove();
                } else {
                    alert('El Atributo no puede eliminarse, ya tiene descargas.')
                }
            } else {
                $('#error-banner').html('<li >Ocurrio un Error, refresque la página y vuélvalo a intentarlo Gracias.</li>');
            }
            $('#download-banner').addClass('hidden');
        },
        error: function () {
            console.log('error');
            $('#download-banner').addClass('hidden');
        }
    });
}

function createAtributosFields(option) {
    var fila = $('<div class="form-group data-list">');
    var count = Math.round(Math.random() * 100000);
    var contenido = '<div class="col-md-3"><textarea title="Atributo" class="form-control atributos" name="atributos[' + count + ']"' +
        ' onkeypress="caracteresRestantes(this)"></textarea><span class="atributos"></span>' +
        '<div class="atributos error"></div></div>' +
        '<div class="col-md-2"><input class="form-control stocks" name="stocks[' + count + ']" type="text" title="Stock">' +
        '<div class="stocks-error error"></div></div>' +
        '<div class="col-md-2"><input class="form-control dato_beneficio" name="beneficios[' + count + ']" type="text" title="Dato Beneficio">' +
        '<div class="beneficios-error error"></div></div>' +
        '<div class="col-md-2 input-vigencia-h"><input class="form-control vigencias datepicker" name="vigencias[' + count + ']" type="text" title="vigencias">' +
        '<div class="vigencias error"></div></div>' +
        '<div class="col-md-2">' +
        '<button class="addfields btn btn-default" type="button" onclick="createAtributosFields(true)">&#43;</button>' +
        '<button class="delfields btn btn-default" type="button" onclick="deleteFields(this)">&#45;</button>' +
        ((option) ? '<input type="hidden" name="atributosId[' + count + ']" value="0" class="atributosId">' : '') +
        '</div>';
    fila.append(contenido);
    $('.precio-content').append(fila);
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });
    var tipoV = $("#tipo").val();
    if(tipoV === "3"){
        $('.precio-content .input-vigencia-h').hide();
        $('.precio-content .input-vigencia-h :input').attr("disabled", true);
    }
}

function caracteresRestantes(e) {
    var text = $(e).val();
    var textLength = text.length;
    var maxLength = 300;
    var total = maxLength - textLength;
    var contenedor = $(e).closest('div');
    if (total <= 0) {
        $(e).val(text.substring(0, 300));
        contenedor.find('span.atributos').html('caracteres restantes: 0');
    } else {
        contenedor.find('span.atributos').html('caracteres restantes: ' + total);
    }
}

$(document).on('keydown', ".stocks", function () {
    if(!eject) {
        stockAnt = Number($(this).val());
        eject = true;
    }
});

$(document).on('keyup', ".stocks", function () {
    if(eject) {
        var stock = Number($(this).val());
        var valor = $("#tipo").find("option:selected").text();

        var resultado = 0;
        if (valor == "Descarga") {
            var descargas = $('input[name="Descarga"]');
            dato_descarga = Number(descargas.val());
            resultado = dato_descarga + (stockAnt - stock);
            if (resultado >= 0) {
                descargas.val(resultado);
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa');
                $(this).val(stockAnt);
            }
            eject = false;
        } else if (valor == "Presencia") {
            var presencia = $('input[name="Presencia"]');
            dato_presencia = Number(presencia.val());
            resultado = dato_presencia + (stockAnt - stock);
            if (resultado >= 0) {
                presencia.val(resultado);
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa');
                $(this).val(stockAnt);
            }
            eject = false;
        } else if (valor == "Lead") {
            var lead = $('input[name="Lead"]');
            dato_lead = Number(lead.val());
            resultado = dato_lead + (stockAnt - stock);
            if (resultado >= 0) {
                lead.val(resultado);
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa');
                $(this).val(stockAnt);
            }
            eject = false;
        }

        var parent = $(this).closest('div.data-list');
        var diasT_content = parent.find('input:hidden.diasTrans');
        var stockI_content = parent.find('input:hidden.stockIni');
        var nuevoStock_content = parent.find('input.stocks');

        var diasT = parseInt(diasT_content.val());
        var stockI = parseInt(stockI_content.val());
        var nuevoStock = parseInt(nuevoStock_content.val());

        if (diasT + nuevoStock != stockI) {
            var suma = parseInt(diasT + nuevoStock, 10);
            stockI_content.val(suma);
        } else if (diasT + nuevoStock === 0) {
            stockI_content.val(0);
        }

        calcularFecha($('#datepicker'));
    }
});

$(document).on('keydown', "#stock", function () {
    if(!eject) {
        stockAnt = Number($(this).val());
        eject = true;
    }
});

$(document).on('keyup', "#stock", function () {
    if(eject) {
        var stock = Number($(this).val());
        var valor = $("#tipo").find("option:selected").text();

        var resultado = 0;
        if (valor == "Descarga") {
            var descargas = $('input[name="Descarga"]');
            dato_descarga = Number(descargas.val());
            resultado = dato_descarga + (stockAnt - stock);
            if (resultado >= 0) {
                descargas.val(resultado);
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa');
                $(this).val(stockAnt);
            }
            eject = false;
        } else if (valor == "Presencia") {
            var presencia = $('input[name="Presencia"]');
            dato_presencia = Number(presencia.val());
            resultado = dato_presencia + (stockAnt - stock);
            if (resultado >= 0) {
                presencia.val(resultado);
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa');
                $(this).val(stockAnt);
            }
            eject = false;
        } else if (valor == "Lead") {
            var lead = $('input[name="Lead"]');
            dato_lead = Number(lead.val());
            resultado = dato_lead + (stockAnt - stock);
            if (resultado >= 0) {
                lead.val(resultado);
            } else {
                alert('La cantidad de stock ingresada supera a la de la bolsa');
                $(this).val(stockAnt);
            }
            eject = false;
        }

        var diasT = parseInt($('input[name="diasT"]').val());
        var nuevoStock = parseInt($(this).val());
        var stockI = parseInt($('input[name="stockI"]').val());

        if (diasT + nuevoStock != stockI) {
            var suma = parseInt(diasT + nuevoStock);
            $('input[name="StockInicial"]').val(suma);
        } else if (diasT + nuevoStock === 0) {
            $('input[name="StockInicial"]').val(0);
        }

        calcularFecha($('#datepicker'));
    }
});
