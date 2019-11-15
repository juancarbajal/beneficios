<?php

namespace Cron\Controller;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

class LeadController extends AbstractActionController
{
    protected $ofertaTable;
    protected $bolsaTable;
    protected $cuponTable;

    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_UPDATE_LEAD_ENDING = 'oferta-lead-actualizar-finalizadas.log';
    //const NAME_LOG_UPDATE_LEAD_EXPIRED = 'oferta-lead-actualizar-caducadas.log';

    public function indexAction()
    {
        return new ViewModel();
    }

    public function updateAction()
    {
        $bodymessage = "";
        $message = "";

        $request = $this->getRequest();
        $this->ofertaTable = $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
        $this->bolsaTable = $this->serviceLocator->get('Paquete\Model\Table\BolsaTotalTable');
        $this->cuponTable = $this->serviceLocator->get('Cupon\Model\Table\CuponTable');

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Sólo puede utilizar esta acción desde una consola!');
        }

        //$expired = $request->getParam('expiradas');
        $finalized = $request->getParam('finalizadas');
        $result = 0;

        if (!empty($finalized)) {
            try {
                $ofertas = $this->ofertaTable->getOfertasFinalizadas(3);
                foreach ($ofertas as $dato) {
                    //Devolucion del stock actual a la bolsa
                    $bodymessage = $bodymessage . " - Oferta " . $dato->id . ": stock='" . (int)$dato->Stock . "', ";
                    $bolsa = $this->bolsaTable->getBolsaTotal(
                        $dato->BNF_BolsaTotal_TipoPaquete_id,
                        $dato->BNF_BolsaTotal_Empresa_id
                    );
                    $bodymessage = $bodymessage . "bolsa anterior='" . (int)$bolsa->BolsaActual . "', ";
                    $bolsa->BolsaActual = $bolsa->BolsaActual + $dato->Stock;
                    $bodymessage = $bodymessage . "nueva bolsa='" . (int)$bolsa->BolsaActual . "'";

                    //Guardamos los cambios
                    $this->bolsaTable->editBolsa($bolsa);
                    $result = $result + $this->ofertaTable->updateOfertasFinalizadas($dato->id);
                    $bodymessage = $bodymessage . "\n";
                }

                //Guardamos el Registro de eventos
                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_UPDATE_LEAD_ENDING);
                if ($result > 0) {
                    $titlemessage = "Actualizar Ofertas Lead.\n";
                    $message = $titlemessage . $bodymessage .
                        "Total de ofertas afectadas: " . $result . ".\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                } else {
                    $message = "No se actualizó ninguna oferta lead.\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::NOTICE, $message);
                }
            } catch (\Exception $ex) {
                return $ex->getMessage() . "\n";
            }
        }
        /*elseif (!empty($expired)) {
            try {
                $ofertas = $this->ofertaTable->getOfertasCaducadas(3);
                foreach ($ofertas as $dato) {
                    $bodymessage = $bodymessage . " - Oferta " . $dato->id . ": stock='" . (int)$dato->Stock . "', ";
                    $bolsa = $this->bolsaTable->getBolsaTotal(
                        $dato->BNF_BolsaTotal_TipoPaquete_id,
                        $dato->BNF_BolsaTotal_Empresa_id
                    );
                    $bodymessage = $bodymessage . "bolsa anterior='" . (int)$bolsa->BolsaActual . "', ";
                    $bolsa->BolsaActual = $bolsa->BolsaActual + $dato->Stock;
                    $bodymessage = $bodymessage . "nueva bolsa='" . (int)$bolsa->BolsaActual . "'";
                    $this->bolsaTable->editBolsa($bolsa);
                    $result = $result + $this->ofertaTable->updateOfertasVencidas($dato->id);
                    $bodymessage = $bodymessage . "\n";
                }

                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_UPDATE_LEAD_EXPIRED);
                if ($result > 0) {
                    $titlemessage = "Actualizar Ofertas Lead Caducadas.\n";
                    $message = $titlemessage . $bodymessage .
                        "Total de ofertas afectadas: " . $result . ".\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                } else {
                    $message = "No se actualizó ninguna oferta lead.\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::NOTICE, $message);
                }
            } catch (\Exception $ex) {
                return $ex->getMessage() . "\n";
            }
        }*/

        return $message;
    }
}
