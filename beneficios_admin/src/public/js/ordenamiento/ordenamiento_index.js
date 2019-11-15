/**
 * Created by marlo on 30/09/15.
 */
$('.elim').click(function() {
    var row =$(this).parents('tr');
    var ids=row.data('id');
    var val="";
    var id_tag='#elim'+ids;
    var elim_id=$(id_tag);
    var type = elim_id.attr("tiplay");
    var id = ids.split("-");

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
        url: '/ordenamiento/delete',
        data: { id: id[0], val: val,type: type },
        success: function(data) {
            console.log(data);
        }
    });
});