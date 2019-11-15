<?php

namespace Reportes\Controller;

use Reportes\Form\PeriodoForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\EmailAddress;
use Zend\View\Model\ViewModel;

class ReporteDescargasController extends AbstractActionController
{
    const USUARIO_CLIENTE = 7;

    public function indexAction()
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

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $nombre_empresa = $empresas[$empresa_value];
            $form = new PeriodoForm('periodo', $empresa_value, $tipo_usuario);
        } else {
            $form = new PeriodoForm('periodo', $empresas);
        }

        return new ViewModel(
            array(
                "reportes" => 'active',
                "reportedescarga" => 'active',
                "form" => $form,
                'nombre_empresa' => $nombre_empresa,
            )
        );
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultados = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $fechaInicio_defecto = '2016-05-01';
            $fechaFin_defecto = date('Y-m-d');
            $fechaInicio = ($request->getPost()->FechaInicio2 == '') ? $fechaInicio_defecto : $request->getPost()->FechaInicio2;
            $fechaFin = ($request->getPost()->FechaFin2 == '') ?
                $fechaFin_defecto : $request->getPost()->FechaFin2;
            $emails = ($request->getPost()->Emails == '') ? '' : $request->getPost()->Emails;

            $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
            $empresa_value = $this->identity()->BNF_Empresa_id;
            if ($tipo_usuario == $this::USUARIO_CLIENTE) {
                $id_empresa = $empresa_value;
            } else {
                $id_empresa = ($request->getPost()->empresa == '') ? '' : (int)$request->getPost()->empresa;
            }

            $valid = new EmailAddress(array('domain' => false));
            if ($valid->isValid($emails)) {

                $data = array(
                    'emails' => $emails,
                    'id_empresa' => $id_empresa,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                );

                $config = $this->getServiceLocator()->get('Config');
                $ch = curl_init($config['API_LARAVEL_HOST'] . "api/v1/reporte_descargas");
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
            } else {
                $this->flashMessenger()->addErrorMessage('El email ingresado es incorrecto');
            }
        }
        return $this->redirect()->toRoute('reporte-descarga', array('action' => 'index'));
    }
}

