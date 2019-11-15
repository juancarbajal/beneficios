/**
 * Created by marlo on 23/12/15.
 */
var result = null;
var serch = '';

$(document).ready(function () {
    serch = $('#search').val();
    var ul_mobile = $('#listubic-mobile');
    ul_mobile.empty();

    $.get("/resultado/home/ofertaUbigeo", function (data) {
        $.each(data.data, function (index, value) {
            ul_mobile.append('<li><a href="#" onclick="enviarIdUbigeo(' + index + ');">' + value +
                '<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></a></li>');
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

$("#search").keyup(function (e) {
    result = null;
    var id = $('#ubigeo_id').val();
    serch = $("#search").val();

    $.get("/resultado/home/ofertaName", {
        id: id,
        val: serch
    }, function (data) {
        result = !!data.result;
    }, 'json');
    if (e.keyCode == 13 && result) {
        $('#form-search').submit();
    }
});
