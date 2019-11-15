<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 02/09/16
 * Time: 03:19 PM
 */

namespace Cron\Controller;

use Cupon\Model\CuponPremiosLog;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;

class CuponPremiosController extends AbstractActionController
{
    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_CUPON_PREMIOS = 'cupon-premios.log';
    const NAME_LOG_CUPON_PREMIOS_UNICO = 'oferta-premios-unico.log';
    const NAME_LOG_CUPON_PREMIOS_SPLIT = 'oferta-premios-split.log';

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
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_CUPON_PREMIOS);

        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        try {
            $cuponPuntoTable = $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosTable');
            $cuponPuntoLogTable = $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosLogTable');
            $config = $this->getServiceLocator()->get('Config');
            $parametros = $config['cron']['cupon-premios'];

            $horaActual = date("H:i");
            $diaSemana = date('l');
            $resultCupon = 0;

            if ($parametros["dia"] == "Todos") {
                if ($horaActual == $parametros['hora']) {
                    $listaCupones = $cuponPuntoTable->getCuponPremiosRedimidos();
                    foreach ($listaCupones as $value) {
                        $cuponPuntoTable->porPagarCuponPremios($value->id);

                        $cuponPremiosLog = new CuponPremiosLog();
                        $cuponPremiosLog->BNF3_Cupon_Premios_id = $value->id;
                        $cuponPremiosLog->CodigoCupon = $value->CodigoCupon;
                        $cuponPremiosLog->EstadoCupon = "Por Pagar";
                        $cuponPremiosLog->BNF3_Oferta_Premios_id = $value->BNF3_Oferta_Premios_id;
                        $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $value->BNF3_Oferta_Premios_Atributos_id;
                        $cuponPremiosLog->BNF_Cliente_id = $value->BNF_Cliente_id;
                        $cuponPremiosLog->BNF_Usuario_id = null;
                        $cuponPremiosLog->Comentario = "Actualizacion por Cron";
                        $cuponPuntoLogTable->saveCuponPremiosLog($cuponPremiosLog);

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
                    $listaCupones = $cuponPuntoTable->getCuponPremiosRedimidos();
                    foreach ($listaCupones as $value) {
                        $cuponPuntoTable->porPagarCuponPremios($value->id);

                        $cuponPremiosLog = new CuponPremiosLog();
                        $cuponPremiosLog->BNF3_Cupon_Premios_id = $value->id;
                        $cuponPremiosLog->CodigoCupon = $value->CodigoCupon;
                        $cuponPremiosLog->EstadoCupon = "Por Pagar";
                        $cuponPremiosLog->BNF3_Oferta_Premios_id = $value->BNF3_Oferta_Premios_id;
                        $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $value->BNF3_Oferta_Premios_Atributos_id;
                        $cuponPremiosLog->BNF_Cliente_id = $value->BNF_Cliente_id;
                        $cuponPremiosLog->BNF_Usuario_id = null;
                        $cuponPremiosLog->Comentario = "Actualizacion por Cron";
                        $cuponPuntoLogTable->saveCuponPremiosLog($cuponPremiosLog);

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
        $ofertaPremiosTable = $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');
        $ofertaPremiosAtributoTable = $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosAtributosTable');
        $cuponPuntoTable = $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosTable');
        $cuponPuntoLogTable = $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosLogTable');

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Sólo puede utilizar esta acción desde una consola!');
        }

        $expired = $request->getParam('expiradas');
        $finalized = $request->getParam('finalizadas');
        $result = 0;

        if (!empty($finalized)) {
            try {
                $ofertas = $ofertaPremiosTable->getOfertasPremiosFinalizadas();
                foreach ($ofertas as $dato) {
                    if ($dato->TipoPrecio == "Split") {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock
                            . "' : atributo='" . (int)$dato->Atributo_id . "', ";
                    } else {
                        $bodyMessage = $bodyMessage . " - Oferta " . $dato->id
                            . " : stock='" . (int)$dato->Stock . "', ";
                    }

                    $cupones = $cuponPuntoTable->getExpiredCuponPremiosCreate($dato->id, $dato->Atributo_id);
                    foreach ($cupones as $dataCupon) {
                        $cupon = $cuponPuntoTable->getCuponPremios($dataCupon->id);
                        $cupon->EstadoCupon = 'Finalizado';
                        $cupon->FechaFinalizado = date("Y-m-d H:i:s");
                        $cuponPuntoTable->saveCuponPremios($cupon);

                        $cuponPremiosLog = new CuponPremiosLog();
                        $cuponPremiosLog->BNF3_Cupon_Premios_id = $dataCupon->id;
                        $cuponPremiosLog->CodigoCupon = $dataCupon->CodigoCupon;
                        $cuponPremiosLog->EstadoCupon = "Finalizado";
                        $cuponPremiosLog->BNF3_Oferta_Premios_id = $dataCupon->BNF3_Oferta_Premios_id;
                        $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $dataCupon->BNF3_Oferta_Premios_Atributos_id;
                        $cuponPremiosLog->BNF_Cliente_id = $dataCupon->BNF_Cliente_id;
                        $cuponPremiosLog->BNF_Usuario_id = null;
                        $cuponPremiosLog->Comentario = "Finalizado por Cron";
                        $cuponPuntoLogTable->saveCuponPremiosLog($cuponPremiosLog);
                    }

                    //Guardar Cambios
                    if ($dato->TipoPrecio == "Split") {
                        $result = $result + $ofertaPremiosAtributoTable->caducarOfertaPremiosAtributos($dato->Atributo_id);
                    } else {
                        $result = $result + $ofertaPremiosTable->updateOfertasPremiosFinalizadas($dato->id);
                    }
                    $bodyMessage = $bodyMessage . "\n";
                }

                //Guardamos el Registro de eventos
                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_CUPON_PREMIOS_UNICO);
                if ($result > 0) {
                    $titleMessage = "Actualizar Ofertas Premios Finalizadas.\n";
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
                $ofertas = $ofertaPremiosTable->getOfertasPremiosCaducadas();
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

                    $cupones = $cuponPuntoTable->getExpiredCuponPremiosFinalized($dato->id, $dato->Atributo_id);
                    foreach ($cupones as $dataCupon) {
                        $cupon = $cuponPuntoTable->getCuponPremios($dataCupon->id);
                        $cupon->EstadoCupon = 'Caducado';
                        $cupon->FechaCaducado = date("Y-m-d H:i:s");
                        $cuponPuntoTable->saveCuponPremios($cupon);

                        $cuponPremiosLog = new CuponPremiosLog();
                        $cuponPremiosLog->BNF3_Cupon_Premios_id = $dataCupon->id;
                        $cuponPremiosLog->CodigoCupon = $dataCupon->CodigoCupon;
                        $cuponPremiosLog->EstadoCupon = "Caducado";
                        $cuponPremiosLog->BNF3_Oferta_Premios_id = $dataCupon->BNF3_Oferta_Premios_id;
                        $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $dataCupon->BNF3_Oferta_Premios_Atributos_id;
                        $cuponPremiosLog->BNF_Cliente_id = $dataCupon->BNF_Cliente_id;
                        $cuponPremiosLog->BNF_Usuario_id = null;
                        $cuponPremiosLog->Comentario = "Caducado por Cron";
                        $cuponPuntoLogTable->saveCuponPremiosLog($cuponPremiosLog);
                    }

                    if ($dato->TipoPrecio == "Split") {
                        $result = $result + $ofertaPremiosAtributoTable->caducarOfertaPremiosAtributos($dato->Atributo_id);
                    } else {
                        $result = $result + $ofertaPremiosTable->updateOfertasPremiosVencidas($dato->id);
                    }
                    $bodyMessage = $bodyMessage . "\n";
                }

                //Guardamos el Registro de eventos
                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_CUPON_PREMIOS_UNICO);
                if ($result > 0) {
                    $titleMessage = "Actualizar Ofertas Premios Caducadas.\n";
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
