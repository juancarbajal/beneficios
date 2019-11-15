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


        var comentario_uno = $('input[name=comentario_uno]').val();

        var comentario_dos = $('input[name=comentario_dos]').val();

        var cm1 = true;
        var cm2 = true;

        if ($.trim(comentario_uno) != "") {
            if ($.trim(comentario_uno).length > 15) {
                cm1 = false;
                $("#adminModal").modal('hide');
                $("#modalComentarioAlter").modal('show');
            }
        }
        if ($.trim(comentario_dos) != "") {
            if ($.trim(comentario_dos).length > 15) {
                cm2 = false;
                $("#adminModal").modal('hide');
                $("#modalComentarioAlter").modal('show');
            }
        }

        if (cm1 && cm2){
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
    }
    });

    $("#limpiar").click(function () {
        clear();
    });
});

function clear() {
    $(".form-cupon").val('');
}