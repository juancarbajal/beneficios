<?php

namespace Cron\Controller;

use Puntos\Model\AsignacionEstadoLog;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

class ReferidosController extends AbstractActionController
{
    protected $asignacionTable;
    protected $asignacionHistorialTable;

    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_PUNTOS_REFERENCIA = 'puntos-referencia.log';

    public function indexAction()
    {
        return new ViewModel();
    }

    public function caducarAction()
    {
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Sólo puede utilizar esta acción desde una consola!');
        }

        $connection = null;
        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_PUNTOS_REFERENCIA);

        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        try {
            $asignacionTable = $this->serviceLocator->get('Puntos\Model\Table\AsignacionTable');
            $asignacionHistorialTable = $this->serviceLocator->get('Puntos\Model\Table\AsignacionEstadoLogTable');

            $vencidos = [];
            $contador = 0;
            $asignaciones_caducadas = $asignacionHistorialTable->getPuntosFinalizados();

            foreach ($asignaciones_caducadas as $value) {
                if (!in_array($value->BNF2_Asignacion_Puntos_id, $vencidos)) {
                    array_push($vencidos, $value->BNF2_Asignacion_Puntos_id);
                }
            }

            foreach ($vencidos as $key) {
                $asignacion = $asignacionTable->getAsignacion($key);
                $estadoPuntos = "Cancelado";
                $puntosDisponibles = $asignacion->CantidadPuntosDisponibles;
                $puntosAsignados = $asignacion->CantidadPuntos;
                $puntos = (int)$asignacion->CantidadPuntosDisponibles;

                $asignacionTable->cambiarEstadoPuntosAsignacion($asignacion->id, $estadoPuntos, $puntosDisponibles);

                $asignacion = $asignacionTable->getAsignacion($asignacion->id);
                $asignacionEstadoLog = new AsignacionEstadoLog();
                $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacion->id;
                $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                $asignacionEstadoLog->TipoAsignamiento = 'Referido';
                $asignacionEstadoLog->CantidadPuntos = (int)$puntosAsignados;
                $asignacionEstadoLog->CantidadPuntosUsados = (int)$asignacion->CantidadPuntosUsados;
                $asignacionEstadoLog->CantidadPuntosDisponibles = (int)$asignacion->CantidadPuntosDisponibles;
                $asignacionEstadoLog->CantidadPuntosEliminados = (int)$asignacion->CantidadPuntosEliminados;
                $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                $asignacionEstadoLog->Operacion = 'Cancelar';
                $asignacionEstadoLog->Puntos = $puntos;
                $asignacionEstadoLog->BNF_Usuario_id = null;
                $asignacionEstadoLog->Motivo = 'Caducar Puntos Referidos';
                $asignacionHistorialTable->saveAsignacionEstadoLog($asignacionEstadoLog);

                $registros_asignaciones = $asignacionHistorialTable
                    ->getAsignacionReferidosLog($asignacion->id, $asignacion->BNF2_Segmento_id);
                foreach ($registros_asignaciones as $historial) {
                    $data = [];
                    $data['Estado_Cron'] = 1;
                    $asignacionHistorialTable->updateAsignacionEstadoLog($data, $historial->id);
                }

                $contador++;
            }

            $titleMessage = "Ejecución de Cron Caducar Referidos.\n";
            $message = $titleMessage . "Total de asignaciones afectadas: " . $contador . "\n";

            $logger->addWriter($writer);
            $logger->log(Logger::INFO, $message);

        } catch (\Exception $ex) {
            $message = $ex->getMessage() . "\n";
            $logger->addWriter($writer);
            $logger->log(Logger::NOTICE, $message);
            return "Error en la ejecución.\n";
        }
        return $message;
    }

}
