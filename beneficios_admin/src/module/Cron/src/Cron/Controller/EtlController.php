<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 12/01/16
 * Time: 10:14 AM
 */

namespace Cron\Controller;

use Cron\Table\DimEmpresaTable;
use Cron\Table\DimLocalidad;
use Cron\Table\MetClientePreguntasTable;
use Cron\Table\MetClienteTable;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class EtlController extends AbstractActionController
{
    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_ETL = 'etl-client.log';

    public function generateAction()
    {
        $connection = null;
        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_ETL);

        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        try {
            $config = $this->getServiceLocator()->get('Config');
            $adapter = new Adapter($config["db"]);

            $metCliente = new MetClienteTable($adapter);
            $metClientePreguntas = new MetClientePreguntasTable($adapter);
            $dimEmpresa = new DimEmpresaTable($adapter);
            $dimLocalidad = new DimLocalidad($adapter);

            //Deletes
            $metCliente->truncate();
            $metClientePreguntas->truncate();
            $resultDelEmpresa = $dimEmpresa->delete();
            $resultDelLocalidad = $dimLocalidad->delete();

            //Inserts
            $resultDimEmpresa = $dimEmpresa->insert();
            $resultDimLocalidad = $dimLocalidad->insert();
            $resultDimCliente = $metCliente->cupones();
            $resultDimCliente2 = $metCliente->envios();
            $resultDimCliente3 = $metClientePreguntas->preguntas();
        } catch (\Exception $ex) {
            $message = $ex->getMessage() . "\n";
            $logger->addWriter($writer);
            $logger->log(Logger::NOTICE, $message);
            return "Error en la ejecución.\n";
        }

        //Resultado de Insersiones
        $rowDelEmpresa = $resultDelEmpresa->count();
        $rowDelLocalidad = $resultDelLocalidad->count();

        //Resultado de Insersiones
        $rowDimEmpresa = $resultDimEmpresa->count();
        $rowDimLocalidad = $resultDimLocalidad->count();
        $rowDimCliente = $resultDimCliente->count();
        $rowDimCliente2 = $resultDimCliente2->count();
        $rowDimCliente3 = $resultDimCliente3->count();

        $titlemessage = "Ejecucion de Cron ETL Cliente.\n";
        $message = $titlemessage .
            "Total Eliminados en BNF_DM_Dim_Empresa: " . $rowDelEmpresa . "\n" .
            "Total Eliminados en BNF_DM_DIM_Localidad: " . $rowDelLocalidad . "\n" .
            "Total Creados en BNF_DM_Dim_Empresa: " . $rowDimEmpresa . "\n" .
            "Total Creados en BNF_DM_DIM_Localidad: " . $rowDimLocalidad . "\n" .
            "Total Creados en BNF_DM_Met_Cliente Cupones: " . $rowDimCliente . "\n" .
            "Total Creados en BNF_DM_Met_Cliente Leads: " . $rowDimCliente2 . "\n" .
            "Total Creados en BNF_DM_Met_Cliente_Preguntas: " . $rowDimCliente3 . "\n";
        $logger->addWriter($writer);
        $logger->log(Logger::INFO, $message);

        return "Ejecución completada.\n";
    }
}