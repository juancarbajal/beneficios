<?php

/**
 * Global Configuration Override
 **/

return array(

    'db' => array(
        'driver' => 'pdo_mysql',
        'hostname' => '127.0.0.1',
        'database' => 'admin_beneficios',
        'username' => 'root',
        'password' => '123456',
        'port' => '3306',
        'options' => array('buffer_results' => true),
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\''
        )
    ),

    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'mail' => array(
        'message' => array(
            'from' => 'contacto@ofertaexpress.pe',
            'from_name' => 'OfertaExpress.pe'
        ),
        'transport' => array(
            'options' => array(
                'host' => 'smtp.gmail.com',
                'connection_class' => 'login',
                'connection_config' => array(
                    'ssl' => 'ssl',
                    'username' => 'weeareroialty@gmail.com',
                    'password' => 'Roialty100%',
                ),
                'port' => 465,
            ),
        ),
    ),
    //Directorio de Imagenes
    'categoria' => 'http://b-web.jnq.io/category/',
    'campania' => 'http://b-web.jnq.io/campaign/',
    //Dimensiones
    'oferta_img' => array('width' => 360, 'height' => 203),
    'oferta_medium' => array('width' => 555, 'height' => 313),
    'oferta_large' => array('width' => 750, 'height' => 425),
    'banner_lead' => array('width' => 1140, 'height' => 285),//dimensiones para el banner de lead
    'banners' => array('width' => 1140, 'height' => 180),
    'imagen_principal' => array('width' => 263, 'height' => 388),
    'galeria' => array('width' => 818, 'height' => 360),
    'popup' => array('width' => 607, 'height' => 1023),
    //Logos de Empresa
    'logo' => array('width' => 190, 'height' => 100),
    'logo_fixed' => array('width' => 150, 'height' => 90),
    'logo_large' => array('width' => 160, 'height' => 100),
    'logo_medium' => array('width' => 140, 'height' => 80),
    'logo_small' => array('width' => 90, 'height' => 50),
    'logo_site' => array('width' => 100, 'height' => 30),
    //JS y CSS
    'version_script' => '38',
    'time_session' => 1200,
    'debug_mode' => false,
    'ids_vista_google_analytics' => '113588585',
    'ids_vista_google_analytics_crm' => '115140374',
    'service_account_email' => 'beneficios@spheric-duality-116315.iam.gserviceaccount.com',
    'key_file_analytics' => 'client_secrets.p12',
    'email_sender' => array(
        'demanda' => "Ajustiniano98@gmail.com",
        'asignacion' => "Ajustiniano98@gmail.com",
        'referidos' => "Ajustiniano98@gmail.com",
    ),
    'cron' => array(
        'cupon-puntos' => array(
            'dia' => "Viernes",
            'hora' => "16:31"
        )
    ),
    'URL_WEB' => 'http://ptos-dev.bnfcios.xyz/',
    'size_file_upload' => 20971520
);
