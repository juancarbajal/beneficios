var express = require('express');
var router = express.Router();
var Controller = require('../controllers-mongo/controller');
var requestIp = require('request-ip');
var os = require('os');


router.get('/ok', function(req, res){
    res.sendStatus(200);
});

//SEGURIDAD

/*
var basic = auth.basic({
        realm: "Authentication"
    }, function (username, password, callback) { // Custom authentication method.
        callback(username === process.env.API_USER && password === process.env.API_PASS);
    }
);
*/

//var authMiddleware = auth.connect(basic);

router.all('/*', function(req, res, next){
    var url = process.env.WEB_URL;
    if (url.length > 0){
        var origin = req.headers['origin'];
        if (typeof origin !== "undefined") {
            var re = new RegExp(eval("/" +url+"$/"));
            if ( re.test(origin) ) {
                next();
            }else {
                var err = new Error('Not Found');
                err.status = 404;
                res.sendStatus(200);
            }
        }else {
            var err = new Error('Not Found');
            err.status = 404;
            res.sendStatus(200);
        }
    }else {
        var err = new Error('Not Found');
        err.status = 404;
        res.sendStatus(200);
    }
});


//router.post('/api/v1/session_category', function(req, res) {
    /*Controller.sessionCategy(req.body, {address:req.connection._peername.address, port : req.connection._peername.port },
    function (result){
      res.json(result);
    }
  );*/
//});

router.get('/api/v1/save_event', function(req, res){
    var data = {
        id_empresa : req.query.id_empresa,
        ip :  req.query.ip,
        slug : req.query.slug,
        dni : req.query.dni,
        id_cookie : req.query.id_key,
        s_o : req.query.os,
        navegador : req.query.navegador,
        dispositivo : req.query.dispositivo,
        sub_dominio : req.query.subdominio
    };

    Controller.saveEvent(data, {address:req.connection._peername.address, port : req.connection._peername.port },
        function (result){
            res.json(result);
        }
    );
});


module.exports = router;
