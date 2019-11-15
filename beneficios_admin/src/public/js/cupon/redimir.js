$(function () {
    $("#addform").submit(function (e) {
        e.preventDefault(); //STOP default action
        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
        var id = $('input[name="id"]').val();
        $.ajax({
                url: formURL,
                type: "POST",
                data: postData,
                dataType: "json"
            })
            .done(function (data, textStatus, jqXHR) {
                if (console && console.log) {
                    if (data.response == true) {
                        $('#myModalLabel').text(data.tittle);
                        $('#message-modal').text(data.message);
                        $('#myModal-Alerta').modal('show');
                        clear();
                    } else {
                        if (data.condition == true) {
                            $('#iddata').val(id);
                            $('#message-redimir').text(data.message);
                            $('#myModal').modal('show');
                        } else {
                            $('#myModalLabel').text(data.tittle);
                            $('#message-modal').text(data.message);
                            $('#myModal-Alerta').modal('show');
                        }
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

    $("#redimir-button").click(function () {
        var postData = $('input[name="iddata"]').val();
        $.ajax({
                url: 'cupon/redimir',
                type: "POST",
                data: {iddata: postData},
                dataType: "json"
            })
            .done(function (data, textStatus, jqXHR) {
                if (console && console.log) {
                    $('#myModalLabel').text(data.tittle);
                    $('#message-modal').text(data.message);
                    $('#myModal-Alerta').modal('show');
                    $('#myModal').modal('hide');
                    clear();
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                if (console && console.log) {
                    console.log("La solicitud a fallado: " + textStatus);
                }
            });
    });

    $("#limpiar").click(function (e) {
        clear();
    });
});

function clear() {
    $(".form-cupon").val('');
}