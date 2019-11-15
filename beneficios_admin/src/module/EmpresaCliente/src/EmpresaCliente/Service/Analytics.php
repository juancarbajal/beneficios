<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 11/01/16
 * Time: 12:03 PM
 */

namespace EmpresaCliente\Service;

use Google_Auth_AssertionCredentials;
use Google_Client;
use Google_Service_Analytics;

class Analytics
{
    protected $analytics;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        $service_account_email = $config['service_account_email'];
        $key_file_location = './public/p12/' . $config['key_file_analytics'];

        $client = new Google_Client();
        $client->setApplicationName("analytics");
        $this->analytics = new Google_Service_Analytics($client);

        $key = file_get_contents($key_file_location);
        $cred = new Google_Auth_AssertionCredentials(
            $service_account_email,
            array(Google_Service_Analytics::ANALYTICS_READONLY),
            $key
        );
        $client->setAssertionCredentials($cred);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }
    }

    public function getResults($FechaInicio, $FechaFin, $array, $id_vista = null)
    {
        if ($id_vista == null) {
            $id_vista = $this->config['ids_vista_google_analytics'];
        }
        $resultado = $this->analytics->data_ga->get(
            'ga:' . $id_vista,
            $FechaInicio,
            $FechaFin,
            'ga:totalEvents,ga:uniqueEvents',
            $array
        );

        if (count($resultado->getRows()) > 0) {
            $rows = $resultado->getRows();
            return $rows;
        } else {
            return array(0, 0);
        }
    }
}