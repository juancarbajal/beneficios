/**
 * Created by marlo on 28/10/16.
 */
$(document).ready(function () {
    $("#flyout span").click(function () {
        closeModal();
        $("#flyout").remove();
    });

    $("#flyoutPremios span").click(function () {
        closeFlyout();
        $("#flyoutPremios").remove();
    });

    $(".close-flyout").click(function () {
        closeModal();
    });

    $('#modalBienvenida').on('hide.bs.modal', function (e) {
        $("#flyoutPremios").removeClass('hidden');
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

    function closeFlyout() {
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
    }
});