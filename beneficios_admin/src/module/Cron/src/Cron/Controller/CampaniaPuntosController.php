<?php

namespace Cron\Controller;

use Puntos\Model\CampaniaPLog;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

class CampaniaPuntosController extends AbstractActionController
{
    protected $campaniaP;
    protected $campaniaLog;
    protected $segmentoP;
    protected $campaniaEmp;

    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_UPDATE_LEAD_ENDING = 'campanias-puntos-actualizar-finalizadas.log';

    public function indexAction()
    {
        return new ViewModel();
    }

    public function caducarAction()
    {
        $bodyMessage = "";

        $request = $this->getRequest();
        $this->campaniaP = $this->serviceLocator->get('Puntos\Model\Table\CampaniasPTable');
        $this->campaniaLog = $this->serviceLocator->get('Puntos\Model\Table\CampaniaPLogTable');
        $this->segmentoP = $this->serviceLocator->get('Puntos\Model\Table\SegmentosPTable');
        $this->campaniaEmp = $this->serviceLocator->get('Puntos\Model\Table\CampaniasPEmpresasTable');

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Sólo puede utilizar esta acción desde una consola!');
        }

        $result = 0;

        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_UPDATE_LEAD_ENDING);

        try {
            $campanias = $this->campaniaP->getCampaniaFinalizadas();
            foreach ($campanias as $value) {
                $value->EstadoCampania = 'Caducado';
                $value->FechaActualizacion = date("Y-m-d H:i:s");
                $this->campaniaP->saveCampaniasP($value);

                $dataSegmento = $this->segmentoP->getAllSegmentosCampania($value->id);
                $segmentos = "";
                $contador = 0;
                foreach ($dataSegmento as $seg) {
                    $segmentos = $contador > 0 ?
                        $segmentos . '; ' . $seg->NombreSegmento : $seg->NombreSegmento;
                    $contador++;
                }

                $empresa = $this->campaniaEmp->getCampaniasPEmpresasActual($value->id);

                $campaniaLog = new CampaniaPLog();
                $campaniaLog->BNF2_Campania_id = $value->id;
                $campaniaLog->NombreCampania = $value->NombreCampania;
                $campaniaLog->TipoSegmento = $value->TipoSegmento;
                $campaniaLog->FechaCampania = $value->FechaCampania;
                $campaniaLog->VigenciaInicio = $value->VigenciaInicio;
                $campaniaLog->VigenciaFin = $value->VigenciaFin;
                $campaniaLog->PresupuestoNegociado = (int)$value->PresupuestoNegociado;
                $campaniaLog->PresupuestoAsignado = (int)$value->PresupuestoAsignado;
                $campaniaLog->ParametroAlerta = (int)$value->ParametroAlerta;
                $campaniaLog->Comentario = !empty($value->Comentario) ? $value->Comentario : "";
                $campaniaLog->Relacionado = (int)$value->Relacionado;
                $campaniaLog->EstadoCampania = "Caducado";
                $campaniaLog->BNF_Empresa_id = $empresa->BNF_Empresa_id;
                $campaniaLog->Segmentos = $segmentos;
                $campaniaLog->RazonEliminado = "Caducado por Cron";
                $this->campaniaLog->saveCampaniaPLog($campaniaLog);

                $result++;
            }

            if ($result > 0) {
                $titleMessage = "Actualizar Campañas Puntos Finalizadas.\n";
                $message = $titleMessage . $bodyMessage .
                    "Total de Campañas Puntos afectadas: " . $result . ".\n";
                $logger->addWriter($writer);
                $logger->log(Logger::INFO, $message);
            } else {
                $message = "No se actualizó ninguna campaña puntos.\n";
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
}
