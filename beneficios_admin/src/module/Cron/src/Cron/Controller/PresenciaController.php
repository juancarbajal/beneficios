<?php

namespace Cron\Controller;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

class PresenciaController extends AbstractActionController
{
    protected $ofertaTable;
    protected $ofertaAtributoTable;
    protected $bolsaTable;
    protected $cuponTable;

    const OFERTA_AGOTADA = 0;
    const ESTADO_OFERTA_AGOTADA = 'Caducado';

    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_UPDATE_PRESENCE_ENDING = 'oferta-presencia-actualizar-finalizadas.log';
    const NAME_LOG_UPDATE_PRESENCE_EXPIRED = 'oferta-presencia-actualizar-caducadas.log';
    const NAME_LOG_CHANGE_PRESENCE = 'oferta-presencia-disminuir-stock.log';

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
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $ending = $request->getParam('finalizadas');
        $expired = $request->getParam('expiradas');
        $result = 0;

        if (!empty($ending)) {
            try {
                $ofertas = $this->ofertaTable->getOfertasFinalizadas(2);
                foreach ($ofertas as $dato) {
                    if ($dato->TipoAtributo == "Split") {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock
                            . "' : atributo='" . (int)$dato->Atributo_id . "', ";
                    } else {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock . "', ";
                    }

                    $bolsa = $this->bolsaTable->getBolsaTotal(
                        $dato->BNF_BolsaTotal_TipoPaquete_id,
                        $dato->BNF_BolsaTotal_Empresa_id
                    );

                    $bodyMessage = $bodyMessage . "bolsa anterior='" . (int)$bolsa->BolsaActual . "', ";
                    $bolsa->BolsaActual = $bolsa->BolsaActual + $dato->Stock;
                    $bodyMessage = $bodyMessage . "nueva bolsa='" . (int)$bolsa->BolsaActual . "'";

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
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_UPDATE_PRESENCE_ENDING);
                if ($result > 0) {
                    $titlemessage = "Actualizar Ofertas Presencia Finalizadas.\n";
                    $message = $titlemessage . $bodyMessage .
                        "Total de ofertas afectadas: " . $result . ".\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                } else {
                    $message = "No se actualizó ninguna oferta presencia.\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::NOTICE, $message);
                }
            } catch (\Exception $ex) {
                return $ex->getMessage() . "\n";
            }
        } elseif (!empty($expired)) {
            try {
                $ofertas = $this->ofertaTable->getOfertasCaducadas(2);
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
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_UPDATE_PRESENCE_EXPIRED);
                if ($result > 0) {
                    $titlemessage = "Actualizar Ofertas Presencia Caducadas.\n";
                    $message = $titlemessage . $bodyMessage .
                        "Total de ofertas afectadas: " . $result . ".\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                } else {
                    $message = "No se actualizó ninguna oferta presencia.\n";
                    $logger->addWriter($writer);
                    $logger->log(Logger::NOTICE, $message);
                }
            } catch (\Exception $ex) {
                return $ex->getMessage() . "\n";
            }
        }

        return $message;
    }

    public function changeAction()
    {
        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_CHANGE_PRESENCE);
        $bodyMessage = "";
        $request = $this->getRequest();
        $this->ofertaTable = $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
        $this->ofertaAtributoTable = $this->serviceLocator->get('Oferta\Model\Table\OfertaAtributosTable');
        $this->bolsaTable = $this->serviceLocator->get('Paquete\Model\Table\BolsaTotalTable');
        $this->cuponTable = $this->serviceLocator->get('Cupon\Model\Table\CuponTable');

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $result = 0;
        $ofertas = $this->ofertaTable->getOfertasPresencia();
        foreach ($ofertas as $dato) {
            $estado = false;
            if ($dato->Stock > $this::OFERTA_AGOTADA) {
                if ($dato->TipoAtributo == "Split") {
                    $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                        . " : stock='" . (int)$dato->Stock
                        . "' : atributo='" . (int)$dato->Atributo_id . "', ";
                } else {
                    $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                        . " : stock='" . (int)$dato->Stock . "', ";
                }

                $dato->Stock = $dato->Stock - 1;
                $bodyMessage = $bodyMessage . "nuevo stock='" . (int)$dato->Stock . "' ";

                if ($dato->Stock == $this::OFERTA_AGOTADA) {
                    $dato->Estado = $this::ESTADO_OFERTA_AGOTADA;
                    $dato->StockInicial = 0;
                    $estado = true;
                    $bodyMessage = $bodyMessage . '(Oferta Caducada)';
                }

                if ($dato->TipoAtributo == "Split") {
                    $atributos = $this->ofertaAtributoTable->getOfertaAtributos($dato->Atributo_id);
                    $atributos->Stock = $dato->Stock;
                    $atributos->StockInicial = $dato->StockInicial;
                    $this->ofertaAtributoTable->saveOfertaAtributos($atributos);
                } else {
                    $this->ofertaTable->saveOferta($dato);
                }
                $result++;
                $bodyMessage = $bodyMessage . "\n";
            }

            if ($estado) {
                $hoy = date_create('now');
                $vigencia = date_create($dato->FechaFinVigencia);
                $diferencia = date_diff($hoy, $vigencia);

                if ($diferencia->format("%r%a") < 0) {
                    $this->cuponTable->updateXofertaFinalizado($dato->id);
                    $this->cuponTable->updateXofertaCaducado($dato->id);
                }
            }
        }

        if ($result > 0) {
            $mensaje = "Actualización del Stock de las Ofertas Presencia.\n" .
                $bodyMessage .
                "Total de ofertas afectadas: " . $result . ".\n";
            $logger->addWriter($writer);
            $logger->log(Logger::INFO, $mensaje);
            return $mensaje;
        } else {
            $mensaje = "No se actualizó ninguna oferta presencia.\n";
            $logger->addWriter($writer);
            $logger->log(Logger::NOTICE, $mensaje);
            return $mensaje;
        }
    }
}
