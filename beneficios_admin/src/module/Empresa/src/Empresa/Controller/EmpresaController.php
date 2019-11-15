<?php

namespace Empresa\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Empresa\Model\Empresa;
use Empresa\Model\EmpresaSegmento;
use Empresa\Model\EmpresaTipoEmpresa;
use Empresa\Model\Ubigeo;
use EmpresaCliente\Service\Resize;
use Usuario\Model\SubGrupo;
use Empresa\Form\EmpresaForm;
use Empresa\Form\BuscarEmpresaForm;
use Zend\Json\Json;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Intervention\Image\ImageManager;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class EmpresaController extends AbstractActionController
{
    #region ObjectTables
    public function getPaqueteProvTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaqueteEmpresaProveedorTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getTipoEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\TipoEmpresaTable');
    }

    public function getEmpresaTipoEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTipoEmpresaTable');
    }

    public function getSubGrupoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\SubGrupoTable');
    }

    public function getUsuarioTable()
    {
        return $this->serviceLocator->get('Usuario\Model\Table\UsuarioTable');
    }

    public function getTipoDocumentoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\TipoDocumentoTable');
    }

    public function getPaisTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaisTable');
    }

    public function getUbigeoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\UbigeoTable');
    }

    public function getSegmentoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\SegmentoTable');
    }

    public function getEmpresaSegmentoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaSegmentoTable');
    }

    #endregion

    public function getSlug($cadena, $ruc)
    {
        if ($cadena != '') {
            $a = array('?', '&', '¿', '#', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', ' ', '--');
            $b = array('-', '-', '-', '-', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'ni', '-', '-');
            return strtolower(str_ireplace($a, $b, strtolower($cadena)));
        } else {
            return $ruc;
        }
    }

    public function indexAction()
    {
        $razon = null;
        $ruc = null;
        $combo = array();
        $busqueda = array(
            'Tipo' => 'NombreTipoEmpresa',
            'NComercial' => 'NombreComercial',
            'Razon' => 'RazonSocial',
            'APaterno' => 'ApellidoPaterno',
            'AMaterno' => 'ApellidoMaterno',
            'Empresa' => 'Nombre',
            'Ruc' => 'Ruc',
            'Clase' => 'ClaseEmpresaCliente',
            'Activo' => 'BNF_EmpresaTipoEmpresa.Eliminado');

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        //Obtenemos todos el listado de Empresas
        $datos = $this->getEmpresaTable()->fetchAll();
        foreach ($datos as $dato) {
            $combo[$dato->id] = $dato->NombreComercial . ' - ' . $dato->RazonSocial;
        }

        $form = new BuscarEmpresaForm('buscar', $combo);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = new Empresa();
            $form->setInputFilter($empresa->getInputFilterSearch());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $razon = $request->getPost()->RazonSocial ? $request->getPost()->RazonSocial : 0;
                $ruc = $request->getPost()->Ruc ? $request->getPost()->Ruc : null;
            }
        } else {
            $razon = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : 0;
            $ruc = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $form->setData(array("RazonSocial" => $razon, "Ruc" => $ruc));
        }

        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'FechaCreacion';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];

        } else {
            $order_by_o = 'id';
            $order_by = 'FechaCreacion';
        }

        //Se obtiene los datos filtrados y la paginacion segun el orden
        $empresa = $this->getEmpresaTable()->getDetailEmpresa($razon, $ruc, $order_by, $order);
        $paginator = new Paginator(new paginatorIterator($empresa, $order_by));
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        return new ViewModel(
            array(
                'empresa' => 'active',
                'elistar' => 'active',
                'empresas' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'form' => $form,
                'p' => $page,
                'q1' => $razon,
                'q2' => $ruc,
            )
        );
    }

    public function addAction()
    {
        $type = "danger";
        $confir = null;
        $result = null;
        $menssage = null;
        $errornd = null;
        $errorco = null;
        $logo = null;
        $clase = null;
        $cliente = false;

        $subgpemp = null;
        $total = null;

        $tipo = array();
        $tipoemp = array();

        $listapais = array();
        $listadepa = array();
        $listaprov = array();
        $listasesores = array();

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $asesores = $this->getUsuarioTable()->getUsuarioAsesor();
        $tipoEmpresas = $this->getTipoEmpresaTable()->fetchAll();
        $tipoDocumentos = $this->getTipoDocumentoTable()->fetchAll();

        $paises = $this->getPaisTable()->fetchAll();
        $provincias = $this->getUbigeoTable()->fetchAllProvince();
        $departamentos = $this->getUbigeoTable()->fetchAllDepartament();
        $dataprovincias = $this->getUbigeoTable()->fetchAllProvince();
        $datadepartamentos = $this->getUbigeoTable()->fetchAllDepartament();

        foreach ($tipoEmpresas as $tipoEmpresa) {
            $tipoemp[$tipoEmpresa->id] = $tipoEmpresa->Nombre;
        }

        foreach ($tipoDocumentos as $tipoDocumento) {
            $tipo[$tipoDocumento->id] = $tipoDocumento->Nombre;
        }

        foreach ($asesores as $asesore) {
            $listasesores[$asesore->id] = $asesore->Nombres . " " . $asesore->Apellidos;
        }

        foreach ($paises as $pais) {
            $listapais[$pais->id] = $pais->NombrePais;
        }

        foreach ($provincias as $provincia) {
            $listadepa[$provincia->id] = $provincia->Nombre;
        }

        foreach ($departamentos as $departamento) {
            $listaprov[$departamento->id] = $departamento->Nombre;
        }

        $form = new EmpresaForm('', $tipoemp, $listasesores);
        $form->get('BNF_TipoDocumento_id')->setValueOptions($tipo);
        $form->get('PaisLegal')->setValueOptions($listapais);
        $form->get('PaisEnvio')->setValueOptions($listapais);
        $form->get('PaisEmpresa')->setValueOptions($listapais);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $logo = $request->getPost()->Logo;

            $empresa = new Empresa();
            $limit = 8;
            $tipodoc = $request->getPost()->BNF_TipoDocumento_id;

            if ($tipodoc == 1) {
                $limit = 8;
            } elseif ($tipodoc == 2) {
                $limit = 15;
            }

            $form->setInputFilter($empresa->getInputFilter($tipoemp, $listasesores, $limit, $tipodoc, 1));

            $nonFile = $request->getPost()->toArray();
            $File = $this->params()->fromFiles('Logo');
            $data = array_merge($nonFile, array('Logo' => $File['name']));

            if (count($request->getPost()->Logo) != 1) {
                $menssage['logo'] = "No ha Ingresado un Logo.";
                $menssage['logoc'] = 'has-error';
            }

            $subdominio = 0;
            $subdominio_data = $request->getPost()->SubDominio;
            if (!empty($subdominio_data)) {
                $subdominio = $this->getEmpresaTable()->getSubDominio($subdominio_data);
            }

            if ($subdominio == 1) {
                $menssage['subd'] = "El SubDominio ya se encuentra en uso.";
                $menssage['subdc'] = 'has-error';
            }

            $form->setData($data);
            $clase = $request->getPost()->ClaseEmpresaCliente;
            $repetido = $this->getEmpresaTable()->getValidateRuc($request->getPost()->Ruc);

            //Validar Subgrupos
            $subgrupos = array();
            $idsSubgrupos = $request->getPost()->idSubgrupo;
            $nombresSubgrupos = $request->getPost()->nameSubgrupo;

            if (!empty($request->getPost()->TipoEmpresa)) {
                foreach ($request->getPost()->TipoEmpresa as $valTipo) {
                    $tEmp = $this->getTipoEmpresaTable()->getIfExist($valTipo);
                    if (!$tEmp) {
                        $menssage['rol'] = "El tipo de Empresa ingresado no existe.";
                        break;
                    } else {
                        //Validacion de Empresa Cliente
                        $result[] = $tEmp;

                        if ($tEmp->Nombre == "Cliente") {
                            $cliente = true;
                            if ($clase == "Normal" or $clase == "Especial") {
                                if ($clase == "Especial") {
                                    if (count(array_filter($nombresSubgrupos)) > 0) {
                                        $subgrupos = $nombresSubgrupos;
                                        $subgrupos = array_map('strtolower', $subgrupos);
                                        $subgrupos = array_filter(array_unique($subgrupos));
                                        $subgrupos = array_map('ucfirst', $subgrupos);
                                    } else {
                                        $menssage['rol'] = "Los Subgrupos no pueden quedar vacíos.";
                                        break;
                                    }
                                }
                            } else {
                                $menssage['rol'] = "La Clase de Empresa no existe.";
                                break;
                            }

                            if (!empty($idsSubgrupos)) {
                                $subgpemp = array();
                                $total = 0;
                                foreach ($idsSubgrupos as $datos) {
                                    $valor = explode('-', $datos);
                                    if (count($valor) != 2) {
                                        $menssage['rol'] = "Ingrese almenos un SubGrupo.";
                                    } else {
                                        array_push($subgpemp, array(
                                            "id" => end($valor),
                                            "Nombre" => reset($valor),
                                        ));
                                        $total++;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($form->isValid() and !$repetido and $menssage == null) {
                //Guardar Datos de Empresa
                $empresa->exchangeArray($form->getData());
                $ruc = $empresa->Ruc;

                foreach ($request->getPost()->TipoEmpresa as $valTipo) {
                    if ($valTipo == 1) {
                        $empresa->Proveedor = 1;
                    } else {
                        $empresa->Cliente = 1;
                    }
                }
                ///////////////////////agregar de imagen
                $filename = $request->getPost()->Logo;
                $partes = explode(".", $request->getPost()->Logo);
                $ext = end($partes);

                $this->agregarImagen($filename, $ruc, $ext);
                $this->eliminarImagen($filename, $ext);
                /////////////
                //logo empresa sitio
                if ($request->getPost()->Logo_sitio != null) {
                    if ($request->getPost()->Logo_sitio != "null") {
                        $filename = $request->getPost()->Logo_sitio;
                        $partes = explode(".", $request->getPost()->Logo_sitio);
                        $ext = end($partes);

                        $this->agregarImagen($filename, $ruc, $ext, true);
                        $this->eliminarImagen($filename, $ext, true);
                        $empresa->Logo_sitio = $ruc . '-site.' . $ext;
                    }
                }

                $empresa->Logo = $ruc . '.' . $ext;

                $empresa->Slug = $this->getSlug(
                    $request->getPost()->NombreComercial,
                    $request->getPost()->Ruc
                );

                $cantidad = $this->getEmpresaTable()->getIfExistSlug($empresa->Slug);
                if ($cantidad > 0) {
                    $empresa->Slug = $empresa->Slug . "-" . ++$cantidad;
                }

                $this->getEmpresaTable()->saveEmpresa($empresa, $cliente);

                //recuperamos empresa ingresada
                $empresa = $this->getEmpresaTable()->getEmpresabyRuc($ruc);

                //Guardar Datos de Tipo Empresa
                foreach ($result as $value) {
                    $empresaTipoEmpresa = new EmpresaTipoEmpresa();
                    $empresaTipoEmpresa->BNF_TipoEmpresa_id = $value->id;
                    $empresaTipoEmpresa->BNF_Empresa_id = $empresa->id;
                    $this->getEmpresaTipoEmpresaTable()->saveEmpresaTipoEmpresa($empresaTipoEmpresa);
                }

                //Guardar Datos de Subgrupos
                if (!empty($subgrupos)) {
                    foreach ($subgrupos as $valuesub) {
                        $subgrupo = new SubGrupo();
                        $subgrupo->Nombre = $valuesub;
                        $subgrupo->BNF_Empresa_id = $empresa->id;
                        $this->getSubGrupoTable()->saveSubGrupo($subgrupo);
                    }
                }

                //Guardar Relacion con Segmentos
                $segmento = $this->getSegmentoTable()->getByNombre('Z');

                $empSegmento = new EmpresaSegmento();
                $empSegmento->BNF_Segmento_id = $segmento->id;
                $empSegmento->BNF_Empresa_id = $empresa->id;
                $empSegmento->Eliminado = 0;
                $this->getEmpresaSegmentoTable()->saveEmpresaSegmento($empSegmento);

                $confir[] = "Empresa Registrada.";
                $type = "success";
                $logo = null;
                $subgpemp = null;
                $total = null;
                $clase = null;

                $form = new EmpresaForm('', $tipoemp, $listasesores);
                $form->get('BNF_TipoDocumento_id')->setValueOptions($tipo);
                $form->get('PaisLegal')->setValueOptions($listapais);
                $form->get('PaisEnvio')->setValueOptions($listapais);
                $form->get('PaisEmpresa')->setValueOptions($listapais);
            } else {
                $confir[] = 'No se Registro, revisar los datos ingresados';
                $type = "danger";
            }

            if ($repetido) {
                $menssage['Ruc'] = "El Ruc ingresado ya existe.";
            };
        }

        if (!empty($form->getMessages())) {
            foreach ($form->getMessages() as $key => $value) {
                if ($key == "TipoEmpresa") {
                    $menssage['rol'] = "El valor del Rol no puede quedar vacío.";
                }
            }
        }

        return new ViewModel(
            array(
                'empresa' => 'active',
                'eadd' => 'active',
                'form' => $form,
                'param1' => $menssage,
                'confir' => $confir,
                'type' => $type,
                'subgrupos' => $subgpemp,
                'total' => $total,
                'clase' => $clase,
                'provincias' => $dataprovincias,
                'departamentos' => $datadepartamentos,
                'errornd' => $errornd,
                'errorco' => $errorco,
                'logo' => $logo,
            )
        );
    }

    public function editAction()
    {
        $confir = array();
        $type = 'danger';
        $result = null;
        $menssage = null;
        $errornd = null;
        $errorco = null;
        $logo = null;
        $logo_sitio = null;
        $cliente = false;

        $subgpemp = null;
        $tipo_empresa = array();
        $total = null;
        $tipo = array();
        $tipoemp = array();

        $listapais = array();
        $listadepa = array();
        $listaprov = array();
        $listasesores = array();

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $id = (int)$this->params()->fromRoute('id', 0);

        try {
            $empresa = $this->getEmpresaTable()->getEmpresa($id);
            $logo = $empresa->Logo;
            $logo_sitio = $empresa->Logo_sitio;
            $tipos = $this->getTipoEmpresaTable()->getAllTipoEmpresa($empresa->id);
            $clase = $empresa->ClaseEmpresaCliente;
            if ($clase == "Especial") {
                $subgpemp = $this->getSubGrupoTable()->getSubgrupoEmpresa($empresa->id)->toArray();
                $total = count($subgpemp);
            }
            $img = $empresa->Logo;
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('empresa', array('action' => 'index'));
        }

        try {
            $envio = $this->getUbigeoTable()->getUbigeo($empresa->BNF_Ubigeo_id_envio);
        } catch (\Exception $ex) {
            $envio = null;
        }

        try {
            $legal = $this->getUbigeoTable()->getUbigeo($empresa->BNF_Ubigeo_id_legal);
        } catch (\Exception $ex) {
            $legal = null;
        }

        try {
            $empresas = $this->getUbigeoTable()->getUbigeo($empresa->BNF_Ubigeo_id_empresa);
        } catch (\Exception $ex) {
            $empresas = null;
        }

        $nombreComercialAnt = $empresa->NombreComercial;
        $slugAnt = $empresa->Slug;

        $asesores = $this->getUsuarioTable()->getUsuarioAsesor();
        $tipoEmpresas = $this->getTipoEmpresaTable()->fetchAll();
        $tipoDocumentos = $this->getTipoDocumentoTable()->fetchAll();

        $paises = $this->getPaisTable()->fetchAll();
        $provincias = $this->getUbigeoTable()->fetchAllProvince();
        $departamentos = $this->getUbigeoTable()->fetchAllDepartament();
        $dataprovincias = $this->getUbigeoTable()->fetchAllProvince();
        $datadepartamentos = $this->getUbigeoTable()->fetchAllDepartament();

        foreach ($tipoEmpresas as $tipoEmpresa) {
            $tipoemp[$tipoEmpresa->id] = $tipoEmpresa->Nombre;
        }

        foreach ($tipoDocumentos as $tipoDocumento) {
            $tipo[$tipoDocumento->id] = $tipoDocumento->Nombre;
        }

        foreach ($asesores as $asesore) {
            $listasesores[$asesore->id] = $asesore->Nombres . " " . $asesore->Apellidos;
        }

        foreach ($paises as $pais) {
            $listapais[$pais->id] = $pais->NombrePais;
        }

        foreach ($provincias as $provincia) {
            $listadepa[$provincia->id] = $provincia->Nombre;
        }

        foreach ($departamentos as $departamento) {
            $listaprov[$departamento->id] = $departamento->Nombre;
        }

        foreach ($tipos as $value) {
            $tipo_empresa[] = $value->id;
        }

        $form = new EmpresaForm('', $tipoemp, $listasesores);
        $form->bind($empresa);
        $form->get('submit')->setAttribute('value', 'Editar');
        $form->get('BNF_TipoDocumento_id')->setValueOptions($tipo);
        $form->get('PaisLegal')->setValueOptions($listapais);
        $form->get('PaisEnvio')->setValueOptions($listapais);
        $form->get('PaisEmpresa')->setValueOptions($listapais);
        $form->get('TipoEmpresa')->setValue($tipo_empresa);

        $request = $this->getRequest();

        if ($request->isPost()) {
            if (($request->getPost()->Logo) != 'null') {
                $logo = $request->getPost()->Logo;
            }
            if (($request->getPost()->Logo_sitio) != 'null') {
                $logo_sitio = $request->getPost()->Logo_sitio;
            }


            $limit = 8;
            $tipodoc = $request->getPost()->BNF_TipoDocumento_id;
            if ($tipodoc == 1) {
                $limit = 8;
            } elseif ($tipodoc == 2) {
                $limit = 15;
            }

            $form->setInputFilter($empresa->getInputFilter($tipoemp, $listasesores, $limit, $tipodoc, 2));
            $nonFile = $request->getPost()->toArray();
            $data = array_merge($nonFile, array('Logo' => null, 'Logo_sitio' => null));

            if (count($request->getPost()->Logo) != 1) {
                $menssage['logo'] = "No ha Ingresado un Logo.";
                $menssage['logoc'] = 'has-error';
            }

            $subdominio = 0;
            $subdominio_data = $request->getPost()->SubDominio;
            if (!empty($subdominio_data)) {
                $subdominio = $this->getEmpresaTable()->getSubDominio($subdominio_data, $id);
            }

            if ($subdominio == 1) {
                $menssage['subd'] = "El SubDominio ya se encuentra en uso.";
                $menssage['subdc'] = 'has-error';
            }

            $form->setData($data);
            $clase = $request->getPost()->ClaseEmpresaCliente;
            $repetido = $this->getEmpresaTable()->getValidateRuc($request->getPost()->Ruc, $id);
            if ($form->isValid() and !$repetido and $repetido == null) {
                $idsSubgrupos = $request->getPost()->idSubgrupo;
                $nombresSubgrupos = $request->getPost()->nameSubgrupo;

                //Validar Tipo de Empresas
                foreach ($request->getPost()->TipoEmpresa as $valTipo) {
                    $tEmp = $this->getTipoEmpresaTable()->getIfExist($valTipo);
                    if (!$tEmp) {
                        $menssage['rol'] = "El tipo de Empresa ingresado no existe.";
                        $type = "danger";
                        break;
                    } else {
                        //Validacion de Empresa Cliente
                        $result[] = $tEmp;

                        if ($tEmp->Nombre == "Cliente") {
                            $cliente = true;
                            if ($clase == "Normal" or $clase == "Especial") {
                                if ($clase == "Especial") {
                                    if (count(array_filter($nombresSubgrupos)) > 0) {
                                        $subgrupos = $nombresSubgrupos;
                                        $subgrupos = array_map('strtolower', $subgrupos);
                                        $subgrupos = array_filter(array_unique($subgrupos));
                                        array_map('ucfirst', $subgrupos);
                                    } else {
                                        $menssage['rol'] = "Los Subgrupos no pueden quedar vacíos.";
                                        $type = "danger";
                                        break;
                                    }
                                }
                            } else {
                                $menssage['rol'] = "La Clase de Empresa no existe.";
                                $type = "danger";
                                break;
                            }
                        }

                        if (!empty($idsSubgrupos)) {
                            $subgpemp = array();
                            $total = 0;
                            foreach ($idsSubgrupos as $datos) {
                                $valor = explode('-', $datos);
                                if (count($valor) != 2) {
                                    $menssage['rol'] = "Ingrese almenos un SubGrupo.";
                                } else {
                                    array_push($subgpemp, array(
                                        "id" => end($valor),
                                        "Nombre" => reset($valor),
                                    ));
                                    $total++;
                                }
                            }
                        }
                    }
                }

                //Guardar Datos de Empresa
                if ($menssage == null) {
                    $empresa->exchangeArray($data);
                    foreach ($request->getPost()->TipoEmpresa as $valTipo) {
                        if ($valTipo == 1) {
                            $empresa->Proveedor = 1;
                        } else {
                            $empresa->Cliente = 1;
                        }
                    }

                    ///////////////////////edicion de imagen
                    ///logo empresa
                    if ($request->getPost()->Logo != "null") {
                        $ruc = $request->getPost()->Ruc;
                        $filename = $request->getPost()->Logo;
                        $partes = explode(".", $request->getPost()->Logo);
                        $ext = end($partes);

                        $this->agregarImagen($filename, $ruc, $ext);
                        $this->eliminarImagen($filename, $ext);
                        $empresa->Logo = $ruc . '.' . $ext;
                    }

                    //logo empresa sitio
                    if ($request->getPost()->Logo_sitio != null) {
                        if ($request->getPost()->Logo_sitio != "null") {
                            $ruc = $request->getPost()->Ruc;
                            $filename = $request->getPost()->Logo_sitio;
                            $partes = explode(".", $request->getPost()->Logo_sitio);
                            $ext = end($partes);

                            $this->agregarImagen($filename, $ruc, $ext, true);
                            $this->eliminarImagen($filename, $ext, true);
                            $empresa->Logo_sitio = $ruc . '-site.' . $ext;
                        }
                    }

                    //Comprobando el Nombre Comercial para generar el slug
                    if ($nombreComercialAnt == $request->getPost()->NombreComercial) {
                        $empresa->Slug = $slugAnt;
                    } else {
                        $empresa->Slug = $this->getSlug(
                            $request->getPost()->NombreComercial,
                            $request->getPost()->Ruc
                        );

                        $cantidad = $this->getEmpresaTable()->getIfExistSlug($empresa->Slug);
                        if ($cantidad > 0) {
                            $empresa->Slug = $empresa->Slug . "-" . ++$cantidad;
                        }
                    }

                    $this->getEmpresaTable()->saveEmpresa($empresa, $cliente);
                    $relTipoEmp = $this->getEmpresaTipoEmpresaTable()->getEmpresaTipoEmpresaRelations($empresa->id);
                    $saveTipo = $request->getPost()->TipoEmpresa;

                    //Guardar Datos de Tipo Empresa Editatos
                    if (count($saveTipo) > count($relTipoEmp)) {
                        foreach ($relTipoEmp as $value) {
                            for ($i = 0; $i < count($saveTipo); $i++) {
                                if ($value->BNF_TipoEmpresa_id != $saveTipo[$i]) {
                                    $empresaTipoEmpresa = new EmpresaTipoEmpresa();
                                    $empresaTipoEmpresa->BNF_TipoEmpresa_id = $saveTipo[$i];
                                    $empresaTipoEmpresa->BNF_Empresa_id = $empresa->id;
                                    $this->getEmpresaTipoEmpresaTable()
                                        ->saveEmpresaTipoEmpresa($empresaTipoEmpresa);
                                }
                            }
                        }
                    } elseif (count($saveTipo) < count($relTipoEmp)) {
                        foreach ($relTipoEmp as $value) {
                            if ($value->BNF_TipoEmpresa_id != $saveTipo[0]) {
                                $this->getEmpresaTipoEmpresaTable()
                                    ->deleteEmpresaTipoEmpresa($id, '1', $value->BNF_TipoEmpresa_id);
                                $this->getEmpresaTipoEmpresaTable()
                                    ->deleteEmpresaTipoEmpresa($id, '0', $saveTipo[0]);
                            }
                        }
                    } else {
                        $it = 0;
                        foreach ($relTipoEmp as $value) {
                            $empresaTipoEmpresa = new EmpresaTipoEmpresa();
                            $empresaTipoEmpresa->id = $value->id;
                            $empresaTipoEmpresa->BNF_TipoEmpresa_id = $saveTipo[$it];
                            $empresaTipoEmpresa->BNF_Empresa_id = $empresa->id;
                            $this->getEmpresaTipoEmpresaTable()->saveEmpresaTipoEmpresa($empresaTipoEmpresa);
                            $it++;
                        }
                    }

                    //Guardamos la data se Subgrupos
                    if ($request->getPost()->ClaseEmpresaCliente == "Especial") {
                        $this->getSubGrupoTable()->deleteAllSubgrupoEmpresa($empresa->id);

                        foreach ($idsSubgrupos as $datos) {
                            $valor = explode('-', $datos);
                            $subgrupo = new SubGrupo();

                            if ($valor[1] != ' ') {
                                $subgrupo->id = $valor[1];
                                $subgrupo->Nombre = $valor[0];
                                $subgrupo->BNF_Empresa_id = $empresa->id;
                                $this->getSubGrupoTable()->saveSubGrupo($subgrupo);
                            } else {
                                $idsub = $this->getSubGrupoTable()->getSubGrupoXName($valor[0], $empresa->id)->id;
                                if ($idsub != 0) {
                                    $this->getSubGrupoTable()->deleteSubGrupo($idsub, 0);
                                } else {
                                    $subgrupo->Nombre = $valor[0];
                                    $subgrupo->BNF_Empresa_id = $empresa->id;
                                    $this->getSubGrupoTable()->saveSubGrupo($subgrupo);
                                }
                            }
                        }
                    } else {
                        //Elimina Subgrupos
                        $this->getSubGrupoTable()->deleteAllSubgrupoEmpresa($empresa->id);
                    }

                    //Verificamos relacion con segmento
                    $segmento = $this->getSegmentoTable()->getByNombre('Z');
                    if (!$this->getEmpresaSegmentoTable()->getEmpresaSegmentoIfExist($empresa->id, $segmento->id)) {
                        $empSegmento = new EmpresaSegmento();
                        $empSegmento->BNF_Segmento_id = $segmento->id;
                        $empSegmento->BNF_Empresa_id = $empresa->id;
                        $empSegmento->Eliminado = 0;
                        $this->getEmpresaSegmentoTable()->saveEmpresaSegmento($empSegmento);
                    }

                    $this->flashMessenger()->addMessage('Empresa Modificada Correctamente');
                    return $this->redirect()->toRoute('empresa');
                } else {
                    $confir[] = 'No se Registro, revisar los datos ingresados';
                    $type = "danger";
                }
            } else {
                $confir[] = 'No se Registro, revisar los datos ingresados';
                $type = "danger";
            }

            if (!empty($form->getMessages())) {
                foreach ($form->getMessages() as $key => $value) {
                    if ($key == "TipoEmpresa") {
                        $menssage['rol'] = "El valor del Rol no puede quedar vacío.";
                    }
                }
                $type = "danger";
            }

            if ($repetido) {
                $menssage['Ruc'] = "El Ruc ingresado ya existe.";
                $type = "danger";
            };
        }

        return new ViewModel(
            array(
                'empresa' => 'active',
                'eadd' => 'active',
                'id' => $id,
                'form' => $form,
                'subgrupos' => $subgpemp,
                'total' => $total,
                'clase' => $clase,
                'envio' => $envio,
                'legal' => $legal,
                'empresas' => $empresas,
                'img' => $img,
                'param1' => $menssage,
                'confir' => $confir,
                'type' => $type,
                'provincias' => $dataprovincias,
                'departamentos' => $datadepartamentos,
                'errornd' => $errornd,
                'errorco' => $errorco,
                'logo' => $logo,
                'logo_sitio' => $logo_sitio
            )
        );
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $state = false;

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            $val = $post_data['val'];
            $tipo = $post_data['tipo'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== FALSE) AND
                    (filter_var($val, FILTER_VALIDATE_INT) !== FALSE) AND
                    (in_array($tipo, array('Proveedor', 'Cliente')) !== FALSE) AND
                    $csrf->verifyToken($post_data['csrf'])
                ) {
                    if ($tipo == 'Proveedor') {
                        $tipo = 1;
                    } else {
                        $tipo = 2;
                    }

                    if ($this->getEmpresaTipoEmpresaTable()->deleteEmpresaTipoEmpresa($id, $val, $tipo)) {
                        $val = ($val == 0) ? 1 : 0;
                        $this->getEmpresaTable()->deleteEmpresaTipo($id, $val, $tipo);
                        if ($tipo == 1) {
                            $this->getPaqueteProvTable()->deletePaqueteProvXEmpresa($id, $val);
                        }
                        $state = true;
                    }
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(\Zend\Json\Json::encode(
            array(
                'response' => $state,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultado = $this->getEmpresaTable()->getReport();
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Empresas")
                ->setSubject("Empresas")
                ->setDescription("Documento listando las Empresas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Empresas");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:AV' . $registros);
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AN')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AO')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AP')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AQ')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AR')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AS')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AT')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AU')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AV')->setAutoSize(true);

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:AW1' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Tipo Empresa')
                ->setCellValue('C1', 'Asesor')
                ->setCellValue('D1', 'Clase de Empresa')
                ->setCellValue('E1', 'Nombre Comercial')
                ->setCellValue('F1', 'Razon Social')
                ->setCellValue('G1', 'Apellido Paterno')
                ->setCellValue('H1', 'Apellido Materno')
                ->setCellValue('I1', 'Nombre')
                ->setCellValue('J1', 'Ruc')
                ->setCellValue('K1', 'Descripción')
                ->setCellValue('L1', 'Sitio Web')
                ->setCellValue('M1', 'Pais Empresa')
                ->setCellValue('N1', 'Departamento Empresa')
                ->setCellValue('O1', 'Provincia Empresa')
                ->setCellValue('P1', 'Distrito Empresa')
                ->setCellValue('Q1', 'Detalle Dirección Empresa')
                ->setCellValue('R1', 'Representante Legal')
                ->setCellValue('S1', 'Tipo Documento')
                ->setCellValue('T1', 'Representante Legal Nro Documento')
                ->setCellValue('U1', 'País Legal')
                ->setCellValue('V1', 'Departamento Legal')
                ->setCellValue('W1', 'Provincia Legal')
                ->setCellValue('X1', 'Distrito Legal')
                ->setCellValue('Y1', 'Detalle Dirección Legal')
                ->setCellValue('Z1', 'País Envio')
                ->setCellValue('AA1', 'Departamento Envio')
                ->setCellValue('AB1', 'Provincia Envio')
                ->setCellValue('AC1', 'Distrito Envio')
                ->setCellValue('AD1', 'Detalle Dirección Envio')
                ->setCellValue('AE1', 'Horario de Atención')
                ->setCellValue('AF1', 'Inicio de Horario de Atención')
                ->setCellValue('AG1', 'Fin de Horario de Atención')
                ->setCellValue('AH1', 'Personal de Atención')
                ->setCellValue('AI1', 'Cargo del Personal de Atención')
                ->setCellValue('AJ1', 'Telefono del Personal')
                ->setCellValue('AK1', 'Celular del Personal')
                ->setCellValue('AL1', 'Correo del Personal')
                ->setCellValue('AM1', 'IdSap')
                ->setCellValue('AN1', 'Fecha de Creación')
                ->setCellValue('AO1', 'Fecha Actualización')
                ->setCellValue('AP1', 'Activo')
                ->setCellValue('AQ1', 'Subgrupo')
                ->setCellValue('AR1', 'Nombre del Contacto')
                ->setCellValue('AS1', 'Telefono del Contacto')
                ->setCellValue('AT1', 'Correo del Contacto')
                ->setCellValue('AU1', 'Horario de Atención del Contacto')
                ->setCellValue('AV1', 'Inicio del Horario de Atención del Contacto')
                ->setCellValue('AW1', 'Fin del Horario de Atención del Contacto');
            $i = 2;
            foreach ($resultado as $registro) {
                $subgrupo = "";
                if ($registro->ClaseEmpresaCliente == "Especial") {
                    try {
                        $subgrupos = $this->getSubGrupoTable()
                            ->getSubgruposEmpresa($registro->id);
                        $c = 0;
                        foreach ($subgrupos as $valor) {
                            $c = $c + 1;
                            if ($c == 1) {
                                $subgrupo = $valor->Nombre;
                            } else {
                                $subgrupo = $subgrupo . " - " . $valor->Nombre;
                            }
                        }
                    } catch (\Exception $ex) {
                        $subgrupo = "";
                    }
                }

                $empresa = new Ubigeo();
                try {
                    $datos = $this->getUbigeoTable()->getLocalizacion($registro->BNF_Ubigeo_id_empresa);
                    foreach ($datos as $valor) {
                        $empresa = $valor;
                    }
                } catch (\Exception $ex) {
                    $empresa = array();
                }

                $envio = new Ubigeo();
                try {
                    $datos = $this->getUbigeoTable()->getLocalizacion($registro->BNF_Ubigeo_id_empresa);
                    foreach ($datos as $valor) {
                        $envio = $valor;
                    }
                } catch (\Exception $ex) {
                    $envio = array();
                }

                $legal = new Ubigeo();
                try {
                    $datos = $this->getUbigeoTable()->getLocalizacion($registro->BNF_Ubigeo_id_empresa);
                    foreach ($datos as $valor) {
                        $legal = $valor;
                    }
                } catch (\Exception $ex) {
                    $legal = array();
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->NombreTipoEmpresa)
                    ->setCellValue('C' . $i, $registro->CNombres . ' ' . $registro->CApellidos)
                    ->setCellValue(
                        'D' . $i,
                        ($registro->NombreTipoEmpresa == 'Proveedor') ? '' : $registro->ClaseEmpresaCliente
                    )
                    ->setCellValue('E' . $i, $registro->NombreComercial)
                    ->setCellValue('F' . $i, $registro->RazonSocial)
                    ->setCellValue('G' . $i, $registro->ApellidoPaterno)
                    ->setCellValue('H' . $i, $registro->ApellidoMaterno)
                    ->setCellValue('I' . $i, $registro->Nombre)
                    ->setCellValue('J' . $i, $registro->Ruc)
                    ->setCellValue('K' . $i, $registro->Descripcion)
                    ->setCellValue('L' . $i, $registro->SitioWeb)
                    ->setCellValue('M' . $i, $empresa->Pais)
                    ->setCellValue('N' . $i, $empresa->Departamento)
                    ->setCellValue('O' . $i, $empresa->Provincia)
                    ->setCellValue('P' . $i, $registro->DireccionEmpresa)
                    ->setCellValue('Q' . $i, $registro->DireccionEmpresaDetalle)
                    ->setCellValue('R' . $i, $registro->RepresentanteLegal)
                    ->setCellValue('S' . $i, $registro->TipoDocumento)
                    ->setCellValue('T' . $i, $registro->RepresentanteNumeroDocumento)
                    ->setCellValue('U' . $i, $legal->Pais)
                    ->setCellValue('V' . $i, $legal->Departamento)
                    ->setCellValue('W' . $i, $legal->Provincia)
                    ->setCellValue('X' . $i, $registro->DireccionLegal)
                    ->setCellValue('Y' . $i, $registro->DireccionLegalDetalle)
                    ->setCellValue('Z' . $i, $envio->Pais)
                    ->setCellValue('AA' . $i, $envio->Departamento)
                    ->setCellValue('AB' . $i, $envio->Provincia)
                    ->setCellValue('AC' . $i, $registro->DireccionEnvio)
                    ->setCellValue('AD' . $i, $registro->DireccionEnvioDetalle)
                    ->setCellValue('AE' . $i, $registro->HoraAtencion)
                    ->setCellValue('AF' . $i, $registro->HoraAtencionInicio)
                    ->setCellValue('AG' . $i, $registro->HoraAtencionFin)
                    ->setCellValue('AH' . $i, $registro->PersonaAtencion)
                    ->setCellValue('AI' . $i, $registro->CargoPersonaAtencion)
                    ->setCellValue('AJ' . $i, $registro->Telefono)
                    ->setCellValue('AK' . $i, $registro->Celular)
                    ->setCellValue('AL' . $i, $registro->CorreoPersonaAtencion)
                    ->setCellValue('AM' . $i, $registro->IdSap)
                    ->setCellValue('AN' . $i, $registro->FechaCreacion)
                    ->setCellValue('AO' . $i, $registro->FechaActualizacion)
                    ->setCellValue('AP' . $i, ((int)$registro->TEliminado == 1) ? 'Inactivo' : 'Activo')
                    ->setCellValue('AQ' . $i, ($registro->NombreTipoEmpresa == 'Proveedor') ? '' : $subgrupo)
                    ->setCellValue('AR' . $i, $registro->NombreContacto)
                    ->setCellValue('AS' . $i, $registro->TelefonoContacto)
                    ->setCellValue('AT' . $i, $registro->CorreoContacto)
                    ->setCellValue('AU' . $i, $registro->HoraAtencionContacto)
                    ->setCellValue('AV' . $i, $registro->HoraAtencionInicioContacto)
                    ->setCellValue('AW' . $i, $registro->HoraAtencionFinContacto);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Empresa.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function saveLogoAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $path = './public/elements/empresa/';
        $response = $this->getResponse();
        $ext = $this->getRequest()->getPost('ext');
        $name = $this->getRequest()->getPost('name');
        $site = $this->getRequest()->getPost('site');
        $partes = explode(".", $name);
        $name = $partes[0];
        $fileName = ($name == 'null') ? date('Y-m-d_h-m-s') . '-' . rand(1000, 9999) : $name;
        $manager = new ImageManager(array('driver' => 'imagick'));
        $img = $manager->make($_FILES['val']['tmp_name']);
        $img2 = $manager->make($_FILES['val']['tmp_name']);
        $img3 = $manager->make($_FILES['val']['tmp_name']);
        $img4 = $manager->make($_FILES['val']['tmp_name']);
        $img5 = $manager->make($_FILES['val']['tmp_name']);
        $img6 = $manager->make($_FILES['val']['tmp_name']);

        $config = $this->getServiceLocator()->get('Config');

        $resize = new Resize();

        if ($site == 'false') {
            $resize_bool['logo'] = $resize->isResize($img, $config, 'logo');
            $resize_bool['logo_fixed'] = $resize->isResize($img2, $config, 'logo_fixed');
            $resize_bool['logo_small'] = $resize->isResize($img3, $config, 'logo_small');
            $resize_bool['logo_medium'] = $resize->isResize($img4, $config, 'logo_medium');
            $resize_bool['logo_large'] = $resize->isResize($img5, $config, 'logo_large');
        } else {
            $resize_bool['logo_site'] = $resize->isResize($img6, $config, 'logo_site');
        }
        //$config['logo_large']['height']
        try {
            if ($site == 'false') {
                $resize->resizeWidthLogo($path, $img, $ext, $fileName, $config, 'logo', '', $resize_bool);
                $resize->resizeWidthLogo($path, $img2, $ext, $fileName, $config, 'logo_fixed', '-fixed', $resize_bool);
                $resize->resizeWidthLogo($path, $img3, $ext, $fileName, $config, 'logo_small', '-small', $resize_bool);
                $resize->resizeWidthLogo($path, $img4, $ext, $fileName, $config, 'logo_medium', '-medium', $resize_bool);
                $resize->resizeWidthLogo($path, $img5, $ext, $fileName, $config, 'logo_large', '-large', $resize_bool);
            } else {
                $resize->resizeHeightLogo($path, $img6, $ext, $fileName, $config, 'logo_site', '-site', $resize_bool);
                $fileName = $fileName . '-site';
            }
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'name' => $fileName . '.' . $ext,
                    )
                )
            );
        } catch (\Exception $e) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false
                    )
                )
            );
        }

        return $response;
    }

    public function deleteLogoAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $img = $this->getRequest()->getPost('val');
        $ext = $this->getRequest()->getPost('ext');
        $fullpath = './public/elements/empresa/' . $img;
        if (file_exists($fullpath)) {
            unlink($fullpath);
            $fullpath2 = str_replace('.' . $ext, '', $fullpath) . '-fixed' . '.' . $ext;
            unlink($fullpath2);
            $fullpath3 = str_replace('.' . $ext, '', $fullpath) . '-small' . '.' . $ext;
            unlink($fullpath3);
            $fullpath4 = str_replace('.' . $ext, '', $fullpath) . '-medium' . '.' . $ext;
            unlink($fullpath4);
            $fullpath5 = str_replace('.' . $ext, '', $fullpath) . '-large' . '.' . $ext;
            unlink($fullpath5);
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false
                    )
                )
            );
        }
        return $response;
    }

    public function agregarImagen($filename, $ruc, $ext, $site = false)
    {
        $path = './public/elements/empresa/';
        $manager = new ImageManager(array('driver' => 'imagick'));
        $partes = explode(".", $filename);
        $filename = $partes[0];

        if (!$site) {
            $img = $manager->make($path . $filename . '.' . $ext);
            $img2 = $manager->make($path . $filename . '-fixed.' . $ext);
            $img3 = $manager->make($path . $filename . '-small.' . $ext);
            $img4 = $manager->make($path . $filename . '-medium.' . $ext);
            $img5 = $manager->make($path . $filename . '-large.' . $ext);
        } else {
            $img6 = $manager->make($path . $filename . '.' . $ext);
        }

        $resize = new Resize();

        try {
            if (!$site) {
                $resize->rename($path, $img, $ext, $ruc, '');
                $resize->rename($path, $img2, $ext, $ruc, '-fixed');
                $resize->rename($path, $img3, $ext, $ruc, '-small');
                $resize->rename($path, $img4, $ext, $ruc, '-medium');
                $resize->rename($path, $img5, $ext, $ruc, '-large');
            } else {
                $resize->rename($path, $img6, $ext, $ruc, '-site');
            }

        } catch (\Exception $ex) {
            echo $ex;
        }
    }

    public function eliminarImagen($fullpath, $ext, $site = false)
    {
        $path = './public/elements/empresa/';
        $fullpath = $path . $fullpath;
        if (file_exists($fullpath) && !$site) {
            unlink($fullpath);
            $fullpath2 = str_replace('.' . $ext, '', $fullpath) . '-fixed' . '.' . $ext;
            unlink($fullpath2);
            $fullpath3 = str_replace('.' . $ext, '', $fullpath) . '-small' . '.' . $ext;
            unlink($fullpath3);
            $fullpath4 = str_replace('.' . $ext, '', $fullpath) . '-medium' . '.' . $ext;
            unlink($fullpath4);
            $fullpath5 = str_replace('.' . $ext, '', $fullpath) . '-large' . '.' . $ext;
            unlink($fullpath5);
        } elseif (file_exists($fullpath) && $site) {
            $fullpath2 = str_replace('.' . $ext, '', $fullpath) . '.' . $ext;
            unlink($fullpath2);
        }
    }
}
