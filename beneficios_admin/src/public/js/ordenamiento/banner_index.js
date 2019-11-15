/**
 * Created by luisvar on 19/10/15.
 */

$(document).ready(function () {
    $(".select2").select2();
    $("a#single_image").fancybox();

    $('#galeryform').submit(function (e) {
        e.preventDefault();

        var formObj = $(this);
        var formURL = formObj.attr("action");
        var formData = new FormData(this);

        var imagenGaleria = $('input[name="Galeria"]');
        var urlGaleria = $('input[name="GaleriaUrl"]');
        $('#loading-img').show();
        if (imagenGaleria.val().length === 0) {
            alert('no se cargo ninguna imagen.');
            $('#loading-img').hide();
        } else {
            $.ajax({
                data: formData,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                type: "POST",
                dataType: "json",
                url: formURL
            })
                .done(function (data, textStatus, jqXHR) {
                    if (data.response) {
                        var nomFich = data.name;
                        $('#galerytable').append(
                            '<tr data-id="' + data.value + '">' +
                            '<td><a id="single_image" href="/elements/galeria/' + nomFich + '">' +
                            '<img style="height: 50px;width: 50px;" src="/elements/galeria/' + nomFich + '"></a></td>' +
                            '<td style="width: 300px">' + nomFich + '</td>' +
                            '<td style="width: 300px"><div class="control-group">' +
                            '<label for="name" class="control-label">' +
                            '<p class="text-info">' + ((urlGaleria.val() === "") ? "no link" : urlGaleria.val()) +
                            '<i class="icon-star"></i></p></label><input type="text" class="edit-input form-control input-sm" ' +
                            'value="' + ((urlGaleria.val() === "") ? "" : urlGaleria.val()) + '" style="display: none" ' +
                            'onblur="salireditar(this,\'galery\');"/>' +
                            '<div class="controls"><button type="button" ' +
                            'class="edit btn btn-default btn-flat" onclick="editabutton(this);">Editar</button></div></div></td>' +
                            '<td style="width: 50px"><input id="elim' + data.value + '" class="elim" type="checkbox"' +
                            'checked="checked" name="0" onclick="elim(this)"></td></tr>'
                        );
                        imagenGaleria.val('');
                        urlGaleria.val('');
                        $('#imagenError').text('');
                        $('#loading-img').hide();
                    } else {
                        $('#imagenError').text(data.message);
                        $('#loading-img').hide();
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    if (console && console.log) {
                        console.log("La solicitud a fallado: " + textStatus);
                        $('#loading-img').hide();
                    }
                });
        }
    });

    $('#empresa').on('change', function () {
        var categoria = $('#categoria');
        categoria.val('');
        $('#campania').val('');
        desactivarCampos();

        var tabla = $("#bannerstable");
        tabla.find('tr').each(function () {
            $(this).remove();
        });

        var tabla2 = $("#galerytable");
        tabla2.find('tr').each(function () {
            $(this).remove();
        });

        $('#r_cat').prop("checked", true);
        categoria.prop("disabled", false);
    });

    $('#categoria').on('change', function () {
        var categoria = $("#categoria").find("option:selected");
        var empresa = $("#empresa");
        var val = categoria.val();

        var emp = empresa.val();
        var url = '/banners/getBannersCategoria';
        if (val === '1') {
            activarCampos();
            getGaleria(emp);
        } else {
            desactivarCampos();
        }
        getBanners(url, val, emp);
        $('#empresa_g').val(isNaN(parseFloat(emp)) ? '' : parseFloat(emp));
    });

    $('#campania').on('change', function () {
        desactivarCampos();
        var campania = $("#campania").find("option:selected");
        var val = campania.val();
        var empresa = $("#empresa");
        var emp = empresa.val();
        var url = '/banners/getBannersCampania';
        getBanners(url, val, emp);
    });

    $('.tipo').change(function () {
        var value = $(this).val();
        var categoria = $('#categoria');
        var campania = $('#campania');
        var empresa = $("#empresa");
        var emp = empresa.val();
        var val = 0;
        var url = "";
        if (value == '0') {
            categoria.attr('disabled', false);
            campania.attr('disabled', true);
            categoria.val('');
            desactivarCampos();
        } else if (value == '1') {
            categoria.attr('disabled', true);
            campania.attr('disabled', false);
            campania.val('');
            desactivarCampos();
        } else if (value == '2') {
            categoria.attr('disabled', true);
            campania.attr('disabled', true);
            categoria.val('');
            campania.val('');
            desactivarCampos();
            url = '/banners/getBannersTienda';
            getBanners(url, val, emp);
        } else if (value == '3') {
            categoria.attr('disabled', true);
            campania.attr('disabled', true);
            val = 9;
            url = '/banners/getBannersCategoria';
            desactivarCampos();
            $(".puntos").css("display", "block");
            $('.puntos input').each(function () {
                $(this).removeAttr('disabled');
            });
            getBanners(url, val, emp);
            $('#empresa_g').val(isNaN(parseFloat(emp)) ? '' : parseFloat(emp));
        } else if (value == '4') {
            val = 10;
            url = '/banners/getBannersCategoria';
            desactivarCampos();
            $(".puntos").css("display", "block");
            $('.puntos input').each(function () {
                $(this).removeAttr('disabled');
            });
            getBanners(url, val, emp);
            $('#empresa_g').val(isNaN(parseFloat(emp)) ? '' : parseFloat(emp));
        }

        $("#galeriapri").css("display", "none");
    });

    $('.edit-input-galery').click(function () {
        editabutton($(this));
    });

    $('#loading-img').hide();
});

$(document).on('change', '.elim', function (e) {
    var row = $(this).closest('tr');
    var id = row.attr('data-id');

    var val = "";
    var id_tag = '#elim' + id;
    var elim_id = $(id_tag);

    if (elim_id.attr("name") == 1) {
        elim_id.attr("name", 0);
        val = '0';
    } else {
        elim_id.attr("name", 1);
        val = '1';
    }

    var r = confirm('¿Desea Eliminar la Imagen Permanentemente?');
    if (r == true) {
        $.ajax({
            data: {id: id, val: val},
            type: "POST",
            dataType: "json",
            url: "/banners/deleteGaleria"
        })
            .done(function (data, textStatus, jqXHR) {
                if (console && console.log) {
                    console.log("La solicitud se ha completado correctamente.");
                }
                row.remove();
            })
            .fail(function (jqXHR, textStatus) {
                if (console && console.log) {
                    console.log("La solicitud a fallado: " + textStatus);
                }
            });
    } else {
        $(this).attr("checked", "checked");
        $(this).prop("checked", true);
    }
});

function editabutton(element) {
    var dad = $(element).parent().parent();
    dad.find('label').hide();
    dad.find('input[type="text"]').show().focus();
}

function salireditar(element, valor) {
    var id = $(element).closest('tr').attr('data-id');
    var val = $(element).val();
    var tipo = $('input[name="tipo"]:checked').val();
    var dad = $(element).parent();
    var url = "";

    if (valor === "galery") {
        url = "/banners/editlinkgalery";
    } else {
        url = "/banners/editlink";
    }

    $.ajax({
        data: {val: val, id: id, tipo: tipo},
        type: "POST",
        dataType: "json",
        url: url
    })
        .done(function (data, textStatus, jqXHR) {
            if (console && console.log) {
                console.log("La solicitud se ha completado correctamente.");
                val = ((val === "") ? "no link" : val);
                dad.find('label').find('p').text(val);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (console && console.log) {
                console.log("La solicitud a fallado: " + textStatus);
            }
            $(".bannerspri").css("display", "none");
        });


    $(element).hide();
    dad.find('label').show();
}

function getBanners(url, val, emp) {
    $.ajax({
        data: {val: val, emp: emp},
        type: "POST",
        dataType: "json",
        url: url
    })
        .done(function (data, textStatus, jqXHR) {
            if (console && console.log) {
                console.log("La solicitud se ha completado correctamente.");

                var tabla = $("#bannerstable");

                tabla.find('tr').each(function () {
                    $(this).remove();
                });

                var head = '<tr role="row">' +
                    '<th style="width: 100px"><a>Banner</a></th>' +
                    '<th style="width: 100px"><a>Imagen</a></th>' +
                    '<th style="width: 300px"><a>Nombre de la Imagen</a></th>' +
                    '<th style="width: 50px"><a>Link</a></th>' +
                    '<th style="width: 50px"><a>Activo</a></th>' +
                    '</tr>';

                tabla.append(head);

                $(".bannerspri").css("display", "block");
                if (data.value.length > 0) {
                    $.each(data.value, function (index, value) {

                        var label = '<div class="control-group">' +
                            '<label for="name" class="control-label">' +
                            '<p class="text-info">' + ((value['Url'] === null) ? "no link" : value['Url']) + '<i class="icon-star"></i></p>' +
                            '</label>' +
                            '<input type="text" class="edit-input form-control input-sm" ' +
                            'value="' + ((value['Url'] === null) ? "" : value['Url']) + '" style="display: none" onblur="salireditar(this,0);"/>' +
                            '<div class="controls">' +
                            '<button type="button" class="edit btn btn-default btn-flat" onclick="editabutton(this);">Editar</button>' +
                            '</div>' +
                            '</div>';

                        var activo = '<tr data-id="' + value['id'] + '">' +
                            '<td>' + value['NombreBanner'] + '</td>' +
                            '<td><a id="single_image" href="/elements/banners/' + value['Imagen'] + '">' +
                            '<img style="height: 50px;width: 50px;" src="/elements/banners/' + value['Imagen'] + '"></a></td>' +
                            '<td style="width: 300px">' + value['Imagen'] + '</td>' +
                            '<td style="width: 300px">' + label + '</td>' +
                            '<td style="width: 50px">' +
                            '<input id="' + value["BNF_Banners_id"] + '" class="banelim" type="checkbox" checked="checked" ' +
                            'onClick="deleteBanner(this,' + val + ');" value="0"></td></tr>';

                        var inactivo = '<tr data-id="' + value['id'] + '">' +
                            '<td>' + value['NombreBanner'] + '</td>' +
                            '<td><a id="single_image" href="/elements/banners/' + value['Imagen'] + '">' +
                            '<img style="height: 50px;width: 50px;" src="/elements/banners/' + value['Imagen'] + '"></a></td>' +
                            '<td style="width: 300px">' + value['Imagen'] + '</td>' +
                            '<td style="width: 300px">' + label + '</td>' +
                            '<td style="width: 50px">' +
                            '<input id="' + value["BNF_Banners_id"] + '" class="banelim" type="checkbox" ' +
                            'onClick="deleteBanner(this,' + val + ');" value="1"></td></tr>';

                        if (value['Eliminado'] === '1') {
                            tabla.find('tr:last').after(inactivo);
                        } else {
                            tabla.find('tr:last').after(activo);
                        }
                    });
                } else {
                    var mensaje = '<tr><td colspan="4">No hay banners registrados</td></tr>';
                    tabla.find('tr:last').after(mensaje);
                }

            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (console && console.log) {
                console.log("La solicitud a fallado: " + textStatus);
            }
            $(".bannerspri").css("display", "none");
        });
}

function getGaleria(emp) {
    $.ajax({
        data: {val: emp},
        type: "POST",
        dataType: "json",
        url: '/banners/getGaleria'
    })
        .done(function (data, textStatus, jqXHR) {
            if (console && console.log) {
                console.log("La solicitud se ha completado correctamente.");
                $.each(data.value, function (index, value) {
                    var nomFich = value['Imagen'];
                    var url = value['Url'];

                    $('#galerytable').append(
                        '<tr data-id="' + value['id'] + '">' +
                        '<td><a id="single_image" href="/elements/galeria/' + nomFich + '">' +
                        '<img style="height: 50px;width: 50px;" src="/elements/galeria/' + nomFich + '"></a></td>' +
                        '<td style="width: 300px">' + nomFich + '</td>' +
                        '<td style="width: 300px"><div class="control-group">' +
                        '<label for="name" class="control-label">' +
                        '<p class="text-info">' + ((url === "") ? "no link" : url) +
                        '<i class="icon-star"></i></p></label><input type="text" class="edit-input form-control input-sm" ' +
                        'value="' + ((url === "") ? "" : url) + '" style="display: none" ' +
                        'onblur="salireditar(this,\'galery\');"/>' +
                        '<div class="controls"><button type="button" ' +
                        'class="edit btn btn-default btn-flat" onclick="editabutton(this);">Editar</button></div></div></td>' +
                        '<td style="width: 50px"><input id="elim' + value['id'] + '" class="elim" type="checkbox"' +
                        'checked="checked" name="0"></td></tr>'
                    );
                });
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (console && console.log) {
                console.log("La solicitud a fallado: " + textStatus);
            }
        });
}

function deleteBanner(element, banner) {
    var id = $(element).closest('tr').attr('data-id');
    var value = $(element).attr('value');
    var idinput = $(element).attr('id');
    var val = "";
    var ban = banner;
    var tipo = $('input[name="tipo"]:checked').val();

    var r = null;

    if (idinput == '1') {
        if (value == 0) {
            r = confirm('¿Desea Ocultar la Imagen 01?');
        } else {
            r = confirm('¿Desea Activar la Imagen 01?');
        }
    } else if (idinput == '5') {
        if (value == 0) {
            r = confirm('¿Desea Ocultar el Banner Principal?');
        } else {
            r = confirm('¿Desea Activar el Banner Principal?');
        }
    }
    else {
        if (value == 0) {
            r = confirm('¿Desea Ocultar el Banner 0' + (idinput - 1) + '?');
        } else {
            r = confirm('¿Desea Activar el Banner 0' + (idinput - 1) + '?');
        }
    }

    if (r == true) {
        if (value == 1) {
            val = '0';
            $(element).attr('value', 0);
        } else {
            val = '1';
            $(element).attr('value', 1);
        }
        $.ajax({
            data: {id: id, val: val, ban: ban, tipo: tipo},
            type: "POST",
            dataType: "json",
            url: "/banners/deleteBanner"
        })
            .done(function (data, textStatus, jqXHR) {
                if (console && console.log) {
                    console.log("La solicitud se ha completado correctamente.");
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                if (console && console.log) {
                    console.log("La solicitud a fallado: " + textStatus);
                }
            });
    } else {
        if ($(element).prop("checked")) {
            $(element).removeAttr("checked");
            $(element).prop("checked", false);
        } else {
            $(element).attr("checked", "checked");
            $(element).prop("checked", true);
        }
    }
}

function activarCampos() {
    $("#galeriapri").css("display", "block");

    $(".principal").css("display", "none");
    $('.principal input').each(function () {
        $(this).attr('disabled', 'disabled');
    });

    $(".secundario").css("display", "block");
    $('.secundario input').each(function () {
        $(this).removeAttr('disabled');
    });

    $(".puntos").css("display", "none");
    $('.puntos input').each(function () {
        $(this).attr('disabled', 'disabled');
    });
}

function desactivarCampos() {
    $("#galeriapri").css("display", "none");

    $(".principal").css("display", "block");
    $('.principal input').each(function () {
        $(this).removeAttr('disabled');
    });

    $(".secundario").css("display", "none");
    $('.secundario input').each(function () {
        $(this).attr('disabled', 'disabled');
    });

    $(".puntos").css("display", "none");
    $('.puntos input').each(function () {
        $(this).attr('disabled', 'disabled');
    });
}
