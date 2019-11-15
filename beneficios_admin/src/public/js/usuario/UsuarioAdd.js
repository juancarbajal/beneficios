/**
 * Created by marlo on 08/09/15.
 */
document.write("<script type='text/javascript' src='../../js/validations.js'></script>");

$(function () {
    var dni = $("input[name=NumeroDocumento]");
    var nom = $("input[name=Nombres]");
    var ape = $("input[name=Apellidos]");

    $(".select2").select2();

    nom.on("keydown", function (event) {
        onlyLeters(event);
    });

    ape.on("keydown", function (event) {
        onlyLeters(event);
    });
    dni.on("keyup", function () {
        maxInput($(this), 15)
    });

    $('#tipusu').trigger('change');
});

$('#tipusu').on('change', function () {
    var selectedValue = $(this).find('option:selected').val();
    var selectedText = $(this).find('option:selected').text();

    if (selectedValue == 6 || selectedValue == 7 ||selectedValue == 8) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/usuario/getEmpresas',
            data: {value: selectedValue, text: selectedText},
            success: function (data) {
                var combo = $('#empresa');
                combo.empty();
                $.each(data.empresas, function (value, text) {
                    $('#empresa').append('<option value="' + value + '">' + text + '</option>');
                });

                combo.select2();
                
                var hidden = $("#emp_val");
                if ((hidden.length > 0)) {
                    combo.val(hidden.val()).trigger("change");
                }
            }
        });
        $('#empresa').prop("disabled", false);
    } else {
        $('#empresa').prop("disabled", true);
    }
});