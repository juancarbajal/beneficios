<?php

namespace Cron\Controller;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

class DescargaController extends AbstractActionController
{
    protected $ofertaTable;
    protected $ofertaAtributoTable;
    protected $bolsaTable;
    protected $cuponTable;

    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_UPDATE_DOWNLOAD_ENDING = 'oferta-descarga-actualizar-finalizadas.log';
    const NAME_LOG_UPDATE_DOWNLOAD_EXPIRED = 'oferta-descarga-actualizar-caducadas.log';

    public function indexAction()
    {
        return new ViewModel();
    }

    public function updateAction()
    {
        $bodyMessage = "";
        $message = "";

        $request = $this->getRequest();
        $this->ofertaTable = $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
        $this->ofertaAtributoTable = $this->serviceLocator->get('Oferta\Model\Table\OfertaAtributosTable');
        $this->bolsaTable = $this->serviceLocator->get('Paquete\Model\Table\BolsaTotalTable');
        $this->cuponTable = $this->serviceLocator->get('Cupon\Model\Table\CuponTable');

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Sólo puede utilizar esta acción desde una consola!');
        }

        $expired = $request->getParam('expiradas');
        $finalized = $request->getParam('finalizadas');
        $result = 0;

        if (!empty($finalized)) {
            try {
                $ofertas = $this->ofertaTable->getOfertasFinalizadas(1);
                foreach ($ofertas as $dato) {
                    if ($dato->TipoAtributo == "Split") {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock
                            . "' : atributo='" . (int)$dato->Atributo_id . "', ";
                    } else {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock . "', ";
                    }

                    //Devolucion del stock actual a la bolsa
                    $bolsa = $this->bolsaTable->getBolsaTotal(
                        $dato->BNF_BolsaTotal_TipoPaquete_id,
                        $dato->BNF_BolsaTotal_Empresa_id
                    );

                    $bodyMessage = $bodyMessage . "bolsa anterior='" . (int)$bolsa->BolsaActual . "', ";
                    $bolsa->BolsaActual = $bolsa->BolsaActual + $dato->Stock;
                    $bodyMessage = $bodyMessage . "nueva bolsa='" . (int)$bolsa->BolsaActual . "'";

                    //Actualizacion de Cupones
                    $cupones = $this->cuponTable->getExpiredCuponCreate($dato->id, $dato->Atributo_id);
                    foreach ($cupones as $data_cupon) {
                        $cupon = $this->cuponTable->getCupon($data_cupon->id);
                        $cupon->EstadoCupon = 'Finalizado';
                        $cupon->FechaFinalizado = date("Y-m-d H:i:s");
                        $this->cuponTable->saveCupon($cupon);
                    }

                    //Guardar Cambios
                    $this->bolsaTable->editBolsa($bolsa);
                    if ($dato->TipoAtributo == "Split") {
                        $result = $result + $this->ofertaAtributoTable->caducarOfertaAtributos($dato->Atributo_id);
                        if ($this->ofertaAtributoTable->totalAtributosActivos($dato->id) == 0) {
                            $this->ofertaTable->updateOfertasFinalizadas($dato->id);
                        }
                    } else {
                        $result = $result + $this->ofertaTable->updateOfertasFinalizadas($dato->id);
                    }
                    $bodyMessage = $bodyMessage . "\n";
                }

                //Guardamos el Registro de eventos
                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_UPDATE_DOWNLOAD_ENDING);
                if ($result > 0) {
                    $titlemessage = "Actualizar Ofertas Descarga Finalizadas.\n";
                    $message = $titlemessage . $bodyMessage .
                        "Total de ofertas afectadas: " . $result . ".\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                } else {
                    $message = "No se actualizó ninguna oferta descarga.\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::NOTICE, $message);
                }
            } catch (\Exception $ex) {
                return $ex->getMessage() . "\n";
            }
        } elseif (!empty($expired)) {
            try {
                $ofertas = $this->ofertaTable->getOfertasCaducadas(1);
                foreach ($ofertas as $dato) {
                    if ($dato->TipoAtributo == "Split") {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock
                            . "' : atributo='" . (int)$dato->Atributo_id . "', " . ": fecha fin vigencia='" .
                            $dato->FechaFinVigencia . "'\n";
                    } else {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock . "', " . ": fecha fin vigencia='" .
                            $dato->FechaFinVigencia . "'\n";
                    }

                    $cupones = $this->cuponTable->getExpiredCuponGenerate($dato->id, $dato->Atributo_id);
                    foreach ($cupones as $data_cupon) {
                        $cupon = $this->cuponTable->getCupon($data_cupon->id);
                        $cupon->EstadoCupon = 'Caducado';
                        $cupon->FechaCaducado = date("Y-m-d H:i:s");
                        $this->cuponTable->saveCupon($cupon);
                    }

                    $result = $result + $this->ofertaTable->updateOfertasVencidas($dato->id);
                }

                //Guardamos el Registro de eventos
                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_UPDATE_DOWNLOAD_EXPIRED);
                if ($result > 0) {
                    $titlemessage = "Actualizar Ofertas Descargas Caducadas.\n";
                    $message = $titlemessage . $bodyMessage .
                        "Total de ofertas afectadas: " . $result . ".\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                } else {
                    $message = "No se actualizó ninguna oferta descarga.\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::NOTICE, $message);
                }
            } catch (\Exception $ex) {
                return $ex->getMessage() . "\n";
            }
        }

        return $message;
    }
}
