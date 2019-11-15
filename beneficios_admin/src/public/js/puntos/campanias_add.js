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

    var tipo_segmento = $('#tipo_segmento');
    tipo_segmento.trigger('change');

    if ($("input[name=id]").length) {
        var div_content = $('.segment-content-classic');
        div_content.hide();
        div_content.attr("disabled", true);

        var segmento = tipo_segmento.val();
        if (segmento === "Personalizado") {
            $("#addSegment").remove();
        }
    }

    $('#updateComment').hide();
});

$(document).ready(function () {
    $('#tipo_segmento').change(function () {
        var tipo = $(this).val();
        var div_content = $('.segment-content-classic');
        if (tipo === "Clasico") {
            div_content.show();
            div_content.attr("disabled", false);
        } else if (tipo === "Personalizado") {
            div_content.hide();
            div_content.attr("disabled", true);
        }
    });

    $("#addSegment").click(function () {
        var div_content = $('.segment-content-classic');
        div_content.show();
        div_content.attr("disabled", false);
        $(this).hide();
    });

    $("#finalizarEliminado").click(function () {
        var csrf = $('input:hidden[name=csrf]').val();
        var id = $('input:hidden[name=id]').val();
        var comment = $("#razonEliminado").val();
        var tipo = $("#eliminarElemento").val();
        var url = "";
        var retornar = "";

        if (tipo == "Campaña") {
            url = "/campanias-puntos/delete";
            retornar = "/campanias-puntos";
        } else if (tipo == "Segmento") {
            var categoria = $('input:hidden[name=campania]').val();
            url = "/campanias-puntos/deleteSegmento";
            retornar = "/campanias-puntos/edit/" + categoria;
        }

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            data: {id: id, csrf: csrf, comment: comment},
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                console.log(data);
                if (data.response) {
                    window.location = retornar;
                    return false;
                } else {
                    $("#razonEliminado").val('');
                    $("#modalComentario").modal('hide');
                }
            }
        });
    });

    $("#editComment").click(function () {
        var contenido = $('#contentComment');
        var comentario = $.trim(contenido.text());
        contenido.hide();
        $('#updateComment').val(comentario).show().focus();
    });
});

$(document).on('click', '.addfields', function () {
    classicFields();
});

$(document).on('click', '.delfields', function () {
    $(this).closest('div.data-list').remove();
    $('.classicSub').trigger('change');
});

$(document).on('change', 'input.classicPers, input.classicPtos', function () {
    var contenedor = $(this).closest('div.data-list');
    var puntos = $(contenedor).find('input.classicPtos').val();
    var personas = $(contenedor).find('input.classicPers').val();
    var subtotal = Number(puntos * personas);
    $(contenedor).find('input.classicSub').val(subtotal).trigger('change');
});

function classicFields() {
    var fila = $('<div class="form-group data-list">');
    var count = Math.round(Math.random() * 100000);
    var contenido = '<div>' +
        '<div class="col-md-2"><input title="nombre del segmento" class="form-control classicSeg" name="classicSeg[' + count + ']" type="text">' +
        '<div class="classicSeg error"></div></div>' +
        '<div class="col-md-2"><input title="puntos por usuario" class="form-control classicPtos" name="classicPtos[' + count + ']" type="text">' +
        '<div class="classicPtos error"></div></div>' +
        '<div class="col-md-2"><input title="cantidad de usuario" class="form-control classicPers" name="classicPers[' + count + ']" type="text">' +
        '<div class="classicPers error"></div></div>' +
        '<div class="col-md-2"><input title="presupuesto" class="form-control classicSub" type="text" disabled></div>' +
        '<div class="col-md-2">' +
        '<textarea title="comentario" class="form-control classicComment" name="classicComment[' + count + ']"></textarea>' +
        '<div class="classicComment error"></div></div>' +
        '<div class="col-md-2">' +
        '<button class="addfields btn btn-default" type="button">&#43;</button>' +
        '<button class="delfields btn btn-default" type="button">&#45;</button>' +
        '</div>' +
        '<div class="col-md-2 asignaciones"></div></div>' +
        '<div class="row"><div class="col-md-2"></div><div class="col-md-10"><div class="classicAsig error"></div></div></div>';
    fila.append(contenido);
    $('.segment-content-classic').find('div.data-footer').before(fila);
}
