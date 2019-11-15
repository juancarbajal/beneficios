require('dotenv').config({path : __dirname + '/.env'});
var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var mysql = require('mysql');
var mongoose = require('mongoose');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var debug = require('debug')('beneficios_analytics:config');
var routes = require('./routes/index');
var users = require('./routes/users');
var hora_actual =  require('./functions/hora');
var app = express();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));


app.use(function(req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers","Access-Control-Allow-Headers");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});

app.use('/', routes);
app.use('/users', users);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
    var err = new Error('Not Found');
    err.status = 404;
    next(err);
});

// error handlers

// development error handler
// will print stacktrace
if (app.get('env') === 'development') {
    app.use(function(err, req, res, next) {
        res.status(err.status || 500);
        res.render('error', {
            message: err.message,
            error: err
        });
    });
}

// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
        message: err.message,
        error: {}
    });
});



/* Mysql START ------------------------ */

var connection = mysql.createPool({
    connectionLimit : 100,
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE
});

connection.getConnection(function(error){
    if(!error) console.log(hora_actual.set_hora({address : process.env.DB_HOST, port: process.env.DB_PORT}) +"Config - Conexion a BD - OK");
    else console.log(hora_actual.set_hora({address : process.env.DB_HOST, port: process.env.DB_PORT}) +"Config - Conexion a BD - ERROR");
});

/* Mysql END   ------------------------ */


/* Mongoose START ------------------------ */
mongoose.connect('mongodb://'+ process.env.MONGO_HOST +'/'+ process.env.MONGO_DATABASE, function function_name (err, res) {
    if (err) console.log(hora_actual.set_hora({address : process.env.MONGO_HOST, port: process.env.MONGO_PORT}) +"Config - Conexion con Mongo - ERROR");
    else console.log(hora_actual.set_hora({address : process.env.MONGO_HOST, port:  process.env.MONGO_PORT}) +"Config - Conexion con Mongo - OK");
});

/* Monngose END   ------------------------ */




module.exports = app;
