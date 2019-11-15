/**
 * Created by luisvar on 28/09/15.
 */
$('.elim').click(function() {
    var row =$(this).parents('tr');
    var id=row.data('id');
    var val="";
    var id_tag='#elim'+id;
    var elim_id=$(id_tag);

    if(elim_id.attr("name")==1){
        elim_id.attr("name",0);
        val ='0';
    }else {
        elim_id.attr("name", 1);
        val = '1';
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/campania/delete',
        data: { id: id, val: val },
        success: function(data) {
            console.log(data);
        }
    });
});