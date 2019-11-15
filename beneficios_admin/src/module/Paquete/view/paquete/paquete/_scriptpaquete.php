<script type="text/javascript">
    $(document).ready(function() {

        var text = parseInt('<?= $form->get('NombrePais')->getValue();?>');
        if(isNaN(text)){
            text = 1;
        }
        $("select[name=NombrePais] option").filter(function() {
            //may want to use $.trim in here
            return $(this).val() == text;
        }).attr('selected', true);

        var des =$('.des');
        var pre =$('.pre');
        var lead=$('.lead');
        <?php
if(isset($tipo)):
    if ($tipo == 2) {
            ?>
            des.addClass('hidden');
            pre.removeClass('hidden');
            lead.removeClass('hidden');
        <?php
    } elseif ($tipo == 3) {
            ?>
            pre.addClass('hidden');
            des.addClass('hidden');
            lead.addClass('hidden');
        <?php
    } else {
            ?>
            pre.addClass('hidden');
            des.removeClass('hidden');
            lead.removeClass('hidden');
        <?php
    }
endif;
        ?>
    });
</script>