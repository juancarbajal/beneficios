<?php
namespace EmpresaCliente\Controller;

use Exception;
use Google_Client;
use Google_Service_Analytics;
use Google_Service_Books;
use OAuth;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function oauthAction() {

        $access_token = null;
        $resultados = null;


        // Crear el objeto cliente y establecer la configuración de autorización
        // Del client_secrets.p12 ha descargado desde la consola de desarrolladores.
        $client = new Google_Client();
        $client->setAuthConfigFile('./public/p12/client_secrets.p12');
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth');
        $client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
        //var_dump($client);exit;
        // Handle flujo autorización del servidor.
        if (! isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            exit;
        } else {
            $client->authenticate($_GET['code']);
            $access_token = $client->getAccessToken();
        }

        if ($access_token != null) {
            // Establecer el token de acceso en el cliente.
            $client->setAccessToken($access_token);

            // Crear un objeto servicio de análisis autorizado.
            $analytics = new Google_Service_Analytics($client);

            // Obtener el primer Identificación del punto de vista (perfil) para el usuario autorizado.
            $profile = $this->getFirstProfileId($analytics);

            // Obtener los resultados del Core Reporting API e imprimir los resultados.
            $results = $this->getResults($analytics, $profile);
            $resultados = $this->printResults($results);
        }

        return new ViewModel(
            array(
                'resultados' => $resultados,
            )
        );

    }

    function getFirstprofileId(&$analytics) {
        // Obtener primera vista (perfil) Identificación del usuario.

        // Obtener la lista de cuentas para el usuario autorizado.
        $accounts = $analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            $firstAccountId = $items[0]->getId();

            // Obtener la lista de propiedades para el usuario autorizado.
            $properties = $analytics->management_webproperties
                ->listManagementWebproperties($firstAccountId);

            if (count($properties->getItems()) > 0) {
                $items = $properties->getItems();
                $firstPropertyId = $items[0]->getId();

                // Obtener la lista de puntos de vista (perfiles) para el usuario autorizado.
                $profiles = $analytics->management_profiles
                    ->listManagementProfiles($firstAccountId, $firstPropertyId);

                if (count($profiles->getItems()) > 0) {
                    $items = $profiles->getItems();
                    // Return the first view (profile) ID.
                    return $items[3]->getId();

                } else {
                    throw new Exception('No views (profiles) found for this user.');
                }
            } else {
                throw new Exception('No properties found for this user.');
            }
        } else {
            throw new Exception('No accounts found for this user.');
        }
    }

    function getResults(&$analytics, $profileId) {
        // Llama al Core de informes activos y de consultas para el número de sesiones
        // Durante los últimos siete días.
        return $analytics->data_ga->get(
            'ga:' . $profileId,
            '7daysAgo',
            'today',
            'ga:browser');
    }

    function printResults(&$results) {
        // Analiza la respuesta del Core Reporting API y grabados
        // El nombre del perfil y sesiones totales.
        if (count($results->getRows()) > 0) {

            // Obtener el nombre del perfil.
            $profileName = $results->getProfileInfo()->getProfileName();

            // Obtener la entrada para el primer número de la primera fila.
            $rows = $results->getRows();
            $sessions = $rows[0][0];

            // Imprima los resultados.
            return "<p>First view (profile) found: {$profileName}</p><p>Total sessions: {$sessions}</p>";
        } else {
            return "<p>No results found.</p>";
        }
    }
}
