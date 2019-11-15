<?php

namespace Oferta\Controller;

use Oferta\Form\RegistrarLeadForm;
use Oferta\Model\Data\RegistrarLeadData;
use Oferta\Model\Filter\RegistrarLeadFilter;
use Oferta\Model\FormularioLead;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RegistrarLeadController extends AbstractActionController
{
    #region ObjectTables
    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
    }

    public function getFormularioLeadTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\FormularioLeadTable');
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $mensaje = null;
        $type = "danger";
        $datos = new RegistrarLeadData($this);
        $form = new RegistrarLeadForm('registrar-oferta', $datos->getFormData());

        $request = $this->getRequest();
        if ($request->isPost()) {

            $post = $request->getPost()->toArray();
            $validate = new RegistrarLeadFilter();
            $form->setInputFilter($validate->getInputFilter($datos->getFilterData(), $post));
            $form->setData($post);

            if ($form->isValid()) {

                //Datos de la Oferta
                $oferta = $post["Oferta"];
                $condiciones = $post["Condiciones"];
                $condicionestext = $post["CondicionesTexto"];
                $condicionesestado = (int)$post["CondicionesEstado"];
                //Datos de los campos dinamicos
                $ids = (isset($post["id"])) ? $post["id"] : null;
                $tipos = (isset($post["tipo"])) ? $post["tipo"] : null;
                $nombre = (isset($post["nombre"])) ? $post["nombre"] : null;
                $activo = (isset($post["activo"])) ? $post["activo"] : null;
                $detalle = (isset($post["detalle"])) ? $post["detalle"] : null;
                $requerido = (isset($post["obligatorio"])) ? $post["obligatorio"] : null;

                if ($nombre != null) {
                    foreach ($nombre as $key => $item) {
                        $cantidad = $this->getFormularioLeadTable()->getIfNameExist($oferta, $item);
                        if ($cantidad >= 1 && !$this->getFormularioLeadTable()->getExistFormulario($key)) {
                            $item = $item . "_" . ++$cantidad;
                        }

                        $formularioLead = new FormularioLead();
                        $formularioLead->id = (isset($ids[$key])) ? (int)$ids[$key] : 0;
                        $formularioLead->BNF_Oferta_id = $oferta;
                        $formularioLead->Nombre_Campo = $item;
                        $formularioLead->Tipo_Campo = "" . (int)$tipos[$key] . "";
                        $formularioLead->Detalle = (isset($detalle[$key])) ? $detalle[$key] : null;
                        $formularioLead->Requerido = (isset($requerido[$key])) ? '1' : '0';
                        $formularioLead->Activo = (isset($activo[$key])) ? '1' : '0';
                        $this->getFormularioLeadTable()->saveFormulario($formularioLead);
                    }
                }

                $data["CondicionesDelivery"] = $condiciones;
                $data["CondicionesDeliveryTexto"] = $condicionestext;
                $data["CondicionesDeliveryEstado"] = ($condicionesestado == 0) ? '0' : '1';
                $this->getOfertaTable()->updateOferta($oferta, $data);
                $form = new RegistrarLeadForm('registrar-oferta', $datos->getFormData());
                $mensaje[] = 'Datos Guardados Correctamente';
                $type = "success";
            } else {
                $mensaje[] = 'No se Registro, revisar los datos ingresados';
                $type = "danger";
            }
        }

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'oferta' => 'active',
                'olead' => 'active',
                'mensaje' => $mensaje,
                'type' => $type,
                'form' => $form
            )
        );
    }

    public function getDataAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            if ($this->getOfertaTable()->getOfertaExits($id) > 0) {
                $oferta = $this->getOfertaTable()->getOferta($id);
                $formulario = $this->getFormularioLeadTable()->getFormulario($id);
                $form_data = array();
                foreach ($formulario as $item) {
                    $form_data[$item->id] =
                        array(
                            "Nombre_Campo" => $item->Nombre_Campo,
                            "Tipo_Campo" => $item->Tipo_Campo,
                            "Detalle" => $item->Detalle,
                            "Requerido" => $item->Requerido,
                            "Activo" => $item->Activo,
                        );
                }

                $data_oferta = array(
                    "Contenido" => $oferta->CondicionesDelivery,
                    "Texto" => $oferta->CondicionesDeliveryTexto,
                    "Estado" => $oferta->CondicionesDeliveryEstado,
                );

                $response->setContent(
                    Json::encode(
                        array('response' => true, 'data' => $data_oferta, 'form' => $form_data)
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false, 'data' => null, 'form' => null)));
            }
        }
        return $response;
    }

    public function deleteDataAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            if ($this->getFormularioLeadTable()->getExistFormulario($id) != false) {
                $this->getFormularioLeadTable()->deleteFormulario($id);
                $response->setContent(
                    Json::encode(
                        array('response' => true)
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false)));
            }
        }
        return $response;
    }
}
