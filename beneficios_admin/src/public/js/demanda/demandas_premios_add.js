/**
 * Created by luisvar on 22/06/16.
 */
$(document).ready(function () {
    $('#empresa-cli').change(function () {
        var empresa = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#campanias").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/demandas-ofertas-premios/getDataEmpresa',
            data: {id: empresa, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    var resultado = data.empresa;
                    $('#ruc').html(resultado.ruc);
                    $('#razon-social').html(resultado.razon);
                    $('#contacto').html(resultado.contacto);
                    $("#campanias").select2({
                        language: 'es',
                        data: data.campanias
                    })
                } else {
                    $('#ruc').html('');
                    $('#razon-social').html('');
                    $('#contacto').html('');
                    $("#campanias").select2({
                        language: 'es',
                        data: []
                    })
                }
            }
        });
    });

    $('#campanias').change(function () {
        var campania = $(this).val();
        var csrf = $('input:hidden[name=csrf]').val();

        $("#segmentos").empty();
        var segmentos = $("#ms-segmentos");
        segmentos.find("div.ms-selectable").find("ul.ms-list").empty();
        segmentos.find("div.ms-selection").find("ul.ms-list").empty();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/demandas-ofertas-premios/getDataSegmentos',
            data: {id: campania, csrf: csrf},
            async: false,
            success: function (data) {
                $('input:hidden[name=csrf]').val(data.csrf);
                if (data.response) {
                    $.each(data.segmentos, function (index, value) {
                        $("#segmentos").multiSelect('addOption', value);
                    });
                } else {
                    $("#segmentos").multiSelect({});
                }
            }
        });
    });

    $('#sendButton').on('click', function () {
        $('input:hidden[name=action]').val('send');
    });

    $('#submitSave').on('click', function () {
        $('input:hidden[name=action]').val('');
    });
});

$(function () {
    $(".select2").select2({
        language: 'es'
    });
    $(".datepicker").datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        autoclose: true
    });
    $(".textarea").wysihtml5();

    $('.searchable').multiSelect({
        selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='buscar...'>",
        selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='buscar...'>",
        afterInit: function (ms) {
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function (e) {
                    if (e.which === 40) {
                        that.$selectableUl.focus();
                        return false;
                    }
                });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function (e) {
                    if (e.which == 40) {
                        that.$selectionUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function () {
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function () {
            this.qs1.cache();
            this.qs2.cache();
        }
    });
});