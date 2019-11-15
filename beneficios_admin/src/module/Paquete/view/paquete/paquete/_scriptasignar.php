<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 05/09/15
 * Time: 03:53 PM
 */
?>
<script type="text/javascript">

    $(document).ready(function () {

        var text = parseInt('<?= $form->get('NombrePais')->getValue();?>');
        if(isNaN(text)){
            text = 1;
        }

        $("select[name=NombrePais] option").filter(function() {
            //may want to use $.trim in here
            return $(this).val() == text;
        }).attr('selected', true).change();

        $('#tipopaq').attr('disabled', true);
        $("input[name=CostoPorLead]").numeric({decimalPlaces: 2});
        $("#paquete").val('<?= $form->get('BNF_Paquete_id')->getValue();?>').change();
    });

    $('#pais').change(function () {
        populate_combo('paquete', $('#pais').val());
    });

    $('#paquete').change(function () {
        tipo_paquete();
    });

    $(document).on('keyup', '.select2-search__fiel', function () {
        populate_combo('empresa', 0);
    });
    $('#empre').keyup(function () {
        populate_combo('empresa', 0);
    });

    function populate_combo(combo, value) {
        var options;
        var newOptions = {
            '': 'Seleccionar'
        };

        if (combo.indexOf('paquete') >= 0) {
            paquetes(newOptions, value);
        } else if (combo.indexOf('empresa') >= 0) {
            empresa(newOptions, value);
        }

        var selectedOption = '';
        var select = $('#' + combo);
        if (select.prop) {
            options = select.prop('options');
        }
        else {
            options = select.attr('options');
        }
        $('option', select).remove();

        $.each(newOptions, function (val, text) {
            options[options.length] = new Option(text, val);
        });
        select.val(selectedOption);

    }

    function paquetes(options, value) {
        var mylist = [];
        <?php foreach($paquetespais as $dato): ?>
        if ("<?= $dato->BNF_Pais_id ?>" == value) {
            mylist.push(<?=$dato->BNF_Paquete_id ;?>);
        }
        <?php
endforeach; ?>

        for (var i = 0; i < mylist.length; i++) {
            <?php foreach($paquetes1 as $dato): ?>
            if ("<?= $dato->id ?>" == mylist[i]) {
                options["<?= $dato->id ?>"] = "<?= $dato->Nombre ?>";
            }
            <?php
endforeach; ?>
        }
    }

    function tipo_paquete() {
        <?php foreach($paquetes2 as $dato): ?>
        if ("<?= $dato->id ?>" == $('#paquete').val()) {
            var lead = $('.lead');
            var pre = $('.pre');
            var tipo = <?=(int) $dato->BNF_TipoPaquete_id;?>;
            $('#tipopaq').val('<?=$dato->NombreTipoPaquete;?>');
            if (tipo === 1 || tipo === 2) {
                lead.addClass('hidden');
            }
            else if (tipo === 3) {
                lead.removeClass('hidden');
            }
            $('#detail').text(
                <?php
    if($dato->BNF_TipoPaquete_id==1):
                    $des='Descargas';
        if ($dato->CantidadDescargas==1) {
                        $des='Descarga';
        }
        echo '"'.$dato->CantidadDescargas.
        ' '.$des.' - S/'.$dato->PrecioUnitarioDescarga.
        ' por Descarga - '.$dato->Bonificacion.
        ' Bonificación - S/'.$dato->PrecioUnitarioBonificacion.
        ' por Bonificación "';
    elseif($dato->BNF_TipoPaquete_id==2):
        echo '"Costo x Día: S/'.$dato->CostoDia.
        ' Número de Días: '.$dato->NumeroDias.'"';
    elseif($dato->BNF_TipoPaquete_id==3):
        echo '" "';
    endif;?>
            );
        }
        <?php
endforeach; ?>
    }

    function empresa(options, value) {
        if ($('#empre').val() != '') {
            <?php foreach($empresas as $dato): ?>
                if ("<?=trim($dato->NombreComercial);?>".indexOf($('#empre').val()) >= 0) {
                    options["<?= $dato->id ?>"] = "<?=trim($dato->NombreComercial);?>";
                }
                else if ("<?=trim($dato->RazonSocial);?>".indexOf($('#empre').val()) >= 0) {
                    options["<?= $dato->id ?>"] = "<?=trim($dato->NombreComercial);?>";
                }
                else if ("<?=$dato->Ruc?>".indexOf($('#empre').val()) >= 0) {
                    options["<?= $dato->id ?>"] = "<?=trim($dato->NombreComercial);?>";
                }
            <?php endforeach; ?>
        } else {
            options[""] = "Seleccionar";
        }
    }
</script>
