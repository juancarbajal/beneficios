function onlyNumbers(event) {
    if (event.shiftKey) {
        event.preventDefault();
    }

    if (!(event.keyCode == 46 || event.keyCode == 17
        || event.keyCode == 8 || event.keyCode == 9
        || event.keyCode == 35 || event.keyCode == 36
        || event.keyCode == 37 || event.keyCode == 39)) {

        if (event.keyCode >= 95) {
            if (event.keyCode < 96 || event.keyCode > 105) {
                event.preventDefault();
            }

        } else {

            if (event.keyCode < 48 || event.keyCode > 57) {
                event.preventDefault();
            }
        }
    }
}

function copyPage(input) {
    var value = input.val();
    if (!$.isNumeric(value)) {
        input.val('');
    }
}

function maxInput(input, limit) {
    var value = input.val();
    var current = value.length;
    if (limit < current) {
        input.val(value.substring(0, limit));
    }
}

function onlyLeters(key) {
    if ((key.keyCode < 65 || key.keyCode > 122) //letras minusculas
        && (key.which != 8) //retroceso
        && (key.which != 9) //tab
        && (key.keyCode != 17) //ctrl
        && (key.keyCode != 16) //shift
        && (key.keyCode != 35) //inicio
        && (key.keyCode != 36) //fin
        && (key.keyCode != 46) //suprim
        && (key.keyCode != 37) //felcha izq
        && (key.keyCode != 39) //felcha der
        && (key.keyCode != 192) //ñ
        && (key.keyCode != 209) //Ñ
        && (key.keyCode != 32) //espacio
        && (key.keyCode != 225) //á
        && (key.keyCode != 233) //é
        && (key.keyCode != 237) //í
        && (key.keyCode != 243) //ó
        && (key.keyCode != 250) //ú
        && (key.keyCode != 193) //Á
        && (key.keyCode != 201) //É
        && (key.keyCode != 205) //Í
        && (key.keyCode != 211) //Ó
        && (key.keyCode != 218) //Ú
    )
        key.preventDefault();
}

function alphaNum(key) {
    if (key.shiftKey) {
        key.preventDefault();
    }

    if ((key.keyCode < 48 || key.keyCode > 58) //numeros
        && (key.keyCode < 65 || key.keyCode > 122) //letras minusculas
        && (key.which != 8) //retroceso
        && (key.which != 9) //tab
        && (key.keyCode != 17) //ctrl
        && (key.keyCode != 35) //inicio
        && (key.keyCode != 36) //fin
        && (key.keyCode != 46) //suprim
        && (key.keyCode != 37) //felcha izq
        && (key.keyCode != 39) //felcha der
        && (key.keyCode != 192) //ñ
        && (key.keyCode != 209) //Ñ
        && (key.keyCode != 32) //espacio
        && (key.keyCode != 225) //á
        && (key.keyCode != 233) //é
        && (key.keyCode != 237) //í
        && (key.keyCode != 243) //ó
        && (key.keyCode != 250) //ú
        && (key.keyCode != 193) //Á
        && (key.keyCode != 201) //É
        && (key.keyCode != 205) //Í
        && (key.keyCode != 211) //Ó
        && (key.keyCode != 218) //Ú
    ) {
        key.preventDefault();
    }
}