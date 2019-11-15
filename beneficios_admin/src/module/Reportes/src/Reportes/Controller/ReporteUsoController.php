<?php

namespace Reportes\Controller;

use DateTime;
use Reportes\Form\PeriodoForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ReporteUsoController extends AbstractActionController
{
    const MAX_RESULTS = 10000;

    public function checkInRange($start_date, $end_date, $fromUser)
    {
        if (new DateTime($fromUser) >= new DateTime($start_date)
            && new DateTime($fromUser) <= new DateTime($end_date)
        ) {
            return true;
        }
        return false;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function reporteDosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $empresas = array();
        $nombre_empresa = null;
        $getEmpresaTable = $this->serviceLocator->get('Empresa\Model\EmpresaTable');
        $dataEmpresas = $getEmpresaTable->getEmpresaCli();
        foreach ($dataEmpresas as $e) {
            $empresas[$e->id] = $e->NombreComercial . " (" . $e->RazonSocial . ") - " . $e->Ruc;
        }
        $form = new PeriodoForm('periodo', $empresas);

        if ($this->identity()->BNF_Empresa_id != null) {
            $empresa = $getEmpresaTable->getEmpresa($this->identity()->BNF_Empresa_id);
            $nombre_empresa = $empresa->NombreComercial . ' - ' . $empresa->RazonSocial . ' - ' . $empresa->Ruc;
        }

        return new ViewModel(
            array(
                "reportes" => 'active',
                "reportedos" => 'active',
                "form" => $form,
                "empresa" => $nombre_empresa
            )
        );
    }

    public function exportDosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultados = null;
        $request = $this->getRequest();

        $form = new PeriodoForm('registrar');

        if ($request->isPost()) {

            $fechaInicio_defecto = '2015-01-01';
            $fechaFin_defecto = date('Y-m-d');
            $fechaInicio = ($request->getPost()->FechaInicio2 == '') ? $fechaInicio_defecto
                : $request->getPost()->FechaInicio2;
            $fechaFin = ($request->getPost()->FechaFin2 == '') ?
                $fechaFin_defecto : $request->getPost()->FechaFin2;
            $id_empresa = ($request->getPost()->empresa == '') ? '' : $request->getPost()->empresa;
            $costo = ($request->getPost()->Costo == '') ? '' : $request->getPost()->Costo;
            $meta = ($request->getPost()->Meta == '') ? '' : $request->getPost()->Meta;
            $emails = ($request->getPost()->Emails == '') ? '' : $request->getPost()->Emails;


            $data = array(
                'emails' => $emails,
                'id_empresa' => $id_empresa,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'costo' => $costo,
                'meta' => $meta
            );

            $config = $this->getServiceLocator()->get('Config');
            $ch = curl_init($config['API_LARAVEL_HOST'] . "api/v1/reporte_crm_2");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_USERPWD, $config['API_LARAVEL_USER'] . ":" . $config['API_LARAVEL_PASS']);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $response = json_decode(curl_exec($ch));
            curl_close($ch);
            if (!$response->error) {
                $this->flashMessenger()->addSuccessMessage('El reporte se enviará a su correo en breves minutos');
            } else {
                $this->flashMessenger()->addErrorMessage('Error en la generacion del reporte');
            }

        }
        return $this->redirect()->toRoute('reporte-uso', array('action' => 'reporteDos'));
    }
}
