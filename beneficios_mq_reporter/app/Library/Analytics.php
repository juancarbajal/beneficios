<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/03/16
 * Time: 11:32 AM
 */

namespace App\Library;

use Google_Auth_AssertionCredentials;
use Google_Client;
use Google_Service_Analytics;

class Analytics
{
    protected $analytics;

    public function __construct()
    {
        $service_account_email = env('service_account_email');
        $key_file_location = './public/p12/' . env('key_file_analytics');

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

    public function getResults($FechaInicio, $FechaFin, $array, $uniquePageviews = false)
    {
        $resultado = $this->analytics->data_ga->get(
            'ga:' . env('ids_vista_google_analytics'),
            $FechaInicio,
            $FechaFin,
            'ga:totalEvents,ga:uniqueEvents,ga:pageviews' . (($uniquePageviews) ?',ga:uniquePageviews' :''),
            $array
        );

        if (count($resultado->getRows()) > 0) {
            $rows = $resultado->getRows();
            return $rows;
        } else {
            return array(0, 0);
        }
    }

    public function getResults_2($FechaInicio, $FechaFin, $array)
    {
        $resultado = $this->analytics->data_ga->get(
            'ga:' . env('ids_vista_google_analytics'),
            $FechaInicio,
            $FechaFin,
            'ga:pageviews,ga:uniquePageviews',
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