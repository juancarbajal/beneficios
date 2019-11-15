/**
 * Created by marlo on 12/08/16.
 */
$(document).ready(function () {
    $('#flyoutMovil').on('hide.bs.modal', function (e) {
        closeModal();
    });
    $('#modalBienvenida').on('hide.bs.modal', function (e) {
        closeModal();
        $('#flyoutMovilPremios').modal('show');
    });
    $('#flyoutMovilPremios').on('hide.bs.modal', function (e) {
        $.ajax({
            type: "GET",
            url: "/premios/closeFlyout",
            success: function (data) {
                console.log('ok!!');
            },
            error: function () {
                console.log('error!!');
            }
        });
    });

    function closeModal() {
        $.ajax({
            type: "GET",
            url: "/puntos/closeModal",
            success: function (data) {
                console.log('ok!!');
            },
            error: function () {
                console.log('error!!');
            }
        });
        $.ajax({
            type: "GET",
            url: "/premios/closeModal",
            success: function (data) {
                console.log('ok!!');
            },
            error: function () {
                console.log('error!!');
            }
        });
    }
});
