<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 06/09/15
 * Time: 11:51 PM
 */
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("input[name=CostoPorLead]").numeric({decimalPlaces: 2});
        var lead = $('.lead');
        var pre = $('.pre');
        <?php
if(isset($tipo)):
    if ($tipo == 'Descarga') {
                ?>
        pre.removeClass('hidden');
        lead.addClass('hidden');
        <?php
    } elseif ($tipo == 'Presencia') {
        ?>
        pre.addClass('hidden');
        lead.addClass('hidden');
        <?php
    } elseif ($tipo == 'Lead') {
        ?>
        pre.removeClass('hidden');
        lead.removeClass('hidden');
        <?php
    }
endif;
?>
    });
</script>