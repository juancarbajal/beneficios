var boolsubdominio = false;
var subDominio;
var URLactual;
var empresas_e = [];
var hasEmpresaStatus = false;

$(document).keypress(function (e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        return false;
    }
});

$(document).ready(function () {
    URLactual = window.location.hostname;

    obj.loginCnt();

    $("#loginForm").validate({
        rules: {
            dni: {required: true, minlength: 5, alphanumeric: true},
            empresa_id: {required: true, digits: true},
            email: {regex: /^[a-zA-Z0-9+]+(?:([\.\_\-][a-zA-Z0-9+]+))*@(?:([a-zA-Z0-9]+(\-[a-zA-Z0-9]+)*)\.)+[a-zA-Z]+$/}
        },
        messages: {
            dni: {
                required: "Lo sentimos, pero este documento no está registrado",
                minlength: "El número de documento ingresado debe tener mínimo 5 dígitos",
                alphanumeric: "El Documento ingresado no tiene el formato correcto"
            },
            empresa_id: {
                required: "Debe seleccionar una empresa afiliada",
                digits: "No es una empresa válida"
            },
            email: {
                regex: "Correo no válido"
            }

        },
        errorPlacement: function (e, i) {
            e.insertAfter($(i).parents(".form-group").find(".cnt-error").html(e))
        }
    });

    $.validator.addMethod("alphanumeric", function (value, element) {
        return this.optional(element) || /^[a-z0-9]+$/i.test(value);
    }, "Username must contain only letters, numbers, or dashes.");

    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please check your input."
    );

    $('#empresa').on('change', function () {
        $('#empresa_id').val($('#empresa').find('option:selected').val());
    });

    $('#NumeroDocumento').on('keyup', function () {
        var inputDocumento = $('#NumeroDocumento');
        var selectEmpresa = $('#empresa');

        selectEmpresa.empty();
        selectEmpresa.css("display", 'none');
        selectEmpresa.removeClass("error");
        selectEmpresa.removeClass("has-error");
        /*selectEmpresa.rules("remove", "required");*/
        inputDocumento.removeClass("error");
        inputDocumento.removeClass("has-error");

        $('#empresa_id').val('');
        $('.validate-dni').find("label").remove();
        $('.has-empresa').find('label').remove();

        hasEmpresaStatus = false;
    });
});


$("#loginForm").submit(function (event) {
    event.preventDefault();
    var self = this;

    var inputDocumento = $('#NumeroDocumento');
    var selectEmpresa = $('#empresa');
    var inputEmpresa = $('#empresa_id');
    var divDocumento = $('div.validate-dni');
    var documento = inputDocumento.val();

    if (boolsubdominio && documento.length >= 5 && !inputDocumento.hasClass("error")) {
        var formAttr = $(this).serializeArray();
        formAttr.push({name: "subdominio", value: subDominio});
        $.ajax({
            data: formAttr,
            url: "/verifyExistDni",
            type: 'POST',
            dataType: "json",
            async: false,
            success: function (data, textStatus, jqXHR) {
                if (data.response) {
                    inputDocumento.removeClass("has-error");
                    $('#NumeroDocumento-error').empty();
                    inputEmpresa.val(data.empresa);
                    selectEmpresa.empty();
                    selectEmpresa.css("display", 'none');
                } else {
                    inputEmpresa.val('');
                    inputDocumento.addClass("has-error");
                    divDocumento.find("label").remove();
                    divDocumento.append('<label for="NumeroDocumento" class="has-error" id="NumeroDocumento-error">' +
                        'Lo sentimos, pero este Documento no pertenece a esta Empresa</label>');
                    selectEmpresa.empty();
                    selectEmpresa.css("display", 'none');
                }
                $('input:hidden[name=csrf]').val(data.csrf);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                inputDocumento.addClass("has-error");
                inputEmpresa.val('');
                selectEmpresa.empty();
                selectEmpresa.css("display", 'none');
            }
        });
    } else if (documento.length >= 5 && hasEmpresaStatus === false && !inputDocumento.hasClass("error")) {
        $.ajax({
            data: $(this).serializeArray(),
            url: "/verifyExist",
            type: 'POST',
            dataType: "json",
            async: false,
            success: function (data, textStatus, jqXHR) {
                if (data.response) {
                    if (data.total === 1) {
                        inputDocumento.removeClass("has-error");
                        $('#NumeroDocumento-error').empty();
                        $.each(data.empresas, function (index, value) {
                            inputEmpresa.val(index);
                        });
                        selectEmpresa.empty();
                        selectEmpresa.css("display", 'none');
                        hasEmpresaStatus = true;
                    } else {
                        /*selectEmpresa.rules("remove", "required");*/
                        inputDocumento.removeClass("has-error");
                        divDocumento.find("label").remove();
                        selectEmpresa.empty();
                        selectEmpresa.css("display", 'block');
                        selectEmpresa.append('<option value="">Seleccione una empresa</option></select>');
                        $.each(data.empresas, function (index, value) {
                            selectEmpresa.append('<option value="' + index + '">' + value + '</option>');
                        });
                        /*selectEmpresa.rules("add", {
                                required: true,
                                messages: {
                                    required: "Debe seleccionar una empresa afiliada"
                                }
                            }
                        );*/
                        hasEmpresaStatus = true;
                    }
                } else {
                    inputEmpresa.val('');
                    inputDocumento.addClass("has-error");
                    divDocumento.find("label").remove();
                    divDocumento.append('<label for="NumeroDocumento" class="has-error" id="NumeroDocumento-error">' +
                        'Lo sentimos, pero este documento no está registrado</label>');
                    selectEmpresa.empty();
                    selectEmpresa.css("display", 'none');
                }
                $('input:hidden[name=csrf]').val(data.csrf);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                inputDocumento.addClass("error");
                inputEmpresa.val('');
                selectEmpresa.empty();
                selectEmpresa.css("display", 'none');
            }
        });
    }

    if ($(this).valid()) {
        $.ajax(
            {
                url: '/validate',
                type: "POST",
                data: $(this).serializeArray(),
                dataType: "json",
                success: function (data, textStatus, jqXHR) {
                    if (data.response) {
                        ja('login', inputEmpresa.val(), documento);
                        $('input:hidden[name=csrf]').val(data.csrf);
                    }
                },
                complete: function (jqXHR, textStatus) {
                    var responseText = jQuery.parseJSON(jqXHR.responseText);
                    if (responseText.response) {
                        $('button:submit').prop('disabled', true);
                        self.submit();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status === 0) {
                        console.log('Not connect.\n Verify Network.');
                    } else if (jqXHR.status === 404) {
                        console.log('Requested page not found. [404]');
                    } else if (jqXHR.status === 500) {
                        console.log('Internal Server Error [500].');
                    } else if (textStatus === 'parsererror') {
                        console.log('Requested JSON parse failed.');
                    } else if (textStatus === 'timeout') {
                        console.log('Time out error.');
                    } else if (textStatus === 'abort') {
                        console.log('Ajax request aborted.');
                    } else {
                        console.log('Uncaught Error.\n' + jqXHR.responseText);
                    }
                }
            });
    }
});

function getSubDominio(dominio) {
    subDominio = URLactual.split(dominio)[0];
    subDominio = subDominio.replace("www.", "");
    subDominio = subDominio.substring(0, subDominio.length - 1);
    if ($.inArray(subDominio, empresas_e) > -1) {
        console.log('ok');
        $.ajax({
            data: {subD: subDominio},
            url: "/loginTebca",
            type: 'POST',
            dataType: "json",
            success: function (data, textStatus, jqXHR) {
                if (data.response) {
                    console.log(data);
                    window.location.href = 'home';
                } else {
                    console.log(data);
                    history.go(-1);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                history.go(-1);
            }
        });
    } else if (subDominio !== "") {
        boolsubdominio = true;
    }
}
