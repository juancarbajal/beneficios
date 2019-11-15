/**
 * Created by marlo on 23/10/15.
 */
var result = true;

$('#filtro-select').on('change', function () {
    var val = $('#filtro-select').val();
    if (val == 1) {
        $('#premium').val(1);
    } else if (val == 2) {
        $('#destacados').val(1);
    } else {
        $('#novedades').val(1);
    }
    $('#form-search').submit();
});

var scrollLoad = true,
    offset = $('div.offset').data('offset'),
    ubigeo = $('div.ubigeo').data('ubigeo'),
    premium = $('div.premium').data('premium'),
    destacados = $('div.destacados').data('destacados'),
    novedades = $('div.novedades').data('novedades'),
    nombre = $('div.nombre').data('nombre'),
    rofertas = $('div.rofertas').data('rofertas'),
    rofertasP = $('div.rofertas').data('rofertasp'),
    rofertasPR = $('div.rofertas').data('rofertaspr'),
    rlogos = $('div.rlogos').data('rlogos');

$(window).scroll(function () {
    if (scrollLoad && $("#afiliadas").isInScene()) {
        scrollLoad = false;
        loadMore();
    }
});

$.fn.extend({
    // Devuelve true si el elemento está en window
    isInScene: function (arg) {
        // Fuerza que arg sea un objeto
        arg = arg || {};
        // Valor por defecto de desfase
        arg.desfase = arg.desfase || 0;
        // Fuerza a que desfase sea númerico
        arg.desfase = parseInt(arg.desfase, 10);

        // Posición vertical del elemento respecto al principio del documento
        var pos_container = $(this).offset().top;

        // Altura del elemento
        var container_height = $(this).outerHeight();

        // Posición vertical de document
        var pos_document = $(document).scrollTop();

        // Alto ventana
        var window_height = $(window).height();

        return (pos_document + window_height > pos_container + arg.desfase && pos_container + container_height > pos_document + arg.desfase);
    }
});

function loadMore() {
    var data = {
        'offset': offset,
        'ubigeo': ubigeo,
        'premium': premium,
        'nombre': nombre,
        'destacados': destacados,
        'novedades': novedades
    };
    $.ajax({
        type: "POST",
        url: "/home/loadOfertaSearch",
        data: data,
        beforeSend: function () {
            $("#loading").show();
        },
        success: function (data) {
            var json = JSON.parse(data);
            var ofertas = json.ofertas;
            var flagcheckboxLogo = json.flagcheckboxLogo;
            $("#loading").hide();
            if (ofertas.length > 0) {
                var html = '',
                    tokens, ext,
                    nofertas = 0,
                    cantofertas = 0,
                    cant_ofertas = ofertas.length;
                for (dat in ofertas) {
                    if (ofertas[dat].TipoOferta == 1) {
                        if (nofertas == 0) {
                            html += '<div class="row margin-b">';
                        }
                        if (nofertas < 3) {
                            html += '<div class="col-md-4 margin-bottom-movil">';
                            html += '<div class="fluid-list cupon-two">';
                            if (ofertas[dat].Premium == 1) {
                                html += '<span class="destacado-icon"></span>';
                            }
                            html += '<a href="/busqueda/coupon/' + ofertas[dat].SlugOferta + '">'
                            html += '<figure>';
                            var imgOferta;
                            if (ofertas[dat].imgOfertaPrincipal != null) {
                                imgOferta = ofertas[dat].imgOfertaPrincipal;
                            } else {
                                imgOferta = ofertas[dat].imagenOferta;
                            }
                            html += '<img class="img-responsive" src="' + rofertas + imgOferta + '" title=""/>';
                            html += '</figure>';
                            html += '</a>';
                            html += '<div class="info-cupon-l">';
                            if(flagcheckboxLogo) {
                                html += '<figure class="left">';
                                html += '<a href="/company/' + ofertas[dat].SlugEmpresa + '">';
                                tokens = ofertas[dat].LogoEmpresa.split('.');
                                ext = tokens[1];
                                ofertas[dat].LogoEmpresa = ofertas[dat].LogoEmpresa.replace('.' + ext, '') + '-small.' + ext;
                                html += '<img src="' + rlogos + ofertas[dat].LogoEmpresa + '"/>';
                                html += '</a>';
                                html += '</figure>';
                            }
                            html += '<div class="info-interna left">';
                            html += '<h2>';
                            html += '<a href="/busqueda/coupon/' + ofertas[dat].SlugOferta + '">';
                            var tamanio = 80;
                            if (ofertas[dat].datoBeneficio != null) {

                                if (ofertas[dat].idTipoBeneficio == 1) {
                                    html += '<span>-' + ofertas[dat].datoBeneficio + '% Dscto. </span>';
                                } else if (ofertas[dat].idTipoBeneficio == 2) {
                                    html += '<span>-S/.' + ofertas[dat].datoBeneficio + ' Dscto. </span>';
                                } else if (ofertas[dat].idTipoBeneficio == 3) {
                                    html += '<span>' + ofertas[dat].datoBeneficio + ' en </span>';
                                } else {
                                    html += '<span>' + ofertas[dat].datoBeneficio + ' </span>';
                                }
                            } else {
                                tamanio = 120;
                            }
                            if (ofertas[dat].TituloCortoOferta != null) {
                                var tituloCorto = ofertas[dat].TituloCortoOferta;
                                if (tituloCorto.toString().length >= tamanio) {
                                    html += tituloCorto.toString().substring(0, tamanio);
                                } else {
                                    html += ofertas[dat].TituloCortoOferta;
                                }
                            }
                            html += '</a>';
                            html += '</h2>';
                            html += '<a href="/company/' + ofertas[dat].SlugEmpresa + '">Descubre más ofertas de la tienda ›</a>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            nofertas++;
                            cantofertas++;
                            html += '</div>';
                        }
                        if (nofertas == 3 || cant_ofertas == cantofertas) {
                            html += '</div>';
                            nofertas = 0;
                        }
                    } else if (ofertas[dat].TipoOferta == 2) {
                        if (nofertas == 0) {
                            html += '<div class="row margin-b">';
                        }
                        if (nofertas < 3) {
                            html += '<div class="col-md-4 margin-bottom-movil">';
                            html += '<div class="fluid-list cupon-two">';
                            html += '<a href="/busqueda/coupon-puntos/' + ofertas[dat].SlugOferta + '">';
                            html += '<figure>';
                            html += '<img src="' + rofertasP + ofertas[dat].imagenOferta + '" title=""/>';
                            html += '<div class="puntos-ico"></div>';
                            html += '</figure>';
                            html += '</a>';
                            html += '<div class="info-cupon-l">';
                            if(flagcheckboxLogo) {
                                html += '<figure class="left">';
                                html += '<a>';
                                tokens = ofertas[dat].LogoEmpresa.split('.');
                                ext = tokens[1];
                                ofertas[dat].LogoEmpresa = ofertas[dat].LogoEmpresa.replace('.' + ext, '') + '-small.' + ext;
                                html += '<img src="' + rlogos + ofertas[dat].LogoEmpresa + '"/>';
                                html += '</a>';
                                html += '</figure>';
                            }
                            html += '<div class="info-interna left">';
                            html += '<h2>';
                            html += '<a href="/busqueda/coupon-puntos/' + ofertas[dat].SlugOferta + '" class="title-short">';
                            tamanio = 55;

                            if (ofertas[dat].TituloCortoOferta.length >= tamanio) {
                                if (ofertas[dat].datoBeneficio) {
                                    html += '<b>S/. ' + ofertas[dat].datoBeneficio + ' por </b>';
                                }
                                html += ofertas[dat].TituloCortoOferta.substr(0, tamanio) + "...";
                            } else {
                                if (ofertas[dat].datoBeneficio)
                                    html += '<b>S/. ' + ofertas[dat].datoBeneficio + ' por </b>';
                                html += ofertas[dat].TituloCortoOferta;
                            }
                            html += '</a>';
                            html += '</h2>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        }
                        if (nofertas == 3 || cant_ofertas == cantofertas) {
                            html += '</div>';
                            nofertas = 0;
                        }
                    } else if (ofertas[dat].TipoOferta == 3) {
                        if (nofertas == 0) {
                            html += '<div class="row margin-b">';
                        }
                        if (nofertas < 3) {
                            html += '<div class="col-md-4 margin-bottom-movil">';
                            html += '<div class="fluid-list cupon-two">';
                            html += '<a href="/busqueda/coupon-premios/' + ofertas[dat].SlugOferta + '">';
                            html += '<figure>';
                            html += '<img src="' + rofertasPR + ofertas[dat].imagenOferta + '" title=""/>';
                            html += '<div class="premios-ico"></div>';
                            html += '</figure>';
                            html += '</a>';
                            html += '<div class="info-cupon-l">';
                            if(flagcheckboxLogo) {
                                html += '<figure class="left">';
                                html += '<a>';
                                tokens = ofertas[dat].LogoEmpresa.split('.');
                                ext = tokens[1];
                                ofertas[dat].LogoEmpresa = ofertas[dat].LogoEmpresa.replace('.' + ext, '') + '-small.' + ext;
                                html += '<img src="' + rlogos + ofertas[dat].LogoEmpresa + '"/>';
                                html += '</a>';
                                html += '</figure>';
                            }
                            html += '<div class="info-interna left">';
                            html += '<h2>';
                            html += '<a href="/busqueda/coupon-premios/' + ofertas[dat].SlugOferta + '" class="title-short">';
                            tamanio = 55;

                            if (ofertas[dat].TituloCortoOferta.length >= tamanio) {
                                html += ofertas[dat].TituloCortoOferta.substr(0, tamanio) + "...";
                            } else {
                                html += ofertas[dat].TituloCortoOferta;
                            }
                            html += '</a>';
                            html += '</h2>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        }
                        if (nofertas == 3 || cant_ofertas == cantofertas) {
                            html += '</div>';
                            nofertas = 0;
                        }
                    }
                }
                $(".content-load").append(html);
                offset = offset + 9;
                $('div.offset').attr('data-offset', offset);
                scrollLoad = true;
            }
            else {
                scrollLoad = false;
            }
        },
        error: function () {
            console.log('error!!');
        }
    });
}

