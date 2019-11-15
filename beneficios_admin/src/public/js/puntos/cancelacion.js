/**
 * Created by luisvar on 21/07/16.
 */

$(function () {
    $(".select2").select2({
        language: 'es'
    });

    $(".enviar").prop("disabled", true);

    $('#empresa-cli').change(function () {
        var empresa = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#campanias").empty();
        $("#segmentos").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/cancelar-puntos/getDataEmpresa',
            data: {id: empresa, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#campanias").select2({
                        language: 'es',
                        data: data.campanias,
                        placeholder: "Seleccione...",
                        allowClear: true
                    }).val("").trigger("change");

                    $("#segmentos").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione...",
                        allowClear: true
                    });
                } else {
                    $("#campanias").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione...",
                        allowClear: true
                    });

                    $("#segmentos").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione...",
                        allowClear: true
                    });
                }
            }
        });
    });

    $('#campanias').change(function () {
        var campania = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#segmentos").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/cancelar-puntos/getDataSegmentos',
            data: {id: campania, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $("#segmentos").select2({
                        language: 'es',
                        placeholder: "Seleccione...",
                        allowClear: true,
                        data: data.segmentos
                    }).val("").trigger("change");
                } else {
                    $("#segmentos").select2({
                        language: 'es',
                        data: [],
                        placeholder: "Seleccione...",
                        allowClear: true
                    });
                }
            }
        });
    });
});

$(document).ready(function () {
    $('#allCheck').change(function () {
        if ($(this).is(":checked")) {
            $('#asignados').find('input[type=checkbox]').each(function () {
                $(this).prop("checked", true);
            });
            $(".enviar").prop("disabled", false);
        } else {
            $('#asignados').find('input[type=checkbox]').each(function () {
                $(this).prop("checked", false);
            });
            $(".enviar").prop("disabled", true);
        }
    });

    $('#resetForm').click(function () {
        $('#asignados').find('input[type=checkbox]').each(function () {
            $(this).prop("checked", false);
        });
        $(".enviar").prop("disabled", true);
    });

    $(".delete").click(function () {
        if ($(this).is(":checked")) {
            $(this).prop("checked", true);
            $(".enviar").prop("disabled", false);
        } else {
            var count = 0;

            $(this).prop("checked", false);
            $('#asignados').find('input[type=checkbox]').each(function () {
                if ($(this).is(":checked")) {
                    count++;
                }
            });

            if (count == 0) {
                $(".enviar").prop("disabled", true);
            } else {
                $(".enviar").prop("disabled", false);
            }
        }
    });

    $("#submitDesactivar").click(function (e) {
        var result = confirm('¿Está seguro que desea Desactivar a los usuarios seleccionados?');
        if (result) {
            $('input:hidden[name=action]').val('desactivar');
            $("#eliminarForm").submit();
        } else {
            return false;
        }
        e.preventDefault();
    });

    $("#submitReactivar").click(function (e) {
        var result = confirm('¿Está seguro que desea Reactivar a los usuarios seleccionados?');
        if (result) {
            $('input:hidden[name=action]').val('reactivar');
            $("#eliminarForm").submit();
        } else {
            return false;
        }
        e.preventDefault();
    });

    $("#submitEliminar").click(function (e) {
        $('#modalEliminar').modal('show');
        e.preventDefault();
    });

    $("#finalizarEliminado").click(function () {
        var comentario = $("#razonEliminado").val();
        $('input:hidden[name=action]').val('eliminar');
        var input = $("<input>")
            .attr("type", "hidden")
            .attr("name", "comment").val(comentario);
        $('#eliminarForm').append($(input));
        $("#eliminarForm").submit();
    });
});
