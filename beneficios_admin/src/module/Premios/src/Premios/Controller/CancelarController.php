<?php

namespace Premios\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Premios\Form\BuscarCancelacionPremios;
use Premios\Form\FormCancelar;
use Premios\Model\AsignacionPremiosEstadoLog;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class CancelarController extends AbstractActionController
{
    const ACCION_DESACTIVAR = "desactivar";
    const ACCION_REACTIVAR = "reactivar";
    const ACCION_ELIMINAR = "eliminar";

    const ESTADO_ACTIVO = "Activado";
    const ESTADO_INACTIVO = "Desactivado";
    const ESTADO_ELIMINADO = "Cancelado";

    const DESACTIVACION_SUCCESS = "La desactivación de premios se realizó correctamente.";
    const REACTIVACION_SUCCESS = "La reactivación de premios se realizó correctamente.";
    const CANCELACION_SUCCESS = "La cancelación de premios se realizó correctamente.";
    const CANCELACION_ERROR = "No se realizó ninguna cancelación.";

    const OPERACION_REACTIVAR = "Reactivar";
    const OPERACION_DESACTIVAR = "Desactivar";
    const OPERACION_CANCELAR = "Cancelar";

    #region ObjectTables
    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosTable');
    }

    public function getSegmentosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\SegmentosPremiosTable');
    }

    public function getCampaniaEmpresaTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosEmpresasTable');
    }

    public function getAsignacionPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosTable');
    }

    public function getEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaClienteClienteTable');
    }

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\ClienteTable');
    }

    public function getAsignacionPremiosEstadoLogTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosEstadoLogTable');
    }
    #endregion

    #region Inicializando Data
    public function inicializacion()
    {
        $dataEmpresa = array();
        $filterEmpresa = array();

        try {
            foreach ($this->getCampaniaTable()->getEmpresasCliente("busqueda") as $empresa) {
                $dataEmpresa[$empresa->id] = $empresa->Empresa;
                $filterEmpresa[$empresa->id] = [$empresa->id];
            }
        } catch (\Exception $ex) {
            $dataEmpresa = array();
        }

        $formulario['empresa'] = $dataEmpresa;
        $filtro['empresa'] = array_keys($filterEmpresa);

        return array($formulario, $filtro);
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $asignaciones = null;
        $presupuesto = null;
        $inicio = null;
        $final = null;
        $activo = null;

        $busqueda = array(
            'Documento' => 'NumeroDocumento',
            'Nombre' => 'Nombre',
            'Apellido' => 'Apellido',
            'Segmento' => 'NombreSegmento',
            'Premios' => 'CantidadPremios',
        );

        $data = $this->inicializacion();
        $form = new BuscarCancelacionPremios('buscar-asignaciones', $data[0]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = !empty($request->getPost()->Empresas) ? $request->getPost()->Empresas : null;
            $campania = !empty($request->getPost()->Campania) ? $request->getPost()->Campania : null;
            $segmento = !empty($request->getPost()->Segmento) ? $request->getPost()->Segmento : null;
            $cliente = !empty($request->getPost()->Cliente) ? $request->getPost()->Cliente : null;
            $estado = !empty($request->getPost()->Estado) ? $request->getPost()->Estado : 'Activado';
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $campania = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $segmento = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : null;
            $cliente = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
            $estado = $this->params()->fromRoute('q5') ? $this->params()->fromRoute('q5') : 'Activado';
        }

        $form->setData(
            array(
                "Empresas" => $empresa,
                "Campania" => $campania,
                "Segmento" => $segmento,
                "Cliente" => $cliente,
                "Estado" => $estado
            )
        );

        $opcion = ($estado == "Cancelado") ? false : true;

        //Determinar ordenamiento
        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];
        } else {
            $order_by_o = 'id';
            $order_by = 'id';
        }

        //Se obtiene los datos filtrados y la paginacion segun el orden
        $asignaciones = $this->getAsignacionPremiosTable()
            ->getListaUsuariosCancelacion($order_by, $order, $empresa, $campania, $segmento, $cliente, $estado);
        $paginator = new Paginator(new paginatorIterator($asignaciones, $order_by));
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        $formAction = new FormCancelar('eliminarForm');

        return new ViewModel(
            array(
                'premios' => 'active',
                'cancelpremios' => 'active',
                'asignaciones' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'form' => $form,
                'formAction' => $formAction,
                'p' => $page,
                'q1' => $empresa,
                'q2' => $campania,
                'q3' => $segmento,
                'q4' => $cliente,
                'q5' => $estado,
                'opcion' => $opcion,
            )
        );
    }

    public function deleteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $asignados = $post_data['delete'];
            $action = $post_data['action'];
            $comentario = isset($post_data['comment']) ? $post_data['comment'] : "";
            if (isset($post_data['csrf'])) {
                if ($csrf->verifyToken($post_data['csrf']) AND is_array($asignados)) {
                    $contador = 0;
                    $estadoBusqueda = "";
                    $estadoPremios = "";
                    $mensaje = "";
                    $operacion = null;
                    $usuarioResponsable = $this->identity()->id;

                    if ($action == $this::ACCION_DESACTIVAR) {
                        $estadoBusqueda = $this::ESTADO_ACTIVO;
                        $estadoPremios = $this::ESTADO_INACTIVO;
                        $mensaje = $this::DESACTIVACION_SUCCESS;
                        $operacion = $this::OPERACION_DESACTIVAR;
                    } elseif ($action == $this::ACCION_REACTIVAR) {
                        $estadoBusqueda = $this::ESTADO_INACTIVO;
                        $estadoPremios = $this::ESTADO_ACTIVO;
                        $mensaje = $this::REACTIVACION_SUCCESS;
                        $operacion = $this::OPERACION_REACTIVAR;
                    } elseif ($action == $this::ACCION_ELIMINAR) {
                        $estadoBusqueda = $this::ESTADO_INACTIVO;
                        $estadoPremios = $this::ESTADO_ELIMINADO;
                        $mensaje = $this::CANCELACION_SUCCESS;
                        $operacion = $this::OPERACION_CANCELAR;
                    }

                    foreach ($asignados as $value) {
                        $premiosDisponibles = 0;
                        $datosAsignacion = $this->getAsignacionPremiosTable()->getAsignacionValid($value, $estadoBusqueda);
                        if (is_object($datosAsignacion)) {
                            $premiosAsignados = (int)$datosAsignacion->CantidadPremios;
                            if ($estadoPremios == $this::ESTADO_ELIMINADO) {
                                $premiosDisponibles = $datosAsignacion->CantidadPremiosDisponibles;
                            } elseif ($estadoPremios == $this::ESTADO_INACTIVO) {
                                $comentario = "Desactivación Premios";
                            } elseif ($estadoPremios == $this::ESTADO_ACTIVO) {
                                $comentario = "Reactivación Premios";
                            }

                            $premios = (int)$datosAsignacion->CantidadPremiosDisponibles;

                            $this->getAsignacionPremiosTable()
                                ->cambiarEstadoPremiosAsignacion($value, $estadoPremios, $premiosDisponibles);

                            $datosAsignacion = $this->getAsignacionPremiosTable()->getAsignacionValid($value, $estadoPremios);

                            $asignacionEstadoLog = new AsignacionPremiosEstadoLog();
                            $asignacionEstadoLog->BNF3_Asignacion_Premios_id = $datosAsignacion->id;
                            $asignacionEstadoLog->BNF3_Segmento_id = $datosAsignacion->BNF3_Segmento_id;
                            $asignacionEstadoLog->BNF_Cliente_id = $datosAsignacion->BNF_Cliente_id;
                            $asignacionEstadoLog->CantidadPremios = $premiosAsignados;
                            $asignacionEstadoLog->CantidadPremiosUsados = (int)$datosAsignacion->CantidadPremiosUsados;
                            $asignacionEstadoLog->CantidadPremiosDisponibles = (int)$datosAsignacion->CantidadPremiosDisponibles;
                            $asignacionEstadoLog->CantidadPremiosEliminados = (int)$datosAsignacion->CantidadPremiosEliminados;
                            $asignacionEstadoLog->EstadoPremios = $datosAsignacion->EstadoPremios;
                            $asignacionEstadoLog->Operacion = $operacion;
                            $asignacionEstadoLog->Premios = $premios;
                            $asignacionEstadoLog->BNF_Usuario_id = $usuarioResponsable;
                            $asignacionEstadoLog->Motivo = $comentario;
                            $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($asignacionEstadoLog);

                            $contador++;
                        }
                    }

                    if ($contador > 0) {
                        $this->flashMessenger()->addMessage($mensaje);
                    } else {
                        $this->flashMessenger()->addMessage($this::CANCELACION_ERROR);
                    }
                } else {
                    $this->flashMessenger()->addMessage($this::CANCELACION_ERROR);
                }
            } else {
                $this->flashMessenger()->addMessage($this::CANCELACION_ERROR);
            }
        }

        return $this->redirect()->toRoute('cancelar-premios', array(
            'cancelpremios' => 'active',
            'premios' => 'active',
        ));
    }

    public function getDataEmpresaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $request = $this->getRequest();
        $dataCampanias = array();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    if ($result = $this->getEmpresaTable()->getEmpresa($id)) {
                        $campanias = $this->getCampaniaTable()->getCampaniasPByEmpresa($id);
                        foreach ($campanias as $value) {
                            $dataCampanias[] = array('id' => $value->id, 'text' => $value->NombreCampania);
                        }

                        $state = true;
                    }
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'campanias' => $dataCampanias,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

    public function getDataSegmentosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $request = $this->getRequest();
        $state = false;
        $dataSegmentos = array();

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    if ($result = $this->getSegmentosTable()->getAllSegmentos($id)) {
                        foreach ($result as $value) {
                            $dataSegmentos[] = array('id' => $value->id, 'text' => $value->NombreSegmento);
                        }
                        $state = true;
                    }
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'segmentos' => $dataSegmentos,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }
}
