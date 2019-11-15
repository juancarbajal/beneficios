var obj = {
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
	menu_lateral: function(){
		var startX, curX, startY, curY; // Variables
		var newXScroll, newYScroll, genXScroll; // More Variables!
		// Change the height of the sidebar, as well as a few things to do with the main content area, so the user
		// can actually scroll in the content area.
		function sideBarHeight() { 
			var docHeight = $(document).height();
			var winHeight = $(window).height();
			$('.slide-in').height(winHeight);
			$('#main-container').height(winHeight);
			$('#sub-container').height($('#sub-container').height());
		}
		sideBarHeight();
		var outIn = 'in';
		Hammer(document.getElementById('main-container')).on('swiperight', function(e) {
			$('.slide-in').toggleClass('on');		
			$('#main-container').toggleClass('on');
			outIn = 'out';
		});
		Hammer(document.getElementById('main-container')).on('swipeleft', function(e) {
			$('.slide-in').toggleClass('on');	
			$('#main-container').toggleClass('on');
			outIn = 'in';
		});
		function runAnimation() {
			if(outIn == 'out') {
				$('.slide-in').toggleClass('on');
				$('#main-container').toggleClass('on');	
				outIn = 'in';
			} else if(outIn == 'in') {
				$('.slide-in').toggleClass('on');	
				$('#main-container').toggleClass('on');	
				outIn = 'out';
			}
		}
		$('.menu-icon')[0].addEventListener('touchend', function(e) {
			$('.slide-in').toggleClass('on');		
			$('#main-container').toggleClass('on');
		});
		$('.menu-icon').click(function() {
			$('.slide-in').toggleClass('on');		
			$('#main-container').toggleClass('on');
		});
	},
	search_show:function(){
		$(".search-lupa").click(function(e){
			e.preventDefault();
			$(".search-movil").show();
        });
        $(".back-search").click(function(e){
			e.preventDefault();
			$(".search-movil").hide();
        });
	},
	search_show_ciudad:function(){
		$(".city_country").click(function(e){
			e.preventDefault();
			$(".search-ciudad").show();
        });
        $(".back-search-ciudad").click(function(e){
			e.preventDefault();
			$(".search-ciudad").hide();
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
		var getCen = map.getCenter();
			google.maps.event.addDomListener(window, 'resize', function() {
			map.setCenter(getCen);
		});
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