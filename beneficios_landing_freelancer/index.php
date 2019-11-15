<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
$banner_url = getenv('URL_BANNER');
$agradecimiento_url = getenv('URL_AGRADECIMIENTO');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/images/ICONO_VERISURE.png">
    <title>Verisure</title>

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Didact+Gothic" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/angular.min.js"></script>
    <script src="js/app.js"></script>
</head>

<body ng-app="mainApp">

<nav class="navbar">
    <a href="http://verisure.beneficios.pe/">
        <img src="./images/logo-empresa.png" alt="" class="img-responsive" width="120">
    </a>
    <!--  <a href="#">
       <img src="./images/logo-beneficios-header.png" alt="" class="img-responsive" width="150">
     </a> -->
</nav>

<input type="hidden" name="landing" ng-init="landing='<?= $banner_url ?>'" ng-value="landing">
<input type="hidden" name="agradecimiento_url" ng-init="agradecimiento_url='<?= $agradecimiento_url ?>'" ng-value="agradecimiento_url">

<section ng-controller="MainController">
    <figure ng-init="loadBannerImage()" ng-if="showForms">
        <a id="link_web">
            <img ng-src={{banner}} alt="" class="img-responsive">
        </a>
    </figure>
    <figure ng-if="!showForms">
        <a id="link_web">
            <img ng-src={{agradecimiento_url}} alt="" class="img-responsive">
        </a>
    </figure>
    <section class="container body" ng-if="showForms">
        <form role="form" name="userForm" id="userForm">
            <legend class="text-center first-legend">REGÍSTRATE Y EMPIEZA A DISFRUTAR DE GRANDES BENEFICIOS</legend>
            <br>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group"
                         ng-class="{'has-error': userForm.document.$error.pattern || userForm.document.$error.minlength}">
                        <label class="col-md-4 col-lg-5 control-label" for="document">N° de documento*:</label>
                        <div class="col-md-8 col-lg-6">
                            <input type="text" ng-required="true" ng-minlength="6" ng-disabled="disabledFields.document"
                                   id="document" ng-pattern="/^(\d{6})(\d{2})?(\d{3})?$/" name="document" maxlength="11"
                                   class="form-control input-sm" placeholder="Número de Documento"
                                   ng-model="user.document">
                            <br class="ng-cloak" ng-show="userForm.document.$error.pattern && userForm.document.$error.minlength">
                            <span class="control-label error ng-cloak" ng-show="userForm.document.$error.pattern">
                               Ingresar un CE o DNI o ruc válido.
                             </span>
                            <br class="ng-cloak" ng-show="userForm.document.$error.pattern && userForm.document.$error.minlength">
                            <span class="control-label error ng-cloak" ng-show="userForm.document.$error.minlength">Ingresar como mínimo 8 números</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 col-lg-5 control-label" for="name">Nombre y Apellidos*:</label>
                        <div class="col-md-7 col-lg-7">
                            <input type="text" id="name" ng-required="true" ng-disabled="disabledFields.name"
                                   name="name" maxlength="120" class="form-control input-sm"
                                   placeholder="Nombre y Apellidos" ng-model="user.name">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group email-form-group" ng-class="{'has-error': userForm.email.$error.pattern}">
                        <label class="col-md-4 col-lg-5 control-label" for="email">Email*:</label>
                        <div class="col-md-7 col-lg-7">
                            <input type="email" id="email" ng-required="true" ng-disabled="disabledFields.email"
                                   ng-pattern="/^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]*\.([a-z]{2,4})$/" name="email"
                                   class="form-control input-sm" placeholder="Email" maxlength="60"
                                   ng-model="user.email">
                            <span class="control-label color-gray text-help" ng-show="!userForm.email.$error.pattern">A este correo te asignaremos los puntos</span>
                            <span class="control-label ng-cloak" ng-show="userForm.email.$error.pattern">Ingresar un email válido</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group" ng-class="{'has-error': userForm.telephone.$error.pattern}">
                        <label class="col-md-4 col-lg-5 control-label" for="telephone">Teléfono*:</label>
                        <div class="col-md-5 col-lg-5">
                            <input type="text" id="telephone" ng-required="true" ng-disabled="disabledFields.telephone"
                                   name="telephone"
                                   ng-pattern="/^([#*0-9]{7,15})$/"
                                   class="form-control input-sm" placeholder="Teléfono" ng-model="user.telephone">
                            <span class="control-label ng-cloak" ng-show="userForm.telephone.$error.pattern">Ingresar un teléfono válido</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 col-lg-5 control-label" for="specialist">Nombre de especialista:</label>
                        <div class="col-md-7 col-lg-7">
                            <input type="text" id="specialist" name="specialist" ng-disabled="disabledFields.specialist"
                                   maxlength="80" class="form-control input-sm" placeholder="Nombre del especialista"
                                   ng-model="user.specialist">
                        </div>
                    </div>
                </div>
            </div>
            <p class="p-left-15 text-help" ng-hide="user.members.length > 0 && !formValid">Los campos marcados con * son obligatorios</p>
            <p class="p-left-15 error ng-cloak" ng-show="user.members.length > 0 && !formValid">* Estos campos son obligatorios</p>
            <span class="hidden">{{ formValid = userForm.$valid }}</span>
        </form>
        <form ng-submit="addMember()" name="friendForm" id="friendForm">
            <legend class="text-center second-legend">INGRESA LOS DATOS DE TUS REFERIDOS</legend>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group" ng-class="{'has-error': newMember.showNameError}">
                        <label class="col-md-4 col-lg-5 control-label"
                               for="newUserFirstName">Nombre y Apellidos*:</label>
                        <div class="col-md-7 col-lg-7">
                            <input type="text"
                                   class="form-control input-sm"
                                   placeholder="Nombres y Apellidos"
                                   id="newUserFirstName"
                                   ng-required="true"
                                   maxlength="120"
                                   ng-model="newMember.name">
                        </div>
                        <p class="error m-h-15 ng-cloak" ng-show="showErrorNameRegistered">* Usuario ya referido.</p>
                        <p class="error m-h-15 ng-cloak" ng-show="showErrorName2Registered">* Usuario referido en el
                            mes.</p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group" ng-class="{'has-error': friendForm.newMemberTelephone.$error.pattern}">
                        <label class="col-md-4 col-lg-5 control-label" for="newMemberTelephone">Teléfono*:</label>
                        <div class="col-md-5 col-lg-5">
                            <input type="text"
                                   class="form-control input-sm"
                                   required
                                   id="newMemberTelephone"
                                   ng-pattern="/^([#*0-9]{7,15})$/"
                                   placeholder="Teléfono"
                                   ng-required="true"
                                   name="newMemberTelephone"
                                   ng-model="newMember.telephone">
                            <span class="control-label ng-cloak" ng-show="friendForm.newMemberTelephone.$error.pattern">Ingresar un teléfono válido</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-sm-12 col-md-12">
                    <button class="btn btn-default btn-lg btn-affiliate " disabled ng-click="addMember()"
                            ng-disabled="friendForm.$invalid">Agregar Referido
                    </button>
                </div>
            </div>
            <section class="affiliate-container">
                <h4>Tus Referidos</h4>
                <ul class="members-list ng-cloak">
                    <li ng-repeat="member in user.members">
                        <span>{{member.name}}</span>
                        <a href="" class="glyphicon glyphicon-remove-circle" ng-click="deleteItem($index)"></a>
                    </li>
                </ul>
            </section>
        </form>
        <div class="row text-center m-top-20">
            <button type="button" name="button" ng-click="sendAllData()" disabled ng-disabled="!isAllIsOk(formValid)"
                    class="btn btn-send btn-lg">Enviar referidos
            </button>
        </div>
    </section>
    <section class="container body m-h-15 text-center ng-cloak" ng-if="!showForms">
        <h3>Gracias por registrarte y referirnos a tus amigos</h3>
        <p>Ahora puedes acceder a grandes beneficios que te ofrece Alarmas Verisure.</p>
        <br>
        <p>Para conocerlos ingresa tu número de documento en <a href="http://verisure.beneficios.pe/">verisure.beneficios.pe</a>
        </p>
    </section>
    <section class="terms container body m-h-15 text-center ng-cloak" ng-if="showForms">
        <h3>REGLAMENTO BENEFICIOS VERISURE</h3>
        <p>1 - Programa de beneficios válido para nuevas contrataciones e instalaciones de Sistemas de Alarmas. No se
            considera nueva contratación un traslado o una conexión de un sistema preinstalado.</p>
        <p>2 – Beneficio no acumulable y válido solo dentro de un mes calendario desde la fecha de instalación del amigo
            o familia.</p>
        <p>3 - Para poder recibir los regalos, la alarma del cliente que refiere debe estar operativa y al día en los
            pagos. </p>
        <p>4 – Los regalos podrán ser canjeados una vez realizada la instalación y conexión de la alarma del amigo o
            familiar.</p>
        <p>5 - Recuerde que con carácter previo a la facilitación de los datos de sus amigos usted debe obtener su
            consentimiento para ceder sus datos a VERISURE de manera que sus amigos conocen y consienten la posibilidad
            de que VERISURE les contacte con el fin de ofertarle sus productos y servicios de seguridad privada. </p>
        <p>6 - Este programa de beneficios no es válido para empleados o familiares de empleados de Verisure Perú. </p>

        <p>7 - Verisure Perú se compromete a la entrega del premio en un plazo máximo de 15 días.</p>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <button type="button" class="boton-cerrar" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <div class="modal-body">
                    <img ng-src={{popup}} id="popup_pub" class="img-responsive" style="display:block; margin:auto;">
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="text-center">
    <img src="./images/logo_vs_blanco.png" alt="" class="img-responsive" width="100">
    <!-- <nav>
      <a href="#">©2010-2017 Beneficios.pe, Todos los derechos reservados</a>
      <a href="#">Términos y Condiciones</a>
    </nav> -->
</footer>
</body>
</html>