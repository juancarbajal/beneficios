/**
 * Created by luisvar on 22/12/15.
 */

$(function () {

    $('.dual-list .selector').click(function () {
        var $checkBox = $(this);
        if (!$checkBox.hasClass('selected')) {
            $checkBox.addClass('selected').closest('.well').find('ul li input:checkbox').prop("checked", true);
            $checkBox.attr('checked', 'checked');
        } else {
            $checkBox.removeClass('selected').closest('.well').find('ul li input:checkbox').       prop("checked", false)
            $checkBox.removeAttr('checked');
        }
    });

    $('[name="SearchDualList"]').keyup(function (e) {
        var code = e.keyCode || e.which;
        if (code == '9') return;
        if (code == '27') $(this).val(null);
        var $rows = $(this).closest('.dual-list').find('.list-group li');
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
        $rows.show().filter(function () {
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });

});