/**
 * Created by marlo on 05/10/15.
 */
var arrayOfertas = [];
var ofertas = {};

$(function () {
    $(".select2").select2();
    var nombre = $("input[name=Nombre]");
    nombre.on("keydown", function (event) {
        alphaNum(event);
    });
});

$(document).ready(function () {
    $('input[name="type"]').change(function () {
        var value = $("input:checked").val();
        var categoria = $('#categoria');
        var campania = $('#campania');
        limpiar();
        if (value === 'categoria') {
            categoria.attr('disabled', false);
            campania.attr('disabled', true);
            campania.val('');
            categoria.val('');
        } else if (value === 'campania') {
            categoria.attr('disabled', true);
            campania.attr('disabled', false);
            campania.val('');
            categoria.val('');
        } else if (value === 'tienda') {
            campania.attr('disabled', true);
            categoria.attr('disabled', true);
            campania.val('');
            categoria.val('');
            tienda();
        } else if (value === 'puntos') {
            campania.attr('disabled', true);
            categoria.attr('disabled', true);
            campania.val('');
            categoria.val('');
            puntos();
        }
    });

    $('#categoria').on('change', function () {
        var id = $('#categoria').val();
        var emp = $('#empresa').val();
        categoriaOfertas();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/ordenamiento/categoriaExist',
            async: false,
            data: {id: id, emp: emp},
            success: function (data) {
                if (data.response) {
                    var array = data.data;
                    $('#fila1').val(array[1]);
                    $('#fila2').val(array[2]);
                    $('#fila3').val(array[3]);
                    cantOfertas(array[1], 1, getIdOfertaCategoria(id, array[1], emp, 1));
                    cantOfertas(array[2], 2, getIdOfertaCategoria(id, array[2], emp, 2));
                    cantOfertas(array[3], 3, getIdOfertaCategoria(id, array[3], emp, 3));
                } else {
                    console.log('no hay asignacion');
                    sinData();
                }
            },
            error: function () {
                console.log('error');
            }
        });
        cargarArrayOferta();
    });

    $('#campania').on('change', function () {
        var id = $('#campania').val();
        var emp = $('#empresa').val();
        campaniaOfertas();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/ordenamiento/campaniaExist',
            async: false,
            data: {id: id, emp: emp},
            success: function (data) {
                if (data.response) {
                    var array = data.data;
                    $('#fila1').val(array[1]);
                    $('#fila2').val(array[2]);
                    $('#fila3').val(array[3]);
                    cantOfertas(array[1], 1, getIdOfertaCampania(id, array[1], emp, 1));
                    cantOfertas(array[2], 2, getIdOfertaCampania(id, array[2], emp, 2));
                    cantOfertas(array[3], 3, getIdOfertaCampania(id, array[3], emp, 3));
                } else {
                    console.log('no hay asignacion');
                    sinData();
                }
            },
            error: function () {
                console.log('error');
            }
        });
        cargarArrayOferta();
    });

    $('#fila1').on('change', function () {
        var id = $(this).val();
        $('.fila1').removeClass('select2').addClass('hidden').empty();
        cantOfertas(id, 1);
    });

    $('#fila2').on('change', function () {
        var id = $(this).val();
        $('.fila2').removeClass('select2').addClass('hidden').empty();
        cantOfertas(id, 2);
    });

    $('#fila3').on('change', function () {
        var id = $(this).val();
        $('.fila3').removeClass('select2').addClass('hidden').empty();
        cantOfertas(id, 3);
    });

    $('.pos-offer').on('change', function () {
        if ($.inArray($(this).val(), arrayOfertas) >= 0) {
            $(this).val('');
            $('.alert-warning').show();
            setTimeout(function () {
                $(".alert-warning").fadeOut(2000);
            }, 1000);
        }

        arrayOfertas = [];

        for (var i = 1; i <= 3; i++) {
            for (var j = 1; j <= 3; j++) {
                try {
                    arrayOfertas.push($('#fila' + i + '_' + j).val());
                } catch (err) {
                    console.log(err.message);
                }
            }
        }
    });

    $('#empresa').on('change', function () {
        var radio = $("input[name=type]");
        var categoria = $('#categoria');
        var campania = $('#campania');
        radio.prop('checked', false);
        campania.val('');
        categoria.val('');
        categoria.prop('disabled', 'disabled');
        limpiar();
        sinData();
    });

});

function init() {
    var categoria = $('#categoria');
    var campania = $('#campania');
    categoria.attr('disabled', false);
    campania.attr('disabled', true);
    campania.val('');
    categoria.val('');
    limpiar();
    sinData();
    $('select#empresa').val('all');
}

function limpiar() {
    $('.pos-offer').removeClass('select2').addClass('hidden').empty();
    ofertas = {};
    $('.div-pos-offer').find('span.select2').remove();
}

function sinData() {
    $('#fila1').val('');
    $('#fila2').val('');
    $('#fila3').val('');
}

function llenarData(opcion, listaOfertas) {
    var categoria = $('#categoria');
    var campania = $('#campania');
    if (opcion === 'categoria') {
        categoria.attr('disabled', false);
        campania.attr('disabled', true);
        campania.val('');
        $('#radio-categoria').prop('checked', true);
        categoriaOfertas();
    } else if (opcion === 'campania') {
        categoria.attr('disabled', true);
        campania.attr('disabled', false);
        categoria.val('');
        $('#radio-campania').prop('checked', true);
        campaniaOfertas();
    } else if (opcion === 'tienda') {
        campania.attr('disabled', true);
        categoria.attr('disabled', true);
        campania.val('');
        categoria.val('');
        $('#radio-tienda').prop('checked', true);
        tiendaOfertas();
    } else if (opcion === 'puntos') {
        campania.attr('disabled', true);
        categoria.attr('disabled', true);
        campania.val('');
        categoria.val('');
        $('#radio-puntos').prop('checked', true);
        ofertasPuntos();
    } else if (opcion === 'premios') {
        campania.attr('disabled', true);
        categoria.attr('disabled', true);
        campania.val('');
        categoria.val('');
        $('#radio-premios').prop('checked', true);
        ofertasPremios();
    }
    cantOfertas($('#fila1').val(), 1, listaOfertas[1]);
    cantOfertas($('#fila2').val(), 2, listaOfertas[2]);
    cantOfertas($('#fila3').val(), 3, listaOfertas[3]);
}

function categoriaOfertas() {
    var id = $('#categoria').val();
    var emp = $('#empresa').val();
    limpiar();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/extraerOfertaxCategoria',
        async: false,
        data: {id: id, emp: emp},
        success: function (data) {
            if (data.response) {
                ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
}

function campaniaOfertas() {
    var id = $('#campania').val();
    var emp = $('#empresa').val();
    limpiar();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/extraerOfertaxCampania',
        async: false,
        data: {id: id, emp: emp},
        success: function (data) {
            if (data.response) {
                ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
}

function tienda() {
    tiendaOfertas();
    var emp = $('#empresa').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/tiendaExist',
        async: false,
        data: {emp: emp},
        success: function (data) {
            if (data.response) {
                var array = data.data;
                $('#fila1').val(array[1]);
                $('#fila2').val(array[2]);
                $('#fila3').val(array[3]);
                cantOfertas(array[1], 1, getIdOfertaTienda(array[1], emp, 1));
                cantOfertas(array[2], 2, getIdOfertaTienda(array[2], emp, 2));
                cantOfertas(array[3], 3, getIdOfertaTienda(array[3], emp, 3));
            } else {
                console.log('no hay asignacion');
                sinData();
            }
        },
        error: function () {
            console.log('error');
        }
    });
    cargarArrayOferta();
}

function tiendaOfertas() {
    var emp = $('#empresa').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/extraerOferta',
        async: false,
        data: {emp: emp},
        success: function (data) {
            if (data.response) {
                ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
}

function puntos() {
    ofertasPuntos();
    var emp = $('#empresa').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/puntosExist',
        async: false,
        data: {emp: emp},
        success: function (data) {
            if (data.response) {
                var array = data.data;
                $('#fila1').val(array[1]);
                $('#fila2').val(array[2]);
                $('#fila3').val(array[3]);
                cantOfertas(array[1], 1, getIdOfertaPuntos(array[1], emp, 1));
                cantOfertas(array[2], 2, getIdOfertaPuntos(array[2], emp, 2));
                cantOfertas(array[3], 3, getIdOfertaPuntos(array[3], emp, 3));
            } else {
                console.log('no hay asignacion');
                sinData();
            }
        },
        error: function () {
            console.log('error');
        }
    });
    cargarArrayOferta();
}

function premios() {
    ofertasPremios();
    var emp = $('#empresa').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/premiosExist',
        async: false,
        data: {emp: emp},
        success: function (data) {
            if (data.response) {
                var array = data.data;
                $('#fila1').val(array[1]);
                $('#fila2').val(array[2]);
                $('#fila3').val(array[3]);
                cantOfertas(array[1], 1, getIdOfertaPremios(array[1], emp, 1));
                cantOfertas(array[2], 2, getIdOfertaPremios(array[2], emp, 2));
                cantOfertas(array[3], 3, getIdOfertaPremios(array[3], emp, 3));
            } else {
                console.log('no hay asignacion');
                sinData();
            }
        },
        error: function () {
            console.log('error');
        }
    });
    cargarArrayOferta();
}

function ofertasPuntos() {
    var emp = $('#empresa').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/extraerOfertaPuntos',
        async: false,
        data: {emp: emp},
        success: function (data) {
            if (data.response) {
                ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
}

function ofertasPremios() {
    var emp = $('#empresa').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/extraerOfertaPremios',
        async: false,
        data: {emp: emp},
        success: function (data) {
            if (data.response) {
                ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
}

function cantOfertas(cant, fila, ids_ofertas) {
    $('.select2-container').remove();
    for (var i = 1; i <= cant; i++) {
        var oferHallada = false;
        var id = "#fila" + fila + '_' + i;
        $(id).empty();
        $(id).removeClass('hidden');
        $(id).addClass('select2');
        var options = '<option value="" selected>Seleccione...</option>';
        $.each(ofertas, function (index, value) {
            var selected = '';
            if (typeof ids_ofertas !== 'undefined') {
                if (index === ids_ofertas[i] && !oferHallada) {
                    selected = 'selected';
                    oferHallada = true;
                }
            }
            options += '<option value="' + index + '" ' + selected + '>' + value + '</option>';
        });
        $(id).append(options);
    }
    $(".select2").select2();
    cargarArrayOferta();
}

function getIdOfertaCategoria(id_cat, id_layout, id_empresa, index) {
    var ids_ofertas = [];
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/getOfertasIdsPorCategoria',
        async: false,
        data: {id_cat: id_cat, id_layout: id_layout, id_empresa: id_empresa, index: index},
        success: function (data) {
            if (data.response) {
                ids_ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
    return ids_ofertas;
}

function getIdOfertaCampania(id_cam, id_layout, id_empresa, index) {
    var ids_ofertas = [];
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/getOfertasIdsPorCampania',
        async: false,
        data: {id_cam: id_cam, id_layout: id_layout, id_empresa: id_empresa, index: index},
        success: function (data) {
            if (data.response) {
                ids_ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
    return ids_ofertas;
}

function getIdOfertaTienda(id_layout, id_empresa, index) {
    var ids_ofertas = [];
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/getOfertasIdsTienda',
        async: false,
        data: {id_layout: id_layout, id_empresa: id_empresa, index: index},
        success: function (data) {
            if (data.response) {
                ids_ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
    return ids_ofertas;
}

function getIdOfertaPuntos(id_layout, id_empresa, index) {
    var ids_ofertas = [];
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/getOfertasIdsPuntos',
        async: false,
        data: {id_layout: id_layout, id_empresa: id_empresa, index: index},
        success: function (data) {
            if (data.response) {
                ids_ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
    return ids_ofertas;
}

function getIdOfertaPremios(id_layout, id_empresa, index) {
    var ids_ofertas = [];
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/ordenamiento/getOfertasIdsPremios',
        async: false,
        data: {id_layout: id_layout, id_empresa: id_empresa, index: index},
        success: function (data) {
            if (data.response) {
                ids_ofertas = data.value;
            } else {
                console.log('no hay ofertas');
            }
        },
        error: function () {
            console.log('error');
        }
    });
    return ids_ofertas;
}

function cargarArrayOferta() {
    arrayOfertas = [$('#fila1_1').val(), $('#fila1_2').val(), $('#fila1_3').val(),
        $('#fila2_1').val(), $('#fila2_2').val(), $('#fila2_3').val(),
        $('#fila3_1').val(), $('#fila3_2').val(), $('#fila3_3').val()];
}
