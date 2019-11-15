/**
 * Created by marlo on 23/09/15.
 */
document.write("<script type='text/javascript' src='../../js/validations.js'></script>");

$(function () {
    var shiftDown = false;
    var shiftkey = 16, numberKey = [48,49,50,51,52,53,54,55,56,57,187,188,189,190,191,219,220,221,222,229];

    $(document).keydown(function(e)
    {
        if (e.keyCode == shiftkey) shiftDown = true;
    }).keyup(function(e)
    {
        if (e.keyCode == shiftkey) shiftDown = false;
    });

    var nombre = $("input[name=Nombre]");

    nombre.on("keydown", function (event) {
        if (shiftDown && (numberKey.indexOf(event.keyCode)==(-1))) {
            return true;
        }else{
            alphaNum(event);
        }
    });
});
