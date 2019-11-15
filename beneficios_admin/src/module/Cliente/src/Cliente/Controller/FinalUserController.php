<?php

namespace Cliente\Controller;

use Cliente\Form\FinalUserSearchForm;
use Cliente\Model\EmpresaSegmentoCliente;
use Cliente\Model\EmpresaSubgrupoCliente;
use Cliente\Model\EmpresaClienteCliente;
use Cliente\Model\Cliente;
use Cliente\Model\Preguntas;
use Cliente\Form\FinalUserForm;
use Empresa\Model\EmpresaSegmento;
use Zend\Escaper\Escaper;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class FinalUserController extends AbstractActionController
{
    const USUARIO_CLIENTE = 7;
    const TIPO_DNI = 1;
    const TIPO_PASAPORTE = 2;
    const TIPO_OTROS = 3;

    #region ObjectTables
    public function getClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\ClienteTable');
    }

    public function getEmpresaSegmentoClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaSegmentoClienteTable');
    }

    public function getEmpresaSubgrupoClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaSubgrupoClienteTable');
    }

    public function getTipoEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\TipoEmpresaTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getEmpresaSegmentoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaSegmentoTable');
    }

    public function getEmpresaSubgrupoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaSubgrupoTable');
    }

    public function getEmpresaTipoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTipoEmpresaTable');
    }

    public function getSegmentoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\SegmentoTable');
    }

    public function getSubGrupoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\SubGrupoTable');
    }

    public function getTipoDocumentoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\TipoDocumentoTable');
    }

    public function getEmpresaCliente()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaClienteClienteTable');
    }

    public function getPreguntas()
    {
        return $this->serviceLocator->get('Cliente\Model\Table\PreguntasTable');
    }

    #endregion

    public function indexAction()
    {
        $empresas = array();
        $nombre_empresa = null;
        $searchClient = null;
        $searchCompany = null;

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        try {
            $dataEmpresas = $this->getEmpresaTable()->getEmpresaCli();
            $empresas["all"] = "Listar Todos";
            foreach ($dataEmpresas as $e) {
                $empresas[$e->id] = $e->NombreComercial . " (" . $e->RazonSocial . ") - " . $e->Ruc;
            }
        } catch (\Exception $ex) {
            $empresas = array();
        }

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $nombre_empresa = $empresas[$empresa_value];
            $form = new FinalUserSearchForm('buscar', $empresa_value, $tipo_usuario);
        } else {
            $form = new FinalUserSearchForm('buscar', $empresas);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $searchClient = $request->getPost()->cliente ? trim($request->getPost()->cliente) : null;
            $searchCompany = $request->getPost()->empresa ? $request->getPost()->empresa : null;
        } else {
            $searchClient = $this->params()->fromRoute('cliente') ? trim($this->params()->fromRoute('cliente')) : null;
            $searchCompany = $this->params()->fromRoute('empresa') ? $this->params()->fromRoute('empresa') : null;
        }

        $form->setData(
            array(
                'cliente' => str_replace("-", " ", $searchClient),
                'empresa' => ($tipo_usuario == $this::USUARIO_CLIENTE) ? $empresa_value : $searchCompany
            )
        );

        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;

        $itemsPerPage = 10;
        $lista_clientes = array();
        $paginator = array();
        if ($searchClient != null or $searchCompany != null) {
            $searchClient = str_replace("-", " ", $searchClient);
            $datoEmpresa = ($searchCompany == 'all') ? null : $searchCompany;
            $clients = $this->getClienteTable()
                ->getAllClients($searchClient, $datoEmpresa, $order_by, $order, $tipo_usuario);
            $paginator = new Paginator(new paginatorIterator($clients));
            $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);

            if ($searchClient != null) {
                $cliente_filter = array();
                $cliente_others = array();
                foreach ($clients as $value) {
                    if ($value->NumeroDocumento == $searchClient ||
                        stripos($value->Nombre, $searchClient) !== false ||
                        stripos($value->Apellido, $searchClient) !== false
                    ) {
                        array_push(
                            $cliente_filter,
                            array(
                                'id' => $value->id,
                                'Nombre' => $value->Nombre,
                                'Apellido' => $value->Apellido,
                                'Genero' => $value->Genero,
                                'NumeroDocumento' => $value->NumeroDocumento,
                                'NombreComercial' => $value->NombreComercial,
                                'NombreSegmento' => $value->NombreSegmento,
                                'NombreSubgrupo' => $value->NombreSubgrupo,
                                'Estado' => $value->Estado,
                                'Activo' => ($value->Estado == "Activo") ? 0 : 1,
                                'idEmpresa' => $value->idEmpresa,
                            )
                        );
                    } else {
                        array_push(
                            $cliente_others,
                            array(
                                'id' => $value->id,
                                'Nombre' => $value->Nombre,
                                'Apellido' => $value->Apellido,
                                'Genero' => $value->Genero,
                                'NumeroDocumento' => $value->NumeroDocumento,
                                'NombreComercial' => $value->NombreComercial,
                                'NombreSegmento' => $value->NombreSegmento,
                                'NombreSubgrupo' => $value->NombreSubgrupo,
                                'Estado' => $value->Estado,
                                'Activo' => ($value->Estado == "Activo") ? 0 : 1,
                                'idEmpresa' => $value->idEmpresa,
                            )
                        );
                    }
                }
                if ($page == 1) {
                    $lista_clientes = array_merge(
                        $cliente_filter,
                        array_splice($cliente_others, 0, $itemsPerPage - count($cliente_filter))
                    );
                } else {
                    $lista_clientes = array_splice(
                        $cliente_others,
                        (($page - 1) * $itemsPerPage) - count($cliente_filter),
                        10
                    );
                }
            } else {
                foreach ($paginator as $value) {
                    array_push(
                        $lista_clientes,
                        array(
                            'id' => $value->id,
                            'Nombre' => $value->Nombre,
                            'Apellido' => $value->Apellido,
                            'Genero' => $value->Genero,
                            'NumeroDocumento' => $value->NumeroDocumento,
                            'NombreComercial' => $value->NombreComercial,
                            'NombreSegmento' => $value->NombreSegmento,
                            'NombreSubgrupo' => $value->NombreSubgrupo,
                            'Estado' => $value->Estado,
                            'Activo' => ($value->Estado == "Activo") ? 0 : 1,
                            'idEmpresa' => $value->idEmpresa,
                        )
                    );
                }
            }
        } elseif ($searchCompany == 'all') {
            $clients = $this->getClienteTable()->getAllClients(null, null, $order_by, $order);
            $paginator = new Paginator(new paginatorIterator($clients));
            $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);
            foreach ($paginator as $value) {
                array_push(
                    $lista_clientes,
                    array(
                        'id' => $value->id,
                        'Nombre' => $value->Nombre,
                        'Apellido' => $value->Apellido,
                        'Genero' => $value->Genero,
                        'NumeroDocumento' => $value->NumeroDocumento,
                        'NombreComercial' => $value->NombreComercial,
                        'NombreSegmento' => $value->NombreSegmento,
                        'NombreSubgrupo' => $value->NombreSubgrupo,
                        'Estado' => $value->Estado,
                        'Activo' => $value->Activo,
                        'idEmpresa' => $value->idEmpresa,
                    )
                );
            }
        }
        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        $client = str_replace(" ", "-", $searchClient);
        return new ViewModel(
            array(
                'final' => 'active',
                'flistar' => 'active',
                'form' => $form,
                'lista_clientes' => $lista_clientes,
                'clientes' => $paginator,
                'order_by' => $order_by,
                'order' => $order,
                'client' => $client,
                'searchClient' => $searchClient,
                'searchCompany' => $searchCompany,
                'nombre_empresa' => $nombre_empresa,
            )
        );
    }

    public function addAction()
    {
        $mensaje = null;
        $alert = 'danger';
        $errors = array();

        $tipoDocumentos = $this->getTipoDocumentoTable()->fetchAll();
        $tipos = array();
        foreach ($tipoDocumentos as $tipoDocumento) {
            $tipos[$tipoDocumento->id] = $tipoDocumento->Nombre;
        }

        $form = new FinalUserForm($tipos);
        $request = $this->getRequest();

        $identity = $this->identity();

        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $listaEmpresas = array($this->getEmpresaTable()->getEmpresa($empresa_value));
        } else {
            $listaEmpresas = $this->getEmpresaTable()->getEmpresasCliente();
        }

        if ($request->isPost()) {
            $cliente = new Cliente();

            if ($request->getPost()->BNF_TipoDocumento_id == $this::TIPO_DNI) {
                $form->setInputFilter($cliente->getInputFilter(8, 1, $tipos));
            } elseif ($request->getPost()->BNF_TipoDocumento_id == $this::TIPO_PASAPORTE) {
                $form->setInputFilter($cliente->getInputFilter(15, 2, $tipos));
            } elseif ($request->getPost()->BNF_TipoDocumento_id == $this::TIPO_OTROS) {
                $form->setInputFilter($cliente->getInputFilter(5, 3, $tipos));
            } else {
                $errors['ntipodoc'] = 'Seleccione un Tipo de Documento.';
                $errors['ntipodocerror'] = 'has-error';
            }

            $form->setData($request->getPost());

            $BNF_Empresa_id = $request->getPost()->idEmpresa;
            $BNF_Segmento_id = $request->getPost()->idSegmento;
            $BNF_Subgrupo_id = $request->getPost()->idSubgrupo;
            $Activo = $request->getPost()->Estado;

            if (count($BNF_Empresa_id) == count($BNF_Segmento_id) and count($BNF_Empresa_id) != 0) {
                $esp = 0;
                $empresaRegister = array();
                foreach ($BNF_Empresa_id as $index => $dato) {
                    $tipo = $this->getEmpresaTable()->getEmpresa($dato);
                    if ($tipo->ClaseEmpresaCliente == 'Especial') {
                        $esp += 1;
                    }
                    if (in_array($dato, $empresaRegister) && !isset($multipleRegister)) {
                        $errors['mclie'] = 'Un usuario final no puede registrarse dos veces en la misma empresa.';
                        $multipleRegister = true;
                    } else {
                        $empresaRegister[] = $dato;
                    }
                }
                if ($esp != count($BNF_Subgrupo_id)) {
                    $errors['mempre'] = 'Debe de Seleccionar un Subgrupo para las Empresas de Tipo Especial';
                }
            } else {
                $errors['empre'] = 'has-error';
                $errors['mempre'] = 'Por favor, llene correctamente los campos asociados a la Empresa.';
            }

            $repetido = $this->getClienteTable()->getDocumento($request->getPost()->NumeroDocumento);
            if ($form->isValid() and !$repetido and $errors == null) {
                $cliente->exchangeArray($form->getData());

                $idClient = $this->getClienteTable()->saveCliente($cliente);

                foreach ($BNF_Empresa_id as $i => $value) {
                    if (!$this->getEmpresaSegmentoTable()
                        ->getEmpresaSegmentoIfExist($BNF_Empresa_id[$i], $BNF_Segmento_id[$i])
                    ) {
                        //Agregar Empresa Segmento
                        $empresaSegmento = new EmpresaSegmento();
                        $empresaSegmento->BNF_Empresa_id = $BNF_Empresa_id[$i];
                        $empresaSegmento->BNF_Segmento_id = $BNF_Segmento_id[$i];
                        $empresaSegmento->Eliminado = '0';
                        $this->getEmpresaSegmentoTable()
                            ->saveEmpresaSegmento($empresaSegmento);
                    }

                    //Agregar EmpresaSegmentoCliente
                    $empresaSegmento = $this->getEmpresaSegmentoTable()
                        ->getEmpresaSegmentoDatos($BNF_Empresa_id[$i], $BNF_Segmento_id[$i]);

                    $empresaSegmentoCliente = new EmpresaSegmentoCliente();
                    $empresaSegmentoCliente->BNF_EmpresaSegmento_id = $empresaSegmento->id;
                    $empresaSegmentoCliente->BNF_Cliente_id = $idClient;
                    $empresaSegmentoCliente->Eliminado = '0';

                    $this->getEmpresaSegmentoClienteTable()
                        ->saveEmpresaSegmentoCliente($empresaSegmentoCliente);

                    //Agregar Relación Cliente Empresa
                    $empresaClienteCliente = new EmpresaClienteCliente();
                    $empresaClienteCliente->BNF_Empresa_id = $BNF_Empresa_id[$i];
                    $empresaClienteCliente->BNF_Cliente_id = $idClient;
                    $empresaClienteCliente->Estado = isset($Activo[$BNF_Empresa_id[$i]]) ? 'Activo' : 'Inactivo';
                    $empresaClienteCliente->Eliminado = 0;

                    $this->getEmpresaCliente()
                        ->saveEmpresaCliente($empresaClienteCliente);

                    //Agregar EmpresaSubgrupoCliente
                    if (isset($BNF_Subgrupo_id[$BNF_Empresa_id[$i]])) {
                        $empresaSubgrupoCliente = new EmpresaSubgrupoCliente();
                        $empresaSubgrupoCliente->BNF_Subgrupo_id = $BNF_Subgrupo_id[$BNF_Empresa_id[$i]];
                        $empresaSubgrupoCliente->BNF_Cliente_id = $idClient;
                        $empresaSubgrupoCliente->Eliminado = '0';

                        $this->getEmpresaSubgrupoClienteTable()
                            ->saveEmpresaSubgrupoCliente($empresaSubgrupoCliente);
                    }
                }

                $pregunta = new Preguntas();
                $pregunta->BNF_Cliente_id = $idClient;
                $this->getPreguntas()->savePreguntas($pregunta);

                $form = new FinalUserForm($tipos);
                $mensaje[] = 'Usuario Final Registrado Correctamente';
                $alert = 'success';
            } elseif ($form->isValid() and $errors == null and $repetido and $tipo_usuario == $this::USUARIO_CLIENTE) {
                //Data del Cliente
                $cliente->exchangeArray($form->getData());
                $dataCliente = $this->getClienteTable()->getClientByDoc($cliente->NumeroDocumento);

                //Data de sus Empresas
                $empresas = array();
                $dataEmpresa = $this->getEmpresaCliente()->searchByDoc($cliente->NumeroDocumento);
                foreach ($dataEmpresa as $value) {
                    $empresas[] = $value->BNF_Empresa_id;
                }

                $dataCliente->BNF_TipoDocumento_id = ($cliente->BNF_TipoDocumento_id == "")
                    ? $dataCliente->BNF_TipoDocumento_id : $cliente->BNF_TipoDocumento_id;
                $dataCliente->Nombre = ($cliente->Nombre == "")
                    ? $dataCliente->Nombre : $cliente->Nombre;
                $dataCliente->Genero = ($cliente->Genero == "")
                    ? $dataCliente->Genero : $cliente->Genero;
                $dataCliente->FechaNacimiento = ($cliente->FechaNacimiento == "")
                    ? $dataCliente->FechaNacimiento : $cliente->FechaNacimiento;

                $this->getClienteTable()->saveCliente($dataCliente);

                $relacion_EmpCliente = $this->getEmpresaCliente()->searchByClientId($dataCliente->id);

                //Desactivar todas las relaciones anteriores
                $this->getEmpresaCliente()->updateArray(
                    array('Eliminado' => 1, "Estado" => 'Inactivo'),
                    array(
                        'BNF_Cliente_id' => $dataCliente->id,
                        'BNF_Empresa_id' => $BNF_Empresa_id[0]
                    )
                );

                $datosEmpSegClient = $this->getEmpresaSegmentoClienteTable()
                    ->getEmpresaSegmentoClienteByEmpresa($BNF_Empresa_id[0], $dataCliente->id);

                foreach ($datosEmpSegClient as $value) {
                    $this->getEmpresaSegmentoClienteTable()->updateArray(
                        array('Eliminado' => 1),
                        array('idBNF_EmpresaSegmentoCliente' => $value->idBNF_EmpresaSegmentoCliente)
                    );
                }

                //Relacion entre Empresa-Cliente
                $total_relacion_EmpCliente = count($relacion_EmpCliente);
                $total_EmpCliente = count($BNF_Empresa_id);

                for ($i = 0; $i < $total_EmpCliente; $i++) {
                    $count = 0;
                    foreach ($relacion_EmpCliente as $value) {
                        if ($value->BNF_Empresa_id != $BNF_Empresa_id[$i]) {
                            $count++;
                        } else {
                            $empresa_Cliente = new EmpresaClienteCliente();
                            $empresa_Cliente->id = $value->id;
                            $empresa_Cliente->BNF_Empresa_id = $value->BNF_Empresa_id;
                            $empresa_Cliente->BNF_Cliente_id = $dataCliente->id;
                            $empresa_Cliente->Estado = isset($Activo[$value->BNF_Empresa_id])
                                ? 'Activo' : 'Inactivo';
                            $empresa_Cliente->Eliminado = isset($Activo[$value->BNF_Empresa_id]) ? 0 : 1;
                            $this->getEmpresaCliente()->saveEmpresaCliente($empresa_Cliente);
                        }
                    }

                    if ($count == $total_relacion_EmpCliente) {
                        $empresa_Cliente = new EmpresaClienteCliente();
                        $empresa_Cliente->BNF_Empresa_id = $BNF_Empresa_id[$i];
                        $empresa_Cliente->BNF_Cliente_id = $dataCliente->id;
                        $empresa_Cliente->Estado = isset($Activo[$BNF_Empresa_id[$i]]) ? 'Activo' : 'Inactivo';
                        $empresa_Cliente->Eliminado = isset($Activo[$BNF_Empresa_id[$i]]) ? 0 : 1;
                        $this->getEmpresaCliente()->saveEmpresaCliente($empresa_Cliente);
                    }
                }

                //Relacion entre Empresa-Segmento-Cliente
                $total_EmpSegCliente = count($BNF_Segmento_id);

                for ($i = 0; $i < $total_EmpSegCliente; $i++) {
                    if ($this->getEmpresaSegmentoTable()
                        ->getEmpresaSegmentoIfExist($BNF_Empresa_id[$i], $BNF_Segmento_id[$i])
                    ) {
                        $segmento = $this->getEmpresaSegmentoTable()
                            ->getEmpresaSegmentoDatos($BNF_Empresa_id[$i], $BNF_Segmento_id[$i]);

                        $id_relacionESC = $this->getEmpresaSegmentoClienteTable()->
                        getEmpresaSegmentoClienteData($segmento->id, $dataCliente->id);
                        if ($id_relacionESC) {
                            $this->getEmpresaSegmentoClienteTable()->updateArray(
                                array("Eliminado" => 0,),
                                array(
                                    "idBNF_EmpresaSegmentoCliente" => $id_relacionESC
                                        ->idBNF_EmpresaSegmentoCliente
                                )
                            );
                        } else {
                            $empresaSegClient = new EmpresaSegmentoCliente();
                            $empresaSegClient->BNF_Cliente_id = $dataCliente->id;
                            $empresaSegClient->BNF_EmpresaSegmento_id = $segmento->id;
                            $empresaSegClient->Eliminado = 0;

                            $this->getEmpresaSegmentoClienteTable()
                                ->saveEmpresaSegmentoCliente($empresaSegClient);
                        }
                    } else {
                        $empresaSegmento = new EmpresaSegmento();
                        $empresaSegmento->BNF_Empresa_id = $BNF_Empresa_id[$i];
                        $empresaSegmento->BNF_Segmento_id = $BNF_Segmento_id[$i];
                        $empresaSegmento->Eliminado = 0;

                        $id_empresaSegmento = $this->getEmpresaSegmentoTable()
                            ->saveEmpresaSegmento($empresaSegmento);

                        $empresaSegClient = new EmpresaSegmentoCliente();
                        $empresaSegClient->BNF_Cliente_id = $dataCliente->id;
                        $empresaSegClient->BNF_EmpresaSegmento_id = $id_empresaSegmento;
                        $empresaSegClient->Eliminado = 0;

                        $this->getEmpresaSegmentoClienteTable()
                            ->saveEmpresaSegmentoCliente($empresaSegClient);
                    }
                }

                //Guardando Sugrupos
                foreach ($BNF_Empresa_id as $i => $v) {
                    $empresa = $this->getEmpresaTable()->getEmpresa($BNF_Empresa_id[$i]);
                    $clase = $empresa->ClaseEmpresaCliente;
                    if ($clase == "Especial") {
                        if (!$this->getEmpresaSubgrupoClienteTable()
                            ->getEmpresaSubgrupoClienteIfExist($BNF_Empresa_id[$i], $dataCliente->id)
                        ) {
                            $empresaSubgrupoCliente = new EmpresaSubgrupoCliente();
                            $empresaSubgrupoCliente->BNF_Subgrupo_id = $BNF_Subgrupo_id[$BNF_Empresa_id[$i]];
                            $empresaSubgrupoCliente->BNF_Cliente_id = $dataCliente->id;
                            $empresaSubgrupoCliente->Eliminado = '0';
                            $this->getEmpresaSubgrupoClienteTable()
                                ->saveEmpresaSubgrupoCliente($empresaSubgrupoCliente);
                        } else {
                            $empresaSubgrupoCliente = new EmpresaSubgrupoCliente();
                            $datos = $this->getEmpresaSubgrupoClienteTable()
                                ->getEmpresaSubgrupoClienteDataExist($BNF_Empresa_id[$i], $dataCliente->id);
                            foreach ($datos as $data) {
                                $empresaSubgrupoCliente = $data;
                            }
                            $empresaSubgrupoCliente->BNF_Subgrupo_id = $BNF_Subgrupo_id[$BNF_Empresa_id[$i]];
                            $empresaSubgrupoCliente->Eliminado = '0';
                            $this->getEmpresaSubgrupoClienteTable()
                                ->saveEmpresaSubgrupoCliente($empresaSubgrupoCliente);
                        }
                    }
                }

                $form = new FinalUserForm($tipos);
                $mensaje[] = 'Usuario Final Registrado Correctamente';
                $alert = 'success';
            }

            if ($repetido and $tipo_usuario != $this::USUARIO_CLIENTE) {
                $errors['ndocc'] = 'has-error';
                $errors['ndocm'] = "El DNI ingresado ya existe.";
            }
        }

        return new ViewModel(
            array(
                'final' => 'active',
                'fadd' => 'active',
                'param1' => $mensaje,
                'empresas' => $listaEmpresas,
                'empresasegmentos' => $this->getEmpresaSegmentoTable()->fetchAll(),
                'empresasubgrupos' => $this->getEmpresaSubgrupoTable()->fetchAll(),
                'empresatipos' => $this->getEmpresaTipoTable()->fetchAll(),
                'segmentos' => $this->getSegmentoTable()->fetchAll(),
                'form' => $form,
                'alert' => $alert,
                'errors' => $errors
            )
        );
    }

    public function editAction()
    {
        $mensaje = null;
        $errors = array();
        $datos = array();
        $segAnt = array();
        $alert = 'danger';

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cliente', array('action' => 'add'));
        }

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $empresa_value = $this->identity()->BNF_Empresa_id;
        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;

        try {
            $cliente = $this->getClienteTable()->getCliente($id);
            $clienteEmpresas = $this->getClienteTable()->getDetailClienteEmpresa($id);
            foreach ($clienteEmpresas as $valor) {
                $subGrupo = $this->getEmpresaSubgrupoClienteTable()
                    ->getSubgruposByClienteAndEmpresa($id, $valor->NombreComercial);

                if ($tipo_usuario == $this::USUARIO_CLIENTE) {
                    if ($subGrupo) {
                        $segAnt[] = $valor->NombreSegmento;
                        if ($empresa_value == $valor->NombreComercial) {
                            $datos[] = array(
                                $valor->NombreComercial,
                                $valor->NombreSegmento,
                                $subGrupo->NombreSubgrupo,
                                $valor->Estado
                            );
                        }
                    } else {
                        if ($empresa_value == $valor->NombreComercial) {
                            $segAnt[] = $valor->NombreSegmento;
                            $datos[] = array($valor->NombreComercial, $valor->NombreSegmento, '', $valor->Estado);
                        }
                    }
                } else {
                    if ($subGrupo) {
                        $segAnt[] = $valor->NombreSegmento;
                        $datos[] = array(
                            $valor->NombreComercial,
                            $valor->NombreSegmento,
                            $subGrupo->NombreSubgrupo,
                            $valor->Estado
                        );
                    } else {
                        $segAnt[] = $valor->NombreSegmento;
                        $datos[] = array($valor->NombreComercial, $valor->NombreSegmento, '', $valor->Estado);
                    }
                }
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cliente', array('action' => 'index'));
        }

        $listaEmpresas = array();
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            for ($i = 0; $i < count($datos); $i++) {
                array_push($listaEmpresas, $this->getEmpresaTable()->getEmpresa($datos[$i][0]));
            }
        } else {
            $listaEmpresas = $this->getEmpresaTable()->getEmpresasCliente();
        }

        if (isset($cliente->FechaNacimiento)) {
            $cliente->FechaNacimiento = date_format(date_create($cliente->FechaNacimiento), 'Y-m-d');
        }

        $cliente->Eliminado = (int)$cliente->Eliminado;

        $tipoDocumentos = $this->getTipoDocumentoTable()->fetchAll();
        $tipos = array();
        foreach ($tipoDocumentos as $tipoDocumento) {
            $tipos[$tipoDocumento->id] = $tipoDocumento->Nombre;
        }

        $form = new FinalUserForm($tipos);
        $form->bind($cliente);
        $form->get('submit')->setAttribute('value', 'Editar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->getPost()->BNF_TipoDocumento_id == $this::TIPO_DNI) {
                $form->setInputFilter($cliente->getInputFilter(8, 1, $tipos));
            } elseif ($request->getPost()->BNF_TipoDocumento_id == $this::TIPO_PASAPORTE) {
                $form->setInputFilter($cliente->getInputFilter(15, 2, $tipos));
            } elseif ($request->getPost()->BNF_TipoDocumento_id == $this::TIPO_OTROS) {
                $form->setInputFilter($cliente->getInputFilter(5, 3, $tipos));
            } else {
                $errors['ntipodoc'] = 'Seleccione un Tipo de Documento.';
                $errors['ntipodocerror'] = 'has-error';
            }

            $form->setData($request->getPost());
            $BNF_Empresa_id = $request->getPost()->idEmpresa;
            $BNF_Segmento_id = $request->getPost()->idSegmento;
            $BNF_Subgrupo_id = $request->getPost()->idSubgrupo;
            $Activo = $request->getPost()->Estado;

            $repetido = $this->getClienteTable()->getDocumentoId($request->getPost()->NumeroDocumento, $id);
            if ($repetido) {
                $errors['ndocc'] = 'has-error';
                $errors['ndocm'] = "El DNI ingresado ya existe.";
            }

            if (count($BNF_Empresa_id) == count($BNF_Segmento_id) and count($BNF_Empresa_id) != 0) {
                $esp = 0;
                $empresaRegister = array();
                foreach ($BNF_Empresa_id as $index => $dato) {
                    $tipo = $this->getEmpresaTable()->getEmpresa($dato);
                    if ($tipo->ClaseEmpresaCliente == 'Especial') {
                        $esp += 1;
                    }
                    if (in_array($dato, $empresaRegister) && !isset($multipleRegister)) {
                        $errors['mclie'] = 'Un usuario final no puede registrarse dos veces en la misma empresa.';
                        $multipleRegister = true;
                    } else {
                        $empresaRegister[] = $dato;
                    }
                }
                if ($esp != count($BNF_Subgrupo_id)) {
                    $errors['mempre'] = 'Debe de Seleccionar un Subgrupo para las Empresas de Tipo Especial';
                }
            } else {
                $errors['mempre'] = 'Por favor, llene correctamente los campos asociados a la Empresa.';
            }

            if ($form->isValid() and !$repetido && $errors == null) {
                $total = count($BNF_Empresa_id);
                if (!isset($BNF_Empresa_id) or !isset($BNF_Segmento_id) or $total != count($BNF_Segmento_id)) {
                    $errors['mempre'] = 'Por favor, llene correctamente los campos asociados a la Empresa.';
                } else {
                    $BNF_Cliente_id = $this->getClienteTable()->saveCliente($cliente);
                    $relacion_EmpCliente = $this->getEmpresaCliente()->searchByClientId($BNF_Cliente_id);
                    $relacion_EmpSegCliente = $this->getEmpresaSegmentoClienteTable()
                        ->searchByClientId($BNF_Cliente_id);

                    //Desactivar todas las relaciones anteriores
                    $this->getEmpresaCliente()->updateArray(
                        array('Eliminado' => 1, "Estado" => 'Inactivo'),
                        array('BNF_Cliente_id' => $BNF_Cliente_id)
                    );

                    $this->getEmpresaSegmentoClienteTable()->updateArray(
                        array('Eliminado' => 1),
                        array('BNF_Cliente_id' => $BNF_Cliente_id)
                    );

                    $this->getEmpresaSubgrupoClienteTable()->updateArray(
                        array('Eliminado' => 1),
                        array('BNF_Cliente_id' => $BNF_Cliente_id)
                    );

                    //Relacion entre Empresa-Cliente
                    $total_relacion_EmpCliente = count($relacion_EmpCliente);
                    $total_EmpCliente = count($BNF_Empresa_id);
                    if ($total_EmpCliente == $total_relacion_EmpCliente) {
                        $it = 0;
                        foreach ($relacion_EmpCliente as $value) {
                            $empresa_Cliente = new EmpresaClienteCliente();
                            $empresa_Cliente->id = $value->id;
                            $empresa_Cliente->BNF_Empresa_id = $BNF_Empresa_id[$it];
                            $empresa_Cliente->BNF_Cliente_id = $BNF_Cliente_id;
                            $empresa_Cliente->Estado = isset($Activo[$BNF_Empresa_id[$it]]) ? 'Activo' : 'Inactivo';
                            $empresa_Cliente->Eliminado = isset($Activo[$BNF_Empresa_id[$it]]) ? 0 : 1;
                            $this->getEmpresaCliente()->saveEmpresaCliente($empresa_Cliente);
                            $it++;
                        }
                    } else {
                        for ($i = 0; $i < $total_EmpCliente; $i++) {
                            $count = 0;
                            foreach ($relacion_EmpCliente as $value) {
                                if ($value->BNF_Empresa_id != $BNF_Empresa_id[$i]) {
                                    $count++;
                                } else {
                                    $empresa_Cliente = new EmpresaClienteCliente();
                                    $empresa_Cliente->id = $value->id;
                                    $empresa_Cliente->BNF_Empresa_id = $value->BNF_Empresa_id;
                                    $empresa_Cliente->BNF_Cliente_id = $BNF_Cliente_id;
                                    $empresa_Cliente->Estado = isset($Activo[$value->BNF_Empresa_id])
                                        ? 'Activo' : 'Inactivo';
                                    $empresa_Cliente->Eliminado = isset($Activo[$value->BNF_Empresa_id]) ? 0 : 1;
                                    $this->getEmpresaCliente()->saveEmpresaCliente($empresa_Cliente);
                                }
                            }

                            if ($count == $total_relacion_EmpCliente) {
                                $empresa_Cliente = new EmpresaClienteCliente();
                                $empresa_Cliente->BNF_Empresa_id = $BNF_Empresa_id[$i];
                                $empresa_Cliente->BNF_Cliente_id = $BNF_Cliente_id;
                                $empresa_Cliente->Estado = isset($Activo[$BNF_Empresa_id[$i]]) ? 'Activo' : 'Inactivo';
                                $empresa_Cliente->Eliminado = isset($Activo[$BNF_Empresa_id[$i]]) ? 0 : 1;
                                $this->getEmpresaCliente()->saveEmpresaCliente($empresa_Cliente);
                            }
                        }
                    }

                    //Relacion entre Empresa-Segmento-Cliente
                    $total_relacion_EmpSegCliente = count($relacion_EmpSegCliente);
                    $total_EmpSegCliente = count($BNF_Segmento_id);
                    if ($total_EmpSegCliente == $total_relacion_EmpSegCliente) {
                        $it = 0;
                        foreach ($relacion_EmpSegCliente as $value) {
                            $segmento = $this->getEmpresaSegmentoTable()
                                ->getEmpresaSegmento($value->BNF_EmpresaSegmento_id);
                            if ($segmento->BNF_Segmento_id == $BNF_Segmento_id[$it] and
                                $BNF_Empresa_id[$it] == $segmento->BNF_Empresa_id
                            ) {
                                $this->getEmpresaSegmentoClienteTable()->updateArray(
                                    array("Eliminado" => 0),
                                    array("idBNF_EmpresaSegmentoCliente" => $value->idBNF_EmpresaSegmentoCliente)
                                );
                            } else {
                                if ($this->getEmpresaSegmentoTable()
                                    ->getEmpresaSegmentoIfExist($BNF_Empresa_id[$it], $BNF_Segmento_id[$it])
                                ) {
                                    $segmento = $this->getEmpresaSegmentoTable()
                                        ->getEmpresaSegmentoDatos($BNF_Empresa_id[$it], $BNF_Segmento_id[$it]);

                                    $id_relacionESC = $this->getEmpresaSegmentoClienteTable()->
                                    getEmpresaSegmentoClienteData($segmento->id, $BNF_Cliente_id);
                                    if ($id_relacionESC) {
                                        $this->getEmpresaSegmentoClienteTable()->updateArray(
                                            array("Eliminado" => 0),
                                            array(
                                                "idBNF_EmpresaSegmentoCliente" => $id_relacionESC
                                                    ->idBNF_EmpresaSegmentoCliente
                                            )
                                        );
                                    } else {
                                        $empresaSegClient = new EmpresaSegmentoCliente();
                                        $empresaSegClient->BNF_Cliente_id = $BNF_Cliente_id;
                                        $empresaSegClient->BNF_EmpresaSegmento_id = $segmento->id;
                                        $empresaSegClient->Eliminado = 0;

                                        $this->getEmpresaSegmentoClienteTable()
                                            ->saveEmpresaSegmentoCliente($empresaSegClient);
                                    }
                                } else {
                                    $empresaSegmento = new EmpresaSegmento();
                                    $empresaSegmento->BNF_Empresa_id = $BNF_Empresa_id[$it];
                                    $empresaSegmento->BNF_Segmento_id = $BNF_Segmento_id[$it];
                                    $empresaSegmento->Eliminado = 0;

                                    $id_empresaSegmento = $this->getEmpresaSegmentoTable()
                                        ->saveEmpresaSegmento($empresaSegmento);

                                    $empresaSegClient = new EmpresaSegmentoCliente();
                                    $empresaSegClient->BNF_Cliente_id = $BNF_Cliente_id;
                                    $empresaSegClient->BNF_EmpresaSegmento_id = $id_empresaSegmento;
                                    $empresaSegClient->Eliminado = 0;

                                    $this->getEmpresaSegmentoClienteTable()
                                        ->saveEmpresaSegmentoCliente($empresaSegClient);
                                }
                            }
                            $it++;
                        }
                    } else {
                        for ($i = 0; $i < $total_EmpSegCliente; $i++) {
                            if ($this->getEmpresaSegmentoTable()
                                ->getEmpresaSegmentoIfExist($BNF_Empresa_id[$i], $BNF_Segmento_id[$i])
                            ) {
                                $segmento = $this->getEmpresaSegmentoTable()
                                    ->getEmpresaSegmentoDatos($BNF_Empresa_id[$i], $BNF_Segmento_id[$i]);

                                $id_relacionESC = $this->getEmpresaSegmentoClienteTable()->
                                getEmpresaSegmentoClienteData($segmento->id, $BNF_Cliente_id);
                                if ($id_relacionESC) {
                                    $this->getEmpresaSegmentoClienteTable()->updateArray(
                                        array("Eliminado" => 0,),
                                        array(
                                            "idBNF_EmpresaSegmentoCliente" => $id_relacionESC
                                                ->idBNF_EmpresaSegmentoCliente
                                        )
                                    );
                                } else {
                                    $empresaSegClient = new EmpresaSegmentoCliente();
                                    $empresaSegClient->BNF_Cliente_id = $BNF_Cliente_id;
                                    $empresaSegClient->BNF_EmpresaSegmento_id = $segmento->id;
                                    $empresaSegClient->Eliminado = 0;

                                    $this->getEmpresaSegmentoClienteTable()
                                        ->saveEmpresaSegmentoCliente($empresaSegClient);
                                }
                            } else {
                                $empresaSegmento = new EmpresaSegmento();
                                $empresaSegmento->BNF_Empresa_id = $BNF_Empresa_id[$i];
                                $empresaSegmento->BNF_Segmento_id = $BNF_Segmento_id[$i];
                                $empresaSegmento->Eliminado = 0;

                                $id_empresaSegmento = $this->getEmpresaSegmentoTable()
                                    ->saveEmpresaSegmento($empresaSegmento);

                                $empresaSegClient = new EmpresaSegmentoCliente();
                                $empresaSegClient->BNF_Cliente_id = $BNF_Cliente_id;
                                $empresaSegClient->BNF_EmpresaSegmento_id = $id_empresaSegmento;
                                $empresaSegClient->Eliminado = 0;

                                $this->getEmpresaSegmentoClienteTable()
                                    ->saveEmpresaSegmentoCliente($empresaSegClient);
                            }
                        }
                    }

                    //Guardando Sugrupos
                    foreach ($BNF_Empresa_id as $i => $v) {
                        $empresa = $this->getEmpresaTable()->getEmpresa($BNF_Empresa_id[$i]);
                        $clase = $empresa->ClaseEmpresaCliente;
                        if ($clase == "Especial") {
                            if (!$this->getEmpresaSubgrupoClienteTable()
                                ->getEmpresaSubgrupoClienteIfExist($BNF_Empresa_id[$i], $BNF_Cliente_id)
                            ) {
                                $empresaSubgrupoCliente = new EmpresaSubgrupoCliente();
                                $empresaSubgrupoCliente->BNF_Subgrupo_id = $BNF_Subgrupo_id[$BNF_Empresa_id[$i]];
                                $empresaSubgrupoCliente->BNF_Cliente_id = $BNF_Cliente_id;
                                $empresaSubgrupoCliente->Eliminado = '0';
                                $this->getEmpresaSubgrupoClienteTable()
                                    ->saveEmpresaSubgrupoCliente($empresaSubgrupoCliente);
                            } else {
                                $empresaSubgrupoCliente = new EmpresaSubgrupoCliente();
                                $datos = $this->getEmpresaSubgrupoClienteTable()
                                    ->getEmpresaSubgrupoClienteDataExist($BNF_Empresa_id[$i], $BNF_Cliente_id);
                                foreach ($datos as $data) {
                                    $empresaSubgrupoCliente = $data;
                                }
                                $empresaSubgrupoCliente->BNF_Subgrupo_id = $BNF_Subgrupo_id[$BNF_Empresa_id[$i]];
                                $empresaSubgrupoCliente->Eliminado = '0';
                                $this->getEmpresaSubgrupoClienteTable()
                                    ->saveEmpresaSubgrupoCliente($empresaSubgrupoCliente);
                            }
                        }
                    }

                    $mensaje[] = 'Usuario Final Actualizado Correctamente';
                    $alert = 'success';

                    $datos = array();
                    $clienteEmpresas = $this->getClienteTable()->getDetailClienteEmpresa($BNF_Cliente_id);
                    foreach ($clienteEmpresas as $valor) {
                        $subGrupo = $this->getEmpresaSubgrupoClienteTable()
                            ->getSubgruposByClienteAndEmpresa($BNF_Cliente_id, $valor->NombreComercial);

                        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
                            if ($subGrupo) {
                                $segAnt[] = $valor->NombreSegmento;
                                if ($empresa_value == $valor->NombreComercial) {
                                    $datos[] = array(
                                        $valor->NombreComercial,
                                        $valor->NombreSegmento,
                                        $subGrupo->NombreSubgrupo,
                                        $valor->Estado
                                    );
                                }
                            } else {
                                if ($empresa_value == $valor->NombreComercial) {
                                    $segAnt[] = $valor->NombreSegmento;
                                    $datos[] = array(
                                        $valor->NombreComercial,
                                        $valor->NombreSegmento,
                                        '',
                                        $valor->Estado
                                    );
                                }
                            }
                        } else {
                            if ($subGrupo) {
                                $segAnt[] = $valor->NombreSegmento;
                                $datos[] = array(
                                    $valor->NombreComercial,
                                    $valor->NombreSegmento,
                                    $subGrupo->NombreSubgrupo,
                                    $valor->Estado
                                );
                            } else {
                                $segAnt[] = $valor->NombreSegmento;
                                $datos[] = array($valor->NombreComercial, $valor->NombreSegmento, '', $valor->Estado);
                            }
                        }
                    }
                }
            }
        }

        return new ViewModel(
            array(
                'final' => 'active',
                'flistar' => 'active',
                'param1' => $mensaje,
                'empresacl' => $datos,
                'empresas' => $listaEmpresas,
                'empresasegmentos' => $this->getEmpresaSegmentoTable()->fetchAll(),
                'empresasubgrupos' => $this->getEmpresaSubgrupoTable()->fetchAll(),
                'empresatipos' => $this->getEmpresaTipoTable()->fetchAll(),
                'segmentos' => $this->getSegmentoTable()->fetchAll(),
                'subgrupos' => $this->getSubGrupoTable()->fetchAll(),
                'tipoempresas' => $this->getTipoEmpresaTable()->fetchAll(),
                'id' => $id,
                'form' => $form,
                'alert' => $alert,
                'errors' => $errors
            )
        );
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $estado = 'Activo';
            $valor = 0;
            if ($post_data['val'] == 0) {
                $estado = 'Inactivo';
                $valor = 1;
            }
            $this->getEmpresaCliente()->updateArray(
                array(
                    'Estado' => $estado,
                    'Eliminado' => $valor
                ),
                array(
                    'BNF_Cliente_id' => $post_data['id'],
                    'BNF_Empresa_id' => $post_data['company']
                )
            );
            $response->setContent(Json::encode($post_data));
        }
        return $response;
    }

    public function activeAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $estado = 'Activo';

            if ($post_data['val'] == 0) {
                $estado = 'Inactivo';
            }

            $this->getEmpresaCliente()->updateArray(
                array(
                    'Estado' => $estado,
                ),
                array(
                    'BNF_Cliente_id' => $post_data['id'],
                    'BNF_Empresa_id' => $post_data['company']
                )
            );
            $response->setContent(Json::encode($post_data));
        }
        return $response;
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        $searchCompany = $this->params()->fromRoute('empresa') ? (int)$this->params()->fromRoute('empresa') : null;

        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $resultado = $clients = $this->getClienteTable()->getReport($empresa_value);
        } else {
            $resultado = $clients = $this->getClienteTable()->getReport($searchCompany);
        }

        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Usuarios Finales")
                ->setSubject("Usuarios Finales")
                ->setDescription("Documento listando los Usuarios Finales")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Usuarios Finales");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:L' . $registros);
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

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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

            $objPHPExcel->getActiveSheet()->getStyle('A1:L' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Nombre')
                ->setCellValue('C1', 'Apellido')
                ->setCellValue('D1', 'Genero')
                ->setCellValue('E1', 'Tipo de Documento')
                ->setCellValue('F1', 'Documento')
                ->setCellValue('G1', 'Empresa - Nombre Comercial')
                ->setCellValue('H1', 'Segmento')
                ->setCellValue('I1', 'Sub Grupo')
                ->setCellValue('J1', 'Fecha de Nacimiento')
                ->setCellValue('K1', 'Clase de Empresa')
                ->setCellValue('L1', 'Eliminado');

            $i = 2;
            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Nombre)
                    ->setCellValue('C' . $i, $registro->Apellido)
                    ->setCellValue('D' . $i, $registro->Genero)
                    ->setCellValue('E' . $i, $registro->TipoDocumento)
                    ->setCellValue('F' . $i, $registro->NumeroDocumento)
                    ->setCellValue('G' . $i, $registro->NombreComercial)
                    ->setCellValue('H' . $i, $registro->NombreSegmento)
                    ->setCellValue('I' . $i, $registro->NombreSubgrupo)
                    ->setCellValue(
                        'J' . $i,
                        isset($registro->FechaNacimiento) ? date('Y-m-d', strtotime($registro->FechaNacimiento)) : ''
                    )
                    ->setCellValue('K' . $i, $registro->ClaseEmpresaCliente)
                    ->setCellValue('L' . $i, ($registro->Estado == 'Activo') ? 'Activo' : 'Inactivo');
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Usuarios_finales.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function searchAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $form = new FinalUserSearchForm();
        $request = $this->getRequest();
        $escaper = new Escaper('utf-8');

        $request->getPost()->cliente = $escaper->escapeHtml(trim($request->getPost()->cliente));
        $request->getPost()->empresa = $escaper->escapeHtml(trim($request->getPost()->empresa));

        $form->setData($request->getPost());
        $cliente = $this->getClienteTable()
            ->getDetailClienteSeach($request->getPost()->cliente, $request->getPost()->empresa);

        return new ViewModel(
            array(
                'final' => 'active',
                'flistar' => 'active',
                'clientes' => $cliente,
                'form' => $form,
            )
        );
    }
}
