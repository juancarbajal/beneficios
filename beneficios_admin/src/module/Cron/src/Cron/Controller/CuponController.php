<?php

namespace Cron\Controller;

use Cron\Table\CuponTable;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;

class CuponController extends AbstractActionController
{
    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_ETL = 'update-cupon.log';

    public function updateAction()
    {
        $connection = null;
        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_ETL);

        try {
            $getCupon = $this->serviceLocator->get('Cupon\Model\Table\CuponTable');

            $config = $this->getServiceLocator()->get('Config');
            $adapter = new Adapter($config["db"]);
            $cupon = new CuponTable($adapter);

            $resultClienteCorreo = $cupon->updateClienteCorreo();
            $resultCupon = $cupon->updateCupon($getCupon->getListIdClient());
        } catch (\Exception $ex) {
            $message = $ex->getMessage() . "\n";
            $logger->addWriter($writer);
            $logger->log(Logger::NOTICE, $message);
            return "Error en la ejecución.\n";
        }

        $rowClienteCorreo = $resultClienteCorreo->count();

        $titlemessage = "Ejecucion de Cron Update Cupon.\n";
        $message = $titlemessage .
            "Total de filas afectadas en BNF_Cupon: " . $resultCupon . "\n" .
            "Total de filas afectadas en BNF_ClienteCorreo: " . $rowClienteCorreo . "\n";

        $logger->addWriter($writer);
        $logger->log(Logger::INFO, $message);

        return "Ejecución completada.\n";
    }
}
