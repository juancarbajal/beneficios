<?php
/**
 * Created by PhpStorm.
 * User: janaq
 * Date: 25/10/16
 * Time: 05:29 PM
 */

namespace Cron\Controller;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class BusquedaController extends AbstractActionController
{
    protected $busquedaTable;

    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_UPDATE_LEAD_ENDING = 'busqueda.log';

    public function updateAction()
    {
        $bodyMessage = "";

        $request = $this->getRequest();
        $this->busquedaTable = $this->serviceLocator->get('Oferta\Model\Table\BusquedaTable');

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Sólo puede utilizar esta acción desde una consola!');
        }

        $result = 0;

        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_UPDATE_LEAD_ENDING);

        try {
            $datosBusqueda = $this->busquedaTable->getAllBusquedaEmpresa();
            foreach ($datosBusqueda as $item) {
                $item->Descripcion = $this->getDescripcionBusqueda($item->Descripcion);
                $this->busquedaTable->saveBusqueda($item);

                $result++;
            }

            if ($result > 0) {
                $titleMessage = "Actualizar Empresas en Busqueda.\n";
                $message = $titleMessage . $bodyMessage .
                    "Total de registros afectados: " . $result . ".\n";
                $logger->addWriter($writer);
                $logger->log(Logger::INFO, $message);
            } else {
                $message = "No se actualizó ningun registro en Busqueda.\n";
                $logger->addWriter($writer);
                $logger->log(Logger::NOTICE, $message);
            }
        } catch (\Exception $ex) {
            $message = $ex->getMessage() . "\n";
            $logger->addWriter($writer);
            $logger->log(Logger::NOTICE, $message);
            return "Error en la ejecución.\n";
        }
        return $message;
    }

    public function getDescripcionBusqueda($cadena)
    {
        $cadena = trim($cadena);
        $a = array(
            'S/.', '!', '¡', ' en vez de ', ' por ', ' en ', '+', ' desde ', '®', '#', ':', '.', ' el ', ' la ',
            ' los ', ' las ', ' un ', ' una ', ' unos ', ' unas ', ' y ', ' ni ', ' que ', ' ya ', ' bien ', ' sea ',
            ' pero ', ' mas ', ' sino ', ' porque ', ' pues ', ' ya que ', ' puesto que ', ' luego ', ' pues ',
            ' así que ', ' así pues ', ' si ', ' con tal que ', ' siempre que ', ' para ', ' para que ',
            ' a fin de que ', ' aunque ', ' por más que ', ' bien que ', ' de ', ' del ', ' al ',
            ' a ', ' e ', ' i ', ' o ', ' u ', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', ',',
            '/', ';', '*', '\\', '$', '%', '@', '', '©', '£', '¥', '|', '°', '¬', '"', '&', '(', ')', '?', '¿', "'",
            '{', '}', '^', '~', '`', '<', '>','´', ' - '
        );
        $b = array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', '',
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '',
            '', '', '', '','', ' '
        );
        $cadena = strtolower(str_ireplace($a, $b, strtolower($cadena)));
        return ucwords(strtolower(preg_replace('/\s\s+/', ' ', $cadena)));
    }
}
