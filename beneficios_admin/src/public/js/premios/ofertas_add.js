var imagenes = [];

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

    $('.precio-content').hide();

    $("a#single_image").fancybox();

    $('.searchable').multiSelect({
        selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='buscar...'>",
        selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='buscar...'>",
        afterInit: function (ms) {
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function (e) {
                    if (e.which === 40) {
                        that.$selectableUl.focus();
                        return false;
                    }
                });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function (e) {
                    if (e.which == 40) {
                        that.$selectionUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function () {
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function () {
            this.qs1.cache();
            this.qs2.cache();
        }
    });

    $('input:hidden[name=action]').prop('disabled', true);
    $('#copyButton').hide();
});

$(document).ready(function () {
    $('.delfields').click(function () {
        deleteFields();
    });

    $('.addfields').click(function () {
        createPrecioFields();
    });

    $('.atributos').keypress(function () {
        caracteresRestantes(this);
    });

    $('#btn-image').click(function () {
        var file = $("#input-file")[0].files[0];
        if (typeof file !== "undefined") {
            var fileName = file.name;
            var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
            var fileSize = file.size;
            if (fileSize < 2097152) {
                if (isImage(fileExtension)) {
                    $('#download').show();
                    $('#error-image').empty();
                    var data = new FormData();
                    data.append('val', file);
                    data.append('ext', fileExtension);
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '/ofertas-premios/saveImage',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            if (data.response) {
                                listImage(data.name);
                                imagenes.push(data.name);
                                $('#download').hide();
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
        }
    });

    $('#tipoPrecio').change(function () {
        var tipo = $(this).val();
        if (tipo === "Split") {
            $('.precio-content').show();
            $('.precio-content :input').attr("disabled", false);
            $('#precio-venta').attr("disabled", true);
            $('#precio-beneficio').attr("disabled", true);
            $('.required').hide();
            $('input[name=FechaVigencia]').attr("disabled", true);
            $('input[name=Stock]').attr("disabled", true);
        } else {
            $('.precio-content').hide();
            $('.precio-content :input').attr("disabled", true);
            $('#precio-venta').attr("disabled", false);
            $('#precio-beneficio').attr("disabled", false);
            $('input[name=FechaVigencia]').attr("disabled", false);
            $('input[name=Stock]').attr("disabled", false);
            $('.required').show();
        }
    });

    $('#empresa-cli').change(function () {
        var empresa = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#campanias").empty();

        $("#segmentos").empty();
        var segmentos = $("#ms-segmentos");
        segmentos.find("div.ms-selectable").find("ul.ms-list").empty();
        segmentos.find("div.ms-selection").find("ul.ms-list").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/ofertas-premios/getDataEmpresa',
            data: {id: empresa, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    var resultado = data.empresa;
                    $('#ruc').html(resultado.ruc);
                    $('#razon-social').html(resultado.razon);
                    $('#contacto').html(resultado.contacto);
                    $("#campanias").select2({
                        language: 'es',
                        data: data.campanias
                    })
                } else {
                    $('#ruc').html('');
                    $('#razon-social').html('');
                    $('#contacto').html('');
                    $("#campanias").select2({
                        language: 'es',
                        data: []
                    });
                }
            }
        });
    });

    $('#campanias').change(function () {
        var campania = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#segmentos").empty();
        var segmentos = $("#ms-segmentos");
        segmentos.find("div.ms-selectable").find("ul.ms-list").empty();
        segmentos.find("div.ms-selection").find("ul.ms-list").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/ofertas-premios/getDataSegmentos',
            data: {id: campania, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $.each(data.segmentos, function (index, value) {
                        $("#segmentos").multiSelect('addOption', value);
                    });
                } else {
                    $("#segmentos").multiSelect({});
                }
            }
        });
    });

    $('#pais').on('change', null, function (event, value) {
        var $pais = $(this);
        var $depa = $(".listdep");
        var $cate = $(".listcat");
        var $camp = $(".listcam");
        var get_val = $pais.val();

        $.post("/ofertas-premios/getDepartamentos", {
            id: get_val
        }, function (data) {
            if (data.response == true) {

                var messagedep = $(".listdep ul");
                var chekadosdep = $(".listdep label input[name='Departamento[]']:checked");
                recargarChecks($depa, data.depa, messagedep, chekadosdep, "Departamento");

                var messagecat = $(".listcat ul");
                var chekadoscat = $(".listcat label input[name='Categoria[]']:checked");
                recargarChecks($cate, data.cate, messagecat, chekadoscat, "Categoria");

                var messagecam = $(".listcam ul");
                var chekadoscam = $(".listcam label input[name='Campania[]']:checked");
                recargarChecks($camp, data.camp, messagecam, chekadoscam, "Campania");
            } else {
                $depa.empty();
                $cate.empty();
                $camp.empty();
            }
        }, 'json');
    });

    $('.delete-image').click(function () {
        var row = $(this).parents('tr');
        var id = row.data('id');

        if (confirm('!La imagen se eliminara inmediatamente¡. ¿esta seguro de seguir?')) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '/ofertas-premios/deleteImagen',
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
                url: '/ofertas-premios/principalImage',
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

    $('#copyButton').on('click', function () {
        var result = confirm("¿Esta seguro de guardar esta oferta como una nueva?");
        if (result) {
            $('input:hidden[name=action]').val('copy');
            $('#editarForm').submit();
        } else {
            return false;
        }
    });

    $('#addButton').on('click', function () {
        $('input:hidden[name=action]').val('');
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
});

function deleteFields(e) {
    $(e).closest('div.data-list').remove();
}

function createPrecioFields() {
    var fila = $('<div class="form-group data-list">');
    var count = Math.round(Math.random() * 100000);
    var contenido = '<div class="col-md-2"><textarea title="Atributo" class="form-control atributos" name="atributos[' + count + ']" onkeypress="caracteresRestantes(this)"></textarea>' +
        '<label class="atributos"><label><div class="atributos error"></div></div>' +
        '<div class="col-md-2"><input class="form-control preciosVenta" name="preciosVenta[' + count + ']" type="text" title="Precio Venta">' +
        '<div class="preciosVenta error"></div></div>' +
        '<div class="col-md-2"><input class="form-control preciosBeneficio" name="preciosBeneficio[' + count + ']" type="text" title="Precio Beneficio">' +
        '<div class="preciosBeneficio error"></div></div>' +
        '<div class="col-md-2"><input class="form-control stocks" name="stocks[' + count + ']" type="text" title="Stock">' +
        '<div class="stocks error"></div></div>' +
        '<div class="col-md-2"><input class="form-control vigencias datepicker" name="vigencias[' + count + ']" type="text" title="Vigencia">' +
        '<div class="vigencias error"></div></div>' +
        '<div class="col-md-2">' +
        '<button class="addfields btn btn-default" type="button" onclick="createPrecioFields()">&#43;</button>' +
        '<button class="delfields btn btn-default" type="button" onclick="deleteFields(this)">&#45;</button>' +
        '</div>';
    fila.append(contenido);
    $('.precio-content').append(fila);
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });
}

function caracteresRestantes(e) {
    var text = $(e).val();
    var textLength = text.length;
    var maxLength = 300;
    var total = maxLength - textLength;
    var contenedor = $(e).closest('div');
    if (total <= 0) {
        $(e).val(text.substring(0, 300));
        contenedor.find('label.atributos').html('caracteres restantes: 0');
    } else {
        contenedor.find('label.atributos').html('caracteres restantes: ' + total);
    }
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

function listImage(name) {
    var id = $('#oferta_id').val();
    var table = document.getElementById("list-image");
    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var index = Math.floor((Math.random() * 100) + 1);
    row.setAttribute('id', index);
    cell1.innerHTML = '<a id="single_image" href="/elements/oferta_premios/' + name + '"><img style="height: 50px;width: '
        + '50px;" src="/elements/oferta_premios/' + name + '"></a>';
    cell2.innerHTML = name + '<input type="hidden" name="Imagen[' + index + ']" value="' + name + '">';
    name = "'" + name + "'";
    cell3.innerHTML = '<input onclick="principalImage(' + index + ')" type="radio" name="principal" checked>';
    cell4.innerHTML = '<a onclick="deleteImage(' + index + ',' + name + ')" class="btn btn-danger">Eliminar</a>';
    principalImage(index);
}

function principalImage(index) {
    $('#principalimage').val(index);
}

function deleteImage(index, name) {
    var fileExtension = name.substring(name.lastIndexOf('.') + 1);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ofertas-premios/deleteImage',
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

function recargarChecks(div, valores, mensaje, checkeds, name) {
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