/**
 * Created by marlo on 12/10/15.
 */
var result = null;
var serch = '';

$(function () {
    $("#search").autocomplete({
        minLength: 3, //le indicamos que busque a partir de haber escrito dos o mas caracteres en el input

        source: function (request, response) {
            var id = $('#ubigeo_id').val();
            serch = $("#search").val();
            $.get("/resultado/home/ofertaName", {
                id: id,
                val: serch
            }, function (data) {
                response(data.data);
                result = !!data.result;
            }, 'json');
        },
        open: function (event, ui) {
            $('#oferta_id').val('0');
        },
        select: function (event, ui) {
            var id = ui.item.id;
            var tipo = ui.item.tipo;
            serch = $('#search').val(ui.item.name);
            $('#oferta_id').val(id);
            $('#tipoOferta').val(tipo);
            $('#form-search').submit();
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        var regexp;
        var cadena = item.value;
        var cadenabase = $.ui.autocomplete.escapeRegex(this.term);
        var datos = cadenabase.split(" ");
        $.each(datos, function (index, value) {
            value = value + ' ';
            regexp = regexp + "(?![^&;]+;)(?!<[^<>]*)(" + value + ")(?![^<>]*>)(?![^&;]+;)|";
        });
        regexp = regexp.substr(9, regexp.length - 1);
        var cadenaNegrita = cadena.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + cadenabase + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>");
        cadenaNegrita = ucWords(cadenaNegrita);
        var width = $("#search").width() + 65;
        return $("<li style='width: "+width+"px;'>")
            .append(cadenaNegrita)
            .appendTo(ul);
    };
});
/*Mayusculas las primeras letras*/
function ucWords(string) {
    var arrayWords;
    var returnString = "";
    var len;
    arrayWords = string.split(" ");
    len = arrayWords.length;
    for (var i = 0; i < len; i++) {
        if (i != (len - 1)) {
            returnString = returnString + ucFirst(arrayWords[i]) + " ";
        }
        else {
            returnString = returnString + ucFirst(arrayWords[i]);
        }
    }
    return returnString;
}
function ucFirst(string) {
    return string.substr(0, 1).toUpperCase() + string.substr(1, string.length).toLowerCase();
}

$(document).ready(function () {
    serch = $('#search').val();
    var ul = $('#listubic');
    ul.empty();
    ul.append('<span></span>');

    $.get("/resultado/home/ofertaUbigeo", function (data) {
        $.each(data.data, function (index, value) {
            ul.append('<li><a class="ubic" href="#" onclick="enviarIdUbigeo(' + index + ');">' + value + '</a></li>');
        });
    }, 'json');
});

function enviarIdUbigeo(index) {
    $('#hidden-ubic').val(index);
    $('#form-ubic').submit();
}

$("#form-search").submit(function (event) {
    if (serch == '') {
        $('.modal-message').modal('show');
        $('#search').val('');
        event.preventDefault();
    } else if (result == null) {
        event.preventDefault();
    } else if (!result) {
        $('.modal-message').modal('show');
        $('#search').val('');
        event.preventDefault();
    }
});

$('#search').keyup(function (e) {
    if (e.keyCode != 13) {
        result = null;
    }
    serch = $("#search").val();
});
