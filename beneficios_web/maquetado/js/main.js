var obj = {
	loginCnt: function(){
		function rezizeModal(){
			$('.login-beneficios').css({
		        'position' : 'absolute',
		        'left' : '50%',
		        'top' : '50%',
		        'margin-left' : -$('.login-beneficios').outerWidth()/2,
		        'margin-top' : -$('.login-beneficios').outerHeight()/2
		    });
		}
		rezizeModal();
	    $(window).resize(function(){
	    	rezizeModal();
	    });
	},
	slideMarcas: function(){
		$('.marcas-s').slick({
		  	slidesToShow: 6,
		  	slidesToScroll: 1,
		  	autoplay: true,
		  	autoplaySpeed: 2000,
		  	arrows:false,
		  	responsive: [
		  		{
			      breakpoint: 1199,
			      settings:{
			        slidesToShow: 5
			      }
			    },
		  		{
			      breakpoint: 991,
			      settings:{
			        slidesToShow: 4
			      }
			    },
			    {
			      breakpoint: 600,
			      settings:{
			        slidesToShow: 3
			      }
			    },
			    {
			      breakpoint: 480,
			      settings:{
			        slidesToShow: 2
			      }
			    }
		    ]
		});
	},
	slidePromocion: function(){
		$('.slider-promocion').slick({
		  	slidesToShow: 1,
		  	slidesToScroll: 1,
		  	autoplay: true,
		  	autoplaySpeed: 2000
		});
	},
	validateLogin: function(){
		$("#loginForm").validate({
        // Specify the validation rules
        rules: {
          dni: {
            required: true
          },
          empresa:{
          	required: true
          }
        },
        
        // Specify the validation error messages
        messages: {
          dni: {
            required: "Lo sentimos pero este DNI no está registrado"
          },
          empresa:{
          	required: "Debe seleccionar una empresa afiliada"
          }
        },
        //errorPlacement: function(error, element) {
          //error.insertAfter($(element).parents('.form-group').find('.cnt-error').html(error));
        //},
        submitHandler: function(form) {
            form.submit();
        }
      });
	},
	validateOfertas: function(){
		$("#form-ofertas").validate({
			// Specify the validation rules
	        rules: {
	          name: {
	            required: true
	          },
	          direccion:{
	          	required: true
	          },
	          telefono:{
	          	required: true
	          },
	          email:{
	          	required: true
	          },
	          genero:{
	          	required: true
	          },
	          departamento:{
	          	required: true
	          },
	          provincia:{
	          	required: true
	          },
	          ciudad:{
	          	required: true
	          },
	          horario:{
	          	required: true
	          },
	          tipo:{
	          	required: true
	          }
	        },
	        
	        // Specify the validation error messages
	        messages: {
	          name: {
	            required: "Ingrese su nombres y apellidos"
	          },
	          direccion:{
	          	required: "Ingrese su dirección"
	          },
	          telefono:{
	          	required: "Ingrese su número de teléfono"
	          },
	          email:{
	          	required: "Ingrese su email"
	          },
	          genero:{
	          	required: "Seleccione su Género"
	          },
	          departamento:{
	          	required: "Seleccione su departamento"
	          },
	          provincia:{
	          	required: "Seleccione su provincia"
	          },
	          ciudad:{
	          	required: "Seleccione su ciudad"
	          },
	          horario:{
	          	required: "Ingrese el horario de contacto"
	          },
	          tipo:{
	          	required: "Ingrese el tipo de contacto"
	          }
	        },
	        //errorPlacement: function(error, element) {
	          //error.insertAfter($(element).parents('.form-group').find('.cnt-error').html(error));
	        //},
	        submitHandler: function(form) {
	            form.submit();
	        }
		});
	},
	validateEmailCupon:function(){
		$("#emailCupon").validate({
			// Specify the validation rules
	        rules: {
	          email: {
	            required: true
	          },
	          terminos:{
	          	required: true
	          }
	        },
	        
	        // Specify the validation error messages
	        messages: {
	          email: {
	            required: "Debe ingresar un email"
	          },
	          terminos:{
	          	required: "Debe aceptar las condiciones, términos y políticas de uso"
	          }
	        },
	        errorPlacement: function(error, element) {
	          error.insertAfter($(element).parents('.cnt-form-error').find('.error-check').html(error));
	        },
	        submitHandler: function(form) {
	            form.submit();
	        }
		});
	},
	menu_fixed: function(){
		var nav = $('header');
		var pos = nav.offset().top;				
		
		$(window).scroll(function () {
			var fix = '';
			if($(this).scrollTop() > pos){
				fix = true;
			}else{
				fix = false;
				$(".search").removeClass("hide-s");
			}
			nav.toggleClass("fix-nav", fix);
			$('body').toggleClass("fix-body", fix);	
		});
	},
	search_active: function(){
		var botonDisabled=false;
		$(".search-fixed").click(function(e){
			e.preventDefault();
            if(botonDisabled==true)
            {
                $(this).removeClass("active");
                $(".search").addClass("hide-s");
                $(".search").removeClass("show-s");
                botonDisabled=false;
            }else{
                $(this).addClass("active");
                $(".search").addClass("show-s");
                $(".search").removeClass("hide-s");
                botonDisabled=true;
            }
        });
	},
	map: function(lapC,lngC){
		var myLatLng = {lat: lapC, lng: lngC};
		var map = new google.maps.Map(document.getElementById('map'), {
		    zoom: 14,
		    center: myLatLng
		});

		var marker = new google.maps.Marker({
		    position: myLatLng,
		    map: map
		});
	},
	modalCenter: function(){
		function centerModals($element) {
			var $modals;
			if ($element.length) {
			    $modals = $element;
			} else {
			    $modals = $('.modal-vcenter:visible');
			}
			$modals.each( function(i) {
			    var $clone = $(this).clone().css('display', 'block').appendTo('body');
			    var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
			    top = top > 0 ? top : 0;
			    $clone.remove();
			    $(this).find('.modal-content').css("margin-top", top);
			});
		}
		$('.modal-active').on('show.bs.modal', function(e) {
		  	centerModals($(this));
		});
		$('.modal-felicitaciones').on('show.bs.modal', function(e) {
		  	centerModals($(this));
		});
		$('.modal-gracias').on('show.bs.modal', function(e) {
		  	centerModals($(this));
		});
		$(window).on('resize', centerModals);
	},
	checkActive: function(){
		var botonDisabled=true;
		$("#activeChek").click(function(){
			if(botonDisabled==true){
				$("#activeChek").prop("checked", true);
				$(".cnt-terminos-info").show();
				botonDisabled=false;
			}else{
				$("#activeChek").prop("checked", false);
				$(".cnt-terminos-info").hide();
				botonDisabled=true;
			}
		});
	}
}