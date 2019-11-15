$(function () {
    $(".textarea").wysihtml5({
        showToolbarAfterInit: false
    });

    $("#changeCupon").submit(function (e) {
        e.preventDefault();
        $('#adminModal').modal('show');
    });

    $('#estado-cupon').change(function (e) {
        var estado = $(this).val();
        $('#span_helper').text(estado);

    });

    $("#redimir-button").click(function (e) {
        e.preventDefault();
        var comentario = $('input[name=Comentarios]').val();
        if ($.trim(comentario) != "") {
            var form = $('#changeCupon');
            var postData = form.serializeArray();
            var formURL = form.attr("action");

            $.ajax({
                url: formURL,
                type: "POST",
                data: postData,
                dataType: "json"
            })
                .done(function (data) {
                    if (console && console.log) {
                        if (data.response == true) {
                            $('#modalAlertaLabel').text(data.tittle);
                            $('#message-modal').text(data.message);
                            $('#modalAlerta').modal('show');
                            $('#adminModal').modal('hide');
                            $('.box-footer').empty();
                            clear();
                        }
                        console.log("La solicitud se ha completado correctamente.");
                    }
                })
                .fail(function (jqXHR, textStatus) {
                    if (console && console.log) {
                        console.log("La solicitud a fallado: " + textStatus);
                    }
                });
        } else {
            $("#adminModal").modal('hide');
            $("#modalComentario").modal('show');
        }

    });

    $("#limpiar").click(function () {
        clear();
    });
});

function clear() {
    $(".form-cupon").val('');
}