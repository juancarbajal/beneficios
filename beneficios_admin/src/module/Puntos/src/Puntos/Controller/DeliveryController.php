<?php

namespace Puntos\Controller;

use Puntos\Form\DeliveryForm;
use Puntos\Model\DeliveryPuntos;
use Puntos\Model\Filter\DeliveryFilter;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Regex;
use Zend\View\Model\ViewModel;

class DeliveryController extends AbstractActionController
{
    #region Constantes
    const MESSAGE_ERROR = "Ocurrió un error al procesar los datos ingresados";
    const MESSAGE_SUCCESS = "Datos Guardados Correctamente";

    #region ObjectTables
    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosTable');
    }

    public function getDeliveryTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\DeliveryPuntosTable');
    }

    public function getOfertaPuntosDeliveryTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosDeliveryTable');
    }

    #endregion

    public function inicializacion()
    {
        $dataOferta = array();
        $filterOferta = array();

        try {
            foreach ($this->getOfertaPuntosTable()->getOfertaPuntosEmpresaCliente() as $oferta) {
                $dataOferta[$oferta->id] = $oferta->Titulo;
                $filterOferta[$oferta->id] = $oferta->id;
            }
        } catch (\Exception $ex) {
            return $combo = array();
        }

        return array($dataOferta, $filterOferta);
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $mensaje = null;
        $errores = [];
        $type = "danger";
        $datos = $this->inicializacion();
        $form = new DeliveryForm('registrar-oferta', $datos[0]);

        $request = $this->getRequest();
        if ($request->isPost()) {

            $post = $request->getPost()->toArray();
            $validate = new DeliveryFilter();
            $form->setInputFilter($validate->getInputFilter($datos[1], $post));
            $form->setData($post);

            //Datos de la Oferta
            $oferta = $post["Oferta"];
            $condiciones = $post["Condiciones"];
            $condicionestext = $post["CondicionesTexto"];
            $condicionesestado = (int)$post["CondicionesEstado"];
            $correo_contacto = $post["CorreoContactoDelivery"];
            //Datos de los campos dinamicos
            $ids = (isset($post["id"])) ? $post["id"] : null;
            $tipos = (isset($post["tipo"])) ? $post["tipo"] : null;
            $nombre = (isset($post["nombre"])) ? $post["nombre"] : null;
            $activo = (isset($post["activo"])) ? $post["activo"] : null;
            $detalle = (isset($post["detalle"])) ? $post["detalle"] : null;
            $requerido = (isset($post["obligatorio"])) ? $post["obligatorio"] : null;

            $validacionCampos = true;
            $valid = new Regex(array('pattern' => '/^[a-zA-Z][a-zA-Z0-9 ÁáÉéÍíÓóÚúÑñ]*?$/'));

            foreach ($nombre as $key => $item) {
                if (!$valid->isValid(trim($item))) {
                    $validacionCampos = false;
                    $errores[] = [
                        'id' => isset($ids[$key]) ? $ids[$key] : null,
                        'valor' => trim($item),
                    ];
                }
            }
            $errores = json_encode($errores);

            if ($form->isValid() and $validacionCampos) {
                if ($nombre != null) {
                    foreach ($nombre as $key => $item) {
                        $cantidad = $this->getDeliveryTable()->getIfNameExist($oferta, $item);
                        if ($cantidad >= 1 && !$this->getDeliveryTable()->getExistFormulario($key)) {
                            $item = $item . "_" . ++$cantidad;
                        }

                        $campo = strtolower(trim($item));
                        $conv = array("á" => "a", "é" => "e", "í" => "i", "ó" => "o", "ú" => "u", " " => "_", ":" => "");
                        $campo = strtr($campo, $conv);

                        $formularioLead = new DeliveryPuntos();
                        $formularioLead->id = (isset($ids[$key])) ? (int)$ids[$key] : 0;
                        $formularioLead->BNF2_Oferta_Puntos_id = $oferta;
                        $formularioLead->Etiqueta_Campo = $item;
                        $formularioLead->Nombre_Campo = $campo;
                        $formularioLead->Tipo_Campo = "" . (int)$tipos[$key] . "";
                        $formularioLead->Detalle = (isset($detalle[$key])) ? $detalle[$key] : null;
                        $formularioLead->Requerido = (isset($requerido[$key])) ? '1' : '0';
                        $formularioLead->Activo = (isset($activo[$key])) ? '1' : '0';
                        $this->getDeliveryTable()->saveDeliveryPuntos($formularioLead);
                    }
                }

                $data["CondicionesDelivery"] = $condiciones;
                $data["CondicionesDeliveryTexto"] = $condicionestext;
                $data["CondicionesDeliveryEstado"] = ($condicionesestado == 0) ? '0' : '1';
                $data["CorreoContactoDelivery"] = $correo_contacto;
                $this->getOfertaPuntosTable()->updateOferta($oferta, $data);
                $form = new DeliveryForm('registrar-delivery', $datos[0]);
                $mensaje[] = $this::MESSAGE_SUCCESS;
                $type = "success";
            } else {
                $mensaje[] = $this::MESSAGE_ERROR;
                $type = "danger";
            }
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'deliveryptos' => 'active',
                'mensaje' => $mensaje,
                'type' => $type,
                'form' => $form,
                'errores' => $errores
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
            if ($this->getOfertaPuntosTable()->getOfertaExits($id) > 0) {
                $oferta = $this->getOfertaPuntosTable()->getOfertaPuntos($id);
                $formulario = $this->getDeliveryTable()->getFormulario($id);
                $form_data = array();
                foreach ($formulario as $item) {
                    $form_data[$item->id] =
                        array(
                            "Nombre_Campo" => !empty($item->Etiqueta_Campo) ? $item->Etiqueta_Campo : $item->Nombre_Campo,
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
                    "Correo" => $oferta->CorreoContactoDelivery,
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

            if (is_object($this->getDeliveryTable()->getExistFormulario($id))) {
                $existe_registros = $this->getOfertaPuntosDeliveryTable()->getExistOfertaPuntosDelivery($id);
                if (is_object($existe_registros)) {
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => false,
                                'message' => 'El campo se encuentra asociado a uno o varios cupones y no se puede eliminar',
                            )
                        )
                    );
                } else {
                    $this->getDeliveryTable()->deleteFormulario($id);
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => true,
                                'message' => 'Eliminación completa',
                            )
                        )
                    );
                }
            } else {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false,
                            'message' => 'No se encontró el campo a eliminar'
                        )
                    )
                );
            }
        }
        return $response;
    }
}
