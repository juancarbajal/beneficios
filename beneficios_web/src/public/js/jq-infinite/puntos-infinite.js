/**
 * Created by janaqlap1 on 17/11/15.
 */

var scrollLoad = true;

function loadMore() {
    var lastRestantes = $(".content-load").find("div.restantes").last().find('div.restantes-items').length;
    var data = {
        'offset': offset,
        'ubigeo': ubigeo,
        'categoria': categoria,
        'campaign': campaign,
        'company' : company,
        'notin' : notin,
        'categories': categories
    };

    $.ajax({
        type: "POST",
        url: "/puntos/loadOfertaPuntos",
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
                var div = '';
                var content = '';
                var nofertas = 0;
                var cantofertas = 0;
                var cant_ofertas = ofertas.length;
                for (dat in ofertas) {
                    if(nofertas == 0) {
                        content = '<div class="row margin-b">';
                    }
                    div = '';
                    div += '<div class="col-md-4 margin-bottom-movil">';
                    div += '<div class="fluid-list cupon-two">';
                    div += '<a href="/'+ category +'/coupon-puntos/'+ ofertas[dat].Slug +'">';
                    div += '<figure>';
                    var imgOferta;
                    if (ofertas[dat].ImgOfertaPrincipal != null){
                        imgOferta = ofertas[dat].ImgOfertaPrincipal;
                    } else {
                        imgOferta = ofertas[dat].ImgOfertaPrincipal;
                    }
                    div += '<img src="'+ rofertas + imgOferta + '" title=""/>';
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
                    div += '<a href="/'+ category +'/coupon/' + ofertas[dat].Slug + '">';
                    var tamanio = 120;
                    if(ofertas[dat].TituloCorto!=null)
                    {
                        var tituloCorto = ofertas[dat].TituloCorto;
                        if( tituloCorto.toString().length >= tamanio) {
                            if (ofertas[dat].PrecioBeneficio)
                                div += '<b>S/. ' + ofertas[dat].PrecioBeneficio + ' por </b>';
                            div += tituloCorto.toString().substring(0,tamanio);
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
                    div += '</div>';

                    if (lastRestantes > 0 && lastRestantes < 3) {
                        $(".content-load").find("div.restantes").last().append(div);
                        lastRestantes++;
                        cantofertas++;
                    } else {
                        nofertas++;
                        cantofertas++;
                        content += div;
                    }

                    if (nofertas == 3 || cant_ofertas == cantofertas) {
                        html = html + content + '</div>';
                        nofertas = 0;
                    }
                }
                $(".content-load").append(html);
                offset = offset + 9;
                $('.offset').attr('data-offset',offset);
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
    isInScene : function(arg)
    {
        // Fuerza que arg sea un objeto
        arg = arg || {};
        // Valor por defecto de desfase
        arg.desfase = arg.desfase || 0;
        // Fuerza a que desfase sea númerico
        arg.desfase = parseInt(arg.desfase,10);

        // Posición vertical del elemento respecto al principio del documento
        var pos_container = $(this).offset().top;

        // Altura del elemento
        var container_height = $(this).outerHeight();

        // Posición vertical de document
        var pos_document = $(document).scrollTop();

        // Alto ventana
        var window_height = $(window).height();

        return (pos_document+window_height > pos_container+arg.desfase && pos_container+container_height > pos_document+arg.desfase);
    }
});