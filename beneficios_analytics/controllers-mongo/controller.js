var AnalyticsModel = require('../models-mongo/analytics');
var hora_actual = require("../functions/hora");

var GenericRestController = {

    sessionCategy: function (req, address, callback) {
        //var query = {imei: req.imei};


        AnalyticsModel.aggregate(
            [
                // Grouping pipeline
                {
                    "$group": {
                        "_id": '$slug',
                        "count_e_u": {"$sum": "$e_u"},
                        "count_e_n": {"$sum": "$e_n"}
                    }
                },
                // Sorting pipeline
                {"$sort": {"_id": 1}},
                // Optionally limit results
                {"$limit": Number(req.limit)}
            ],
            function (err, result) {

                if (!err) {
                    callback(result);
                }
                else {
                    console.log(err);
                    callback({error: 1});
                }
            }
        );
    },

    saveEvent: function (req, address, callback) {
        if (req.id_cookie && req.id_empresa && req.slug && req.dni) {
            var query = {
                id_cookie: req.id_cookie,
                e_u: 1,
                id_empresa: req.id_empresa,
                slug: req.slug,
                dni: req.dni
            };
            AnalyticsModel.findOne(query, function (err, result) {
                if (!err) {

                    if (!result) {
                        req.e_u = 1;
                    }
                    req.fecha_registro = hora_actual.get_hora();
                    var analytics = new AnalyticsModel(req);
                    analytics.save(function (err1) {
                        if (!err1) console.log(hora_actual.set_hora(address) + "save event - OK");
                        else console.log(err1);
                    });

                    callback({error: 0});
                } else {
                    console.log(hora_actual.set_hora(address) + "save event - Error");
                    console.log(err);
                    callback({error: 1});
                }
            });
        } else {
            callback({error: 0});
        }
    },
    getBrowser: function (cadena) {
        if ((cadena.indexOf("Opera") || cadena.indexOf('OPR')) != -1) {
            return 'Opera';
        }
        else if (cadena.indexOf("Chrome") != -1) {
            return 'Chrome';
        }
        else if (cadena.indexOf("Safari") != -1) {
            return 'Safari';
        }
        else if (cadena.indexOf("Firefox") != -1) {
            return 'Firefox';
        }
        else if (cadena.indexOf("MSIE") != -1) {
            return 'IE';
        }
        else if (cadena.indexOf("Edge") != -1) {
            return 'Microsoft Edge';
        }
        else {
            return 'Otros';
        }
    },
    getSubDominio: function (url){
        subDominio = url.split( process.env.WEB_URL)[0];
        subDominio = subDominio.replace("www.","");
        subDominio = subDominio.replace('https://',"");
        subDominio = subDominio.replace('http://',"");
        subDominio = subDominio.substring(0,subDominio.length-1);
        return subDominio;
    }
};

module.exports = GenericRestController;
