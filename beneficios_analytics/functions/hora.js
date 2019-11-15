var set_hora = function (data) {
    d = new Date();
    utc = d.getTime() + (d.getTimezoneOffset() * 60000);
    date = new Date(utc + (3600000*-5));
    fecha_fin = date.getFullYear() + '-' +
        ('00' + (date.getMonth()+1)).slice(-2) + '-' +
        ('00' + date.getDate()).slice(-2) + ' ' +
        ('00' + date.getHours()).slice(-2) + ':' +
        ('00' + date.getMinutes()).slice(-2) + ':' +
        ('00' + date.getSeconds()).slice(-2);
    var ip = (typeof(data.address) != "undefined" )? data.address: "ip-null";
    var puerto = ( typeof(data.port) != "undefined" )? data.port: "ip-null";
    fecha_fin = ip + " " + puerto + " [" + fecha_fin +"] ";
    return fecha_fin;
};

var get_hora = function () {
    d = new Date();
    utc = d.getTime() + (d.getTimezoneOffset() * 60000);
    date = new Date(utc + (3600000*-5));
    fecha_fin = date.getFullYear() + '-' +
        ('00' + (date.getMonth()+1)).slice(-2) + '-' +
        ('00' + date.getDate()).slice(-2) + ' ' +
        ('00' + date.getHours()).slice(-2) + ':' +
        ('00' + date.getMinutes()).slice(-2) + ':' +
        ('00' + date.getSeconds()).slice(-2);

    return fecha_fin;
};

module.exports.set_hora = set_hora;
module.exports.get_hora = get_hora;