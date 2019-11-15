<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 05/10/15
 * Time: 06:58 PM
 */
?>
<script>
    $(document).ready(function () {
        var value = "<?= $type; ?>";
        var categoria = $('#categoria');
        var campania = $('#campania');
        if (value == 'categoria') {
            categoria.attr('disabled', false);
            campania.attr('disabled', true);
        } else {
            categoria.attr('disabled', true);
            campania.attr('disabled', false);
        }
    });
</script>
