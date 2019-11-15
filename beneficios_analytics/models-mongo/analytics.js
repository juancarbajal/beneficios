var mongoose = require('mongoose');
var Schema = mongoose.Schema;
d = new Date();
utc = d.getTime() + (d.getTimezoneOffset() * 60000);
date = new Date(utc + (3600000*-5));
var now = fecha_fin = date.getFullYear() + '-' +
    ('00' + (date.getMonth()+1)).slice(-2) + '-' +
    ('00' + date.getDate()).slice(-2) + ' ' +
    ('00' + date.getHours()).slice(-2) + ':' +
    ('00' + date.getMinutes()).slice(-2) + ':' +
    ('00' + date.getSeconds()).slice(-2);
var analyticsSchema  = new Schema({
        id_cookie : { type: String, index: true},
        id_empresa : { type: Number, default:null,index:true },
        dni : {type: String, index: true },
        slug : {type:String, index: true },
        sub_dominio: {type: String, index: true},
        ip : String,
        e_n : { type: Number, default:1},
        e_u : { type: Number, default:0},
        fecha_registro : { type: String, default: now },
        navegador : String,
        s_o : String,
        dispositivo : String
    });

var Analytics = mongoose.model('analytics', analyticsSchema);
module.exports = Analytics;