/**
 * Created by marlo on 13/04/16.
 */
var subDominio;
var URLactual;
var empresas_e = [];

$(document).ready(function () {
    URLactual = window.location.hostname;
});

function getModal(dominio, active)
{
    subDominio = URLactual.split(dominio)[0];
    subDominio = subDominio.replace("www.","");
    subDominio = subDominio.substring(0,subDominio.length-1);
    if ($.inArray(subDominio, empresas_e) > -1) {
        if (active == 0) {
            $('.modal-tebca').modal('show');
        }
    }
}