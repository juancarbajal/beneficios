/**
 * Created by luisvar on 19/02/16.
 */
var scrollLoad = true;

function loadMore() {
    var data = {
        'offset': offset,
        'ubigeo': ubigeo,
        'categoria': categoria,
        'campaign': campaign,
        'company': company,
        'notin': notin,
        'categories': categories
    };

    $.ajax({
        type: "POST",
        url: "/premios/loadOfertaPremios",
        data: data,
        beforeSend: function () {
            $("#loading").show();
        },
        success: function (data) {
            var json = JSON.parse(data);
            var ofertas = json.ofertas;
            var category = json.category;
            var flagcheckboxLogo = json.flagcheckboxLogo;
            $("#loading").hide();
            if (ofertas.length > 0) {
                var html = '';
                var content = '';
                var cantofertas = 0;
                var cant_ofertas = ofertas.length;
                for (var dat in ofertas) {
                    content += '<div class="fluid-list margin-b">';
                    var div = '<div class="fluid-list cupon-two">';
                    div += '<a href="/' + category + '/coupon-premios/' + ofertas[dat].Slug + '">';
                    div += '<figure>';
                    var imgOferta;
                    if (ofertas[dat].ImgOfertaPrincipal != null) {
                        imgOferta = ofertas[dat].ImgOfertaPrincipal;
                    } else {
                        imgOferta = ofertas[dat].ImgOfertaPrincipal;
                    }
                    div += '<img src="' + rofertas + imgOferta + '" title=""/>';
                    div += '</figure>';
                    div += '</a>';
                    div += '<div class="info-cupon-l">';
                    if(flagcheckboxLogo) {
                        div += '<figure class="left">';
                        div += '<a>';
                        var tokens = ofertas[dat].LogoEmpresa.split('.');
                        var ext = tokens[1];
                        ofertas[dat].LogoEmpresa = ofertas[dat].LogoEmpresa.replace('.' + ext, '') + '-small.' + ext;
                        div += '<img src="' + rlogos + ofertas[dat].LogoEmpresa + '"/>';
                        div += '</a>';
                        div += '</figure>';
                    }
                    div += '<div class="info-interna left">';
                    div += '<h2>';
                    div += '<a href="/' + category + '/coupon-premios/' + ofertas[dat].Slug + '">';
                    var tamanio = 58;
                    if (ofertas[dat].TituloCorto != null) {
                        var tituloCorto = ofertas[dat].TituloCorto;
                        if (tituloCorto.toString().length >= tamanio) {
                            if (ofertas[dat].PrecioBeneficio)
                                div += '<b>S/. ' + ofertas[dat].PrecioBeneficio + ' por </b>';
                            div += tituloCorto.toString().substring(0, tamanio) + "...";
                        } else {
                            if (ofertas[dat].PrecioBeneficio)
                                div += '<b>S/. ' + ofertas[dat].PrecioBeneficio + ' por </b>';
                            div += ofertas[dat].TituloCorto;
                        }
                    }
                    div += '</a>';
                    div += '</h2>';
                    div += '</div>';
                    div += '</div>';
                    div += '</div>';

                    cantofertas++;
                    content += div + '</div>';
                    if (cant_ofertas == cantofertas) {
                        html = html + content;
                    }
                }
                $("div.ofertas-restantes").append(html);
                offset = offset + 9;
                $('.offset').attr('data-offset', offset);
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
var offset = $('.offset').data('offset'),
    notin = $('.notin').data('notin'),
    ubigeo = $('.ubigeo').data('ubigeo'),
    categoria = $('.categoria').data('categoria'),
    campaign = $('.campaign').data('campaign'),
    company = $('.companya').data('companya'),
    rofertas = $('.rofertas').data('rofertas'),
    rlogos = $('.rlogos').data('rlogos'),
    categories = $('.categories').data('categories');

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