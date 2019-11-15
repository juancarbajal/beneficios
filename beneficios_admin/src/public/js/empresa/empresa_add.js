/**
 * Created by luisvar on 11/09/15.
 */
//Lamando Validaciones

document.write("<script type='text/javascript' src='../../js/validations.js'></script>");
var error = true;
$(function () {

    var ctrlDown = false;
    var ctrlKey = 17, vKey = 86, cKey = 67, xKey = 88;

    var shiftDown = false;
    var shiftkey = 16, numberKey = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 187, 188, 189, 190, 191, 219, 220, 221, 222, 229];

    $(document).keydown(function (e) {
        if (e.keyCode == ctrlKey) ctrlDown = true;
    }).keyup(function (e) {
        if (e.keyCode == ctrlKey) ctrlDown = false;
    });

    $(document).keydown(function (e) {
        if (e.keyCode == shiftkey) shiftDown = true;
    }).keyup(function (e) {
        if (e.keyCode == shiftkey) shiftDown = false;
    });

    $(".select2").select2();

    var apepat = $("input[name=ApellidoPaterno]");
    var apemat = $("input[name=ApellidoMaterno]");
    var nombre = $("input[name=Nombre]");
    var replegal = $("input[name=RepresentanteLegal]");
    var percontacto = $("input[name=PersonaAtencion]");
    var carcontactos = $("input[name=CargoPersonaAtencion]");

    var ruc = $("input[name=Ruc]");
    var dni = $("input[name=RepresentanteNumeroDocumento]");
    var tel = $("input[name=Telefono]");
    var cel = $("input[name=Celular]");
    var idsap = $("input[name=IdSap]");

    ruc.on("keydown", function (event) {
        if (ctrlDown && (event.keyCode == vKey || event.keyCode == cKey || event.keyCode == xKey)) {
            return true;
        } else {
            onlyNumbers(event);
        }
    });

    tel.on("keydown", function (event) {
        if (ctrlDown && (event.keyCode == vKey || event.keyCode == cKey || event.keyCode == xKey)) {
            return true;
        } else {
            onlyNumbers(event);
        }
    });

    cel.on("keydown", function (event) {
        if (ctrlDown && (event.keyCode == vKey || event.keyCode == cKey || event.keyCode == xKey)) {
            return true;
        } else {
            onlyNumbers(event);
        }
    });

    ruc.on("keyup", function () {
        maxInput($(this), 11);
        copyPage($(this));
    });

    dni.on("keyup", function () {
        maxInput($(this), 15);
    });

    tel.on("keyup", function () {
        maxInput($(this), 9);
        copyPage($(this));
    });

    cel.on("keyup", function () {
        maxInput($(this), 9);
        copyPage($(this));
    });

    idsap.on("keyup", function () {
        maxInput($(this), 45);
    });

    apepat.on("keydown", function (event) {
        if (shiftDown && (numberKey.indexOf(event.keyCode) == (-1))) {
            return true;
        } else {
            onlyLeters(event);
        }
    });

    apemat.on("keydown", function (event) {
        if (shiftDown && (numberKey.indexOf(event.keyCode) == (-1))) {
            return true;
        } else {
            onlyLeters(event);
        }
    });

    nombre.on("keydown", function (event) {
        if (shiftDown && (numberKey.indexOf(event.keyCode) == (-1))) {
            return true;
        } else {
            onlyLeters(event);
        }
    });

    replegal.on("keydown", function (event) {
        if (shiftDown && (numberKey.indexOf(event.keyCode) == (-1))) {
            return true;
        } else {
            onlyLeters(event);
        }
    });

    percontacto.on("keydown", function (event) {
        if (shiftDown && (numberKey.indexOf(event.keyCode) == (-1))) {
            return true;
        } else {
            onlyLeters(event);
        }
    });

    carcontactos.on("keydown", function (event) {
        if (shiftDown && (numberKey.indexOf(event.keyCode) == (-1))) {
            return true;
        } else {
            onlyLeters(event);
        }
    });

    x = $("[name='id']").val();//verifica si hay un id de empres en el input id
    if(x !== '') {
        var variable = $('body').find('ul').hasClass("error-logo");
        if ($('#list-logo tr').length == 2 && !variable) {
            $("#list-logo tr:last").find('input').val('null');
        }

        var variable2 = $('body').find('ul').hasClass("error-logo-site");
        if ($('#list-logo-site tr').length == 2 && !variable) {
            $("#list-logo-site tr:last").find('input').val('null');
        }
    }
});

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

///logo imagen
$('#btn-logo').on('click', function () {
    var file = $("#input-logo")[0].files[0];
    var fileName = file.name;
    var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
    var fileSize = file.size;
    var name = null;
    if ($('#list-logo tr').length == 2) {
        var ultimafila = $("#list-logo tr:last");
        name = ultimafila.find('input').val();
        $("#list-logo tr:last").remove();
    }
    if ($('#list-logo tr').length == 1) {
        if (fileSize < 2097152) {
            if (isImage(fileExtension)) {
                $('#download-logo').removeClass('hidden');
                $('#error-logo').empty();
                var data = new FormData();
                data.append('val', file);
                data.append('ext', fileExtension);
                data.append('name', name);
                data.append('site', 'false');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '/empresa/savelogo',
                    data: data,
                    //necesario para subir archivos via ajax
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.response) {
                            CreatelistaLogo(data.name, "list-logo", 'Logo');
                            $('#download-logo').addClass('hidden');
                            $('.error-logo').empty();
                        }
                    },
                    error: function () {
                        console.log('error');
                        $('#download-logo').addClass('hidden');
                    }
                });
            } else {
                $('.error-logo').append(
                    '<li >El archivo ' + fileName
                    + ' tiene una extensión incorrecta. Solo se admiten imagenes jpg y png.</li>');
            }
        } else {
            $('.error-logo').append('<li >El tamaño máximo permitido para el archivo es de 2MB.</li>');
        }
    }
});
///logo sitio
$('#btn-logo-site').on('click', function () {
    var file = $("#input-logo-site")[0].files[0];
    var fileName = file.name;
    var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
    var fileSize = file.size;
    var name = null;
    if ($('#list-logo-site tr').length == 2) {
        var ultimafila = $("#list-logo-site tr:last");
        name = ultimafila.find('input').val();
        $("#list-logo-site tr:last").remove();
    }
    if ($('#list-logo-site tr').length == 1) {
        if (fileSize < 2097152) {
            if (isImage(fileExtension)) {
                $('#download-logo-site').removeClass('hidden');
                $('#error-logo-site').empty();
                var data = new FormData();
                data.append('val', file);
                data.append('ext', fileExtension);
                data.append('name', name);
                data.append('site', 'true');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '/empresa/savelogo',
                    data: data,
                    //necesario para subir archivos via ajax
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.response) {
                            CreatelistaLogo(data.name, 'list-logo-site', 'Logo_sitio');
                            $('#download-logo-site').addClass('hidden');
                            $('.error-logo-site').empty();
                        }
                    },
                    error: function () {
                        console.log('error');
                        $('#download-logo-site').addClass('hidden');
                    }
                });
            } else {
                $('.error-logo-site').append(
                    '<li >El archivo ' + fileName
                    + ' tiene una extensión incorrecta. Solo se admiten imagenes jpg y png.</li>');
            }
        } else {
            $('.error-logo-site').append('<li >El tamaño máximo permitido para el archivo es de 2MB.</li>');
        }
    }
});

function CreatelistaLogo(name, id_list, input) {
    var table = document.getElementById(id_list);
    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var index = Math.floor((Math.random() * 100) + 1);
    row.setAttribute('id', index);
    cell1.innerHTML = '<a id="single_image" href="/elements/empresa/' + name + '?' + index + '"><img style="height: 50px;width: '
        + '100px;" src="/elements/empresa/' + name + '?' + index + '"></a><input type="hidden" name="' + input + '" value="' + name + '">';
    name = "'" + name + "'";
    cell2.innerHTML = '<a onclick="deleteLogo(' + index + ',' + name + ')" class="btn btn-danger">Eliminar</a>';
}

function deleteLogo(index, name) {
    var fileExtension = name.substring(name.lastIndexOf('.') + 1);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/empresa/deleteLogo',
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

function confimacion(index, name) {
    if (confirm('!El Logo se eliminara inmediatamente¡. ¿esta seguro de seguir?')) {
        deleteLogo(index, name);
    }
}