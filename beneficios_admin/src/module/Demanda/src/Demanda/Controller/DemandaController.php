<?php

namespace Demanda\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Demanda\Form\BuscarDemandasForm;
use Demanda\Form\FormDemanda;
use Demanda\Model\Demanda;
use Demanda\Model\DemandaDepartamentos;
use Demanda\Model\DemandaEmpresas;
use Demanda\Model\DemandaEmpresasAdicionales;
use Demanda\Model\DemandaLog;
use Demanda\Model\DemandaRubros;
use Demanda\Model\DemandaSegmentos;
use Demanda\Model\Filter\DemandaFilter;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\Identical;
use Zend\Validator\InArray;
use Zend\Validator\Regex;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class DemandaController extends AbstractActionController
{
    const MESSAGE_ERROR = "No se Registró, revisar los datos ingresados.";
    const MESSAGE_SAVE = "Demanda Registrada Correctamente.";
    const MESSAGE_UPDATE = "Demanda Actualizada Correctamente.";
    const MESSAGE_SAVE_SEND = "La Demanda fue registrada y enviada al administrador.";
    const MESSAGE_UPDATE_SEND = "La Demanda fue actualizada y enviada al administrador.";

    #region ObjectTables
    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getUbigeoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\UbigeoTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\CampaniasPTable');
    }

    public function getSegmentoTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\SegmentosPTable');
    }

    public function getRubroTable()
    {
        return $this->serviceLocator->get('Rubro\Model\Table\RubroTable');
    }

    public function getDemandaTable()
    {
        return $this->serviceLocator->get('Demanda\Model\Table\DemandaTable');
    }

    public function getDemandaEmpresaTable()
    {
        return $this->serviceLocator->get('Demanda\Model\Table\DemandaEmpresasTable');
    }

    public function getDemandaDepartamentoTable()
    {
        return $this->serviceLocator->get('Demanda\Model\Table\DemandaDepartamentosTable');
    }

    public function getDemandaRubroTable()
    {
        return $this->serviceLocator->get('Demanda\Model\Table\DemandaRubrosTable');
    }

    public function getDemandaEmpresaAdicionalTable()
    {
        return $this->serviceLocator->get('Demanda\Model\Table\DemandaEmpresasAdicionalesTable');
    }

    public function getDemandaLogTable()
    {
        return $this->serviceLocator->get('Demanda\Model\Table\DemandaLogTable');
    }

    public function getDemandaSegmentosTable()
    {
        return $this->serviceLocator->get('Demanda\Model\Table\DemandaSegmentosTable');
    }

    #endregion

    #region Inicializacion
    public function inicializacionBusqueda()
    {
        $dataEmpresaCli = array();
        $filterEmpresaCli = array();
        $dataCampaniasP = array();
        $filterCampaniasP = array();

        try {
            foreach ($this->getDemandaTable()->getEmpresasDemandas() as $empresa) {
                $dataEmpresaCli[$empresa->id] = $empresa->Empresa;
                $filterEmpresaCli[$empresa->id] = [$empresa->id];
            }

            foreach ($this->getDemandaTable()->getCampaniaDemandas() as $campania) {
                $dataCampaniasP[$campania->id] = $campania->Campania;
                $filterCampaniasP[$campania->id] = [$campania->id];
            }
        } catch (\Exception $ex) {
            $dataEmpresaCli = array();
            $dataCampaniasP = array();
        }

        $formulario['empcli'] = $dataEmpresaCli;
        $filtro['empcli'] = array_keys($filterEmpresaCli);
        $formulario['campania'] = $dataCampaniasP;
        $filtro['campania'] = array_keys($filterCampaniasP);

        return array($formulario, $filtro);
    }

    public function inicializacion()
    {
        $dataEmpresaProv = array();
        $filterEmpresaProv = array();
        $dataEmpresaCli = array();
        $filterEmpresaCli = array();
        $dataRubro = array();
        $filterRubro = array();
        $dataDepartamento = array();
        $filterDepartamento = array();

        try {
            foreach ($this->getEmpresaTable()->getEmpresaProv() as $empresa) {
                $dataEmpresaProv[$empresa->id] = $empresa->NombreComercial . ' - ' . $empresa->RazonSocial .
                    ' - ' . $empresa->Ruc;
                $filterEmpresaProv[$empresa->id] = [$empresa->id];
            }

            foreach ($this->getEmpresaTable()->getEmpresasCliente() as $empresa) {
                $dataEmpresaCli[$empresa->id] = $empresa->NombreComercial . ' - ' . $empresa->RazonSocial .
                    ' - ' . $empresa->Ruc;
                $filterEmpresaCli[$empresa->id] = [$empresa->id];
            }

            foreach ($this->getRubroTable()->fetchAll() as $rubro) {
                $dataRubro[$rubro->id] = $rubro->Nombre;
                $filterRubro[$rubro->id] = [$rubro->id];
            }

            foreach ($this->getUbigeoTable()->fetchAllDepartament() as $departamentos) {
                $dataDepartamento[$departamentos->id] = $departamentos->Nombre;
                $filterDepartamento[$departamentos->id] = [$departamentos->id];
            }
        } catch (\Exception $ex) {
            $dataEmpresaProv = array();
            $dataEmpresaCli = array();
            $dataRubro = array();
            $dataDepartamento = array();
        }

        $formulario['empprov'] = $dataEmpresaProv;
        $filtro['empprov'] = array_keys($filterEmpresaProv);
        $formulario['empcli'] = $dataEmpresaCli;
        $filtro['empcli'] = array_keys($filterEmpresaCli);
        $formulario['rubro'] = $dataRubro;
        $filtro['rubro'] = array_keys($filterRubro);
        $formulario['depas'] = $dataDepartamento;
        $filtro['depas'] = array_keys($filterDepartamento);

        return array($formulario, $filtro);
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $empresa = null;
        $fecha = null;
        $demanda = null;

        $busqueda = array(
            'Empresa' => 'Empresa',
            'Concepto' => 'BNF2_Campanias.id',
            'Fecha' => 'FechaDemanda'
        );

        $data = $this->inicializacionBusqueda();
        $form = new BuscarDemandasForm('demandas', $data[0]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = $request->getPost()->EmpresaCliente ? $request->getPost()->EmpresaCliente : null;
            $fecha = $request->getPost()->FechaDemanda ? $request->getPost()->FechaDemanda : null;
            $demanda = $request->getPost()->Campania ? $request->getPost()->Campania : null;
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $fecha = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $demanda = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : null;
        }
        $form->setData(
            array(
                "EmpresaCliente" => $empresa,
                "Campania" => $demanda,
                "FechaDemanda" => $fecha,
            )
        );

        //Determinar ordenamiento
        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'FechaCreacion';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];
        } else {
            $order_by_o = 'id';
            $order_by = 'BNF2_Demanda.FechaCreacion';
        }

        //Se obtiene los datos filtrados y la paginacion segun el orden
        $campanias = $this->getDemandaTable()->getDetails($order_by, $order, $empresa, $fecha, $demanda);
        $paginator = new Paginator(new paginatorIterator($campanias, $order_by));
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'demanda' => 'active',
                'demandlist' => 'active',
                'q3' => $demanda,
                'demandas' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'form' => $form,
                'p' => $page,
                'q1' => $empresa,
                'q2' => $fecha,
            )
        );
    }

    public function addAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $confirm = null;
        $type = null;
        $messages = array();

        $datos = $this->inicializacion();
        $form = new FormDemanda('registrar', $datos[0]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $validate = new DemandaFilter();
            $form->setInputFilter($validate->getInputFilter($datos[1]));
            $form->setData($request->getPost());

            $messages = $this->validarCampos($request, $datos);

            if ($form->isValid() and empty($messages)) {
                $demanda = new Demanda();
                $demanda->BNF_Empresa_id = $request->getPost()->EmpresaCliente;
                $demanda->FechaDemanda = $request->getPost()->FechaDemanda;
                $demanda->PrecioMinimo = (float)$request->getPost()->PrecioMin;
                $demanda->PrecioMaximo = (float)$request->getPost()->PrecioMax;
                $demanda->Target = $request->getPost()->Target;
                $demanda->Comentarios = $request->getPost()->Comentarios;
                $demanda->Actualizaciones = $request->getPost()->Actualizaciones;
                $demanda->Eliminado = 0;
                $demanda->id = $this->getDemandaTable()->saveDemanda($demanda);

                //Agregando Segmentos
                foreach ($request->getPost()->Segmento as $value) {
                    $demandaSegmentos = new DemandaSegmentos();
                    $demandaSegmentos->BNF2_Segmento_id = $value;
                    $demandaSegmentos->BNF2_Demanda_id = $demanda->id;
                    $demandaSegmentos->Eliminado = '0';
                    $this->getDemandaSegmentosTable()->saveDemandaSegmentos($demandaSegmentos);
                }

                //Agregando Departamentos
                foreach ($request->getPost()->Departamentos as $value) {
                    $demandaDepartamento = new DemandaDepartamentos();
                    $demandaDepartamento->BNF_Departamentos_id = $value;
                    $demandaDepartamento->BNF2_Demanda_id = $demanda->id;
                    $demandaDepartamento->Eliminado = '0';
                    $this->getDemandaDepartamentoTable()->saveDemandaDepartamento($demandaDepartamento);
                }

                //Agregando Rubros
                foreach ($request->getPost()->Rubros as $value) {
                    $demandaRubro = new DemandaRubros();
                    $demandaRubro->BNF_Rubro_id = $value;
                    $demandaRubro->BNF2_Demanda_id = $demanda->id;
                    $demandaRubro->Eliminado = '0';
                    $this->getDemandaRubroTable()->saveDemandaRubro($demandaRubro);
                }

                //Agregando Empresas Prov
                foreach ($request->getPost()->EmpresaProveedor as $value) {
                    $demandaEmpProv = new DemandaEmpresas();
                    $demandaEmpProv->BNF_Empresa_id = $value;
                    $demandaEmpProv->BNF2_Demanda_id = $demanda->id;
                    $demandaEmpProv->Eliminado = '0';
                    $this->getDemandaEmpresaTable()->saveDemandaEmpresa($demandaEmpProv);
                }

                //Agregando Empresas Prov Adicionales
                $parts = explode(';', $request->getPost()->EmpresasAdicionales);
                if (!empty($parts)) {
                    foreach ($parts as $value) {
                        $demandaEmpAdd = new DemandaEmpresasAdicionales();
                        $demandaEmpAdd->NombreEmpresa = $value;
                        $demandaEmpAdd->BNF2_Demanda_id = $demanda->id;
                        $demandaEmpAdd->Eliminado = '0';
                        $this->getDemandaEmpresaAdicionalTable()->saveDemandaEmpresasAdicionales($demandaEmpAdd);
                    }
                }

                if ($request->getPost()->action == "send") {
                    $config = $this->getServiceLocator()->get('Config');
                    $email = $config['email_sender']['demanda'];
                    $this->enviarCorreo($request, $email);
                    $confirm[] = $this::MESSAGE_SAVE_SEND;
                    $type = "success";
                } else {
                    $confirm[] = $this::MESSAGE_SAVE;
                    $type = "success";
                }

                $form = new FormDemanda('registrar', $datos[0]);
            } else {
                $confirm[] = $this::MESSAGE_ERROR;
                $type = "danger";
            }
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'demanda' => 'active',
                'demandadd' => 'active',
                'form' => $form,
                'confirm' => $confirm,
                'type' => $type,
                'message' => $messages,
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $confirm = null;
        $type = null;
        $messages = array();

        $dataProv = array();
        $dataRubro = array();
        $dataDepa = array();
        $dataSeg = array();
        $dataAdicionales = "";
        $dataProvIds = "";
        $dataRubroIds = "";
        $dataDepaIds = "";
        $dataSegIds = "";
        $dataAdicionalesIds = "";

        $dataSegJS = "";

        $demandaAnt = null;

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('demandas-ofertas', array('action' => 'add'));
        }

        try {
            $demandaAnt = $this->getDemandaTable()->getDemanda($id);
            $demanda = $this->getDemandaTable()->getDemanda($id);
            $demanda->FechaDemanda = date_format(date_create($demanda->FechaDemanda), 'Y-m-d');
            //Rubros
            $contador = 0;
            $rubros = $this->getDemandaRubroTable()->getDemandaRubroByDemanda($demanda->id);
            foreach ($rubros as $value) {
                $dataRubro[] = $value->BNF_Rubro_id;
                $dataRubroIds = $contador > 0 ? $dataRubroIds . '; ' . $value->BNF_Rubro_id : $value->BNF_Rubro_id;
                $contador++;
            }
            //Empresas Proveedoras
            $contador = 0;
            $proveedores = $this->getDemandaEmpresaTable()->getDemandaEmpresaByDemanda($demanda->id);
            foreach ($proveedores as $prov) {
                $dataProv[] = $prov->BNF_Empresa_id;
                $dataProvIds = $contador > 0 ? $dataProvIds . '; ' . $prov->BNF_Empresa_id : $prov->BNF_Empresa_id;
                $contador++;
            }
            //Empresas Adicionales
            $contador = 0;
            $adicionales = $this->getDemandaEmpresaAdicionalTable()->getDemandaEmpresaAdicionalByDemanda($demanda->id);
            foreach ($adicionales as $adicional) {
                $dataAdicionales = $contador > 0 ?
                    $dataAdicionales . '; ' . $adicional->NombreEmpresa : $adicional->NombreEmpresa;
                $dataAdicionalesIds = $contador > 0 ?
                    $dataAdicionalesIds . '; ' . $adicional->id : $adicional->id;
                $contador++;
            }
            //Departamentos
            $contador = 0;
            $departamentos = $this->getDemandaDepartamentoTable()->getDemandaDepartamentoByDemanda($demanda->id);
            foreach ($departamentos as $depa) {
                $dataDepa[] = $depa->BNF_Departamentos_id;
                $dataDepaIds = $contador > 0 ?
                    $dataDepaIds . '; ' . $depa->BNF_Departamentos_id : $depa->BNF_Departamentos_id;
                $contador++;
            }
            //Segmentos
            $contador = 0;
            $segmentos = $this->getDemandaSegmentosTable()->getDemandaSegmentosByDemanda($demanda->id);
            foreach ($segmentos as $segmento) {
                $dataSeg[] = $segmento->BNF2_Segmento_id;
                $dataSegJS = $contador > 0 ?
                    $dataSegJS . ", '" . $segmento->BNF2_Segmento_id . "'" : "['" . $segmento->BNF2_Segmento_id . "'";
                $dataSegIds = $contador > 0 ?
                    $dataSegIds . '; ' . $segmento->BNF2_Segmento_id : $segmento->BNF2_Segmento_id;
                $contador++;
            }
            $dataSegJS = $dataSegJS . "]";

            //Campaña y Segmento
            $segmento = $this->getSegmentoTable()->getSegmentosP($dataSeg[0]);
            $campania = $segmento->BNF2_Campania_id;
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('demandas-ofertas', array('action' => 'index'));
        }

        $datos = $this->inicializacion();
        $form = new FormDemanda('editar', $datos[0]);

        $form->bind($demanda);
        $form->get('EmpresaCliente')->setAttribute('value', $demanda->BNF_Empresa_id);
        $form->get('Rubros')->setAttribute('value', $dataRubro);
        $form->get('EmpresaProveedor')->setAttribute('value', $dataProv);
        $form->get('EmpresasAdicionales')->setAttribute('value', $dataAdicionales);
        $form->get('Departamentos')->setAttribute('value', $dataDepa);
        $form->get('Campania')->setAttribute('value', $campania);
        $form->get('Segmento')->setAttribute('value', $segmento->id);
        $form->get('PrecioMin')->setAttribute('value', $demanda->PrecioMinimo);
        $form->get('PrecioMax')->setAttribute('value', $demanda->PrecioMaximo);
        $form->get('submit')->setAttribute('value', 'Editar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $validate = new DemandaFilter();
            $form->setInputFilter($validate->getInputFilter($datos[1]));
            $form->setData($request->getPost());

            $messages = $this->validarCampos($request, $datos);

            if ($form->isValid() and empty($messages)) {
                $demanda = new Demanda();
                $demanda->id = $id;
                $demanda->BNF_Empresa_id = $request->getPost()->EmpresaCliente;
                $demanda->FechaDemanda = $request->getPost()->FechaDemanda;
                $demanda->PrecioMinimo = (float)$request->getPost()->PrecioMin;
                $demanda->PrecioMaximo = (float)$request->getPost()->PrecioMax;
                $demanda->Target = $request->getPost()->Target;
                $demanda->Comentarios = $request->getPost()->Comentarios;
                $demanda->Actualizaciones = $request->getPost()->Actualizaciones;
                $demanda->Eliminado = 0;
                $this->getDemandaTable()->saveDemanda($demanda);

                //Actualizando Segmentos
                $this->getDemandaSegmentosTable()->deleteDemandaSegmentos($demanda->id);
                foreach ($request->getPost()->Segmento as $value) {
                    if ($this->getDemandaSegmentosTable()->getIfExist($demanda->id, $value)) {
                        $this->getDemandaSegmentosTable()->updateDemandaSegmentos($demanda->id, $value);
                    } else {
                        $demandaSegmentos = new DemandaSegmentos();
                        $demandaSegmentos->BNF2_Segmento_id = $value;
                        $demandaSegmentos->BNF2_Demanda_id = $demanda->id;
                        $demandaSegmentos->Eliminado = '0';
                        $this->getDemandaSegmentosTable()->saveDemandaSegmentos($demandaSegmentos);
                    }
                }

                //Actualizando Departamentos
                $this->getDemandaDepartamentoTable()->deleteDemandaDepartamentos($demanda->id);
                foreach ($request->getPost()->Departamentos as $value) {
                    if ($this->getDemandaDepartamentoTable()->getIfExist($demanda->id, $value)) {
                        $this->getDemandaDepartamentoTable()->updateDemandaDepartamentos($demanda->id, $value);
                    } else {
                        $demandaSegmentos = new DemandaDepartamentos();
                        $demandaSegmentos->BNF_Departamentos_id = $value;
                        $demandaSegmentos->BNF2_Demanda_id = $demanda->id;
                        $demandaSegmentos->Eliminado = '0';
                        $this->getDemandaDepartamentoTable()->saveDemandaDepartamento($demandaSegmentos);
                    }
                }

                //Actualizando Rubros
                $this->getDemandaRubroTable()->deleteDemandaRubro($demanda->id);
                foreach ($request->getPost()->Rubros as $value) {
                    if ($this->getDemandaRubroTable()->getIfExist($demanda->id, $value)) {
                        $this->getDemandaRubroTable()->updateDemandaRubros($demanda->id, $value);
                    } else {
                        $demandaRubro = new DemandaRubros();
                        $demandaRubro->BNF_Rubro_id = $value;
                        $demandaRubro->BNF2_Demanda_id = $demanda->id;
                        $demandaRubro->Eliminado = '0';
                        $this->getDemandaRubroTable()->saveDemandaRubro($demandaRubro);
                    }
                }

                //Actualizando Empresas Prov
                $this->getDemandaEmpresaTable()->deleteDemandaEmpresa($demanda->id);
                foreach ($request->getPost()->EmpresaProveedor as $value) {
                    if ($this->getDemandaEmpresaTable()->getIfExist($demanda->id, $value)) {
                        $this->getDemandaEmpresaTable()->updateDemandaEmpresas($demanda->id, $value);
                    } else {
                        $demandaEmpProv = new DemandaEmpresas();
                        $demandaEmpProv->BNF_Empresa_id = $value;
                        $demandaEmpProv->BNF2_Demanda_id = $demanda->id;
                        $demandaEmpProv->Eliminado = '0';
                        $this->getDemandaEmpresaTable()->saveDemandaEmpresa($demandaEmpProv);
                    }
                }

                //Agregando Empresas Prov Adicionales
                $parts = explode(';', $request->getPost()->EmpresasAdicionales);
                if (!empty($parts)) {
                    $this->getDemandaEmpresaAdicionalTable()->deleteDemandaEmpresaAdicional($demanda->id);
                    foreach ($parts as $value) {
                        $value = trim($value);
                        if ($this->getDemandaEmpresaAdicionalTable()->getIfExist($demanda->id, $value)) {
                            $this->getDemandaEmpresaAdicionalTable()
                                ->updateDemandaEmpresaAdicionales($demanda->id, $value);
                        } else {
                            $demandaEmpAdd = new DemandaEmpresasAdicionales();
                            $demandaEmpAdd->NombreEmpresa = $value;
                            $demandaEmpAdd->BNF2_Demanda_id = $demanda->id;
                            $demandaEmpAdd->Eliminado = '0';
                            $this->getDemandaEmpresaAdicionalTable()->saveDemandaEmpresasAdicionales($demandaEmpAdd);
                        }
                    }
                }

                if ($request->getPost()->action == "send") {
                    $config = $this->getServiceLocator()->get('Config');
                    $email = $config['email_sender']['demanda'];
                    $this->enviarCorreo($request, $email);
                    $confirm[] = $this::MESSAGE_UPDATE_SEND;
                    $type = "success";
                } else {
                    //Confirmacion del Registro
                    $confirm[] = $this::MESSAGE_UPDATE;
                    $type = "success";
                }

                #region Guardar Log
                $demandaLog = new DemandaLog();
                $demandaLog->BNF2_Demanda_id = $id;
                $demandaLog->BNF_Empresa_id = $demandaAnt->BNF_Empresa_id;
                $demandaLog->FechaDemanda = $demandaAnt->FechaDemanda;
                $demandaLog->PrecioMinimo = $demandaAnt->PrecioMinimo;
                $demandaLog->PrecioMaximo = $demandaAnt->PrecioMaximo;
                $demandaLog->Target = $demandaAnt->Target;
                $demandaLog->Comentarios = $demandaAnt->Comentarios;
                $demandaLog->Actualizaciones = $demandaAnt->Actualizaciones;
                $demandaLog->Eliminado = (int)$demandaAnt->Eliminado;
                $demandaLog->Rubros = $dataRubroIds;
                $demandaLog->Segmentos = $dataSegIds;
                $demandaLog->EmpresaProveedor = $dataProvIds;
                $demandaLog->EmpresasAdicionales = $dataAdicionalesIds;
                $demandaLog->Departamentos = $dataDepaIds;
                $this->getDemandaLogTable()->saveDemandaLog($demandaLog);
                #endregion

                $dataRubro = array();
                $dataProv = array();
                $dataDepa = array();
                $dataAdicionales = "";

                //Rubros
                $rubros = $this->getDemandaRubroTable()->getDemandaRubroByDemanda($demanda->id);
                foreach ($rubros as $value) {
                    $dataRubro[] = $value->BNF_Rubro_id;
                }
                //Empresas Proveedoras
                $proveedores = $this->getDemandaEmpresaTable()->getDemandaEmpresaByDemanda($demanda->id);
                foreach ($proveedores as $prov) {
                    $dataProv[] = $prov->BNF_Empresa_id;
                }
                //Empresas Adicionales
                $adicionales = $this->getDemandaEmpresaAdicionalTable()
                    ->getDemandaEmpresaAdicionalByDemanda($demanda->id);
                $contador = 0;
                foreach ($adicionales as $adicional) {
                    if ($contador > 0) {
                        $dataAdicionales = $dataAdicionales . '; ' . $adicional->NombreEmpresa;
                    } else {
                        $dataAdicionales = $adicional->NombreEmpresa;
                    }
                    $contador++;
                }
                //Departamentos
                $departamentos = $this->getDemandaDepartamentoTable()->getDemandaDepartamentoByDemanda($demanda->id);
                foreach ($departamentos as $depa) {
                    $dataDepa[] = $depa->BNF_Departamentos_id;
                }
                //Segmentos
                $contador = 0;
                $segmentos = $this->getDemandaSegmentosTable()->getDemandaSegmentosByDemanda($demanda->id);
                foreach ($segmentos as $segmento) {
                    $dataSeg[] = $segmento->BNF2_Segmento_id;
                    $dataSegJS = $contador > 0 ?
                        $dataSegJS . ", '" . $segmento->BNF2_Segmento_id . "'" : "['" . $segmento->BNF2_Segmento_id . "'";
                    $contador++;
                }
                $dataSegJS = $dataSegJS . "]";

                //Campaña y Segmento
                $segmento = $this->getSegmentoTable()->getSegmentosP($dataSeg[0]);
                $campania = $segmento->BNF2_Campania_id;

                $form->get('EmpresaCliente')->setAttribute('value', $demanda->BNF_Empresa_id);
                $form->get('Rubros')->setAttribute('value', $dataRubro);
                $form->get('EmpresaProveedor')->setAttribute('value', $dataProv);
                $form->get('EmpresasAdicionales')->setAttribute('value', $dataAdicionales);
                $form->get('Departamentos')->setAttribute('value', $dataDepa);
                $form->get('Campania')->setAttribute('value', $campania);
                $form->get('Segmento')->setAttribute('value', $segmento->id);
                $form->get('PrecioMin')->setAttribute('value', $demanda->PrecioMinimo);
                $form->get('PrecioMax')->setAttribute('value', $demanda->PrecioMaximo);
            } else {
                $confirm[] = $this::MESSAGE_ERROR;
                $type = "danger";
            }
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'demanda' => 'active',
                'demandadd' => 'active',
                'form' => $form,
                'dataSegJS' => $dataSegJS,
                'confirm' => $confirm,
                'type' => $type,
                'id' => $id,
                'message' => $messages,
            )
        );
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $empresa = (int)$this->params()->fromRoute('id', 0);
        $fecha = $this->params()->fromRoute('val', null);
        $campania = (int)$this->params()->fromRoute('val2', 0);

        $resultado = $this->getDemandaTable()->getReporte($empresa, $fecha, $campania);
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Demandas Ofertas")
                ->setSubject("Demandas Ofertas")
                ->setDescription("Documento listando Demandas de Ofertas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Demanas Ofertas");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:P' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);

            #region Styles
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );

            $styleArray2 = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    ),
                ),
            );
            #endregion

            $objPHPExcel->getActiveSheet()->getStyle('A1:P' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'RUC')
                ->setCellValue('C1', 'Razón Social')
                ->setCellValue('D1', 'Personal de Contacto')
                ->setCellValue('E1', 'Campaña')
                ->setCellValue('F1', 'Segmento')
                ->setCellValue('G1', 'Fecha de Creación')
                ->setCellValue('H1', 'Rubros')
                ->setCellValue('I1', 'Empresa Proveedoras')
                ->setCellValue('J1', 'Empresa Proveedoras Adicionales')
                ->setCellValue('K1', 'Departamentos')
                ->setCellValue('L1', 'Precio Mínimo')
                ->setCellValue('M1', 'Precio Máximo')
                ->setCellValue('N1', 'Target')
                ->setCellValue('O1', 'Comentarios')
                ->setCellValue('P1', 'Actualizaciones');
            $i = 2;

            foreach ($resultado as $registro) {
                $segmentos = $this->getDemandaSegmentosTable()->getDemandaSegmentosByDemanda($registro->id);
                $count = 0;
                foreach ($segmentos as $value) {
                    $segmento = $this->getSegmentoTable()->getSegmentosP($value->BNF2_Segmento_id);
                    if($count == 0)
                        @$registro->Segmentos .= $segmento->NombreSegmento;
                    else
                        @$registro->Segmentos .= ', ' . $segmento->NombreSegmento;
                    $count++;
                }
                $empresas = $this->getDemandaEmpresaTable()->getDemandaEmpresaByDemanda($registro->id);
                $count = 0;
                foreach ($empresas as $value) {
                    $empresa = $this->getEmpresaTable()->getEmpresa($value->BNF_Empresa_id);
                    if($count == 0)
                        @$registro->Empresas .= $empresa->NombreComercial;
                    else
                        @$registro->Empresas .= ', ' . $empresa->NombreComercial;
                    $count++;
                }
                $departamentos = $this->getDemandaDepartamentoTable()->getDemandaDepartamentoByDemanda($registro->id);
                $count = 0;
                foreach ($departamentos as $value) {
                    $departamento = $this->getUbigeoTable()->getUbigeo($value->BNF_Departamentos_id);
                    if($count == 0)
                        @$registro->Departamentos .= $departamento->Nombre;
                    else
                        @$registro->Departamentos .= ', ' . $departamento->Nombre;
                    $count++;
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Ruc)
                    ->setCellValue('C' . $i, $registro->Empresa)
                    ->setCellValue('D' . $i, $registro->CorreoPersonaAtencion)
                    ->setCellValue('E' . $i, $registro->Campania)
                    ->setCellValue('F' . $i, $registro->Segmentos)
                    ->setCellValue('G' . $i, $registro->FechaDemanda)
                    ->setCellValue('H' . $i, $registro->Rubro)
                    ->setCellValue('I' . $i, $registro->Empresas)
                    ->setCellValue('J' . $i, $registro->EmpresasAdicionales)
                    ->setCellValue('K' . $i, $registro->Departamentos)
                    ->setCellValue('L' . $i, $registro->PrecioMinimo)
                    ->setCellValue('M' . $i, $registro->PrecioMaximo)
                    ->setCellValue('N' . $i, $registro->Target)
                    ->setCellValue('O' . $i, $registro->Comentarios)
                    ->setCellValue('P' . $i, $registro->Actualizaciones);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="DemandasOfertas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function getDataEmpresaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $response = $this->getResponse();
        $request = $this->getRequest();
        $dataEmpresa = array();
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
                        $dataEmpresa['ruc'] = $result->Ruc;
                        $dataEmpresa['razon'] = $result->RazonSocial;
                        $dataEmpresa['contacto'] = $result->CorreoPersonaAtencion;

                        $campanias = $this->getCampaniaTable()->getCampaniasPByEmpresa($id);
                        $dataCampanias[] = array('id' => '', 'text' => 'Seleccione...');
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
                'empresa' => $dataEmpresa,
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
                    if ($result = $this->getSegmentoTable()->getAllSegmentos($id)) {
                        foreach ($result as $value) {
                            $dataSegmentos[] = array('value' => $value->id, 'text' => $value->NombreSegmento);
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

    public function validarCampos($request, $datos)
    {
        $messages = array();
        //Validar Segmentos
        $segmentos = $request->getPost()->Segmento;
        if (empty($segmentos)) {
            $messages['segmento'] = "Debe seleccionar un valor";
        } else {
            $valid = new RecordExists(
                array(
                    'table' => 'BNF2_Segmentos',
                    'field' => 'id',
                    'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
                )
            );
            foreach ($segmentos as $value) {
                if (!$valid->isValid($value)) {
                    $messages['segmento'] = "El valor seleccionado, no es válido.";
                }
            }
        }
        //Validar Rubros
        $rubros = $request->getPost()->Rubros;
        if (empty($rubros)) {
            $messages['rubro'] = "Debe seleccionar un valor";
        } else {
            $valid = new InArray(
                array('haystack' => $datos[1]['rubro'])
            );

            foreach ($rubros as $value) {
                if (!$valid->isValid($value)) {
                    $messages['rubro'] = "El valor seleccionado, no es válido.";
                }
            }
        }
        //Validar Empresas
        $empresasProv = $request->getPost()->EmpresaProveedor;
        if (empty($empresasProv)) {
            $messages['empresa'] = "Debe seleccionar un valor";
        } else {
            $valid = new InArray(
                array('haystack' => $datos[1]['empprov'])
            );

            foreach ($empresasProv as $value) {
                if (!$valid->isValid($value)) {
                    $messages['empresa'] = "El valor seleccionado, no es válido.";
                }
            }
        }
        //Validar Departamentos
        $departamentos = $request->getPost()->Departamentos;
        if (empty($departamentos)) {
            $messages['departamento'] = "Debe seleccionar un valor";
        } else {
            $valid = new InArray(
                array('haystack' => $datos[1]['depas'])
            );

            foreach ($departamentos as $value) {
                if (!$valid->isValid($value)) {
                    $messages['departamento'] = "El valor seleccionado, no es válido.";
                }
            }
        }
        //Validar Empresas Adicionales
        $adicionales = $request->getPost()->EmpresasAdicionales;
        if (!empty($adicionales)) {
            $parts = explode(';', $adicionales);
            $valid = new Regex(
                array('pattern' => "/^([a-zA-Z ÑñÁáÉéÍíÓóÚú&\´\.\/'\,\-])+$/")
            );

            foreach ($parts as $value) {
                if (!$valid->isValid($value)) {
                    $messages['adicionales'][] = "El valor ingresado '" . $value . "', no es válido.";
                }
            }
        }
        //Validar Envio
        $enviar = $request->getPost()->action;
        if (!empty($enviar)) {
            $valid = new Identical(
                array('token' => 'send', 'strict' => false)
            );
            $valid->isValid($enviar); //false

            if (!$valid->isValid($enviar)) {
                $messages['action'] = "No se Registro, revisar los datos ingresados.";
            }
        }
        return $messages;
    }

    public function enviarCorreo($request, $email)
    {
        $segmentos = '';
        $campania = '';
        $count = 0;
        foreach ($request->getPost()->Segmento as $value) {
            $segmento = $this->getSegmentoTable()->getSegmentosP((int)$value);
            if($count == 0)
                $campania = $this->getCampaniaTable()->getCampaniasP($segmento->BNF2_Campania_id);
            if($count == 0)
                $segmentos .= $segmento->NombreSegmento;
            else
                $segmentos .= ', ' . $segmento->NombreSegmento;
            $count++;
        }
        $empresa = $this->getEmpresaTable()->getEmpresa($request->getPost()->EmpresaCliente);

        $nombreRubros = "";
        $contador = 0;
        foreach ($request->getPost()->Rubros as $rubro) {
            $data = $this->getRubroTable()->getRubro($rubro);
            $nombreRubros = $contador > 0 ?
                $nombreRubros . '; ' . $data->Nombre : $data->Nombre;
            $contador++;
        }

        $nombreProveedores = "";
        $contador = 0;
        foreach ($request->getPost()->EmpresaProveedor as $proveedor) {
            $data = $this->getEmpresaTable()->getEmpresa($proveedor);
            $nombreProveedores = $contador > 0 ?
                $nombreProveedores . '; ' . $data->NombreComercial : $data->NombreComercial;
            $contador++;
        }

        $nombreDepartamentos = "";
        $contador = 0;
        foreach ($request->getPost()->Departamentos as $departamento) {
            $data = $this->getUbigeoTable()->getUbigeo($departamento);
            $nombreDepartamentos = $contador > 0 ?
                $nombreDepartamentos . '; ' . $data->Nombre : $data->Nombre;
            $contador++;
        }

        $mailContent = array(
            "empresa" => $empresa->NombreComercial,
            "ruc" => $empresa->Ruc,
            "razon" => $empresa->RazonSocial,
            "contacto" => $empresa->CorreoPersonaAtencion,
            "campania" => $campania->NombreCampania,
            "segmento" => $segmentos,
            "fecha" => $request->getPost()->FechaDemanda,
            "preciomin" => (float)$request->getPost()->PrecioMin,
            "preciomax" => (float)$request->getPost()->PrecioMax,
            "target" => $request->getPost()->Target,
            "comentarios" => $request->getPost()->Comentarios,
            "actualizaciones" => $request->getPost()->Actualizaciones,
            "adicionales" => $request->getPost()->EmpresasAdicionales,
            "rubros" => $nombreRubros,
            "proveedores" => $nombreProveedores,
            "departamentos" => $nombreDepartamentos
        );

        $transport = $this->getServiceLocator()->get('mail.transport');
        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        $content = $renderer->render(
            'mail-demandas-ofertas',
            ['contenido' => $mailContent]
        );

        $messageEmail = new Message();
        $messageEmail->addTo($email)
            ->addFrom('oferta@beneficios.pe', 'Beneficios.pe')
            ->setSubject('Registro de Pedidos Demandas');

        $htmlBody = new MimePart($content);
        $htmlBody->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlBody));
        $messageEmail->setBody($body);
        $transport->send($messageEmail);
    }
}
