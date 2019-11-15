<?php

namespace Puntos\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Cupon\Model\CuponPuntos;
use Oferta\Model\Busqueda;
use Puntos\Form\BuscarOfertasPuntos;
use Puntos\Form\FormOfertaPuntos;
use Puntos\Model\Filter\OfertasPFilter;
use Puntos\Model\OfertaPuntos;
use Puntos\Model\OfertaPuntosAtributos;
use Puntos\Model\OfertaPuntosCategoria;
use Puntos\Model\OfertaPuntosImagen;
use Puntos\Model\OfertaPuntosRubro;
use Puntos\Model\OfertaPuntosSegmento;
use Puntos\Model\OfertaPuntosUbigeo;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Validator\Date;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\Step;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use EmpresaCliente\Service\Resize;
use Intervention\Image\ImageManager;

class OfertaPuntosController extends AbstractActionController
{
    const MESSAGE_SAVE = "Oferta Puntos Registrada";
    const MESSAGE_UPDATE = "Oferta Puntos Actualizada";
    const MESSAGE_UPDATE_DES = "La oferta ya cuenta con descargas, por lo que no se puede editar el PVP y PB";
    const MESSAGE_COPY = "Oferta Puntos Guardada como nueva";
    const MESSAGE_ERROR = 'No se Registro, revisar los datos ingresados.';

    #region ObjectTables
    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getPaisTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaisTable');
    }

    public function getUbigeoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\UbigeoTable');
    }

    public function getCampaniaPuntosEmpresaTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\CampaniasPEmpresasTable');
    }

    public function getCampaniaPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\CampaniasPTable');
    }

    public function getSegmentoPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\SegmentosPTable');
    }

    public function getRubroTable()
    {
        return $this->serviceLocator->get('Rubro\Model\Table\RubroTable');
    }

    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Categoria\Model\Table\CategoriaTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Campania\Model\Table\CampaniaTable');
    }

    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosTable');
    }

    public function getOfertaPuntosRubroTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosRubroTable');
    }

    public function getOfertaPuntosAtributosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosAtributosTable');
    }

    public function getOfertaPuntosImagenTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosImagenTable');
    }

    public function getOfertaPuntosCampaniaTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosCampaniaTable');
    }

    public function getOfertaPuntosCategoriaTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosCategoriaTable');
    }

    public function getOfertaPuntosUbigeoTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosUbigeoTable');
    }

    public function getOfertaPuntosSegmentoTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosSegmentoTable');
    }

    public function getCategoriaUbigeoTable()
    {
        return $this->serviceLocator->get('Categoria\Model\Table\CategoriaUbigeoTable');
    }

    public function getCampaniaUbigeoTable()
    {
        return $this->serviceLocator->get('Campania\Model\Table\CampaniaUbigeoTable');
    }

    public function getCuponPuntosTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponPuntosTable');
    }

    public function getBusquedaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\BusquedaTable');
    }
    #endregion

    #region Inicializando Data
    public function inicializacion()
    {
        $dataEmpProv = array();
        $dataEmpCli = array();
        $dataRubro = array();
        $dataPais = array();
        $dataDepas = array();

        $filterEmpProv = array();
        $filterEmpCli = array();
        $filterRubro = array();
        $filterPais = array();
        $filterDepas = array();

        try {
            foreach ($this->getEmpresaTable()->getEmpresaProv() as $empresaPro) {
                $dataEmpProv[$empresaPro->id] = $empresaPro->NombreComercial . ' - ' . $empresaPro->RazonSocial .
                    ' - ' . $empresaPro->Ruc;
                $filterEmpProv[$empresaPro->id] = [$empresaPro->id];
            }

            foreach ($this->getCampaniaPuntosTable()->getEmpresasCliente("busqueda") as $empresa) {
                $dataEmpCli[$empresa->id] = $empresa->Empresa;
                $filterEmpCli[$empresa->id] = [$empresa->id];
            }

            foreach ($this->getRubroTable()->fetchAll() as $rubro) {
                $dataRubro[$rubro->id] = $rubro->Nombre;
                $filterRubro[$rubro->id] = $rubro->id;
            }

            foreach ($this->getPaisTable()->fetchAll() as $pais) {
                $dataPais[$pais->id] = $pais->NombrePais;
                $filterPais[$pais->id] = $pais->id;
            }

            foreach ($this->getUbigeoTable()->fetchAllDepartament() as $dato) {
                $dataDepas[$dato->id] = $dato->Nombre;
                $filterDepas[$dato->id] = $dato->id;
            }
        } catch (\Exception $ex) {
            $dataEmpProv = array();
            $dataEmpCli = array();
            $dataRubro = array();
            $dataPais = array();
            $dataDepas = array();
        }

        $formulario['empprov'] = $dataEmpProv;
        $formulario['empcli'] = $dataEmpCli;
        $formulario['rubro'] = $dataRubro;
        $formulario['pais'] = $dataPais;
        $formulario['depas'] = $dataDepas;

        $filtro['empprov'] = array_keys($filterEmpProv);
        $filtro['empcli'] = array_keys($filterEmpCli);
        $filtro['rubro'] = array_keys($filterRubro);
        $filtro['pais'] = array_keys($filterPais);
        $filtro['depas'] = array_keys($filterDepas);
        return array($formulario, $filtro);
    }

    public function inicializacionBusqueda()
    {
        $dataOfertas = array();
        $dataEmpCli = array();

        $filterOfertas = array();
        $filterEmpCli = array();

        try {
            foreach ($this->getOfertaPuntosTable()->getEmpresaClientes() as $empresa) {
                $dataEmpCli[$empresa->id] = $empresa->Empresa;
                $filterEmpCli[$empresa->id] = [$empresa->id];
            }

            foreach ($this->getOfertaPuntosTable()->getOfertaPuntosEmpresaCliente() as $ofertas) {
                $dataOfertas[$ofertas->id] = $ofertas->Titulo;
                $filterOfertas[$ofertas->id] = [$ofertas->id];
            }
        } catch (\Exception $ex) {
            $dataOfertas = array();
            $dataEmpCli = array();
        }

        $formulario['ofertas'] = $dataOfertas;
        $formulario['emp'] = $dataEmpCli;

        $filtro['ofertas'] = array_keys($filterOfertas);
        $filtro['emp'] = array_keys($filterEmpCli);
        return array($formulario, $filtro);
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $titulo = null;
        $activo = null;

        $busqueda = array(
            'Titulo' => 'Titulo',
            'Empresa' => 'NombreComercial',
            'TipoPrecio' => 'TipoPrecio',
            'Estado' => 'Estado',
            'Segmentos' => 'BNF2_Segmentos.id',
        );

        $data = $this->inicializacionBusqueda();
        $form = new BuscarOfertasPuntos('buscar ofertas', $data[0]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = $request->getPost()->Empresas ? $request->getPost()->Empresas : null;
            $titulo = $request->getPost()->Ofertas ? $request->getPost()->Ofertas : null;
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $titulo = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
        }
        $form->setData(array("Empresas" => $empresa, "Ofertas" => $titulo));

        //Determinar ordenamiento
        $order_by = $this->params()->fromRoute('order_by') ?
            $this->params()->fromRoute('order_by') : 'BNF2_Oferta_Puntos.FechaCreacion';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];
        } else {
            $order_by_o = 'id';
            $order_by = 'BNF2_Oferta_Puntos.FechaCreacion';
        }

        //Se obtiene los datos filtrados y la paginacion segun el orden
        $ofertas = $this->getOfertaPuntosTable()->getDetails($order_by, $order, $empresa, $titulo);
        $paginator = new Paginator(new paginatorIterator($ofertas, $order_by));
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'puntos_oferta' => 'active',
                'offerptos' => 'active',
                'ofertas' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'form' => $form,
                'p' => $page,
                'q1' => $empresa,
                'q2' => $titulo,
            )
        );
    }

    public function addAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $acceptance = true;
        $confirm = null;
        $messageImage = array();
        $type = null;
        $error = array();
        $totalAtributos = 0;
        $message = 0;

        $imagenesXAsignar = array();
        $atributos = null;
        $preciosVenta = null;
        $preciosBeneficio = null;
        $stocks = null;
        $vigencias = null;
        $atributosMessage = array();
        $preciosVentaMessage = array();
        $preciosBeneficioMessage = array();
        $stocksMessage = array();
        $vigenciasMessage = array();

        $datos = $this->inicializacion();
        $form = new FormOfertaPuntos('registrar', $datos[0]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $imagenesXAsignar = $request->getPost()->Imagen;
            $filter = new OfertasPFilter();

            $files = $this->getRequest()->getFiles()->toArray();
            $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $files
            );

            if (count($request->getPost()->Imagen) < 1) {
                $messageImage['image'] = "No hay por lo menos imagen adjunta.";
                $messageImage['imagec'] = 'has-error';
                $acceptance = false;
            } elseif ($request->getPost()->principal == null) {
                $messageImage['image'] = "No hay una Imagen Principal seleccionada.";
                $messageImage['imagec'] = 'has-error';
                $acceptance = false;
            }

            $activator = $request->getPost()->TipoPrecio == "Split" ? false : true;

            $form->setInputFilter(
                $filter->getInputFilter($datos[1], $activator, date('Y-m-d'))
            );

            if ($request->getPost()->TipoPrecio == "Split") {
                $postAtributos = array();
                foreach ($request->getPost()->atributos as $key => $item) {
                    $postAtributos[$key] = preg_replace('/\s+/', ' ', trim($item));
                }
                $request->getPost()->atributos = $postAtributos;
                //Validar Campos
                $resultados = $this->validarCampos($request);
                $approved = $resultados[0];
                $message = $resultados[1];
                //Validar Fechas
                $resultadosFechas = $this->validarFechasVigencia($request);
                $approvedFechas = $resultadosFechas[0];
                $message = array_merge($message, $resultadosFechas[1]);
            } else {
                $approved = true;
                $approvedFechas = true;
            }

            if (isset($request->getPost()->SegmentoPuntos) and ((int)$request->getPost()->SegmentoPuntos != 0)) {
                $segmentoValid = true;
            } else {
                $error['segmento'] = "El campo no puede quedar vacío";
                $segmentoValid = false;
            }

            $form->setData($post);
            if ($form->isValid() and $acceptance and $approved and $approvedFechas and $segmentoValid) {
                $datoSegmento = $post['SegmentoPuntos'];
                $datoDepartamento = $post['Departamento'];
                $datoRubro = $post['Rubro'];
                $datoPais = $post['Pais'];

                $oferta = new OfertaPuntos();
                $oferta->exchangeArray($post);

                $oferta->BNF_Empresa_id = $request->getPost()->EmpresaProv;
                $oferta->DescargaMaxima = $request->getPost()->DescargaMaxima;
                $oferta->PrecioVentaPublico = (int)$request->getPost()->PrecioVentaPublico;
                $oferta->PrecioBeneficio = (int)$request->getPost()->PrecioBeneficio;
                $oferta->Premium = (int)$request->getPost()->Premium;
                $oferta->Eliminado = 0;
                $oferta->Slug = $request->getPost()->Titulo;
                $id = $this->getOfertaPuntosTable()->saveOfertaPuntos($oferta);

                $oferta->id = $id;
                $oferta->Slug = $this->getSlug($request->getPost()->Titulo, $id);
                $this->getOfertaPuntosTable()->saveOfertaPuntos($oferta);

                //Guardamos datos de Oferta Rubro
                $ofertaRubro = new OfertaPuntosRubro();
                $ofertaRubro->BNF_Rubro_id = $datoRubro;
                $ofertaRubro->BNF2_Oferta_Puntos_id = $id;
                $ofertaRubro->Eliminado = '0';
                $this->getOfertaPuntosRubroTable()->saveOfertaPuntosRubro($ofertaRubro);

                //Guardamos datos de Oferta Segmento
                foreach ($datoSegmento as $segmento) {
                    $ofertaSegmento = new OfertaPuntosSegmento();
                    $ofertaSegmento->BNF2_Segmento_id = $segmento;
                    $ofertaSegmento->BNF2_Oferta_Puntos_id = $id;
                    $ofertaSegmento->Eliminado = '0';
                    $this->getOfertaPuntosSegmentoTable()->saveOfertaPuntosSegmento($ofertaSegmento);
                }

                //Guardamos datos de Oferta Ubigeo
                foreach ($datoDepartamento as $departamento) {
                    $ofertaUbigeo = new OfertaPuntosUbigeo();
                    $ofertaUbigeo->BNF_Ubigeo_id = $departamento;
                    $ofertaUbigeo->BNF2_Oferta_Puntos_id = $id;
                    $ofertaUbigeo->Eliminado = '0';
                    $this->getOfertaPuntosUbigeoTable()->saveOfertaPuntosUbigeo($ofertaUbigeo);
                }

                //Guardamos los datos de la Oferta Categoria Ubigeo
                $categoriaData = $this->getCategoriaTable()->getCategoriaBySlug("puntos");

                $categoriaUbigeo = $this->getCategoriaUbigeoTable()
                    ->getCategoriaUbigeoPaisDelete($categoriaData->id, $datoPais);

                $ofertaCategoriaUbigeo = new OfertaPuntosCategoria();
                $ofertaCategoriaUbigeo->BNF_CategoriaUbigeo_id = $categoriaUbigeo->id;
                $ofertaCategoriaUbigeo->BNF2_Oferta_Puntos_id = $id;
                $ofertaCategoriaUbigeo->Eliminado = '0';
                $this->getOfertaPuntosCategoriaTable()->saveOfertaPuntosCategoria($ofertaCategoriaUbigeo);

                //Guardamos los datos de Atributos y Cupones
                if ($request->getPost()->TipoPrecio == "Split") {
                    foreach ($post['atributos'] as $key => $value) {
                        $ofertaAtributo = new OfertaPuntosAtributos();
                        $ofertaAtributo->BNF2_Oferta_Puntos_id = $id;
                        $ofertaAtributo->NombreAtributo = $value;
                        $ofertaAtributo->PrecioVentaPublico = $post['preciosVenta'][$key];
                        $ofertaAtributo->PrecioBeneficio = $post['preciosBeneficio'][$key];
                        $ofertaAtributo->Stock = $post['stocks'][$key];
                        $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                        $ofertaAtributo->Eliminado = 0;
                        $atributo_id = $this->getOfertaPuntosAtributosTable()
                            ->saveOfertaPuntosAtributos($ofertaAtributo);

                        for ($i = 0; $i < $post['stocks'][$key]; $i++) {
                            $cupon = new CuponPuntos();
                            $cupon->BNF2_Oferta_Empresa_id = $request->getPost()->EmpresaProv;
                            $cupon->BNF2_Oferta_Puntos_id = $id;
                            $cupon->BNF2_Oferta_Puntos_Atributos_id = $atributo_id;
                            $cupon->EstadoCupon = 'Creado';
                            $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
                        }
                    }
                } else {
                    for ($i = 0; $i < $oferta->Stock; $i++) {
                        $cupon = new CuponPuntos();
                        $cupon->BNF2_Oferta_Empresa_id = $request->getPost()->EmpresaProv;
                        $cupon->BNF2_Oferta_Puntos_id = $id;
                        $cupon->EstadoCupon = 'Creado';
                        $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
                    }
                }

                //Guardar Imagen
                $principal = (int)$request->getPost()->principalimage;
                foreach ($request->getPost()->Imagen as $key => $img) {
                    $imagenOferta = new OfertaPuntosImagen();
                    $imagenOferta->Nombre = $img;
                    $imagenOferta->BNF2_Oferta_Puntos_id = $id;
                    $imagenOferta->Eliminado = 0;
                    if ($key == $principal) {
                        $imagenOferta->Principal = '1';
                        $this->getOfertaPuntosImagenTable()->noprincipalImagen($id);
                    } else {
                        $imagenOferta->Principal = '0';
                    }
                    $this->getOfertaPuntosImagenTable()->saveOfertaPuntosImagen($imagenOferta);
                }

                $busqueda = new Busqueda();
                $busqueda->BNF_Oferta_id = $id;
                $busqueda->TipoOferta = 2;
                $busqueda->Descripcion = $this->getDescripcionBusqueda($request->getPost()->Titulo);
                $this->getBusquedaTable()->saveBusqueda($busqueda);

                $confirm[] = $this::MESSAGE_SAVE;
                $type = "success";
                $form = new FormOfertaPuntos('registrar', $datos[0]);
                $imagenesXAsignar = array();
            } else {
                $confirm[] = $this::MESSAGE_ERROR;
                $type = "danger";
                if ($request->getPost()->TipoPrecio == "Split") {
                    $totalAtributos = count($request->getPost()->atributos);
                    //Datos
                    $atributos = $this->generarArreglosJS($request->getPost()->atributos);
                    $preciosVenta = $this->generarArreglosJS($request->getPost()->preciosVenta);
                    $preciosBeneficio = $this->generarArreglosJS($request->getPost()->preciosBeneficio);
                    $stocks = $this->generarArreglosJS($request->getPost()->stocks);
                    $vigencias = $this->generarArreglosJS($request->getPost()->vigencias);
                    //Mensajes de Error
                    $atributosMessage = $this->generarArreglosJS($message["atributos"]);
                    $preciosVentaMessage = $this->generarArreglosJS($message["preciosVenta"]);
                    $preciosBeneficioMessage = $this->generarArreglosJS($message["preciosBeneficio"]);
                    $stocksMessage = $this->generarArreglosJS($message["stocks"]);
                    $vigenciasMessage = $this->generarArreglosJS($message["vigencias"]);
                }
            }
        }

        if ($imagenesXAsignar == null) {
            $imagenesXAsignar = array();
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'puntos_oferta' => 'active',
                'offerptosadd' => 'active',
                'form' => $form,
                'confirm' => $confirm,
                'type' => $type,
                'error' => $error,
                'messageImage' => $messageImage,
                'totalAtributos' => $totalAtributos,
                'imagenesXAsignar' => $imagenesXAsignar,
                'atributos' => $atributos,
                'preciosVenta' => $preciosVenta,
                'preciosBeneficio' => $preciosBeneficio,
                'stocks' => $stocks,
                'vigencias' => $vigencias,
                'atributosMessage' => $atributosMessage,
                'preciosVentaMessage' => $preciosVentaMessage,
                'preciosBeneficioMessage' => $preciosBeneficioMessage,
                'stocksMessage' => $stocksMessage,
                'vigenciasMessage' => $vigenciasMessage,
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $id = (int)$this->params()->fromRoute('id', 0);
        $acceptance = true;
        $confirm = null;
        $messageImage = array();
        $type = null;
        $error = array();
        $message = array();
        $totalAtributos = 0;

        $atributosAnt = array();
        $atributoStockAnt = array();
        $dataSeg = array();
        $segmentoAnt = null;
        $tipoAnt = null;
        $dataSegIds = null;

        $listDepartamento = array();
        $fechasAnteriores = array();

        $imagenesXAsignar = array();
        $atributosId = array();
        $atributos = array();
        $preciosVenta = array();
        $preciosBeneficio = array();
        $stocks = array();
        $vigencias = array();
        $atributosMessage = array();
        $preciosVentaMessage = array();
        $preciosBeneficioMessage = array();
        $stocksMessage = array();
        $vigenciasMessage = array();
        $slug = null;
        $message_des = null;

        $config = $this->getServiceLocator()->get('Config');
        $datos = $this->inicializacion();
        $form = new FormOfertaPuntos('editarForm', $datos[0]);

        try {
            $dataOferta = $this->getOfertaPuntosTable()->getOfertaPuntos($id);
            $slug = $dataOferta->Slug;
            $dataOferta->Stock = (int)$dataOferta->Stock;
            $tipoAnt = $dataOferta->TipoPrecio;
            $stockAnt = $dataOferta->Stock;
            $tituloOferta = $this->getDescripcionBusqueda($dataOferta->Titulo);
            if (strtotime($dataOferta->FechaVigencia) >= strtotime(date("Y-m-d"))) {
                $fechaAnt = date("Y-m-d");
            } else {
                $fechaAnt = $dataOferta->FechaVigencia;
            }

            $ofertaAnt = $id;

            $dataOferta->PrecioVentaPublico = (int)$dataOferta->PrecioVentaPublico;
            $dataOferta->PrecioBeneficio = (int)$dataOferta->PrecioBeneficio;

            //Segmentos
            $contador = 0;
            $segmentos = $this->getOfertaPuntosSegmentoTable()->getOfertaPuntosSegmentoByOferta($id);
            if(count($segmentos) > 0) {
                foreach ($segmentos as $segmento) {
                    $dataSeg[] = $segmento->BNF2_Segmento_id;
                    $segmentoAnt = $contador > 0 ?
                        $segmentoAnt . ", '" . $segmento->BNF2_Segmento_id . "'" : "['" . $segmento->BNF2_Segmento_id . "'";
                    $dataSegIds = $contador > 0 ?
                        $dataSegIds . '; ' . $segmento->BNF2_Segmento_id : $segmento->BNF2_Segmento_id;
                    $contador++;
                }
                $segmentoAnt = $segmentoAnt . "]";

                //Campaña
                $dataSegmento = $this->getSegmentoPuntosTable()->getSegmentosP($dataSeg[0]);
                $campaniaIni = $dataSegmento->BNF2_Campania_id;
                $dataCampania = $this->getCampaniaPuntosTable()->getCampaniasP($campaniaIni);

                $empresaCliIni = $dataCampania->id;
                $dataEmpresaCli = $this->getCampaniaPuntosEmpresaTable()->getbyCampaniasP($empresaCliIni);
            }
            $dataRubro = $this->getOfertaPuntosRubroTable()->getOfertaPuntosRubroByIdOferta($id);

            $dataDepartamentos = $this->getOfertaPuntosUbigeoTable()->getAllOfertaPuntosUbigeo($id);
            foreach ($dataDepartamentos as $departamento) {
                $listDepartamento[] = $departamento->BNF_Ubigeo_id;
            }

            $imagenes = $this->getOfertaPuntosImagenTable()->getAllOfertaPuntosImagen($id);

            if ($dataOferta->TipoPrecio == "Split") {
                $dataAtributos = $this->getOfertaPuntosAtributosTable()->getAllOfertaPuntosAtributos($id);
                $totalAtributos = count($dataAtributos);
                foreach ($dataAtributos as $atributo) {
                    $atributosId[] = $atributo->id;
                    $atributos[] = $atributo->NombreAtributo;
                    $atributosAnt[] = $atributo->NombreAtributo;
                    $preciosVenta[] = (int)$atributo->PrecioVentaPublico;
                    $preciosBeneficio[] = (int)$atributo->PrecioBeneficio;
                    $stocks[] = (int)$atributo->Stock;
                    $atributoStockAnt[] = (int)$atributo->Stock;
                    $vigencias[] = $atributo->FechaVigencia;
                    $fechasAnteriores[] = $atributo->FechaVigencia;
                }
                $atributosId = $this->generarArreglosJS($atributosId);
                $atributos = $this->generarArreglosJS($atributos);
                $preciosVenta = $this->generarArreglosJS($preciosVenta);
                $preciosBeneficio = $this->generarArreglosJS($preciosBeneficio);
                $stocks = $this->generarArreglosJS($stocks);
                $vigencias = $this->generarArreglosJS($vigencias);
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('ofertas-puntos', array('action' => 'index'));
        }

        $form->bind($dataOferta);
        $form->get('EmpresaProv')->setAttribute('value', $dataOferta->BNF_Empresa_id);
        if(count($segmentos) > 0) {
            $form->get('EmpresaCli')->setAttribute('value', $dataEmpresaCli->BNF_Empresa_id);
            $form->get('CampaniaPuntos')->setAttribute('value', $dataSegmento->BNF2_Campania_id);
        }
        $form->get('Departamento')->setAttribute('value', $listDepartamento);
        $form->get('Rubro')->setAttribute('value', $dataRubro->BNF_Rubro_id);
        $form->get('submit')->setAttribute('value', 'Editar');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $imagenesXAsignar = $request->getPost()->Imagen;
            $principal = (int)$request->getPost()->principalimage;
            $filter = new OfertasPFilter();

            $files = $this->getRequest()->getFiles()->toArray();
            $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $files
            );

            if ($request->getPost()->principal == null) {
                $messageImage['image'] = "No hay una Imagen Principal seleccionada.";
                $messageImage['imagec'] = 'has-error';
                $acceptance = false;
            }

            $copyState = $request->getPost()->action == "copy" ? true : false;

            $activator = $request->getPost()->TipoPrecio == "Split" ? false : true;

            $fechaAnt = ($copyState == true) ? date("Y-m-d") : $fechaAnt;
            $form->setInputFilter(
                $filter->getInputFilter($datos[1], $activator, $fechaAnt)
            );

            if ($request->getPost()->TipoPrecio == "Split") {
                $postAtributos = array();
                foreach ($request->getPost()->atributos as $key => $item) {
                    $postAtributos[$key] = preg_replace('/\s+/', ' ', trim($item));
                }
                $request->getPost()->atributos = $postAtributos;
                //Validar Campos
                $resultados = $this->validarCampos($request);
                $approved = $resultados[0];
                $message = $resultados[1];
                //Validar Fechas
                $fechasAnteriores = ($copyState == true) ? null : $fechasAnteriores;
                $resultadosFechas = $this->validarFechasVigencia($request, $fechasAnteriores);
                $approvedFechas = $resultadosFechas[0];
                $message = array_merge($message, $resultadosFechas[1]);
            } else {
                $approved = true;
                $approvedFechas = true;
            }

            if (isset($request->getPost()->SegmentoPuntos) and ((int)$request->getPost()->SegmentoPuntos != 0)) {
                $segmentoValid = true;
            } else {
                $error['segmento'] = "El campo no puede quedar vacío";
                $segmentoValid = false;
            }

            $approvedTipo = true;
            $has_downloads = $this->getCuponPuntosTable()->hasCuponPuntosDescargas($id);
            if ($has_downloads > 0 and $request->getPost()->TipoPrecio != $tipoAnt) {
                $approvedTipo = false;
            }

            $form->setData($post);
            if ($form->isValid() and $acceptance and $approved and $approvedFechas and $segmentoValid and $approvedTipo) {
                $datoDepartamento = $post['Departamento'];
                $datoSegmentos = $post['SegmentoPuntos'];
                $datoRubro = $post['Rubro'];
                $datoPais = $post['Pais'];

                $oferta = new OfertaPuntos();
                $oferta->exchangeArray($post);
                unset($oferta->PrecioVentaPublico);
                unset($oferta->PrecioBeneficio);
                $ofertaObj = $this->getOfertaPuntosTable()->getOfertaPuntos($oferta->id);
                $descargas = $this->getCuponPuntosTable()->getCuponPuntosDescargados($id);
                $oferta->PrecioVentaPublico = ($descargas == 0) ? (int)$request->getPost()->PrecioVentaPublico
                    : $ofertaObj->PrecioVentaPublico;
                $oferta->PrecioBeneficio = ($descargas == 0) ? (int)$request->getPost()->PrecioBeneficio
                    : $ofertaObj->PrecioBeneficio;
                $oferta->BNF_Empresa_id = $request->getPost()->EmpresaProv;
                $oferta->DescargaMaxima = $request->getPost()->DescargaMaxima;

                if ($descargas > 0) {
                    $message_des = $this::MESSAGE_UPDATE_DES;
                }

                $oferta->Premium = (int)$request->getPost()->Premium;

                $oferta->Slug = $this->getSlug($request->getPost()->Titulo, $id);
                $oferta->Eliminado = 0;

                #region Guardar Oferta
                if (!$copyState) {
                    $oferta->id = $id;
                } else {
                    $oferta->id = 0;
                    $oferta->Slug = rand(0,100) . (string)date('Y-m-d');
                }

                $id = $this->getOfertaPuntosTable()->saveOfertaPuntos($oferta);

                if ($copyState) {
                    $copySlug['Slug'] = $this->getSlug($request->getPost()->Titulo . " Copia Slug", (int)$id);
                    $this->getOfertaPuntosTable()->updateOferta($id, $copySlug);
                }
                #endregion

                $oferta->id = $id;
                $oferta->Slug = $this->getSlug($request->getPost()->Titulo, $id);
                $this->getOfertaPuntosTable()->saveOfertaPuntos($oferta);

                #region Crear Cupones Grabar denuevo
                if ($copyState) {
                    for ($i = 0; $i < $stockAnt; $i++) {
                        $cupon = new CuponPuntos();
                        $cupon->BNF2_Oferta_Empresa_id = $request->getPost()->EmpresaProv;
                        $cupon->BNF2_Oferta_Puntos_id = $id;
                        $cupon->EstadoCupon = 'Creado';
                        $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
                    }
                }
                #endregion

                #region Actualizar Cupones
                $stockAct = $request->getPost()->Stock;
                if ($stockAct > $stockAnt) {
                    $dif = $stockAct - $stockAnt;
                    for ($i = 0; $i < $dif; $i++) {
                        $cupon = new CuponPuntos();
                        $cupon->BNF2_Oferta_Empresa_id = $request->getPost()->EmpresaProv;
                        $cupon->BNF2_Oferta_Puntos_id = $id;
                        $cupon->EstadoCupon = 'Creado';
                        $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
                    }
                } elseif ($stockAct < $stockAnt) {
                    $dif = $stockAnt - $stockAct;
                    $ultimo = $this->getCuponPuntosTable()->getLastCuponPuntos($id);
                    $ultimo->id = (int)$ultimo->id + 1;
                    for ($i = 0; $i < $dif; $i++) {
                        $ultimo = $this->getCuponPuntosTable()->getLastCuponPuntos($id, $ultimo->id);
                        if ($ultimo != false) {
                            $cupon = $this->getCuponPuntosTable()->getCuponPuntos($ultimo->id);
                            $cupon->EstadoCupon = 'Eliminado';
                            $cupon->FechaEliminado = date("Y-m-d H:i:s");
                            $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
                        }
                    }
                }
                #endregion

                #region Actualizar Rubro
                if (!$copyState) {
                    $this->getOfertaPuntosRubroTable()->deleteOfertaPuntosRubro($id, $dataRubro->BNF_Rubro_id);
                }

                if ($this->getOfertaPuntosRubroTable()->getIfExist($id, $datoRubro) > 0 and !$copyState) {
                    $ofertaRubro = $this->getOfertaPuntosRubroTable()->getOfertaPuntosRubroSearch($id, $datoRubro);
                    $ofertaRubro->Eliminado = '0';
                    $this->getOfertaPuntosRubroTable()->saveOfertaPuntosRubro($ofertaRubro);
                } else {
                    $ofertaRubro = new OfertaPuntosRubro();
                    $ofertaRubro->BNF_Rubro_id = $datoRubro;
                    $ofertaRubro->BNF2_Oferta_Puntos_id = $id;
                    $ofertaRubro->Eliminado = '0';
                    $this->getOfertaPuntosRubroTable()->saveOfertaPuntosRubro($ofertaRubro);
                }
                #endregion

                #region Actualizamos los Ubigeos
                if (count($datoDepartamento) > 0) {
                    if (!$copyState) {
                        $this->getOfertaPuntosUbigeoTable()->deleteAllOfertaPuntosUbigeo($id);
                    }
                    foreach ($datoDepartamento as $valor) {
                        if ($this->getOfertaPuntosUbigeoTable()->getIfExist($id, $valor) > 0 and !$copyState) {
                            $ofertaUbigeo = $this->getOfertaPuntosUbigeoTable()->getOfertaUbigeoSearch($id, $valor);
                            $ofertaUbigeo->Eliminado = '0';
                            $this->getOfertaPuntosUbigeoTable()->saveOfertaPuntosUbigeo($ofertaUbigeo);
                        } else {
                            $ofertaUbigeo = new OfertaPuntosUbigeo();
                            $ofertaUbigeo->BNF_Ubigeo_id = $valor;
                            $ofertaUbigeo->BNF2_Oferta_Puntos_id = $id;
                            $ofertaUbigeo->Eliminado = '0';
                            $this->getOfertaPuntosUbigeoTable()->saveOfertaPuntosUbigeo($ofertaUbigeo);
                        }
                    }
                } else {
                    $this->getOfertaPuntosUbigeoTable()->deleteAllOfertaPuntosUbigeo($id);
                }
                #endregion

                #region Actualizamos los Segmentos
                if (count($datoSegmentos) > 0) {
                    if (!$copyState) {
                        $this->getOfertaPuntosSegmentoTable()->deleteAllOfertaPuntosSegmento($id);
                    }
                    foreach ($datoSegmentos as $valor) {
                        if ($this->getOfertaPuntosSegmentoTable()->getIfExist($id, $valor) > 0 and !$copyState) {
                            $ofertaSegmento = $this->getOfertaPuntosSegmentoTable()
                                ->getOfertaPuntosSegmentoSearch($id, $valor);
                            $ofertaSegmento->Eliminado = '0';
                            $this->getOfertaPuntosSegmentoTable()->saveOfertaPuntosSegmento($ofertaSegmento);
                        } else {
                            $ofertaSegmento = new OfertaPuntosSegmento();
                            $ofertaSegmento->BNF2_Segmento_id = $valor;
                            $ofertaSegmento->BNF2_Oferta_Puntos_id = $id;
                            $ofertaSegmento->Eliminado = '0';
                            $this->getOfertaPuntosSegmentoTable()->saveOfertaPuntosSegmento($ofertaSegmento);
                        }
                    }
                } else {
                    $this->getOfertaPuntosSegmentoTable()->deleteAllOfertaPuntosSegmento($id);
                }
                #endregion

                #region Actualizar Categoria
                $categoriaData = $this->getCategoriaTable()->getCategoriaBySlug("puntos");

                $categoriaUbigeo = $this->getCategoriaUbigeoTable()
                    ->getCategoriaUbigeoPaisDelete($categoriaData->id, $datoPais);

                $ofertaCategoriaUbigeo = $this->getOfertaPuntosCategoriaTable()
                    ->getOfertaPuntosCategoriaUbigeoSearch($id, $categoriaUbigeo->id);
                if (is_object($ofertaCategoriaUbigeo)) {
                    $ofertaCategoriaUbigeo->BNF_CategoriaUbigeo_id = $categoriaUbigeo->id;
                    $ofertaCategoriaUbigeo->BNF2_Oferta_Puntos_id = $id;
                    $ofertaCategoriaUbigeo->Eliminado = '0';
                    $this->getOfertaPuntosCategoriaTable()->saveOfertaPuntosCategoria($ofertaCategoriaUbigeo);
                } else {
                    $ofertaCategoriaUbigeo = new OfertaPuntosCategoria();
                    $ofertaCategoriaUbigeo->BNF_CategoriaUbigeo_id = $categoriaUbigeo->id;
                    $ofertaCategoriaUbigeo->BNF2_Oferta_Puntos_id = $id;
                    $ofertaCategoriaUbigeo->Eliminado = '0';
                    $this->getOfertaPuntosCategoriaTable()->saveOfertaPuntosCategoria($ofertaCategoriaUbigeo);
                }
                #endregion

                #region Actualizar los Atributos
                if ($request->getPost()->TipoPrecio == "Split") {
                    if (!$copyState) {
                        $this->getOfertaPuntosAtributosTable()->deleteOfertaPuntosAtributos($id);
                    }
                    foreach ($post['atributos'] as $key => $value) {
                        if ($this->getOfertaPuntosAtributosTable()->getIfExist($id, $value) > 0 and !$copyState) {
                            $ofertaAtributo = $this->getOfertaPuntosAtributosTable()
                                ->getOfertaPuntosAtributosSearch($id, $value);
                            $descargas = $this->getCuponPuntosTable()
                                ->getCuponPuntosDescargados($id, $ofertaAtributo->id);
                            if ($descargas == 0) {
                                $ofertaAtributo->PrecioVentaPublico = $post['preciosVenta'][$key];
                                $ofertaAtributo->PrecioBeneficio = $post['preciosBeneficio'][$key];
                            } else {
                                $message_des = $this::MESSAGE_UPDATE_DES;
                            }

                            $ofertaAtributo->Stock = $post['stocks'][$key];
                            $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                            $ofertaAtributo->Eliminado = 0;
                            $this->getOfertaPuntosAtributosTable()->saveOfertaPuntosAtributos($ofertaAtributo);

                            foreach ($atributosAnt as $keyAttr => $valueAttr) {
                                if ($valueAttr == $value) {
                                    $stockAct = $post['stocks'][$key];
                                    $stockAnt = $atributoStockAnt[$keyAttr];
                                    if ($stockAct > $stockAnt) {
                                        $dif = $stockAct - $stockAnt;
                                        for ($i = 0; $i < $dif; $i++) {
                                            $cupon = new CuponPuntos();
                                            $cupon->BNF2_Oferta_Empresa_id = $request->getPost()->EmpresaProv;
                                            $cupon->BNF2_Oferta_Puntos_id = $id;
                                            $cupon->BNF2_Oferta_Puntos_Atributos_id = $ofertaAtributo->id;
                                            $cupon->EstadoCupon = 'Creado';
                                            $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
                                        }
                                    } elseif ($stockAct < $stockAnt) {
                                        $dif = $stockAnt - $stockAct;
                                        $ultimo = $this->getCuponPuntosTable()->getLastCuponPuntos($id, null, $ofertaAtributo->id);
                                        $ultimo->id = (int)$ultimo->id + 1;
                                        for ($i = 0; $i < $dif; $i++) {
                                            $ultimo = $this->getCuponPuntosTable()->getLastCuponPuntos($id, $ultimo->id, $ofertaAtributo->id);
                                            if ($ultimo != false) {
                                                $cupon = $this->getCuponPuntosTable()->getCuponPuntos($ultimo->id);
                                                $cupon->EstadoCupon = 'Eliminado';
                                                $cupon->FechaEliminado = date("Y-m-d H:i:s");
                                                $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $ofertaAtributo = new OfertaPuntosAtributos();
                            $ofertaAtributo->BNF2_Oferta_Puntos_id = $id;
                            $ofertaAtributo->NombreAtributo = $value;
                            $ofertaAtributo->PrecioVentaPublico = $post['preciosVenta'][$key];
                            $ofertaAtributo->PrecioBeneficio = $post['preciosBeneficio'][$key];
                            $ofertaAtributo->Stock = $post['stocks'][$key];
                            $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                            $ofertaAtributo->Eliminado = 0;
                            $atributo_id = $this->getOfertaPuntosAtributosTable()
                                ->saveOfertaPuntosAtributos($ofertaAtributo);

                            for ($i = 0; $i < $post['stocks'][$key]; $i++) {
                                $cupon = new CuponPuntos();
                                $cupon->BNF2_Oferta_Empresa_id = $request->getPost()->EmpresaProv;
                                $cupon->BNF2_Oferta_Puntos_id = $id;
                                $cupon->BNF2_Oferta_Puntos_Atributos_id = $atributo_id;
                                $cupon->EstadoCupon = 'Creado';
                                $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
                            }
                        }
                    }
                }
                #endregion

                //Caducación manual de la Oferta
                if ($request->getPost()->Estado == "Caducado") {
                    $this->getCuponPuntosTable()->updateOfertaFinalizado($id);
                    if ($request->getPost()->TipoPrecio == "Split") {
                        foreach ($post['atributos'] as $key => $value) {
                            if ($this->getOfertaPuntosAtributosTable()->getIfExist($id, $value) > 0 and !$copyState) {
                                $ofertaAtributo = $this->getOfertaPuntosAtributosTable()
                                    ->getOfertaPuntosAtributosSearch($id, $value);
                                $ofertaAtributo->Stock = 0;
                                $ofertaAtributo->Eliminado = 0;
                                $this->getOfertaPuntosAtributosTable()->saveOfertaPuntosAtributos($ofertaAtributo);
                            }
                        }
                    } else {
                        $oferta->id = $id;
                        $oferta->Stock = 0;
                        $this->getOfertaPuntosTable()->saveOfertaPuntos($oferta);
                    }
                }

                //Agregar Imagenes Grabar denuevo
                if ($copyState) {
                    $allImage = $this->getOfertaPuntosImagenTable()->getAllImagesOfertaPuntos($ofertaAnt);
                    foreach ($allImage as $imagen) {
                        $nuevaImagen = new OfertaPuntosImagen();
                        $nuevaImagen->BNF2_Oferta_Puntos_id = $id;
                        $nuevaImagen->Nombre = $imagen->Nombre;
                        $nuevaImagen->Principal = $imagen->Principal == 1 ? '1' : '0';
                        $nuevaImagen->Eliminado = 0;
                        $this->getOfertaPuntosImagenTable()->saveOfertaPuntosImagen($nuevaImagen);
                    }
                }

                //Actualizamos Imagen
                if (is_array($request->getPost()->Imagen) || is_object($request->getPost()->Imagen)) {
                    foreach ($request->getPost()->Imagen as $key => $img) {
                        $imagenOferta = new OfertaPuntosImagen();
                        $imagenOferta->Nombre = $img;
                        $imagenOferta->BNF2_Oferta_Puntos_id = $id;
                        if ($key == $principal) {
                            $imagenOferta->Principal = '1';
                            $this->getOfertaPuntosImagenTable()->noPrincipalImagen($id);
                        } else {
                            $imagenOferta->Principal = '0';
                        }
                        $imagenOferta->Eliminado = 0;
                        $this->getOfertaPuntosImagenTable()->saveOfertaPuntosImagen($imagenOferta);
                    }
                }

                //Actualizar Busqueda
                $busqueda = new Busqueda();
                $busqueda->BNF_Oferta_id = $id;
                $busqueda->TipoOferta = 2;

                if (!$copyState) {
                    $busqueda->Descripcion = $this->getDescripcionBusqueda($request->getPost()->Titulo);
                } else {
                    $tituloActual = $this->getDescripcionBusqueda($request->getPost()->Titulo);
                    if ($tituloOferta == $tituloActual) {
                        $busqueda->Descripcion = $this->getDescripcionBusqueda($tituloOferta) . " copia";
                    } else {
                        $busqueda->Descripcion = $this->getDescripcionBusqueda($tituloActual);
                    }
                }

                if (!$copyState) {
                    $this->getBusquedaTable()->updateBusqueda($busqueda);
                } else {
                    $this->getBusquedaTable()->saveBusqueda($busqueda);
                }

                if (!$copyState) {
                    $this->flashMessenger()
                        ->addMessage(($message_des != null) ? $this::MESSAGE_UPDATE_DES : $this::MESSAGE_UPDATE);
                } else {
                    $this->flashMessenger()->addMessage($this::MESSAGE_COPY);
                }

                return $this->redirect()->toRoute('ofertas-puntos');
            } else {
                $confirm[] = $this::MESSAGE_ERROR;
                $type = "danger";

                if (!$approvedTipo) {
                    $form->get('TipoPrecio')->setMessages(array('No se puede cambiar el tipo de precio, porque la oferta tiene descargas'));
                }

                if ($request->getPost()->TipoPrecio == "Split") {
                    $totalAtributos = count($request->getPost()->atributos);
                    //Datos
                    $atributosId = $this->generarArreglosJS($request->getPost()->atributosId);
                    $atributos = $this->generarArreglosJS($request->getPost()->atributos);
                    $preciosVenta = $this->generarArreglosJS($request->getPost()->preciosVenta);
                    $preciosBeneficio = $this->generarArreglosJS($request->getPost()->preciosBeneficio);
                    $stocks = $this->generarArreglosJS($request->getPost()->stocks);
                    $vigencias = $this->generarArreglosJS($request->getPost()->vigencias);
                    //Mensajes de Error
                    $atributosMessage = $this->generarArreglosJS($message["atributos"]);
                    $preciosVentaMessage = $this->generarArreglosJS($message["preciosVenta"]);
                    $preciosBeneficioMessage = $this->generarArreglosJS($message["preciosBeneficio"]);
                    $stocksMessage = $this->generarArreglosJS($message["stocks"]);
                    $vigenciasMessage = $this->generarArreglosJS($message["vigencias"]);
                }
            }
        }

        if ($imagenesXAsignar == null) {
            $imagenesXAsignar = array();
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'puntos_oferta' => 'active',
                'offerptosadd' => 'active',
                'id' => $id,
                'form' => $form,
                'confirm' => $confirm,
                'type' => $type,
                'error' => $error,
                'segmentoAnt' => $segmentoAnt,
                'imagenes' => $imagenes,
                'messageImage' => $messageImage,
                'totalAtributos' => $totalAtributos,
                'imagenesXAsignar' => $imagenesXAsignar,
                'atributosId' => $atributosId,
                'atributos' => $atributos,
                'preciosVenta' => $preciosVenta,
                'preciosBeneficio' => $preciosBeneficio,
                'stocks' => $stocks,
                'vigencias' => $vigencias,
                'atributosMessage' => $atributosMessage,
                'preciosVentaMessage' => $preciosVentaMessage,
                'preciosBeneficioMessage' => $preciosBeneficioMessage,
                'stocksMessage' => $stocksMessage,
                'vigenciasMessage' => $vigenciasMessage,
                'slug' => $slug,
                'config' => $config
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
        $oferta = (int)$this->params()->fromRoute('val', 0);

        $resultado = $this->getOfertaPuntosTable()->getReporte($empresa, $oferta);
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Información del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Oferta Puntos")
                ->setSubject("Oferta Puntos")
                ->setDescription("Documento listando Oferta Puntos")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Oferta Puntos");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:I' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

            #region Style
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

            $objPHPExcel->getActiveSheet()->getStyle('A1:I' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Título')
                ->setCellValue('C1', 'Empresa Cliente')
                ->setCellValue('D1', 'Tipo Precio')
                ->setCellValue('E1', 'Segmentos')
                ->setCellValue('F1', 'Estado')
                ->setCellValue('G1', 'PVP')
                ->setCellValue('H1', 'PB')
                ->setCellValue('I1', 'Vigencia del cupón');
            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Titulo)
                    ->setCellValue('C' . $i, $registro->BNF_Empresa_id)
                    ->setCellValue('D' . $i, $registro->TipoPrecio)
                    ->setCellValue('E' . $i, $registro->Segmentos)
                    ->setCellValue('F' . $i, $registro->Estado)
                    ->setCellValue('G' . $i, $registro->PrecioVentaPublico)
                    ->setCellValue('H' . $i, $registro->PrecioBeneficio)
                    ->setCellValue('I' . $i, $registro->FechaVigencia);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="OfertasPuntos.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function saveImageAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $guardado = false;
        $resize_bool['oferta_img'] = false;
        $resize_bool['oferta_medium'] = false;
        $resize_bool['oferta_large'] = false;
        $path = './public/elements/oferta_puntos/';

        $response = $this->getResponse();
        $ext = $this->getRequest()->getPost('ext');
        $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);

        $manager = new ImageManager(array('driver' => 'imagick'));
        $img = $manager->make($_FILES['val']['tmp_name']);
        $img2 = $manager->make($_FILES['val']['tmp_name']);
        $img3 = $manager->make($_FILES['val']['tmp_name']);

        $proporcion = $img->getWidth() / $img->getHeight();

        $config = $this->getServiceLocator()->get('Config');

        $resize = new Resize();

        $resize_bool['oferta_img'] = $resize->isResize($img, $config, 'oferta_img');
        $resize_bool['oferta_medium'] = $resize->isResize($img, $config, 'oferta_medium');
        $resize_bool['oferta_large'] = $resize->isResize($img, $config, 'oferta_large');

        if ($proporcion < 1.74) {
            try {
                $resize->resizeHeight($path, $img, $ext, $fileName, $config, 'oferta_img', '', $resize_bool);
                $resize->resizeHeight($path, $img2, $ext, $fileName, $config, 'oferta_medium', '-medium', $resize_bool);
                $resize->resizeHeight($path, $img3, $ext, $fileName, $config, 'oferta_large', '-large', $resize_bool);
                $guardado = true;
            } catch (\Exception $e) {
                $guardado = false;
            }
        } elseif ($proporcion > 1.81) {
            try {
                $resize->resizeWidth($path, $img, $ext, $fileName, $config, 'oferta_img', '', $resize_bool);
                $resize->resizeWidth($path, $img2, $ext, $fileName, $config, 'oferta_medium', '-medium', $resize_bool);
                $resize->resizeWidth($path, $img3, $ext, $fileName, $config, 'oferta_large', '-large', $resize_bool);
                $guardado = true;
            } catch (\Exception $e) {
                $guardado = false;
            }
        } else {
            try {
                $resize->rename($path, $img, $ext, $fileName, '');
                $resize->rename($path, $img2, $ext, $fileName, '-medium');
                $resize->rename($path, $img2, $ext, $fileName, '-large');
                $guardado = true;
            } catch (\Exception $e) {
                $guardado = false;
            }
        }

        if ($guardado) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'name' => $fileName . '.' . $ext,
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

    public function deleteImageAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $response = $this->getResponse();
        $img = $this->getRequest()->getPost('val');
        $ext = $this->getRequest()->getPost('ext');
        $fullpath = './public/elements/oferta_puntos/' . $img;
        if (file_exists($fullpath)) {
            unlink($fullpath);
            $fullpath2 = str_replace('.' . $ext, '', $fullpath) . '-medium' . '.' . $ext;
            unlink($fullpath2);
            $fullpath3 = str_replace('.' . $ext, '', $fullpath) . '-large' . '.' . $ext;
            unlink($fullpath3);
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

                        $campanias = $this->getCampaniaPuntosTable()->getCampaniasPByEmpresa($id);
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
                    if ($result = $this->getSegmentoPuntosTable()->getAllSegmentos($id)) {
                        $dataSegmentos[] = array('id' => '', 'text' => 'Seleccione...');
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

    public function getDepartamentosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $request = $this->getRequest();
        $response = $this->getResponse();
        $valoresdepa = array();
        $valorescate = array();
        $valorescamp = array();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            if ($id != 0) {
                try {
                    $departamentos = $this->getUbigeoTable()->getDepartamentPais($id);
                    foreach ($departamentos as $departamento) {
                        $valoresdepa[$departamento->id] = $departamento->Nombre;
                    }
                } catch (\Exception $ex) {
                    $valoresdepa = array();
                }

                try {
                    //Obtenemos Todas Las Categorias
                    $categorias = $this->getCategoriaTable()->getCategoriaPais($id);
                    foreach ($categorias as $categoria) {
                        $valorescate[$categoria->id] = $categoria->Nombre;
                    }
                } catch (\Exception $ex) {
                    $valorescate = array();
                }

                try {
                    //Obtenemos Todas Las Campañas
                    $campanias = $this->getCampaniaTable()->getCampaniaPais($id);
                    foreach ($campanias as $campania) {
                        $valorescamp[$campania->id] = $campania->Nombre;
                    }
                } catch (\Exception $ex) {
                    $valorescamp = array();
                }

                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'depa' => $valoresdepa,
                            'camp' => $valorescamp,
                            'cate' => $valorescate,
                        )
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false)));
            }
        }
        return $response;
    }

    public function validarCampos($request)
    {
        $approved = false;
        $messages = array();

        $validStock = new Step(array('baseValue' => 0, 'step' => 1));
        $validNotEmpty = new NotEmpty(NotEmpty::ALL);
        $validDate = new Date(array('format' => 'Y-m-d'));

        $atributos = $request->getPost()->atributos;
        $precioVenta = $request->getPost()->preciosVenta;
        $precioBeneficio = $request->getPost()->preciosBeneficio;
        $stock = $request->getPost()->stocks;
        $vigencia = $request->getPost()->vigencias;
        if (count($atributos) == count($precioVenta) and count($precioVenta) == count($precioBeneficio)
            and count($precioBeneficio) == count($stock) and count($stock) == count($vigencia)
        ) {
            //Validar Atributos
            $atributosState = true;
            if (is_array($atributos) || is_object($atributos)) {
                foreach ($atributos as $value) {
                    if (!$validNotEmpty($value)) {
                        $messages['atributos'][] = "El campo no puede quedar vacío.";
                        $atributosState = false;
                    } else {
                        $messages['atributos'][] = "";
                    }
                }
            }
            //Validar Precio Venta
            $precioVentaState = true;
            if (is_array($precioVenta) || is_object($precioVenta)) {
                foreach ($precioVenta as $value) {
                    if (!$validStock($value)) {
                        $messages['preciosVenta'][] = "El campo solo acepta números enteros.";
                        $precioVentaState = false;
                    } else {
                        $messages['preciosVenta'][] = "";
                    }
                }
            }
            //Validar Precio Beneficios
            $precioBeneficioState = true;
            if (is_array($precioBeneficio) || is_object($precioBeneficio)) {
                foreach ($precioBeneficio as $value) {
                    if (!$validStock($value)) {
                        $messages['preciosBeneficio'][] = "El campo solo acepta números enteros.";
                        $precioBeneficioState = false;
                    } else {
                        $messages['preciosBeneficio'][] = "";
                    }
                }
            }
            //Validar Stocks
            $stockState = true;
            if (is_array($stock) || is_object($stock)) {
                foreach ($stock as $value) {
                    if (!$validStock($value)) {
                        $messages['stocks'][] = "El campo solo acepta números enteros.";
                        $stockState = false;
                    } else {
                        $messages['stocks'][] = "";
                    }
                }
            }
            //Validar Vigencias
            $vigenciaState = true;
            if (is_array($vigencia) || is_object($vigencia)) {
                foreach ($vigencia as $value) {
                    if (!$validNotEmpty($value)) {
                        $messages['vigencias'][] = "El campo no puede quedar vacío.";
                        $vigenciaState = false;
                    } elseif (!$validDate($value)) {
                        $messages['vigencias'][] = "El campo solo acepta números enteros.";
                        $vigenciaState = false;
                    } else {
                        $messages['vigencias'][] = "";
                    }
                }
            }
            //Validar Envio
            $enviar = $request->getPost()->action;
            $accionState = true;
            if (!empty($enviar)) {
                $valid = new Identical(
                    array('token' => 'copy', 'strict' => false)
                );
                $valid->isValid($enviar); //false

                if (!$valid->isValid($enviar)) {
                    $accionState = false;
                    $messages['action'] = "No se Registro, revisar los datos ingresados.";
                }
            }

            //Comprobando validaciones
            if ($atributosState and $precioVentaState and $precioBeneficioState and $stockState
                and $vigenciaState and $accionState
            ) {
                $approved = true;
            }
        }

        return array($approved, $messages);
    }

    public function validarFechasVigencia($request, $fechas = array())
    {
        $approved = false;
        $messages = array();

        //Validar Vigencias
        $vigencia = $request->getPost()->vigencias;
        $vigenciaState = true;

        if (empty($fechas)) {
            if (is_array($vigencia) || is_object($vigencia)) {
                $fechaActual = strtotime(date("Y-m-d"));
                foreach ($vigencia as $value) {
                    $timestamp2 = strtotime($value);
                    if ($timestamp2 < $fechaActual) {
                        $vigenciaState = false;
                        $messages['vigencias'][] = "La fecha no es mayor o igual a la fecha actual";
                    } else {
                        $messages['vigencias'][] = "";
                    }
                }
            }
        } else {
            if (is_array($vigencia) || is_object($vigencia)) {
                $key = 0;
                foreach ($vigencia as $value) {
                    if (!isset($fechas[$key])) {
                        $fechaActual = strtotime(date("Y-m-d"));
                        $fechaObtenida = date("Y-m-d");
                    } else {
                        if (strtotime($fechas[$key]) >= strtotime(date("Y-m-d"))) {
                            $fechaActual = strtotime(date("Y-m-d"));
                            $fechaObtenida = date("Y-m-d");
                        } else {
                            $fechaActual = strtotime($fechas[$key]);
                            $fechaObtenida = $fechas[$key];
                        }
                    }
                    $timestamp2 = strtotime($value);
                    if ($timestamp2 < $fechaActual) {
                        $vigenciaState = false;
                        $messages['vigencias'][] = "La fecha no es mayor o igual a la fecha" .
                            " registrada anteriormente:  " . $fechaObtenida;
                    } else {
                        $messages['vigencias'][] = "";
                    }
                    $key++;
                }
            }
        }

        //Comprobando validaciones
        if ($vigenciaState) {
            $approved = true;
        }

        return array($approved, $messages);
    }

    public function generarArreglosJS($arreglo)
    {
        $temp = "[";
        $contador = 0;
        if (is_array($arreglo)) {
            foreach ($arreglo as $value) {
                $temp = $contador > 0 ? $temp . ", '" . addslashes($value) . "'" : $temp . "'" . addslashes($value) . "'";
                $contador++;
            }
        }
        $temp = $temp . "]";
        $arreglo = $temp;
        return $arreglo;
    }

    public function getSlug($cadena, $id)
    {
        $cadena = trim($cadena);
        $a = array(
            'S/.', '!', '¡', 'en vez de', ' por ', ' en ', ' el ', ' la ', ' + ', ' desde ', '®', '#', ':',
            ',', '/', ';', '*', '\\', '.', '$', '%', '@', '', '©', '£', '¥',
            '|', '°', '¬', '"', '&', '(', ')', '?', '¿', "'", '{', '}', '^', '~', '`', '<', '>',
            ' a ', ' e ', ' i ', ' o ', ' u ', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ',
            ' ', '-.', '.-', '--',
        );

        $b = array(
            '', '', '', 'x', '-', '-', '-', '-', '-', '', '', '', '',
            '-', '-', '-', '-', '-', '-', '-', '-', '', '', '', '', '',
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
            '-', '-', '-', '-', '-', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'ni',
            '-', '-', '-', '-',
        );

        return strtolower(str_ireplace($a, $b, strtolower($cadena))) . '-oferta-' . $id;
    }

    public function getDescripcionBusqueda($cadena)
    {
        $a = array(
            'S/.', '!', '¡', ' en vez de ', ' por ', ' en ', '+', ' desde ', '®', '#', ':', '.', ' el ', ' la ',
            ' los ', ' las ', ' un ', ' una ', ' unos ', ' unas ', ' y ', ' ni ', ' que ', ' ya ', ' bien ', ' sea ',
            ' pero ', ' mas ', ' sino ', ' porque ', ' pues ', ' ya que ', ' puesto que ', ' luego ', ' pues ',
            ' así que ', ' así pues ', ' si ', ' con tal que ', ' siempre que ', ' para ', ' para que ',
            ' a fin de que ', ' aunque ', ' por más que ', ' bien que ', ' de ', ' del ', ' al ',
            ' a ', ' e ', ' i ', ' o ', ' u ', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', ',',
            '/', ';', '*', '\\', '$', '%', '@', '', '©', '£', '¥', '|', '°', '¬', '"', '&', '(', ')', '?', '¿', "'",
            '{', '}', '^', '~', '`', '<', '>',
        );
        $b = array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', '',
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '',
            '', '', '', '',
        );
        $cadena = strtolower(str_ireplace($a, $b, strtolower($cadena)));
        return ucwords(strtolower(preg_replace('/\s\s+/', ' ', $cadena)));
    }

    public function deleteImagenAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $id = (int)$this->getRequest()->getPost('id');
        $response = $this->getResponse();

        $count = $this->getOfertaPuntosImagenTable()
            ->getAllImagesOfertaPuntos(
                $this->getOfertaPuntosImagenTable()->getOfertaPuntosImagen($id)->BNF2_Oferta_Puntos_id
            );
        if (count($count) > 1) {
            if ($this->getOfertaPuntosImagenTable()->getOfertaPuntosImagen($id)->Principal == 0) {
                $dato = $this->getOfertaPuntosImagenTable()->deleteOfertaPuntosImagen($id);

                $trozos = explode(".", $dato);
                $ext = end($trozos);

                $fullpath = './public/elements/oferta_puntos/' . $dato;
                if (file_exists($fullpath)) {
                    unlink($fullpath);
                    $fullpath2 = str_replace('.' . $ext, '', $fullpath) . '-medium' . '.' . $ext;
                    unlink($fullpath2);
                    $fullpath3 = str_replace('.' . $ext, '', $fullpath) . '-large' . '.' . $ext;
                    unlink($fullpath3);
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => true,
                                'data' => $dato
                            )
                        )
                    );
                }
            } else {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false,
                            'message' => 'No Puede Eliminar La Imagene Principal.'
                        )
                    )
                );
            }
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                        'message' => 'No Puede Eliminar Todas Las Imagenes.'
                    )
                )
            );
        }

        return $response;
    }

    public function principalImageAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $oferta_id = (int)$this->getRequest()->getPost('oferta_id');
        $id = (int)$this->getRequest()->getPost('id');

        $dato = $this->getOfertaPuntosImagenTable()->principalImagen($id, $oferta_id);

        $response = $this->getResponse();
        $response->setContent(
            Json::encode(
                array(
                    'response' => true,
                    'data' => $dato
                )
            )
        );

        return $response;
    }

    public function getDescargasByAtributoAction() {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $id = $this->getRequest()->getPost('id');
        $has_downloads = $this->getCuponPuntosTable()->hasCuponPuntosDescargasByAtributo($id);
        return $response->setContent(
            Json::encode(
                array(
                    'response' => true,
                    'cupon' => $has_downloads,
                )
            )
        );
    }
}
