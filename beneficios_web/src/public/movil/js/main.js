var obj = {
    loginCnt: function () {
        function rezizeModal() {
            $('.login-beneficios').css({
                'position': 'absolute',
                'left': '50%',
                'top': '50%',
                'margin-left': -$('.login-beneficios').outerWidth() / 2,
                'margin-top': -$('.login-beneficios').outerHeight() / 2
            });
        }

        rezizeModal();
        $(window).resize(function () {
            rezizeModal();
        });
    },
    slideMarcas: function () {
        $('.marcas-s').slick({
            slidesToShow: 6,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: false,
            responsive: [
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 5
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        });
    },
    slidePromocion: function () {
        $('.slider-promocion').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 6000
        });
    },
    validateLogin: function () {
        $("#loginForm").validate({
            // Specify the validation rules
            rules: {
                dni: {
                    required: true,
                    minlength: 5
                },
                empresa: {
                    required: true
                }
            },

            // Specify the validation error messages
            messages: {
                dni: {
                    required: "Lo sentimos pero este DNI no está registrado",
                    minlength: "El número de documento ingresado debe tener mínimo 5 dígitos"
                },
                empresa: {
                    required: "Debe seleccionar una empresa afiliada"
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter($(element).parents('.form-group').find('.cnt-error').html(error));
            }
        });
    },
    menu_fixed: function () {
        var nav = $('header');
        var pos = nav.offset().top;

        $(window).scroll(function () {
            var fix = '';
            if ($(this).scrollTop() > pos) {
                fix = true;
            } else {
                fix = false;
                $(".search").removeClass("hide-s");
            }
            nav.toggleClass("fix-nav", fix);
            $('body').toggleClass("fix-body", fix);
        });
    },
    menu_lateral: function () {
        var slideout = new Slideout({
            'panel': document.getElementById('panel'),
            'menu': document.getElementById('menu'),
            'padding': 256,
            'tolerance': 70,
            'touch': false
        });
        document.querySelector('.js-slideout-toggle').addEventListener('click', function () {
            slideout.toggle();
            if(slideout.isOpen()){
                $("#menu").css("z-index", 1);
                $("#menu-user").addClass('hide-menu');
            }else{
                $("#menu").css("z-index", "inherit");
                $("#menu-user").removeClass('hide-menu');
            }
        });

        document.querySelector('.menu').addEventListener('click', function (eve) {
            if (eve.target.nodeName === 'A') {
                slideout.close();
            }
        });

        slideout.on('beforeopen', function() {
            $("#menu").css("z-index", 1);
            $("#menu-user").addClass('hide-menu');
        });
        slideout.on('close', function() {
            $("#menu").css("z-index", "inherit");
            $("#menu-user").removeClass('hide-menu');
        });
    },
    menu_user: function(){
        if($("#menu-user").length > 0){
            var slideoutUser = new Slideout({
                'panel': document.getElementById('panel'),
                'menu': document.getElementById('menu-user'),
                'side': 'right',
                'padding': 256,
                'tolerance': 70,
                'touch': false
            });
            document.querySelector('.js-slideout-toggle-user').addEventListener('click', function () {
                slideoutUser.toggle();
                if(slideoutUser.isOpen()){
                    $("#menu").addClass('hide-menu');
                }else{
                    $("#menu").removeClass('hide-menu');
                }
            });

            document.querySelector('.menu-user').addEventListener('click', function (eve) {
                if (eve.target.nodeName === 'A') {
                    slideoutUser.close();
                }
            });
            slideoutUser.on('beforeopen', function() {
                $("#menu").addClass('hide-menu');
            });
            slideoutUser.on('close', function() {
                $("#menu").removeClass('hide-menu');
            });

        }
    },
    search_show: function () {
        $(".search-lupa").click(function (e) {
            e.preventDefault();
            $(".search-movil").show();
        });
        $(".back-search").click(function (e) {
            e.preventDefault();
            $(".search-movil").hide();
        });
    },
    search_show_ciudad: function () {
        $(".city_country").click(function (e) {
            e.preventDefault();
            $(".search-ciudad").show();
        });
        $(".back-search-ciudad").click(function (e) {
            e.preventDefault();
            $(".search-ciudad").hide();
        });
    },
    validateOfertas: function () {
        $("#form-ofertas").validate({
            // Specify the validation rules
            rules: {
                name: {
                    required: true
                },
                direccion: {
                    required: true
                },
                telefono: {
                    required: true
                },
                email: {
                    required: true
                },
                genero: {
                    required: true
                },
                departamento: {
                    required: true
                },
                provincia: {
                    required: true
                },
                ciudad: {
                    required: true
                },
                horario: {
                    required: true
                },
                tipo: {
                    required: true
                }
            },

            // Specify the validation error messages
            messages: {
                name: {
                    required: "Ingrese su nombres y apellidos"
                },
                direccion: {
                    required: "Ingrese su dirección"
                },
                telefono: {
                    required: "Ingrese su número de teléfono"
                },
                email: {
                    required: "Ingrese su email"
                },
                genero: {
                    required: "Seleccione su Género"
                },
                departamento: {
                    required: "Seleccione su departamento"
                },
                provincia: {
                    required: "Seleccione su provincia"
                },
                ciudad: {
                    required: "Seleccione su ciudad"
                },
                horario: {
                    required: "Ingrese el horario de contacto"
                },
                tipo: {
                    required: "Ingrese el tipo de contacto"
                }
            },
            //errorPlacement: function(error, element) {
            //error.insertAfter($(element).parents('.form-group').find('.cnt-error').html(error));
            //},
            submitHandler: function (form) {
                form.submit();
            }
        });
    },
    validateEmailCupon: function () {
        $("#emailCupon").validate({
            // Specify the validation rules
            rules: {
                email: {
                    required: true
                },
                terminos: {
                    required: true
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
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter($(element).parents('.cnt-form-error').find('.error-check').html(error));
            }
        });
    },
    modalCenter: function () {
        function centerModals($element) {
            var $modals;
            if ($element.length) {
                $modals = $element;
            } else {
                $modals = $('.modal-vcenter:visible');
            }
            $modals.each(function (i) {
                var $clone = $(this).clone().css('display', 'block').appendTo('body');
                var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
                top = top > 0 ? top : 0;
                $clone.remove();
                $(this).find('.modal-content').css("margin-top", top);
            });
        }

        $('.modal-active').on('show.bs.modal', function (e) {
            centerModals($(this));
        });
        $('.modal-felicitaciones').on('show.bs.modal', function (e) {
            centerModals($(this));
        });
        $('.modal-gracias').on('show.bs.modal', function (e) {
            centerModals($(this));
        });
        $('#modalBienvenida').on('show.bs.modal', function (e) {
            centerModals($(this));
        });
        $('.modal-error').on('show.bs.modal', function (e) {
            centerModals($(this));
        });
        $(window).on('resize', centerModals);
    },
    /*map: function (lapC, lngC) {
     var myLatLng = {lat: lapC, lng: lngC};
     var map = new google.maps.Map(document.getElementById('map'), {
     zoom: 14,
     center: myLatLng
     });
     var marker = new google.maps.Marker({
     position: myLatLng,
     map: map
     });
     var getCen = map.getCenter();
     google.maps.event.addDomListener(window, 'resize', function () {
     map.setCenter(getCen);
     });
     },*/
    checkActive: function () {
        var botonDisabled = true;
        $("#activeChek").click(function () {
            if (botonDisabled == true) {
                $("#activeChek").prop("checked", true);
                $(".cnt-terminos-info").show();
                botonDisabled = false;
            } else {
                $("#activeChek").prop("checked", false);
                $(".cnt-terminos-info").hide();
                botonDisabled = true;
            }
        });
    },
    flyoutMovil: function(){
        $('#flyoutMovil').modal('show');
    },
    flyoutMovilPremios: function(){
        $('#flyoutMovilPremios').modal('show');
    },
    enviarPuntos: function(){
        $(".enviarPuntos").click(function(){
            $('#elegirOpcion').modal('toggle');
            setTimeout(function () {
                $("body").addClass("modal-open");
            }, 1000);
            $('#enviarPuntos').modal('show');
        });
    },
    modalBienvenido: function(){
        $('#modalBienvenida').modal('show');
    }
};