$(document).ready(function () {
    $(".textarea").wysihtml5({
        showToolbarAfterInit: false
    });

    $("#addFormCupon").submit(function (e) {
        e.preventDefault();
        var form = $('#addFormCupon');
        var postData = form.serializeArray();
        var formURL = form.attr("action");

        $.ajax({
            url: formURL,
            type: "POST",
            data: postData,
            dataType: "json"
        })
            .done(function (data) {
                if (data.response == true) {
                    $('#modalAlertaLabel').text(data.tittle);
                    $('#message-modal').text(data.message);
                    $('#modalAlerta').modal('show');
                    $('.box-footer').empty();
                    clear();
                } else {
                    if (data.condition == true) {
                        $('#message-redimir').text(data.message);
                        $('#modalRedimir').modal('show');
                    } else {
                        $('#modalAlertaLabel').text(data.tittle);
                        $('#message-modal').text(data.message);
                        $('#modalAlerta').modal('show');
                    }
                }
                console.log("La solicitud se ha completado correctamente.");
            })
            .fail(function (jqXHR, textStatus) {
                if (console && console.log) {
                    console.log("La solicitud a fallado: " + textStatus);
                }
            });
    });

    $("#redimir-button").click(function () {
        var url = '/cupon-premios/redimir';
        var form = $('#addFormCupon');
        var postData = form.serializeArray();

        $.ajax({
            url: url,
            type: "POST",
            data: postData,
            dataType: "json"
        })
            .done(function (data) {
                if (console && console.log) {
                    $('#modalAlertaLabel').text(data.tittle);
                    $('#message-modal').text(data.message);
                    $('#modalAlerta').modal('show');
                    $('#modalRedimir').modal('hide');
                    $('.box-footer').empty();
                    clear();
                }
            })
            .fail(function (jqXHR, textStatus) {
                if (console && console.log) {
                    console.log("La solicitud a fallado: " + textStatus);
                }
            });
    });

    $("#limpiar").click(function () {
        clear();
    });
});

function clear() {
    $(".form-cupon").val('');
}