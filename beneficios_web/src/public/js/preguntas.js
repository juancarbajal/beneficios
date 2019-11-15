var elements = [];
var subDominio;
var empresas_e = [];

$(document).ready(function () {
    obj.validateEmailCupon();
    validarPreguntas.validateRespuesta();
    validarEnvio.validateEmailCuponPuntos();
    validarEnvioPremios.validateEmailCuponPremios();

    $("#activeChek").prop("checked", true);
    $(".cnt-terminos-info").show();

    $.validator.addMethod(
        "weekText",
        function (val) {
            var strings = elements;
            var flag = false;
            for (var i = 0; i < strings.length; i++) {
                if (val.toLowerCase() == strings[i].toLowerCase()) {
                    flag = true;
                }
            }
            return flag;
        }, "Por favor, seleccione una opción válida");

    $.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please check your input."
    );

    $("#emailCupon").submit(function (e) {
        e.preventDefault();
        $('#send_coupon').attr('disabled', 'disabled');

        var email = $('#email');
        var ofertaID = $('#idOferta');
        var empresaID = $('#idEmpresa');
        var clienteID = $('#idCliente');
        var slugcat = $('#slug_cat');
        var atributo = $('#atributo');
        var valid = false;

        var emailExpre = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        var slugExpre = /^[a-z\-]+$/;
        var degit = /^\d+$/;

        if (emailExpre.test(email.val()) && slugExpre.test(slugcat.val()) && degit.test(ofertaID.val()) &&
            degit.test(empresaID.val())) {
            if ($.inArray(subDominio, empresas_e) > -1) {
                valid = true;
            } else if (degit.test(clienteID.val())) {
                valid = true;
            }
        }

        if (valid) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "/home/envioCupon",
                data: {
                    email: email.val(),
                    idOferta: ofertaID.val(),
                    idEmpresa: empresaID.val(),
                    idCliente: clienteID.val(),
                    slug_cat: slugcat.val(),
                    atributo: atributo.val()
                },
                success: function (data) {
                    if (data.session) {
                        if (data.response) {
                            $('#enviarMail').modal('toggle');
                            $('#send_coupon').removeAttr('disabled');
                            if ($.inArray(subDominio, empresas_e) > -1) {
                                email.val('');
                            }
                            if (data.question == '') {
                                $('.questions').css('display', 'none');
                            } else {
                                $('.questions').css('display', '')
                                    .find('h5')
                                    .empty()
                                    .append(data.question["titulo"]);
                                $('#question_number').val(data.number);

                                var input = "";
                                var contenedor = $('div.preguntas');

                                switch (data.question["tipo_campo"]) {
                                    case "date":
                                        input = $('<select name="respuesta" id="respuesta" class="form-control"></select>');
                                        input.append($("<option>").attr('value', '').text("Seleccione..."));
                                        for (var i = 0; i < 90; i++) {
                                            input.append($("<option>")
                                                .attr('value', (new Date).getFullYear() - i)
                                                .text((new Date).getFullYear() - i));
                                        }
                                        var numero = (new Date).getFullYear() - 90;
                                        contenedor.html(input);
                                        contenedor.append('<div class="error-check"></div>');
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            min: numero,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe seleccionar un año válido",
                                                min: "El año seleccionado debe ser mayor a {0}"
                                            }
                                        });
                                        break;
                                    case "combo":
                                        input = $('<select name="respuesta" id="respuesta" class="form-control"></select>');
                                        input.append($("<option>").attr('value', '').text("Seleccione..."));

                                        $.each(data.question["value_combo"], function (index, value) {
                                            input.append($("<option>").attr('value', value).text(value));
                                            elements.push(value);
                                        });

                                        contenedor.html(input);
                                        contenedor.append('<div class="error-check"></div>');
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            weekText: true,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                weekText: "Por favor, seleccione una opción válida"
                                            }
                                        });
                                        break;
                                    case "numb":
                                        contenedor.html($('<input type="number" min="0" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe ingresar una cantidad válida"
                                            }
                                        });
                                        break;
                                    case "textnumb":
                                        contenedor.html($('<input type="text" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            maxlength: 9,
                                            minlength: 9,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe ingresar solo numeros",
                                                maxlength: "El número debe de tener 9 dígitos",
                                                minlength: "El número debe de tener 9 dígitos"
                                            }
                                        });
                                        break;
                                    case "text":
                                    default:
                                        contenedor.html($('<input type="text" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            regex: "^[a-zA-ZÑñÁáÉéÍíÓóÚú\\s]+$",
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                regex: "El campo solo acepta letras"
                                            }
                                        });
                                        break;
                                }
                            }
                            $('.modal-felicitaciones').modal('show');
                            setTimeout(function () {
                                $("body").addClass("modal-open");
                            }, 1000);
                        } else {
                            if (data.status == 404) {
                                $(location).attr("href", 404);
                            } else {
                                $('#enviarMail').modal('toggle');
                                $('.modal-error').modal('show');
                                $('#send_coupon').removeAttr('disabled');
                                setTimeout(function () {
                                    $("body").addClass("modal-open");
                                }, 1000);
                            }
                        }
                    } else {
                        window.location.href = "/login";
                    }
                },
                error: function () {
                    window.location.href = "/login";
                }
            });

        } else {
            $('#send_coupon').removeAttr('disabled');
        }
    });

    $("#emailCuponPuntos").submit(function (e) {
        e.preventDefault();
        var send_coupon = $('#send_coupon');
        send_coupon.attr('disabled', 'disabled');

        var email = $('#email_envio_puntos');
        var ofertaID = $('#idOferta');
        var empresaID = $('#idEmpresa');
        var clienteID = $('#idCliente');
        var slugcat = $('#slug_cat');
        var puntos = $('#puntos');
        var atributo = $('#atributo');
        var valid = false;

        var emailExpre = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        var slugExpre = /^[a-z\-]+$/;
        var degit = /^\d+$/;

        if (emailExpre.test(email.val()) && slugExpre.test(slugcat.val()) && degit.test(ofertaID.val()) &&
            degit.test(empresaID.val())) {
            if ($.inArray(subDominio, empresas_e) > -1) {
                valid = true;
            } else if (degit.test(clienteID.val())) {
                valid = true;
            }
        }

        var values = {};
        $.each($('.deliveryPuntos'), function (i, field) {
            values[field.name] = field.value;
        });

        if ($(this).valid() && valid) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "/puntos/envioCupon",
                data: {
                    email: email.val(),
                    idOferta: ofertaID.val(),
                    idEmpresa: empresaID.val(),
                    idCliente: clienteID.val(),
                    slug_cat: slugcat.val(),
                    puntos: puntos.val(),
                    atributo: atributo.val(),
                    delivery: values
                },
                success: function (data) {
                    if (data.session) {
                        if (data.response) {
                            $("#enviarPuntos").modal('hide');
                            send_coupon.removeAttr('disabled');

                            var saldo = $('#puntos-final');
                            var precioBeneficio = $('#precio-beneficio');
                            saldo.data("value", data.disponibles);
                            precioBeneficio.html(data.disponibles);
                            $('#cant-puntos').text(data.puntos + ' puntos');

                            var campoPuntos = $("#puntos");
                            campoPuntos.rules('remove');
                            campoPuntos.rules("add", {
                                required: true,
                                min: 1,
                                max: function () {
                                    var precio = parseInt($('#precio-final').data("value"));
                                    var puntos = parseInt($('#puntos-final').data("value"));

                                    if (precio < puntos) {
                                        return precio;
                                    } else if (precio > puntos) {
                                        return puntos;
                                    } else {
                                        return precio;
                                    }
                                },
                                messages: {
                                    required: "Debe ingresar una cantidad de puntos para usar",
                                    min: "Debe ingresar una cantidad válidad",
                                    max: ".Debe ingresar una cantidad menor o igual a sus puntos disponibles o al precio del cupón"
                                }
                            });

                            campoPuntos.keyup();

                            if ($.inArray(subDominio, empresas_e) > -1) {
                                email.val('');
                            }
                            if (data.question == '') {
                                $('.questions').css('display', 'none');
                            } else {
                                $('.questions').css('display', '')
                                    .find('h5')
                                    .empty()
                                    .append(data.question["titulo"]);
                                $('#question_number').val(data.number);

                                var input = "";
                                var contenedor = $('div.preguntas');

                                switch (data.question["tipo_campo"]) {
                                    case "date":
                                        input = $('<select name="respuesta" id="respuesta" class="form-control"></select>');
                                        input.append($("<option>").attr('value', '').text("Seleccione..."));
                                        for (var i = 0; i < 90; i++) {
                                            input.append($("<option>")
                                                .attr('value', (new Date).getFullYear() - i)
                                                .text((new Date).getFullYear() - i));
                                        }
                                        var numero = (new Date).getFullYear() - 90;
                                        contenedor.html(input);
                                        contenedor.append('<div class="error-check"></div>');
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            min: numero,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe seleccionar un año válido",
                                                min: "El año seleccionado debe ser mayor a {0}"
                                            }
                                        });
                                        break;
                                    case "combo":
                                        input = $('<select name="respuesta" id="respuesta" class="form-control"></select>');
                                        input.append($("<option>").attr('value', '').text("Seleccione..."));

                                        $.each(data.question["value_combo"], function (index, value) {
                                            input.append($("<option>").attr('value', value).text(value));
                                            elements.push(value);
                                        });

                                        contenedor.html(input);
                                        contenedor.append('<div class="error-check"></div>');
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            weekText: true,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                weekText: "Por favor, seleccione una opción válida"
                                            }
                                        });
                                        break;
                                    case "numb":
                                        contenedor.html($('<input type="number" min="0" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe ingresar una cantidad válida"
                                            }
                                        });
                                        break;
                                    case "textnumb":
                                        contenedor.html($('<input type="text" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            maxlength: 9,
                                            minlength: 9,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe ingresar solo numeros",
                                                maxlength: "El número debe de tener 9 dígitos",
                                                minlength: "El número debe de tener 9 dígitos"
                                            }
                                        });
                                        break;
                                    case "text":
                                    default:
                                        contenedor.html($('<input type="text" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            regex: "^[a-zA-ZÑñÁáÉéÍíÓóÚú\\s]+$",
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                regex: "El campo solo acepta letras"
                                            }
                                        });
                                        break;
                                }
                            }
                            $('.del-error-check').empty();
                            $('#modalFelicitacionesPuntos').modal('show');
                            setTimeout(function () {
                                $("body").addClass("modal-open");
                            }, 1000);
                        } else {
                            if (data.status == 404) {
                                $(location).attr("href", 404);
                            } else {
                                if (data.errorMessage != "") {
                                    $('#' + data.errorField).focus().closest('div.form-group')
                                        .find('.error-check').html(data.errorMessage);

                                    $('#send_coupon').removeAttr('disabled');
                                }
                                else {
                                    $('#modalErrorPuntos').modal('show');
                                    $('#send_coupon').removeAttr('disabled');
                                    setTimeout(function () {
                                        $("body").addClass("modal-open");
                                    }, 1000);
                                }
                            }
                        }
                    } else {
                        window.location.href = "/login";
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    if (XMLHttpRequest.status != 200 && textStatus == "parsererror") {
                        window.location.href = "/login";
                    } else {
                        console.log(XMLHttpRequest.responseText);
                    }
                }
            });
        } else {
            send_coupon.removeAttr('disabled');
        }
    });

    $('#puntos').keyup(function (e) {
        var campoPrecio = $('#precio-final');
        var campoPuntos = $('#puntos-final');
        var flagcheckboxMoney = $('#flagcheckboxMoney');

        var puntosUsados = parseInt($(this).val());
        var precio = parseInt(campoPrecio.data("value"));
        var puntos = parseInt(campoPuntos.data("value"));
        var flagcheckboxMoneyFinal = parseInt(flagcheckboxMoney.data("value"));

        if (precio - puntosUsados < 0 || puntos - puntosUsados < 0 || isNaN(precio) || isNaN(puntosUsados)) {
            e.preventDefault();
            campoPuntos.html(puntos);
        } else {
            var resultado = (flagcheckboxMoneyFinal) ? (precio - puntosUsados) + " puntos " :
                "S/ " + (precio - puntosUsados);
            campoPrecio.html(resultado);

            var resultado2 = (puntos - puntosUsados);
            campoPuntos.html(resultado2);
        }
        campoPuntos.val('');
    });


    $('.list-change a.btn-belcorp').click(function (e) {
        e.preventDefault();
        var value = $(this).data("value");

        var nombre = $(this).find('#nombreAtributo').val();
         var tituloCupon = $("#tituloCupon");
         tituloCupon.html(nombre);

        $('#elegirOpcion').modal('hide');
        $("#atributo").val(value);
		//alert(nombre);
        //$("#title").val(nombre);
        $("h1").append(nombre);
    });


    $('a.enviarMail').click(function (e) {
        var value = $(this).data("value");

        var nombre = $(this).find('#nombreAtributo').val();
        var tituloCupon = $("#tituloCupon");
        tituloCupon.html(nombre);

        $("#atributo").val(value);

        $('#elegirOpcion').modal('hide');
        $('#enviarMail').modal('show');
		$('#meTitle').text(nombre);
        setTimeout(function () {
            $("body").addClass("modal-open");
        }, 1000);
    });

    $("#emailCuponPremios").submit(function (e) {
        e.preventDefault();
        var send_coupon = $('#send_coupon');
        send_coupon.attr('disabled', 'disabled');

        var email = $('#email');
        var ofertaID = $('#idOferta');
        var empresaID = $('#idEmpresa');
        var clienteID = $('#idCliente');
        var slugcat = $('#slug_cat');
        var premios = $('#premios');
        var atributo = $('#atributo');
        var valid = false;

        var emailExpre = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        var slugExpre = /^[a-z\-]+$/;
        var degit = /^\d+$/;

        if (emailExpre.test(email.val()) && slugExpre.test(slugcat.val()) && degit.test(ofertaID.val()) &&
            degit.test(empresaID.val())) {
            if ($.inArray(subDominio, empresas_e) > -1) {
                valid = true;
            } else if (degit.test(clienteID.val())) {
                valid = true;
            }
        }

        if ($(this).valid() && valid) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "/premios/envioCupon",
                data: {
                    email: email.val(),
                    idOferta: ofertaID.val(),
                    idEmpresa: empresaID.val(),
                    idCliente: clienteID.val(),
                    slug_cat: slugcat.val(),
                    premios: premios.val(),
                    atributo: atributo.val()
                },
                success: function (data) {
                    if (data.session) {
                        if (data.response) {
                            $("#enviarPremios").modal('hide');
                            send_coupon.removeAttr('disabled');

                            var saldo = $('#premios-final');
                            var precioBeneficio = $('#precio-beneficio');
                            saldo.data("value", data.disponibles);
                            precioBeneficio.html(data.disponibles);
                            $('#cant-premios').text(data.premios + ' premios');

                            if ($.inArray(subDominio, empresas_e) > -1) {
                                email.val('');
                            }
                            if (data.question == '') {
                                $('.questions').css('display', 'none');
                            } else {
                                $('.questions').css('display', '')
                                    .find('h5')
                                    .empty()
                                    .append(data.question["titulo"]);
                                $('#question_number').val(data.number);

                                var input = "";
                                var contenedor = $('div.preguntas');

                                switch (data.question["tipo_campo"]) {
                                    case "date":
                                        input = $('<select name="respuesta" id="respuesta" class="form-control"></select>');
                                        input.append($("<option>").attr('value', '').text("Seleccione..."));
                                        for (var i = 0; i < 90; i++) {
                                            input.append($("<option>")
                                                .attr('value', (new Date).getFullYear() - i)
                                                .text((new Date).getFullYear() - i));
                                        }
                                        var numero = (new Date).getFullYear() - 90;
                                        contenedor.html(input);
                                        contenedor.append('<div class="error-check"></div>');
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            min: numero,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe seleccionar un año válido",
                                                min: "El año seleccionado debe ser mayor a {0}"
                                            }
                                        });
                                        break;
                                    case "combo":
                                        input = $('<select name="respuesta" id="respuesta" class="form-control"></select>');
                                        input.append($("<option>").attr('value', '').text("Seleccione..."));

                                        $.each(data.question["value_combo"], function (index, value) {
                                            input.append($("<option>").attr('value', value).text(value));
                                            elements.push(value);
                                        });

                                        contenedor.html(input);
                                        contenedor.append('<div class="error-check"></div>');
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            weekText: true,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                weekText: "Por favor, seleccione una opción válida"
                                            }
                                        });
                                        break;
                                    case "numb":
                                        contenedor.html($('<input type="number" min="0" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe ingresar una cantidad válida"
                                            }
                                        });
                                        break;
                                    case "textnumb":
                                        contenedor.html($('<input type="text" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            number: true,
                                            maxlength: 9,
                                            minlength: 9,
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                number: "Debe ingresar solo numeros",
                                                maxlength: "El número debe de tener 9 dígitos",
                                                minlength: "El número debe de tener 9 dígitos"
                                            }
                                        });
                                        break;
                                    case "text":
                                    default:
                                        contenedor.html($('<input type="text" name="respuesta" id="respuesta" class="form-control" ' +
                                            'placeholder="Ingrese aquí su respuesta" autocomplete="off"><div class="error-check"></div>'));
                                        $("#respuesta").rules('remove');
                                        $("#respuesta").rules("add", {
                                            required: true,
                                            regex: "^[a-zA-ZÑñÁáÉéÍíÓóÚú\\s]+$",
                                            messages: {
                                                required: "El campo no puede quedar vacío",
                                                regex: "El campo solo acepta letras"
                                            }
                                        });
                                        break;
                                }
                            }

                            $('#modalFelicitacionesPremios').modal('show');
                            setTimeout(function () {
                                $("body").addClass("modal-open");
                            }, 1000);
                        } else {
                            if (data.status == 404) {
                                $(location).attr("href", 404);
                            } else {
                                $("#enviarPremios").modal('hide');
                                $('#modalErrorPremios').modal('show');
                                $('#send_coupon').removeAttr('disabled');
                                setTimeout(function () {
                                    $("body").addClass("modal-open");
                                }, 1000);
                            }
                        }
                    } else {
                        window.location.href = "/login";
                    }
                },
                error: function () {
                    window.location.href = "/login";
                }
            });
        } else {
            send_coupon.removeAttr('disabled');
        }
    });

    $('#premios').keyup(function (e) {
        var campoPrecio = $('#precio-final');
        var campoPremios = $('#premios-final');

        var premiosUsados = parseInt($(this).val());
        var precio = parseInt(campoPrecio.data("value"));
        var premios = parseInt(campoPremios.data("value"));

        if (precio - premiosUsados < 0 || premios - premiosUsados < 0 || isNaN(precio) || isNaN(premiosUsados)) {
            e.preventDefault();
        } else {
            var resultado = "S/ " + (precio - premiosUsados);
            campoPrecio.html(resultado);

            var resultado2 = (premios - premiosUsados);
            campoPremios.html(resultado2);
        }
    });

    $('a.enviarPremios').click(function (e) {
        var value = $(this).data("value");
        var precio = $(this).find('#precioAtributo').val();
        var nombre = $(this).find('#nombreAtributo').val();

        var campoPrecio = $("#precio-final");
        campoPrecio.data("value", precio);
        campoPrecio.html("S/ " + precio);

        var precioVenta = $("#precio-venta");
        precioVenta.html("S/ " + precio);

        var tituloCupon = $("#tituloCupon");
        tituloCupon.html(nombre);

        $("#atributo").val(value);

        $('#elegirOpcion').modal('hide');
        $('#enviarPremios').modal('show');
        setTimeout(function () {
            $("body").addClass("modal-open");
        }, 500);
    });
});

var validarPreguntas = {
    validateRespuesta: function () {
        $("#formRespuesta").validate({
            errorPlacement: function (error, element) {
                error.insertAfter($(element).parents('.cnt-form-error').find('.error-check').html(error));
            },
            submitHandler: function (form) {
                var respuesta = $('#respuesta');
                var clienteID = $('#idCliente');
                var question = $('#question_number');
                $.post("/home/registrarRespuesta", {
                    answer: respuesta.val(),
                    client: clienteID.val(),
                    question: question.val()
                }, function (data) {
                    if (data.response) {
                        $('#listU').text(data.NomSession);
                        $('.modal-felicitaciones').modal('hide');
                        $('.modal-gracias').modal('show');
                        setTimeout(function () {
                            $("body").addClass("modal-open");
                        }, 1000);
                    }
                }, 'json');
            }
        });
    }
};

var validarEnvio = {
    validateEmailCuponPuntos: function () {
        $("#emailCuponPuntos").validate({
            rules: {
                email: {
                    required: true
                },
                terminos: {
                    required: true
                },
                puntos: {
                    required: true,

                    min:(parseInt($('#flagcheckboxTotalPuntos').data("value")))?parseInt($('#precio-final').data("value")):1,

                    max: function () {
                        var precio = parseInt($('#precio-final').data("value"));
                        var puntos = parseInt($('#puntos-final').data("value"));

                        var flagcheckboxTotalPuntos = parseInt($('#flagcheckboxTotalPuntos').data("value"));

                        if (flagcheckboxTotalPuntos) {
                            return precio;
                        }else {
                            if (precio < puntos) {
                                return precio;
                            } else if (precio > puntos) {
                                return puntos;
                            } else {
                                return precio;
                            }
                        }
                    }
                }
            },

            // Specify the validation error messages
            messages: {
                email: {
                    required: "Debe ingresar un email",
                    email: "Ingrese un correo válido"
                },
                terminos: {
                    required: "Debe aceptar las condiciones, términos y políticas de uso"
                },

                puntos: {
                    required: "Debe ingresar una cantidad de puntos para usar",
                    min:(parseInt($('#flagcheckboxTotalPuntos').data("value")))?"Debe ingresar una cantidad de puntos igual al precio del cupón":"Debe ingresar una cantidad válida",
                    max: (parseInt($('#flagcheckboxTotalPuntos').data("value")))?"Debe ingresar una cantidad de puntos igual al precio del cupón":"Debe ingresar una cantidad menor o igual a sus puntos disponibles o al precio del cupón"
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter($(element).parents('.cnt-form-error').find('.error-check').html(error));
            }
        });
    }
};


var validarEnvioPremios = {
    validateEmailCuponPremios: function () {
        $("#emailCuponPremios").validate({
            rules: {
                email: {
                    required: true
                },
                terminos: {
                    required: true
                },
                premios: {
                    required: true,
                    min: 1,
                    max: function () {
                        var precio = parseInt($('#precio-final').data("value"));
                        var premios = parseInt($('#premios-final').data("value"));

                        if (precio < premios) {
                            return precio;
                        } else if (precio > premios) {
                            return premios;
                        } else {
                            return precio;
                        }
                    }
                }
            },

            // Specify the validation error messages
            messages: {
                email: {
                    required: "Debe ingresar un email",
                    email: "Ingrese un correo válido"
                },
                terminos: {
                    required: "Debe aceptar las condiciones, términos y políticas de uso"
                },
                premios: {
                    required: "Debe ingresar una cantidad de premios para usar",
                    min: "Debe ingresar una cantidad válida",
                    max: "Debe ingresar una cantidad menor o igual a sus premios disponibles o al precio del cupón"
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter($(element).parents('.cnt-form-error').find('.error-check').html(error));
            }
        });
    }
};

function getSubDominio(dominio) {
    var URLactual = window.location.hostname;
    subDominio = URLactual.split(dominio)[0];
    subDominio = subDominio.replace("www.", "");
    subDominio = subDominio.substring(0, subDominio.length - 1);
}
