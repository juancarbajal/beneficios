<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 02/09/16
 * Time: 03:19 PM
 */

namespace Cron\Controller;

use Cupon\Model\CuponPuntosLog;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;

class CuponPuntosController extends AbstractActionController
{
    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_CUPON_PUNTOS = 'cupon-puntos.log';
    const NAME_LOG_CUPON_PUNTOS_UNICO = 'oferta-puntos-unico.log';
    const NAME_LOG_CUPON_PUNTOS_SPLIT = 'oferta-puntos-split.log';

    public function updateAction()
    {
        $semana = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miercoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sabado',
            'Sunday' => 'Domingo'
        ];

        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Sólo puede utilizar esta acción desde una consola!');
        }

        $connection = null;
        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_CUPON_PUNTOS);

        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        try {
            $cuponPuntoTable = $this->serviceLocator->get('Cupon\Model\Table\CuponPuntosTable');
            $cuponPuntoLogTable = $this->serviceLocator->get('Cupon\Model\Table\CuponPuntosLogTable');
            $config = $this->getServiceLocator()->get('Config');
            $parametros = $config['cron']['cupon-puntos'];

            $horaActual = date("H:i");
            $diaSemana = date('l');
            $resultCupon = 0;

            if ($parametros["dia"] == "Todos") {
                if ($horaActual == $parametros['hora']) {
                    $listaCupones = $cuponPuntoTable->getCuponPuntosRedimidos();
                    foreach ($listaCupones as $value) {
                        $cuponPuntoTable->porPagarCuponPuntos($value->id);

                        $cuponPuntosLog = new CuponPuntosLog();
                        $cuponPuntosLog->BNF2_Cupon_Puntos_id = $value->id;
                        $cuponPuntosLog->CodigoCupon = $value->CodigoCupon;
                        $cuponPuntosLog->EstadoCupon = "Por Pagar";
                        $cuponPuntosLog->BNF2_Oferta_Puntos_id = $value->BNF2_Oferta_Puntos_id;
                        $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $value->BNF2_Oferta_Puntos_Atributos_id;
                        $cuponPuntosLog->BNF_Cliente_id = $value->BNF_Cliente_id;
                        $cuponPuntosLog->BNF_Usuario_id = null;
                        $cuponPuntosLog->Comentario = "Actualizacion por Cron";
                        $cuponPuntoLogTable->saveCuponPuntosLog($cuponPuntosLog);

                        $resultCupon++;
                    }

                    $titleMessage = "Ejecución de Cron Update Cupon.\n";
                    $message = $titleMessage .
                        "Total de filas afectadas en BNF_Cupon: " . $resultCupon . "\n";

                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                }
            } else {
                if ($horaActual == $parametros['hora'] && $semana[$diaSemana] == $parametros['dia']) {
                    $listaCupones = $cuponPuntoTable->getCuponPuntosRedimidos();
                    foreach ($listaCupones as $value) {
                        $cuponPuntoTable->porPagarCuponPuntos($value->id);

                        $cuponPuntosLog = new CuponPuntosLog();
                        $cuponPuntosLog->BNF2_Cupon_Puntos_id = $value->id;
                        $cuponPuntosLog->CodigoCupon = $value->CodigoCupon;
                        $cuponPuntosLog->EstadoCupon = "Por Pagar";
                        $cuponPuntosLog->BNF2_Oferta_Puntos_id = $value->BNF2_Oferta_Puntos_id;
                        $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $value->BNF2_Oferta_Puntos_Atributos_id;
                        $cuponPuntosLog->BNF_Cliente_id = $value->BNF_Cliente_id;
                        $cuponPuntosLog->BNF_Usuario_id = null;
                        $cuponPuntosLog->Comentario = "Actualizacion por Cron";
                        $cuponPuntoLogTable->saveCuponPuntosLog($cuponPuntosLog);

                        $resultCupon++;
                    }

                    $titleMessage = "Ejecución de Cron Update Cupon.\n";
                    $message = $titleMessage .
                        "Total de filas afectadas en BNF_Cupon: " . $resultCupon . "\n";

                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                }
            }
        } catch (\Exception $ex) {
            $message = $ex->getMessage() . "\n";
            $logger->addWriter($writer);
            $logger->log(Logger::NOTICE, $message);
            return "Error en la ejecución.\n";
        }
        return "Ejecución completada.\n";
    }

    public function caducarAction()
    {
        $bodyMessage = "";
        $message = "";

        $request = $this->getRequest();
        $ofertaPuntosTable = $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosTable');
        $ofertaPuntosAtributoTable = $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosAtributosTable');
        $cuponPuntoTable = $this->serviceLocator->get('Cupon\Model\Table\CuponPuntosTable');
        $cuponPuntoLogTable = $this->serviceLocator->get('Cupon\Model\Table\CuponPuntosLogTable');

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Sólo puede utilizar esta acción desde una consola!');
        }

        $expired = $request->getParam('expiradas');
        $finalized = $request->getParam('finalizadas');
        $result = 0;

        if (!empty($finalized)) {
            try {
                $ofertas = $ofertaPuntosTable->getOfertasPuntosFinalizadas();
                foreach ($ofertas as $dato) {
                    if ($dato->TipoPrecio == "Split") {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock
                            . "' : atributo='" . (int)$dato->Atributo_id . "', ";
                    } else {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock . "', ";
                    }

                    $cupones = $cuponPuntoTable->getExpiredCuponPuntosCreate($dato->id, $dato->Atributo_id);
                    foreach ($cupones as $dataCupon) {
                        $cupon = $cuponPuntoTable->getCuponPuntos($dataCupon->id);
                        $cupon->EstadoCupon = 'Finalizado';
                        $cupon->FechaFinalizado = date("Y-m-d H:i:s");
                        $cuponPuntoTable->saveCuponPuntos($cupon);

                        $cuponPuntosLog = new CuponPuntosLog();
                        $cuponPuntosLog->BNF2_Cupon_Puntos_id = $dataCupon->id;
                        $cuponPuntosLog->CodigoCupon = $dataCupon->CodigoCupon;
                        $cuponPuntosLog->EstadoCupon = "Finalizado";
                        $cuponPuntosLog->BNF2_Oferta_Puntos_id = $dataCupon->BNF2_Oferta_Puntos_id;
                        $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $dataCupon->BNF2_Oferta_Puntos_Atributos_id;
                        $cuponPuntosLog->BNF_Cliente_id = $dataCupon->BNF_Cliente_id;
                        $cuponPuntosLog->BNF_Usuario_id = null;
                        $cuponPuntosLog->Comentario = "Finalizado por Cron";
                        $cuponPuntoLogTable->saveCuponPuntosLog($cuponPuntosLog);
                    }

                    //Guardar Cambios
                    if ($dato->TipoPrecio == "Split") {
                        $result = $result + $ofertaPuntosAtributoTable->caducarOfertaPuntosAtributos($dato->Atributo_id);
                    } else {
                        $result = $result + $ofertaPuntosTable->updateOfertasPuntosFinalizadas($dato->id);
                    }
                    $bodyMessage = $bodyMessage . "\n";
                }

                //Guardamos el Registro de eventos
                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_CUPON_PUNTOS_UNICO);
                if ($result > 0) {
                    $titleMessage = "Actualizar Ofertas Puntos Finalizadas.\n";
                    $message = $titleMessage . $bodyMessage .
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
                $ofertas = $ofertaPuntosTable->getOfertasPuntosCaducadas();
                foreach ($ofertas as $dato) {
                    if ($dato->TipoPrecio == "Split") {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock
                            . "' : atributo='" . (int)$dato->Atributo_id
                            . "' : fecha fin vigencia='" . $dato->FechaVigencia . "', ";
                    } else {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock
                            . "' : fecha fin vigencia='" . $dato->FechaVigencia . "', ";
                    }

                    $cupones = $cuponPuntoTable->getExpiredCuponPuntosFinalized($dato->id, $dato->Atributo_id);
                    foreach ($cupones as $dataCupon) {
                        $cupon = $cuponPuntoTable->getCuponPuntos($dataCupon->id);
                        $cupon->EstadoCupon = 'Caducado';
                        $cupon->FechaCaducado = date("Y-m-d H:i:s");
                        $cuponPuntoTable->saveCuponPuntos($cupon);

                        $cuponPuntosLog = new CuponPuntosLog();
                        $cuponPuntosLog->BNF2_Cupon_Puntos_id = $dataCupon->id;
                        $cuponPuntosLog->CodigoCupon = $dataCupon->CodigoCupon;
                        $cuponPuntosLog->EstadoCupon = "Caducado";
                        $cuponPuntosLog->BNF2_Oferta_Puntos_id = $dataCupon->BNF2_Oferta_Puntos_id;
                        $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $dataCupon->BNF2_Oferta_Puntos_Atributos_id;
                        $cuponPuntosLog->BNF_Cliente_id = $dataCupon->BNF_Cliente_id;
                        $cuponPuntosLog->BNF_Usuario_id = null;
                        $cuponPuntosLog->Comentario = "Caducado por Cron";
                        $cuponPuntoLogTable->saveCuponPuntosLog($cuponPuntosLog);
                    }

                    if ($dato->TipoPrecio == "Split") {
                        $result = $result + $ofertaPuntosAtributoTable->caducarOfertaPuntosAtributos($dato->Atributo_id);
                    } else {
                        $result = $result + $ofertaPuntosTable->updateOfertasPuntosVencidas($dato->id);
                    }
                    $bodyMessage = $bodyMessage . "\n";
                }

                //Guardamos el Registro de eventos
                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_CUPON_PUNTOS_UNICO);
                if ($result > 0) {
                    $titleMessage = "Actualizar Ofertas Puntos Caducadas.\n";
                    $message = $titleMessage . $bodyMessage .
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
