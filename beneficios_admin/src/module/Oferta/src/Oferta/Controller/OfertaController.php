<?php

namespace Oferta\Controller;

use EmpresaCliente\Service\Resize;
use Oferta\Form\BuscarEmpresaForm;
use Oferta\Form\BuscarOfertaForm;
use Oferta\Form\OfertaForm;
use Oferta\Model\Busqueda;
use Cupon\Model\Cupon;
use Oferta\Model\Filter\BuscarEmpresaFilter;
use Oferta\Model\Filter\OfertaFilter;
use Oferta\Model\Imagen;
use Oferta\Model\Oferta;
use Oferta\Model\OfertaAtributos;
use Oferta\Model\OfertaCampaniaUbigeo;
use Oferta\Model\OfertaCategoriaUbigeo;
use Oferta\Model\OfertaEmpresaCliente;
use Oferta\Model\OfertaFormulario;
use Oferta\Model\OfertaRubro;
use Oferta\Model\OfertaSegmento;
use Oferta\Model\OfertaSubgrupo;
use Oferta\Model\OfertaUbigeo;
use Oferta\Model\TarjetasOferta;
use Oferta\Model\Data\BuscarEmpresaData;
use Paquete\Model\BolsaTotal;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\Validator\File\UploadFile;
use Zend\Validator\Date;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\Step;
use Zend\View\Model\ViewModel;
use Intervention\Image\ImageManager;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class OfertaController extends AbstractActionController
{
    const TIPO_OFERTA_DESCARGA = 1;
    const TIPO_OFERTA_PRESENCIA = 2;
    const TIPO_OFERTA_LEAD = 3;
    const ESTADO_OFERTA_CADUCADO = "Caducado";
    const ERROR_STOCK_SPLIT = "La cantidad de stock ingresada supera a la de la bolsa";

    const DIR_LOGS = './data/logs/image/';
    const NAME_LOG_IMAGE_TEMP = 'image_temp.log';
    const NAME_LOG_IMAGE_OFERTA = 'image_oferta.log';
    const NAME_LOG_IMAGE_TEMP_DELETE = 'image_temp_delete.log';
    const NAME_LOG_IMAGE_OFERTA_DELETE = 'image_oferta_delete.log';

    #region ObjectTables
    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Categoria\Model\Table\CategoriaTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Campania\Model\Table\CampaniaTable');
    }

    public function getRubroTable()
    {
        return $this->serviceLocator->get('Rubro\Model\Table\RubroTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getPaqueteEmpresaProveedorTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaqueteEmpresaProveedorTable');
    }

    public function getTipoPaqueteTable()
    {
        return $this->serviceLocator->get('Paquete\Model\TipoPaqueteTable');
    }

    public function getBolsaTotalTable()
    {
        return $this->serviceLocator->get('Paquete\Model\Table\BolsaTotalTable');
    }

    public function getTipoBeneficioTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\TipoBeneficioTable');
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

    public function getSubgrupoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\SubGrupoTable');
    }

    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
    }

    public function getOfertaUbigeoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaUbigeoTable');
    }

    public function getOfertaRubroTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaRubroTable');
    }

    public function getOfertaSegmentoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaSegmentoTable');
    }

    public function getOfertaSubgrupoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaSubgrupoTable');
    }

    public function getCategoriaUbigeoTable()
    {
        return $this->serviceLocator->get('Categoria\Model\Table\CategoriaUbigeoTable');
    }

    public function getOfertaCategoriaUbigeoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaCategoriaUbigeoTable');
    }

    public function getCampaniaUbigeoTable()
    {
        return $this->serviceLocator->get('Campania\Model\Table\CampaniaUbigeoTable');
    }

    public function getOfertaCampaniaUbigeoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaCampaniaUbigeoTable');
    }

    public function getImagenTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\ImagenTable');
    }

    public function getCuponTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponTable');
    }

    public function getOfertaEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaEmpresaClienteTable');
    }

    public function getOfertaAtributosTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaAtributosTable');
    }

    public function getBusquedaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\BusquedaTable');
    }
    #endregion

    #region Inicializacion
    public function inicializacion()
    {
        $comboemp = array();
        $combotp = array();
        $combotb = array();
        $comborub = array();
        $combocat = array();
        $combocam = array();
        $combopais = array();
        $comboubig = array();
        $comboseg = array();

        $combofemp = array();
        $comboftp = array();
        $comboftb = array();
        $combofrub = array();
        $combofcat = array();
        $combofcam = array();
        $combofpais = array();
        $combofubig = array();
        $combofseg = array();

        try {
            foreach ($this->getEmpresaTable()->getPaqueteEmpresas() as $empresa) {
                $comboemp[$empresa->id] = $empresa->NombreComercial . ' - ' . $empresa->RazonSocial .
                    ' - ' . $empresa->Ruc;
                $combofemp[$empresa->id] = [$empresa->id];
            }

            foreach ($this->getTipoPaqueteTable()->fetchAll() as $tipospaq) {
                $combotp[$tipospaq->id] = $tipospaq->NombreTipoPaquete;
                $comboftp[$tipospaq->id] = $tipospaq->id;
            }

            foreach ($this->getTipoBeneficioTable()->fetchAll() as $tiposben) {
                $combotb[$tiposben->id] = $tiposben->NombreBeneficio;
                $comboftb[$tiposben->id] = $tiposben->id;
            }

            foreach ($this->getRubroTable()->fetchAll() as $rubro) {
                $comborub[$rubro->id] = $rubro->Nombre;
                $combofrub[$rubro->id] = $rubro->id;
            }

            foreach ($this->getPaisTable()->fetchAll() as $pais) {
                $combopais[$pais->id] = $pais->NombrePais;
                $combofpais[$pais->id] = $pais->id;
            }

            foreach ($this->getCategoriaTable()->fetchAll() as $categoria) {
                $combocat[$categoria->id] = $categoria->Nombre;
                $combofcat[$categoria->id] = $categoria->id;
            }

            foreach ($this->getCampaniaTable()->fetchAll() as $campaña) {
                $combocam[$campaña->id] = $campaña->Nombre;
                $combofcam[$campaña->id] = $campaña->id;
            }

            foreach ($this->getUbigeoTable()->fetchAllDepartament() as $dato) {
                $comboubig[$dato->id] = $dato->Nombre;
                $combofubig[$dato->id] = $dato->id;
            }

            foreach ($this->getSegmentoTable()->fetchAll() as $dato) {
                $comboseg[$dato->id] = $dato->Nombre;
                $combofseg[$dato->id] = $dato->id;
            }
        } catch (\Exception $ex) {
            $comboemp = array();
            $combotp = array();
            $combotb = array();
            $comborub = array();
            $combopais = array();
            $combocat = array();
            $combocam = array();
            $comboubig = array();
            $comboseg = array();
        }

        $formulario['emp'] = $comboemp;
        $formulario['tip'] = $combotp;
        $formulario['tib'] = $combotb;
        $formulario['rub'] = $comborub;
        $formulario['cat'] = $combocat;
        $formulario['cam'] = $combocam;
        $formulario['pais'] = $combopais;
        $formulario['ubig'] = $comboubig;
        $formulario['seg'] = $comboseg;

        $filtro['emp'] = array_keys($combofemp);
        $filtro['tip'] = array_keys($comboftp);
        $filtro['tib'] = array_keys($comboftb);
        $filtro['rub'] = array_keys($combofrub);
        $filtro['cat'] = array_keys($combofcat);
        $filtro['cam'] = array_keys($combofcam);
        $filtro['pais'] = array_keys($combofpais);
        $filtro['ubig'] = array_keys($combofubig);
        $filtro['seg'] = $comboseg;

        return array($formulario, $filtro);
    }

    public function getempnormal($razon = null, $ruc = null)
    {
        $comboempnorm = array();
        try {
            foreach ($this->getEmpresaTable()->getEmpresaCliNorm($razon, $ruc) as $empresa) {
                $comboempnorm[] = $empresa;
            }
        } catch (\Exception $ex) {
            $comboempnorm = array();
        }
        return $comboempnorm;
    }

    public function getempcliente($razon = null, $ruc = null)
    {
        $comboempesp = array();
        try {
            foreach ($this->getEmpresaTable()->getEmpresaCliEsp($razon, $ruc) as $empresa) {
                $comboempesp[] = $empresa;
            }
        } catch (\Exception $ex) {
            return $comboempesp = array();
        }
        return $comboempesp;
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $busqueda = array(
            'Tipo' => 'TipoOferta',
            'Nombre' => 'NombreComercial',
            'Titulo' => 'Titulo',
            'DatoBeneficio' => 'DatoBeneficio',
            'Categoria' => 'BNF_Categoria.Nombre',
            'Campania' => 'BNF_Campanias.Nombre',
            'Asignaciones' => 'Asignaciones',
            'Activo' => 'Eliminado'
        );

        $lista = array();
        $tipo = null;
        $rubro = null;
        $empresa = null;
        $campania = null;
        $categoria = null;
        $nombre = null;

        $combocam = array();
        $combocat = array();
        $comborub = array();
        $comboemp = array();
        $combotip = array();
        $ofertaNombres = array();

        //Llenar el formulario de busqueda
        try {
            foreach ($this->getCategoriaTable()->fetchAll() as $dato) {
                $combocat[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $combocat = array();
        }

        try {
            foreach ($this->getCampaniaTable()->fetchAll() as $dato) {
                $combocam[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $combocam = array();
        }

        try {
            foreach ($this->getRubroTable()->getRubroDetails() as $dato) {
                $comborub[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $comborub = array();
        }

        try {
            foreach ($this->getEmpresaTable()->getPaqueteEmpresas() as $dato) {
                $comboemp[$dato->id] = $dato->NombreComercial . ' - ' . $dato->RazonSocial .
                    ' - ' . $dato->Ruc;
            }
        } catch (\Exception $ex) {
            return $comboemp = array();
        }

        try {
            foreach ($this->getTipoPaqueteTable()->fetchAll() as $tipospaq) {
                $combotip[$tipospaq->id] = $tipospaq->NombreTipoPaquete;
            }
        } catch (\Exception $ex) {
            return $combotip = array();
        }

        try {
            foreach ($this->getOfertaTable()->getOfertasTitulo() as $oferta) {
                $ofertaNombres[$oferta->Titulo] = $oferta->Titulo;
            }
        } catch (\Exception $ex) {
            return $combotip = array();
        }

        $value['emp'] = $comboemp;
        $value['tip'] = $combotip;
        $value['cam'] = $combocam;
        $value['cat'] = $combocat;
        $value['rub'] = $comborub;
        $value['nom'] = $ofertaNombres;


        $form = new BuscarOfertaForm('buscar', $value);
        //Obteniendo parametros de busqueda
        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = $request->getPost()->Empresa ? $request->getPost()->Empresa : null;
            $tipo = $request->getPost()->Tipo ? $request->getPost()->Tipo : null;
            $rubro = $request->getPost()->Rubro ? $request->getPost()->Rubro : null;
            $campania = $request->getPost()->Campania ? $request->getPost()->Campania : null;
            $categoria = $request->getPost()->Categoria ? $request->getPost()->Categoria : null;
            $nombre = $request->getPost()->Nombre ? $request->getPost()->Nombre : null;
            $form->setData($request->getPost());
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $tipo = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $rubro = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : null;
            $categoria = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
            $campania = $this->params()->fromRoute('q5') ? $this->params()->fromRoute('q5') : null;
            $nombre = $this->params()->fromRoute('q6') ? $this->params()->fromRoute('q6') : null;
            $form->setData(
                array(
                    'Tipo' => $tipo,
                    'Rubro' => $rubro,
                    'Empresa' => $empresa,
                    'Categoria' => $categoria,
                    'Campania' => $campania,
                    'Nombre' => $nombre
                )
            );
        }

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
            $order_by = 'BNF_Oferta.FechaCreacion';
        }

        $paginator = $this->getOfertaTable()
            ->getOfertaDetails($empresa, $tipo, $rubro, $categoria, $campania, $nombre, $order_by, $order);
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        foreach ($paginator as $oferta) {
            $listoferta = array();
            $categoriadata = array();
            $campaniadata = array();
            $listoferta['id'] = $oferta->id;
            $listoferta['Nombre'] = $oferta->Nombre;
            $listoferta['Titulo'] = $oferta->Titulo;
            $listoferta['DatoBeneficio'] = $oferta->DatoBeneficio;
            $listoferta['Eliminado'] = (int)$oferta->Eliminado;
            $listoferta['TipoPaquete'] = $oferta->TipoOferta;
            $listoferta['Asignaciones'] = (int)$oferta->Asignaciones;

            try {
                $ofertacategoria = $this->getOfertaCategoriaUbigeoTable()->getOfertaCategoriaUbigeos($oferta->id);
                foreach ($ofertacategoria as $valor) {
                    $categoriadata[] = $valor->Nombre;
                }
            } catch (\Exception $ex) {
                $categoriadata = array();
            }

            try {
                $ofertacampania = $this->getOfertaCampaniaUbigeoTable()->getOfertaCampaniaUbigeos($oferta->id);
                foreach ($ofertacampania as $valor) {
                    $campaniadata[] = $valor->Nombre;
                }
            } catch (\Exception $ex) {
                $campaniadata = array();
            }

            $listoferta['Categoria'] = $categoriadata;
            $listoferta['Campania'] = $campaniadata;
            $lista[] = $listoferta;
        }

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'oferta' => 'active',
                'olistar' => 'active',
                'ofertas' => $paginator,
                'ofertal' => $lista,
                'order_by' => $order_by_o,
                'order' => $order,
                'form' => $form,
                'p' => $page,
                'q1' => $empresa,
                'q2' => $tipo,
                'q3' => $rubro,
                'q4' => $categoria,
                'q5' => $campania,
                'q6' => $nombre,
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
        $acceptance = true;
        $approvedStockSplit = true;
        $mensajes = array();
        $imagenesXAsignar = array();
        $imagenesBanner = null;
        $camposXAsignar = null;
        $camposXAsignarReq = null;
        $tarjetasAsignar = null;
        $tipoAtributo = null;

        $messageAttrib = 0;
        $totalAtributos = 0;
        $atributos = "";
        $stocks = "";
        $beneficios = "";
        $vigencias = "";
        $errorStockSplit = "";
        $atributosMessage = array();
        $stocksMessage = array();
        $beneficiosMessage = array();
        $vigenciasMessage = array();

        $datos = $this->inicializacion();

        $formularioTable = $this->serviceLocator->get('Oferta\Model\Table\FormularioTable');
        $tarjetasTable = $this->serviceLocator->get('Oferta\Model\Table\TarjetasTable');

        $formulario = $formularioTable->fetchAll();
        $form_config = $formularioTable->fetchAll();
        $tarjetas = $tarjetasTable->fetchAll();

        $form = new OfertaForm('registrar', $datos[0]);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $imagenesXAsignar = $request->getPost()->Imagen;
            $imagenesBanner = $request->getPost()->banner;
            $validate = new OfertaFilter();

            $files = $this->getRequest()->getFiles()->toArray();
            $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $files
            );

            $tipoAtributo = !empty($post["TipoAtributo"]) ? $post["TipoAtributo"] : null;
            if (!empty($post["Tipo"]) and !empty($post["Empresa"])) {
                $bolsa = $this->getBolsaTotalTable()->getBolsaTotal($post["Tipo"], $post["Empresa"]);

                if ($post["Tipo"] == 1 || $post["Tipo"] == 2) {
                    if (empty($tipoAtributo)) {
                        $minimo = 1;
                    } else {
                        $minimo = 0;
                    }
                } else {
                    $minimo = 1;
                }
            } else {
                $bolsa = new BolsaTotal();
                $bolsa->BolsaActual = 0;
                $minimo = 1;
            }

            $form->setInputFilter(
                $validate->getInputFilter($datos[1], $bolsa->BolsaActual, $minimo, $post["Tipo"], 0, false, $tipoAtributo)
            );

            if (count($request->getPost()->Imagen) < 1) {
                $mensajes['image'] = "No hay por lo menos imagen adjunta.";
                $mensajes['imagec'] = 'has-error';
                $acceptance = false;
            } elseif ($request->getPost()->principal == null) {
                $mensajes['image'] = "No hay una Imagen Principal seleccionada.";
                $mensajes['imagec'] = 'has-error';
                $acceptance = false;
            }

            if ($tipoAtributo == "Split") {
                $postAtributos = array();
                foreach ($request->getPost()->atributos as $key => $item) {
                    $postAtributos[$key] = preg_replace('/\s+/', ' ', trim($item));
                }
                $request->getPost()->atributos = $postAtributos;
                //Validar Campos
                $resultados = $this->validarCampos($request);
                $approved = $resultados[0];
                $messageAttrib = $resultados[1];

                //Validar Fechas

                if ($request->getPost()->Tipo != $this::TIPO_OFERTA_LEAD) {
                    $resultadosFechas = $this->validarFechasVigencia($request);
                    $approvedFechas = $resultadosFechas[0];
                    $messageAttrib = array_merge($messageAttrib, $resultadosFechas[1]);
                } else {
                    $approvedFechas = true;
                }


                $stockTotalAtributo = 0;
                foreach ($request->getPost()->stocks as $dataStock) {
                    $stockTotalAtributo = $stockTotalAtributo + $dataStock;
                }
                if ($stockTotalAtributo > $bolsa->BolsaActual) {
                    $approvedStockSplit = false;
                }

            } else {
                $approved = true;
                $approvedFechas = true;
                $approvedStockSplit = true;
            }
            $form->setData($post);
            $camposXAsignar = $request->getPost()->form_imput;
            $camposXAsignarReq = $request->getPost()->form_imput_req;
            $tarjetasAsignar = $request->getPost()->tarjetas_input;


            if ($form->isValid() and $acceptance and $approved and $approvedFechas and $approvedStockSplit) {

                $oferta = new Oferta();
                $datoDepartamento = $post['Departamento'];
                $datoRubro = $post['Rubro'];
                $datoSegmento = $post['Segmento'];
                $datoCategoria = $post['Categoria'];
                $datoCampania = $post['Campania'];
                $datoPais = $post['Pais'];

                $oferta->exchangeArray($post);
                $oferta->BNF_BolsaTotal_Empresa_id = $post['Empresa'];
                $oferta->BNF_BolsaTotal_TipoPaquete_id = $post['Tipo'];

                #region Calculamos la Fecha Cuando es del tipo Presencia
                if ($post['Tipo'] == $this::TIPO_OFERTA_PRESENCIA) {
                    if ($post['TipoAtributo'] == "Split") {
                        $fecha = date($oferta->FechaInicioPublicacion);
                        $stockFecha = 0;
                        foreach ($post['stocks'] as $value) {
                            $stockFecha = $stockFecha + $value;
                        }

                        $nuevaFecha = strtotime('+' . ($stockFecha) . 'day', strtotime($fecha));
                        $nuevaFecha = date('Y-m-j', $nuevaFecha);
                        $oferta->FechaFinPublicacion = $nuevaFecha;
                    } else {
                        $fecha = date($oferta->FechaInicioPublicacion);
                        $nuevaFecha = strtotime('+' . ($oferta->Stock) . 'day', strtotime($fecha));
                        $nuevaFecha = date('Y-m-j', $nuevaFecha);
                        $oferta->FechaFinPublicacion = $nuevaFecha;
                    }
                }
                #endregion

                $oferta->Nombre = trim($request->getPost()->Nombre);
                $oferta->Titulo = trim($request->getPost()->Titulo);
                $oferta->TituloCorto = trim($request->getPost()->TituloCorto);
                $oferta->SubTitulo = trim($request->getPost()->SubTitulo);
                $oferta->Slug = $request->getPost()->Titulo;
                $oferta->StockInicial = (int)$request->getPost()->Stock;

                $id = $this->getOfertaTable()->saveOferta($oferta);

                $oferta->id = $id;
                $oferta->Slug = $this->getSlug($request->getPost()->Titulo, $id);

                $this->getOfertaTable()->saveOferta($oferta);

                #region Guardamos los datos de Atributos y Cupones
                if ($request->getPost()->TipoAtributo == "Split") {
                    foreach ($post['atributos'] as $key => $value) {
                        $ofertaAtributo = new OfertaAtributos();
                        $ofertaAtributo->BNF_Oferta_id = $id;
                        $ofertaAtributo->NombreAtributo = $value;
                        $ofertaAtributo->Stock = $post['stocks'][$key];
                        $ofertaAtributo->StockInicial = $post['stocks'][$key];

                        $ofertaAtributo->FechaVigencia = ($request->getPost()->Tipo != $this::TIPO_OFERTA_LEAD) ?
                            null : $post['vigencias'][$key];
                        $ofertaAtributo->FechaVigencia = null;


                        $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                        $ofertaAtributo->Eliminado = 0;
                        $atributo_id = $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);

                        if ($post['Tipo'] == $this::TIPO_OFERTA_DESCARGA) {
                            for ($i = 0; $i < $post['stocks'][$key]; $i++) {
                                $cupon = new Cupon();
                                $cupon->BNF_Oferta_id = $id;
                                $cupon->BNF_Oferta_Atributo_id = $atributo_id;
                                $cupon->EstadoCupon = 'Creado';
                                $this->getCuponTable()->saveCupon($cupon);
                            }
                        }

                        $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotal($post['Tipo'], $post['Empresa']);
                        $bolsaTotal->BolsaActual = $bolsaTotal->BolsaActual - $post['stocks'][$key];
                        $this->getBolsaTotalTable()->editBolsa($bolsaTotal);
                    }
                } else {
                    if ($post['Tipo'] == $this::TIPO_OFERTA_DESCARGA) {
                        for ($i = 0; $i < $oferta->Stock; $i++) {
                            $cupon = new Cupon();
                            $cupon->BNF_Oferta_id = $id;
                            $cupon->EstadoCupon = 'Creado';
                            $this->getCuponTable()->saveCupon($cupon);
                        }
                    }

                    $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotal($post['Tipo'], $post['Empresa']);
                    $bolsaTotal->BolsaActual = $bolsaTotal->BolsaActual - $post['Stock'];
                    $this->getBolsaTotalTable()->editBolsa($bolsaTotal);
                }
                #endregion

                #region Guardamos datos de Oferta Rubro
                $ofertaRubro = new OfertaRubro();
                $ofertaRubro->BNF_Rubro_id = $datoRubro;
                $ofertaRubro->BNF_Oferta_id = $id;
                $ofertaRubro->Eliminado = '0';
                $this->getOfertaRubroTable()->saveOfertaRubro($ofertaRubro);
                #endregion

                #region Guardamos datos de Oferta Ubigeo
                foreach ($datoDepartamento as $depa) {
                    $ofertaUbigeo = new OfertaUbigeo();
                    $ofertaUbigeo->BNF_Ubigeo_id = $depa;
                    $ofertaUbigeo->BNF_Oferta_id = $id;
                    $ofertaUbigeo->Eliminado = '0';
                    $this->getOfertaUbigeoTable()->saveOfertaUbigeo($ofertaUbigeo);
                }
                #endregion

                #region Guardados datos de Oferta Segmento
                foreach ($datoSegmento as $seg) {
                    $ofertaSegmento = new OfertaSegmento();
                    $ofertaSegmento->BNF_Segmento_id = $seg;
                    $ofertaSegmento->BNF_Oferta_id = $id;
                    $ofertaSegmento->Eliminado = '0';
                    $this->getOfertaSegmentoTable()->saveOfertaSegmento($ofertaSegmento);
                }
                #endregion

                #region Guardamos los datos de la Oferta Categoria Ubigeo
                foreach ($datoCategoria as $cate) {
                    $categoriaUbigeo = $this->getCategoriaUbigeoTable()
                        ->getCategoriaUbigeoPais($cate, $datoPais);

                    $ofertaCategoriaUbigeo = new OfertaCategoriaUbigeo();
                    $ofertaCategoriaUbigeo->BNF_CategoriaUbigeo_id = $categoriaUbigeo->id;
                    $ofertaCategoriaUbigeo->BNF_Oferta_id = $id;
                    $ofertaCategoriaUbigeo->Eliminado = '0';
                    $this->getOfertaCategoriaUbigeoTable()->saveOfertaCategoriaUbigeo($ofertaCategoriaUbigeo);
                }
                #endregion

                #region Guardamos los datos de la Oferta Campaña Ubigeo
                if (!empty($datoCampania)) {
                    foreach ($datoCampania as $camp) {
                        $campaniaUbigeo = $this->getCampaniaUbigeoTable()
                            ->getCampaniaUbigeoPais($camp, $datoPais);

                        $ofertaCampaniaUbigeo = new OfertaCampaniaUbigeo();
                        $ofertaCampaniaUbigeo->BNF_CampaniaUbigeo_id = $campaniaUbigeo->id;
                        $ofertaCampaniaUbigeo->BNF_Oferta_id = $id;
                        $ofertaCampaniaUbigeo->Eliminado = '0';
                        $this->getOfertaCampaniaUbigeoTable()->saveOfertaCampaniaUbigeo($ofertaCampaniaUbigeo);
                    }
                }
                #endregion

                #region Guardar Imagen
                $principal = (int)$request->getPost()->principalimage;
                foreach ($request->getPost()->Imagen as $key => $img) {
                    $imagenOferta = new Imagen();
                    $imagenOferta->Nombre = $img;
                    $imagenOferta->BNF_Oferta_id = $id;
                    if ($key == $principal) {
                        $imagenOferta->Principal = '1';
                    } else {
                        $imagenOferta->Principal = '0';
                    }
                    $this->getImagenTable()->saveImagen($imagenOferta);

                    $logger = new Logger;
                    $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_OFERTA);
                    $message = "Imagen asignada a la oferta " . $id . ": " . $img;
                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);
                }
                #endregion

                #region Crear Busqueda
                $busquedaTable = $this->serviceLocator->get('Oferta\Model\Table\BusquedaTable');
                $busqueda = new Busqueda();
                $busqueda->BNF_Oferta_id = $id;
                $busqueda->TipoOferta = 1;
                $busqueda->Descripcion = $this->getDescripcionBusqueda($request->getPost()->Titulo);
                $busquedaTable->saveBusqueda($busqueda);
                #endregion

                #region Crear Formulario
                if ($post['Tipo'] == $this::TIPO_OFERTA_LEAD) {
                    $form_imput = $request->getPost()->form_imput;
                    $form_imput_req = $request->getPost()->form_imput_req;
                    $ofertaformulariotable = $this->serviceLocator->get('Oferta\Model\Table\OfertaFormularioTable');
                    $ofertaformulario = new OfertaFormulario();
                    $ofertaformulario->BNF_Oferta_id = $id;
                    $form_array = array();
                    foreach ($formulario as $dato) {
                        $form_array[$dato->id] = $dato->id;
                    }
                    //Agregamos el campo CorreoContacto de Lead
                    if (isset($request->getPost()->CorreoContacto)) {
                        $ofertaformulario->BNF_Formulario_id = 12;
                        $ofertaformulario->Descripcion = $request->getPost()->CorreoContacto;
                        $ofertaformulario->Activo = '1';
                        $ofertaformulario->Requerido = '0';
                        $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                        unset($form_array[12]);
                    }
                    //Agregamos el banner de Lead
                    if (count($request->getPost()->banner) <= 1) {
                        $ofertaformulario->BNF_Formulario_id = 1;
                        $ofertaformulario->Descripcion = $request->getPost()->banner;
                        $ofertaformulario->Activo = '1';
                        $ofertaformulario->Requerido = '0';
                        $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                        unset($form_array[1]);

                        $logger = new Logger;
                        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_OFERTA);
                        $message = "Banner asignado a la oferta " . $id . ": " . $request->getPost()->banner;
                        $logger->addWriter($writer);
                        $logger->log(Logger::INFO, $message);
                    }
                    //Agregamos el mensaje de Lead
                    if (isset($request->getPost()->textobanner)) {
                        $ofertaformulario->BNF_Formulario_id = 13;
                        $ofertaformulario->Descripcion = $request->getPost()->textobanner;
                        $ofertaformulario->Activo = '1';
                        $ofertaformulario->Requerido = '0';
                        $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                        unset($form_array[13]);
                    }
                    //Agregamos los campos seleccionados del formulario lead
                    foreach ($form_imput as $dato) {
                        unset($form_array[$dato]);
                        $ofertaformulario->BNF_Formulario_id = $dato;
                        $ofertaformulario->Descripcion = null;
                        $ofertaformulario->Activo = '1';
                        $ofertaformulario->Requerido = (in_array($dato, $form_imput_req)) ? '1' : '0';
                        $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                    }
                    //Agregamos los campos desactivados del formulario lead
                    foreach ($form_array as $dato) {
                        $ofertaformulario->BNF_Formulario_id = $dato;
                        $ofertaformulario->Descripcion = null;
                        $ofertaformulario->Activo = '0';
                        $ofertaformulario->Requerido = (in_array($dato, $form_imput_req)) ? '1' : '0';
                        $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                    }
                    if ($form_imput == null) {
                        foreach ($form_array as $dato) {
                            $ofertaformulario->BNF_Formulario_id = $dato;
                            $ofertaformulario->Descripcion = null;
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = (in_array($dato, $form_imput_req)) ? '1' : '0';
                            $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                        }
                    }
                }
                #endregion

                #region Crear Relacion con Tarjetas
                $tarjetasOfertaTable = $this->serviceLocator->get('Oferta\Model\Table\TarjetasOfertaTable');

                if (is_object($tarjetasAsignar) || is_array($tarjetasAsignar)) {
                    foreach ($tarjetasAsignar as $tarjeta) {
                        $tarjetasOferta = new TarjetasOferta();
                        $tarjetasOferta->BNF_Tarjetas_id = $tarjeta;
                        $tarjetasOferta->BNF_Oferta_id = $id;
                        $tarjetasOferta->Eliminado = 0;
                        $tarjetasOfertaTable->saveTarjetasOferta($tarjetasOferta);
                    }
                }
                #endregion

                //Confirmacion del Registro
                $confirm[] = "Oferta Registrada.";
                $type = "success";
                $form = new OfertaForm('registrar', $datos[0]);
                $imagenesXAsignar = null;
                $imagenesBanner = null;
                $camposXAsignar = null;
                $camposXAsignarReq = null;
                $tarjetasAsignar = null;
            } else {
                $confirm[] = 'No se Registro, revisar los datos ingresados';
                $type = "danger";

                if (!$approvedStockSplit) {
                    $errorStockSplit = $this::ERROR_STOCK_SPLIT;
                }

                if ($request->getPost()->TipoAtributo == "Split") {
                    $totalAtributos = count($request->getPost()->atributos);

                    //Datos
                    $atributos = $this->generarArreglosJS($request->getPost()->atributos);
                    $stocks = $this->generarArreglosJS($request->getPost()->stocks);
                    $beneficios = $this->generarArreglosJS($request->getPost()->beneficios);

                    $vigencias = $this->generarArreglosJS($request->getPost()->vigencias);
                    $vigenciasMessage = $this->generarArreglosJS($messageAttrib["vigencias"]);


                    //Mensajes de Error
                    $atributosMessage = $this->generarArreglosJS($messageAttrib["atributos"]);
                    $stocksMessage = $this->generarArreglosJS($messageAttrib["stocks"]);
                    $beneficiosMessage = $this->generarArreglosJS($messageAttrib["beneficios"]);


                }
            }
        }

        if ($imagenesXAsignar == null) {
            $imagenesXAsignar = array();
        }


        return new ViewModel(
            array(
                'beneficios' => 'active',
                'oferta' => 'active',
                'oadd' => 'active',
                'form' => $form,
                'confirm' => $confirm,
                'type' => $type,
                'menssage' => $mensajes,
                'ofertas' => null,
                'imagenesXAsignar' => $imagenesXAsignar,
                'imagenebanner' => $imagenesBanner,
                'form_config' => $form_config,
                'tarjetas' => $tarjetas,
                'camposXAsignar' => $camposXAsignar,
                'camposXAsignarReq' => $camposXAsignarReq,
                'tarjetasAsignar' => $tarjetasAsignar,
                'totalAtributos' => $totalAtributos,
                'atributos' => $atributos,
                'stocks' => $stocks,
                'datoBeneficios' => $beneficios,
                'vigencias' => $vigencias,
                'atributosMessage' => $atributosMessage,
                'stocksMessage' => $stocksMessage,
                'vigenciasMessage' => $vigenciasMessage,
                'beneficiosMessage' => $beneficiosMessage,
                'errorStockSplit' => $errorStockSplit,
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

        $datos = $this->inicializacion();
        $stockAct = 0;
        $estadoAnt = null;
        $ofertaEdit = null;
        $rubroData = null;
        $paisData = null;
        $acceptance = true;
        $approvedStockSplit = true;
        $segmentoData = array();
        $departamentoData = array();
        $categoriaData = array();
        $campaniaData = array();
        $imagenData = array();
        $imagenesXAsignar = array();
        $camposXAsignar = null;
        $camposXAsignarReq = null;
        $imagenesBanner = null;
        $mensajes = array();
        $empresaInactiva = null;
        $id_empresa = null;
        $disabled = false;

        $tarjetasAsignar = array();
        $tarjetasData = null;
        $tipoAtributo = null;
        $messageAttrib = array();
        $fechasAnteriores = null;

        $stocksBolsa = array();
        $atributosAnt = array();
        $atributoStockAnt = array();
        $atributoStockInicialAnt = array();
        $fechaAnt = null;
        $totalAtributos = 0;
        $atributosId = "";
        $atributosIdAnt = "";
        $atributos = "";
        $tituloAnterior = "";
        $slugAnterior = "";
        $stocks = "";
        $stockAnterior = "";
        $stockIniciales = "";
        $beneficios = "";
        $vigencias = "";
        $errorStockSplit = "";
        $atributosMessage = array();
        $stocksMessage = array();
        $beneficiosMessage = array();
        $vigenciasMessage = array();

        $confirm = null;
        $type = null;
        $slug = null;
        $copyState = null;
        $bolsa = new BolsaTotal();
        $config = $this->getServiceLocator()->get('Config');

        #region Load Data
        try {
            $ofertaEdit = $this->getOfertaTable()->getOferta($id);
            $tipoAtributosAnt = $ofertaEdit->TipoAtributo;
            $empresaInactiva = $this->getEmpresaTable()->getEmpresaProvActiva($ofertaEdit->BNF_BolsaTotal_Empresa_id);
            $tituloOferta = $this->getDescripcionBusqueda($ofertaEdit->Titulo);
            $ofertaAnt = $id;

            if ($empresaInactiva->TEliminado == 1) {
                $id_empresa = $empresaInactiva->id;
                $empresaInactiva =
                    $empresaInactiva->NombreComercial .
                    ' - ' . $empresaInactiva->RazonSocial .
                    ' - ' . $empresaInactiva->Ruc;
                $disabled = true;
            } else {
                $empresaInactiva = null;
            }

            $slug = $ofertaEdit->Slug;

            $ofertaRubro = $this->getOfertaRubroTable()->getOfertaRubros($id);
            $rubroData = $ofertaRubro->BNF_Rubro_id;
            $estadoAnt = (int)$ofertaEdit->Eliminado;

            $rubros = $this->getRubroTable()->getRubro($rubroData);
            if ($ofertaEdit->TipoAtributo == "Split") {
                $dataAtributos = $this->getOfertaAtributosTable()->getAllOfertaAtributos($id);

                $totalAtributos = count($dataAtributos);
                foreach ($dataAtributos as $atributo) {
                    $atributosId[] = $atributo->id;
                    $atributosIdAnt[] = $atributo->id;
                    $atributos[] = $atributo->NombreAtributo;
                    $atributosAnt[] = $atributo->NombreAtributo;
                    $stocks[] = (int)$atributo->Stock;
                    $stocksBolsa[] = (int)$atributo->Stock;
                    $atributoStockAnt[] = (int)$atributo->Stock;
                    $stockIniciales[] = (int)$atributo->StockInicial;
                    $atributoStockInicialAnt[] = (int)$atributo->StockInicial;
                    $beneficios[] = $atributo->DatoBeneficio;
                    $vigencias[] = $atributo->FechaVigencia;
                    $fechasAnteriores[] = $atributo->FechaVigencia;
                }


                $atributosId = $this->generarArreglosJS($atributosId);
                $atributos = $this->generarArreglosJS($atributos);
                $stockAnterior = $this->generarArreglosJS($stocks);
                $stocks = $this->generarArreglosJS($stocks);
                $stockIniciales = $this->generarArreglosJS($stockIniciales);
                $beneficios = $this->generarArreglosJS($beneficios);
                $vigencias = $this->generarArreglosJS($vigencias);

            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('oferta', array('action' => 'index'));
        }

        try {
            $ofertaSegmentos = $this->getOfertaSegmentoTable()->getOfertaSegmentos($id);
            foreach ($ofertaSegmentos as $valor) {
                $segmentoData[] = $valor->BNF_Segmento_id;
            }
        } catch (\Exception $ex) {
            return $segmentoData;
        }

        try {
            $ofertaDepartamento = $this->getOfertaUbigeoTable()->getOfertaUbigeos($id);
            foreach ($ofertaDepartamento as $valor) {
                $departamentoData[] = $valor->BNF_Ubigeo_id;
                $paisData = $this->getPaisTable()->getPaisByDepartament($valor->BNF_Ubigeo_id);
            }
        } catch (\Exception $ex) {
            return $departamentoData;
        }

        try {
            $ofertaCategoria = $this->getOfertaCategoriaUbigeoTable()->getOfertaCategoriaUbigeos($id);
            foreach ($ofertaCategoria as $valor) {
                $categoriaData[] = $valor->Categoria;
            }
        } catch (\Exception $ex) {
            return $categoriaData;
        }

        try {
            $ofertaCampania = $this->getOfertaCampaniaUbigeoTable()->getOfertaCampaniaUbigeos($id);
            foreach ($ofertaCampania as $valor) {
                $campaniaData[] = $valor->Campania;
            }
        } catch (\Exception $ex) {
            return $campaniaData;
        }

        try {
            $imagenData = $this->getImagenTable()->getImagenOferta($id);
        } catch (\Exception $ex) {
            return $imagenData;
        }

        $formularioTable = $this->serviceLocator->get('Oferta\Model\Table\FormularioTable');
        $formulario = $formularioTable->fetchAll();

        $ofertaformulariotable = $this->serviceLocator->get('Oferta\Model\Table\OfertaFormularioTable');
        $form_config = $ofertaformulariotable->getFormularios($id);

        $tarjetasTable = $this->serviceLocator->get('Oferta\Model\Table\TarjetasTable');
        $tarjetas = $tarjetasTable->fetchAll();

        $tarjetasOfertaTable = $this->serviceLocator->get('Oferta\Model\Table\TarjetasOfertaTable');
        try {
            $tarjetasData = $tarjetasOfertaTable->getAllTarjetasOferta($id);
        } catch (\Exception $ex) {
            return $tarjetasData;
        }

        if (!empty($tarjetasData)) {
            foreach ($tarjetasData as $data) {
                array_push($tarjetasAsignar, $data->BNF_Tarjetas_id);
            }
        }

        if (!array_key_exists($rubros->id, $datos[0]['rub'])) {
            $datos[0]['rub'][$rubros->id] = $rubros->Nombre;
            $datos[1]['rub'][] = (int)$rubros->id;
        }

        $form = new OfertaForm('registrar', $datos[0]);
        $oferta = (array)$ofertaEdit;


        $stockAnt = (int)$oferta['Stock'];
        $stockInicial = (int)$oferta['StockInicial'];
        $tipoAnt = $oferta['BNF_BolsaTotal_TipoPaquete_id'];
        $empAnt = $oferta['BNF_BolsaTotal_Empresa_id'];
        $TipoAtributoAnt = $oferta['TipoAtributo'];
        $tituloAnterior = $oferta['Titulo'];
        $slugAnterior = $oferta['Slug'];
        $oferta['Empresa'] = $oferta['BNF_BolsaTotal_Empresa_id'];
        $oferta['Tipo'] = $oferta['BNF_BolsaTotal_TipoPaquete_id'];
        unset($oferta['BNF_BolsaTotal_Empresa_id']);
        unset($oferta['BNF_BolsaTotal_TipoPaquete_id']);

        $oferta['Rubro'] = $rubroData;
        $oferta['Segmento'] = $segmentoData;
        $oferta['Departamento'] = $departamentoData;
        $oferta['Pais'] = $paisData[0]['id'];
        $oferta['Categoria'] = $categoriaData;
        $oferta['Campania'] = $campaniaData;
        $oferta['Stock'] = (int)$oferta['Stock'];
        $oferta['StockInicial'] = (int)$oferta['StockInicial'];

        $oferta['FechaFinVigencia'] = (!empty($oferta['FechaFinVigencia']))
            ? date_format(date_create($oferta['FechaFinVigencia']), 'Y-m-d') : null;
        $oferta['FechaInicioPublicacion'] = (!empty($oferta['FechaInicioPublicacion']))
            ? date_format(date_create($oferta['FechaInicioPublicacion']), 'Y-m-d') : null;
        $oferta['FechaFinPublicacion'] = (!empty($oferta['FechaFinPublicacion']))
            ? date_format(date_create($oferta['FechaFinPublicacion']), 'Y-m-d') : null;
        $oferta['Eliminado'] = (int)$oferta['Eliminado'];

        $form->setData($oferta);
        $form->get('submit')->setAttribute('value', 'Editar');

        foreach ($ofertaformulariotable->getFormularios($id) as $dato) {
            if ($dato->Descripcion == 'CorreoContacto') {
                $form->get('CorreoContacto')->setValue($dato->valor);
            }
            if ($dato->Descripcion == 'textobanner') {
                $form->get('textobanner')->setValue($dato->valor);
            }
        }

        #endregion

        $request = $this->getRequest();

        if ($request->isPost()) {


            if ($oferta['TipoEspecial'] == 1) {
                $request->getPost()->TipoAtributo = ($oferta['TipoAtributo']) ? $oferta['TipoAtributo'] : '';
                $request->getPost()->Tipo = ($oferta['Tipo']) ? $oferta['Tipo'] : '';
            }
            $imagenesXAsignar = $request->getPost()->Imagen;
            $imagenesBanner = $request->getPost()->banner;
            $principal = (int)$request->getPost()->principalimage;

            $validate = new OfertaFilter();

            $files = $this->getRequest()->getFiles()->toArray();
            $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $files
            );

            $copyState = $request->getPost()->action == "copy" ? true : false;

            if ($copyState) {
                $type = 'info';
                $confirm[] = 'Debe actualizar el stock de la oferta';
                $form->get('submit')->setAttribute('value', 'Guardar');
                $form->get('Stock')->setAttribute('value', 0);
                $form->get('StockInicial')->setAttribute('value', 0);
                $stockAnt = 0;
                $stocks = '';
                $stockIniciales = '';
                $stockAnterior = '';

                $nuevo_formulario = array();
                foreach ($form_config as $value) {
                    if ($value->Descripcion == 'banner') {
                        $path = './public/elements/banners/';
                        $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);
                        $ext = explode('.', $value->valor);

                        $manager = new ImageManager(array('driver' => 'imagick'));
                        $img = $manager->make($path . $value->valor);

                        $resize = new Resize();
                        $resize->rename($path, $img, $ext[1], $fileName, '');
                        $value->valor = $fileName . '.' . $ext[1];
                    }
                    array_push($nuevo_formulario, $value);
                }
                $form_config = $nuevo_formulario;

                $view = new ViewModel();
                $view->setVariables(
                    array(
                        'beneficios' => 'active',
                        'oferta' => 'active',
                        'oadd' => 'active',
                        'id' => $id,
                        'form' => $form,
                        'stockant' => $stockAnt,
                        'estado' => $estadoAnt,
                        'stockInicial' => $stockInicial,
                        'imagenes' => $imagenData,
                        'confirm' => $confirm,
                        'type' => $type,
                        'ofertas' => null,
                        'imagenesXAsignar' => $imagenesXAsignar,
                        'imagenebanner' => $imagenesBanner,
                        'menssage' => $mensajes,
                        'form_config' => $form_config,
                        'empresainactiva' => $empresaInactiva,
                        'id_empresa' => $id_empresa,
                        'camposXAsignar' => $camposXAsignar,
                        'camposXAsignarReq' => $camposXAsignarReq,
                        'tarjetas' => $tarjetas,
                        'tarjetasAsignar' => $tarjetasAsignar,
                        'totalAtributos' => $totalAtributos,
                        'atributosId' => $atributosId,
                        'atributos' => $atributos,
                        'stocks' => $stocks,
                        'stockIniciales' => $stockIniciales,
                        'stockAnterior' => $stockAnterior,
                        'datoBeneficios' => $beneficios,
                        'vigencias' => $vigencias,
                        'atributosMessage' => $atributosMessage,
                        'stocksMessage' => $stocksMessage,
                        'vigenciasMessage' => $vigenciasMessage,
                        'beneficiosMessage' => $beneficiosMessage,
                        'errorStockSplit' => $errorStockSplit,
                        'slug' => $slug,
                        'config' => $config
                    )
                );
                $view->setTemplate('oferta/oferta/copy');
                return $view;
            }

            $copyState = $request->getPost()->input_copy == "1" ? true : false;

            $tipoAtributo = isset($post["TipoAtributo"]) ? $post["TipoAtributo"] : null;

            if ($post["Tipo"] != null and $post["Empresa"] != null and $estadoAnt == 0) {
                $bolsa = $this->getBolsaTotalTable()->getBolsaTotal($post["Tipo"], $post["Empresa"]);
            } else {
                $bolsa = new BolsaTotal();
                $bolsa->BolsaActual = 0;
            }

            if ($tipoAtributo == "Split") {
                foreach ($stocksBolsa as $stk) {
                    $stockAnt = $stockAnt + $stk;
                }
            }

            if (!$copyState) {
                if ($bolsa->BolsaActual <= 0) {
                    $bolsa->BolsaActual = $stockAnt;
                } else {
                    $bolsa->BolsaActual = $bolsa->BolsaActual + $stockAnt;
                }
            }

            if ($TipoAtributoAnt != $post['TipoAtributo']) {
                if ($post['TipoAtributo'] == "Split") {
                    $stockAnt = 0;
                } else {
                    $stocks = str_ireplace(["'", "[", "]"], ['', '', ''], $stocks);
                    $stocks = explode(',', $stocks);
                    foreach ($stocks as $stk) {
                        $bolsa->BolsaActual = $bolsa->BolsaActual + (int)$stk;
                    }
                }
            }

            $form->setInputFilter(
                $validate->getInputFilter($datos[1], $bolsa->BolsaActual, 0, $request->getPost()->Tipo, 2, $disabled, $tipoAtributo)
            );

            $form->setData($post);

            if ($request->getPost()->principal == null) {
                $mensajes['image'] = "No hay una Imagen Principal seleccionada.";
                $mensajes['imagec'] = 'has-error';
                $acceptance = false;
            }

            if ($tipoAtributo == "Split") {
                $postAtributos = array();
                foreach ($request->getPost()->atributos as $key => $item) {
                    $postAtributos[$key] = preg_replace('/\s+/', ' ', trim($item));
                }
                $request->getPost()->atributos = $postAtributos;
                //Validar Campos
                $resultados = $this->validarCampos($request);
                $approved = $resultados[0];
                $messageAttrib = $resultados[1];


                //---------------------------QUITAR PARA LEAD SPLIT-----------------------------------------------------//
                //Validar Fechas
                if ($request->getPost()->Tipo != $this::TIPO_OFERTA_LEAD) {
                    $fechasAnteriores = ($copyState == true) ? null : $fechasAnteriores;
                    $resultadosFechas = $this->validarFechasVigencia($request, $fechasAnteriores);
                    $approvedFechas = $resultadosFechas[0];

                    $messageAttrib = array_merge($messageAttrib, $resultadosFechas[1]);
                } else {
                    $approvedFechas = true;
                }


                $stockTotalAtributo = 0;
                foreach ($request->getPost()->stocks as $dataStock) {
                    $stockTotalAtributo = $stockTotalAtributo + $dataStock;
                }
                if ($stockTotalAtributo > $bolsa->BolsaActual) {
                    $approvedStockSplit = false;
                }
            } else {
                $approved = true;
                $approvedFechas = true;
                $approvedStockSplit = true;
            }

            $approvedTipo = true;
            $has_downloads = $this->getCuponTable()->hasCuponDescargas($id);
            if ($has_downloads > 0 and ($request->getPost()->TipoAtributo != $tipoAtributosAnt
                    or $request->getPost()->Tipo != $tipoAnt) and !$copyState
            ) {
                $approvedTipo = false;
            }

            $camposXAsignar = $request->getPost()->form_imput;
            $camposXAsignarReq = $request->getPost()->form_imput_req;
            $tarjetasAsignar = $request->getPost()->tarjetas_input;
            if ($form->isValid() and $acceptance and $approved and $approvedFechas and $approvedTipo and $approvedStockSplit) {

                #region Instanciar Objeto de la Oferta
                $oferta = new Oferta();
                $datoDepartamento = $post['Departamento'];
                $datoRubro = $post['Rubro'];
                $datoSegmento = $post['Segmento'];
                $datoCategoria = $post['Categoria'];
                $datoCampania = $post['Campania'];
                $datoPais = $post['Pais'];

                $oferta->exchangeArray($request->getPost());

                $stockIni = 0;
                $stockPost = 0;
                if (isset($post['TipoAtributo']) && $post['TipoAtributo'] == "Split") {
                    foreach ($post['stocks'] as $key => $value) {
                        $stockPost = $stockPost + $value;
                        $stockIni = $stockIni + $post['stockIniciales'][$key];
                    }
                } else {
                    $stockIni = (int)$oferta->StockInicial;
                    $stockPost = (int)$oferta->Stock;
                }

                $oferta->BNF_BolsaTotal_Empresa_id = $post['Empresa'];
                $oferta->StockInicial = $stockIni;
                $oferta->BNF_BolsaTotal_TipoPaquete_id = $post['Tipo'];

                $oferta->Nombre = trim($request->getPost()->Nombre);
                $oferta->Titulo = trim($request->getPost()->Titulo);
                $oferta->TituloCorto = trim($request->getPost()->TituloCorto);
                $oferta->SubTitulo = trim($request->getPost()->SubTitulo);

                if ($oferta->Titulo != trim($tituloAnterior)) {
                    $oferta->Slug = $this->getSlug($oferta->Titulo, $id);
                } else {
                    $oferta->Slug = $slugAnterior;
                }
                #endregion

                #region Calculamos la Fecha Cuando es del tipo Presencia
                if ($post['Tipo'] == $this::TIPO_OFERTA_PRESENCIA) {
                    if ($post['TipoAtributo'] == "Split") {
                        $fecha = date($oferta->FechaInicioPublicacion);
                        $stockFecha = 0;
                        foreach ($post['stockIniciales'] as $value) {
                            $stockFecha = $stockFecha + $value;
                        }

                        $nuevaFecha = strtotime('+' . ($stockFecha) . 'day', strtotime($fecha));
                        $nuevaFecha = date('Y-m-j', $nuevaFecha);
                        $oferta->FechaFinPublicacion = $nuevaFecha;
                    } else {
                        $fecha = date($oferta->FechaInicioPublicacion);
                        $nuevaFecha = strtotime('+' . $stockIni . 'day', strtotime($fecha));
                        $nuevaFecha = date('Y-m-j', $nuevaFecha);
                        $oferta->FechaFinPublicacion = $nuevaFecha;
                    }
                }
                #endregion

                #region Guardar Oferta
                if (!$copyState) {
                    $oferta->id = $id;
                } else {
                    $oferta->id = 0;
                    $oferta->Slug = 'generando...';
                }

                $id = $this->getOfertaTable()->saveOferta($oferta);

                if ($copyState) {
                    $copySlug['Slug'] = $this->getSlug($oferta->Titulo . " Copia Slug", (int)$id);
                    $this->getOfertaTable()->updateOferta($id, $copySlug);
                }
                #endregion
                #region Actualizar Bolsa Total Segun Empresa y Tipos Nuevos y Anteriores
                if ($post['Estado'] != $this::ESTADO_OFERTA_CADUCADO) {
                    $stockAct = $stockPost;
                    if (!$copyState) {
                        if ($empAnt == $post['Empresa']) {
                            if ($tipoAnt == (int)$post['Tipo']) {
                                if ($TipoAtributoAnt == $post['TipoAtributo']) {
                                    $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotal($post['Tipo'], $post['Empresa']);
                                    $bolsaTotal->BolsaActual = $bolsaTotal->BolsaActual + ($stockAnt - $stockAct);
                                    $this->getBolsaTotalTable()->editBolsa($bolsaTotal);
                                } else {
                                    $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotal($post['Tipo'], $post['Empresa']);
                                    $bolsaTotal->BolsaActual = $bolsa->BolsaActual + ($stockAnt - $stockAct);
                                    $this->getBolsaTotalTable()->editBolsa($bolsaTotal);
                                    $stockAnt = $bolsa->BolsaActual;
                                }
                            } else {
                                //Tipo Anterior
                                $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotal($tipoAnt, $post['Empresa']);
                                $bolsaTotal->BolsaActual = $bolsaTotal->BolsaActual + $stockAnt;
                                $this->getBolsaTotalTable()->editBolsa($bolsaTotal);
                                //Tipo Nuevo
                                $bolsaTotalNueva = $this->getBolsaTotalTable()
                                    ->getBolsaTotal($post['Tipo'], $post['Empresa']);
                                $bolsaTotalNueva->BolsaActual = $bolsaTotalNueva->BolsaActual - $stockAct;
                                $this->getBolsaTotalTable()->editBolsa($bolsaTotalNueva);
                            }
                        } else {
                            if ($tipoAnt == (int)$post['Tipo']) {
                                //Empresa Anterior
                                $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotal($post['Tipo'], $empAnt);
                                $bolsaTotal->BolsaActual = $bolsaTotal->BolsaActual + $stockAnt;
                                $this->getBolsaTotalTable()->editBolsa($bolsaTotal);
                                //Empresa Nuevo
                                $bolsaTotalNueva = $this->getBolsaTotalTable()
                                    ->getBolsaTotal($post['Tipo'], $post['Empresa']);
                                $bolsaTotalNueva->BolsaActual = $bolsaTotalNueva->BolsaActual - $stockAct;
                                $this->getBolsaTotalTable()->editBolsa($bolsaTotalNueva);
                            } else {
                                //Empresa Anterior Tipo Anterior
                                $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotal($tipoAnt, $empAnt);
                                $bolsaTotal->BolsaActual = $bolsaTotal->BolsaActual + $stockAnt;
                                $this->getBolsaTotalTable()->editBolsa($bolsaTotal);
                                //Empresa Nuevo Tipo Nuevo
                                $bolsaTotalNueva = $this->getBolsaTotalTable()
                                    ->getBolsaTotal($post['Tipo'], $post['Empresa']);
                                $bolsaTotalNueva->BolsaActual = $bolsaTotalNueva->BolsaActual - $stockAct;
                                $this->getBolsaTotalTable()->editBolsa($bolsaTotalNueva);
                            }
                        }
                    } else {
                        $bolsaTotalNueva = $this->getBolsaTotalTable()
                            ->getBolsaTotal($post['Tipo'], $post['Empresa']);
                        $bolsaTotalNueva->BolsaActual = $bolsaTotalNueva->BolsaActual - $stockAct;
                        $this->getBolsaTotalTable()->editBolsa($bolsaTotalNueva);
                    }
                }
                #endregion

                #region Actualizar los Atributos y Cupones
                if ($post['Tipo'] == $this::TIPO_OFERTA_DESCARGA) {
                    if ($tipoAnt == $this::TIPO_OFERTA_PRESENCIA || $tipoAnt == $this::TIPO_OFERTA_LEAD) {
                        $this->getOfertaAtributosTable()->deleteOfertaAtributos($id);
                    }
                    if ($copyState || $tipoAnt != $this::TIPO_OFERTA_DESCARGA) {
                        if ($post['TipoAtributo'] == "Split") {
                            foreach ($post['atributos'] as $key => $value) {
                                $ofertaAtributo = new OfertaAtributos();
                                $ofertaAtributo->BNF_Oferta_id = $id;
                                $ofertaAtributo->NombreAtributo = $value;
                                $ofertaAtributo->Stock = $post['stocks'][$key];
                                $ofertaAtributo->StockInicial = $post['stocks'][$key];
                                $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                                $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                $ofertaAtributo->Eliminado = 0;
                                $atributo_id = $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);

                                for ($i = 0; $i < $post['stocks'][$key]; $i++) {
                                    $cupon = new Cupon();
                                    $cupon->BNF_Oferta_id = $id;
                                    $cupon->BNF_Oferta_Atributo_id = $atributo_id;
                                    $cupon->EstadoCupon = 'Creado';
                                    $this->getCuponTable()->saveCupon($cupon);
                                }
                            }
                        } else {
                            for ($i = 0; $i < $stockAct; $i++) {
                                $cupon = new Cupon();
                                $cupon->BNF_Oferta_id = $id;
                                $cupon->EstadoCupon = 'Creado';
                                $this->getCuponTable()->saveCupon($cupon);
                            }
                        }
                    } elseif ($tipoAnt == (int)$post['Tipo']) {
                        if ($TipoAtributoAnt == $post['TipoAtributo']) {
                            if ($post['TipoAtributo'] == "Split") {
                                foreach ($post['atributosId'] as $key => $value) {
                                    if ($this->getOfertaAtributosTable()->getIfExistById($id, $value) > 0) {
                                        $ofertaAtributo = $this->getOfertaAtributosTable()
                                            ->getOfertaAtributosSearchById($id, $value);

                                        $ofertaAtributo->NombreAtributo = $post['atributos'][$key];
                                        $ofertaAtributo->Stock = $post['stocks'][$key];
                                        $ofertaAtributo->StockInicial = $post['stockIniciales'][$key];
                                        $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                                        $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                        $ofertaAtributo->Eliminado = 0;
                                        $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);

                                        foreach ($atributosIdAnt as $keyAttr => $valueAttr) {
                                            if ($valueAttr == $value) {
                                                $stockAct = $post['stocks'][$key];
                                                if ($TipoAtributoAnt == $post['TipoAtributo']) {
                                                    $stockAnt = $atributoStockAnt[$keyAttr];
                                                }

                                                if ($stockAct > $stockAnt) {
                                                    $dif = $stockAct - $stockAnt;
                                                    for ($i = 0; $i < $dif; $i++) {
                                                        $cupon = new Cupon();
                                                        $cupon->BNF_Oferta_id = $id;
                                                        $cupon->BNF_Oferta_Atributo_id = $ofertaAtributo->id;
                                                        $cupon->EstadoCupon = 'Creado';
                                                        $this->getCuponTable()->saveCupon($cupon);
                                                    }
                                                } elseif ($stockAct < $stockAnt) {
                                                    $dif = $stockAnt - $stockAct;
                                                    $ultimo = $this->getCuponTable()->getLastCupon($id, null, $ofertaAtributo->id);
                                                    $ultimo->id = (int)$ultimo->id + 1;
                                                    for ($i = 0; $i < $dif; $i++) {
                                                        $ultimo = $this->getCuponTable()->getLastCupon($id, $ultimo->id, $ofertaAtributo->id);
                                                        if ($ultimo != false) {
                                                            $cupon = $this->getCuponTable()->getCupon($ultimo->id);
                                                            $cupon->EstadoCupon = 'Eliminado';
                                                            $cupon->FechaEliminado = date("Y-m-d H:i:s");
                                                            $this->getCuponTable()->saveCupon($cupon);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $ofertaAtributo = new OfertaAtributos();
                                        $ofertaAtributo->BNF_Oferta_id = $id;
                                        $ofertaAtributo->NombreAtributo = $post['atributos'][$key];
                                        $ofertaAtributo->Stock = $post['stocks'][$key];
                                        $ofertaAtributo->StockInicial = $post['stocks'][$key];
                                        $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                                        $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                        $ofertaAtributo->Eliminado = 0;
                                        $atributo_id = $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);

                                        if ($post['Tipo'] == $this::TIPO_OFERTA_DESCARGA) {
                                            for ($i = 0; $i < $post['stocks'][$key]; $i++) {
                                                $cupon = new Cupon();
                                                $cupon->BNF_Oferta_id = $id;
                                                $cupon->BNF_Oferta_Atributo_id = $atributo_id;
                                                $cupon->EstadoCupon = 'Creado';
                                                $this->getCuponTable()->saveCupon($cupon);
                                            }
                                        }
                                    }
                                }

                                foreach ($post['atributosId'] as $key => $value) {
                                    foreach ($atributosIdAnt as $keyAnt => $valueAnt) {
                                        if ($value == $valueAnt) {
                                            unset($atributosIdAnt[$keyAnt]);
                                        }
                                    }
                                }

                                foreach ($atributosIdAnt as $key => $value) {
                                    $this->getOfertaAtributosTable()->deleteOfertaAtributosById($value);
                                    $this->getCuponTable()->deleteCuponByAtributo($value);
                                }
                            } else {
                                if ($stockAct > $stockAnt) { //Crear Nuevos Cupones
                                    $dif = $stockAct - $stockAnt;
                                    for ($i = 0; $i < $dif; $i++) {
                                        $cupon = new Cupon();
                                        $cupon->BNF_Oferta_id = $id;
                                        $cupon->EstadoCupon = 'Creado';
                                        $this->getCuponTable()->saveCupon($cupon);
                                    }
                                } elseif ($stockAct < $stockAnt) { //Eliminar Cupones
                                    $dif = $stockAnt - $stockAct;
                                    $ultimo = $this->getCuponTable()->getLastCupon($id);
                                    $ultimo->id = (int)$ultimo->id + 1;
                                    for ($i = 0; $i < $dif; $i++) {
                                        $ultimo = $this->getCuponTable()->getLastCupon($id, $ultimo->id);
                                        if ($ultimo != false) {
                                            $cupon = $this->getCuponTable()->getCupon($ultimo->id);
                                            $cupon->EstadoCupon = 'Eliminado';
                                            $cupon->FechaEliminado = date("Y-m-d H:i:s");
                                            $this->getCuponTable()->saveCupon($cupon);
                                        }
                                    }
                                }
                            }
                        } elseif ($TipoAtributoAnt != $post['TipoAtributo']) {
                            $this->getCuponTable()->deleteCuponPorOferta($id);
                            if ($post['TipoAtributo'] == "Split") {
                                foreach ($post['atributos'] as $key => $value) {
                                    $ofertaAtributo = new OfertaAtributos();
                                    $ofertaAtributo->BNF_Oferta_id = $id;
                                    $ofertaAtributo->NombreAtributo = $value;
                                    $ofertaAtributo->Stock = $post['stocks'][$key];
                                    $ofertaAtributo->StockInicial = $post['stocks'][$key];
                                    $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                                    $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                    $ofertaAtributo->Eliminado = 0;
                                    $atributo_id = $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);

                                    for ($i = 0; $i < $post['stocks'][$key]; $i++) {
                                        $cupon = new Cupon();
                                        $cupon->BNF_Oferta_id = $id;
                                        $cupon->BNF_Oferta_Atributo_id = $atributo_id;
                                        $cupon->EstadoCupon = 'Creado';
                                        $this->getCuponTable()->saveCupon($cupon);
                                    }
                                }
                            } else {
                                $this->getOfertaAtributosTable()->deleteOfertaAtributos($id);
                                for ($i = 0; $i < $stockAct; $i++) {
                                    $cupon = new Cupon();
                                    $cupon->BNF_Oferta_id = $id;
                                    $cupon->EstadoCupon = 'Creado';
                                    $this->getCuponTable()->saveCupon($cupon);
                                }
                            }
                        }
                    }
                } elseif ($post['Tipo'] == $this::TIPO_OFERTA_PRESENCIA) {
                    if ($tipoAnt == $this::TIPO_OFERTA_DESCARGA) {
                        $this->getCuponTable()->deleteCuponPorOferta($id);
                        $this->getOfertaAtributosTable()->deleteOfertaAtributos($id);
                    } elseif ($tipoAnt == $this::TIPO_OFERTA_LEAD) {
                        $this->getOfertaAtributosTable()->deleteOfertaAtributos($id);
                    }

                    if ($post['TipoAtributo'] == "Split") {

                        foreach ($post['atributosId'] as $key => $value) {
                            if ($this->getOfertaAtributosTable()->getIfExistById($id, $value) > 0 and !$copyState) {
                                $ofertaAtributo = $this->getOfertaAtributosTable()
                                    ->getOfertaAtributosSearchById($id, $value);

                                $ofertaAtributo->NombreAtributo = $post['atributos'][$key];
                                $ofertaAtributo->Stock = $post['stocks'][$key];
                                $ofertaAtributo->StockInicial = $post['stockIniciales'][$key];
                                $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                                $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                $ofertaAtributo->Eliminado = 0;
                                $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);
                            } else {
                                $ofertaAtributo = new OfertaAtributos();
                                $ofertaAtributo->BNF_Oferta_id = $id;
                                $ofertaAtributo->NombreAtributo = $value;
                                $ofertaAtributo->Stock = $post['stocks'][$key];
                                $ofertaAtributo->StockInicial = $post['stocks'][$key];
                                $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                                $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                $ofertaAtributo->Eliminado = 0;
                                $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);
                            }
                        }

                        foreach ($post['atributosId'] as $key => $value) {
                            foreach ($atributosIdAnt as $keyAnt => $valueAnt) {
                                if ($value == $valueAnt) {
                                    unset($atributosIdAnt[$keyAnt]);
                                }
                            }
                        }

                        foreach ($atributosIdAnt as $key => $value) {
                            $this->getOfertaAtributosTable()->deleteOfertaAtributosById($value);
                            $this->getCuponTable()->deleteCuponByAtributo($value);
                        }
                    }
                } elseif ($post['Tipo'] == $this::TIPO_OFERTA_LEAD) {
                    if ($tipoAnt == $this::TIPO_OFERTA_DESCARGA) {
                        $this->getCuponTable()->deleteCuponPorOferta($id);
                        $this->getOfertaAtributosTable()->deleteOfertaAtributos($id);
                    } elseif ($tipoAnt == $this::TIPO_OFERTA_PRESENCIA) {
                        $this->getOfertaAtributosTable()->deleteOfertaAtributos($id);
                    }
                    if ($TipoAtributoAnt != $post['TipoAtributo']) {
                        if ($post['TipoAtributo'] != "Split") {
                            $this->getOfertaAtributosTable()->deleteOfertaAtributos($id);
                        }
                    }
                    if ($post['TipoAtributo'] == "Split") {

                        if ($copyState || $tipoAnt != $this::TIPO_OFERTA_LEAD) {
                            foreach ($post['atributos'] as $key => $value) {
                                $ofertaAtributo = new OfertaAtributos();
                                $ofertaAtributo->BNF_Oferta_id = $id;
                                $ofertaAtributo->NombreAtributo = $value;
                                $ofertaAtributo->Stock = $post['stocks'][$key];
                                $ofertaAtributo->StockInicial = $post['stocks'][$key];
                                $ofertaAtributo->FechaVigencia = null;
                                $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                $ofertaAtributo->Eliminado = 0;
                                $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);
                            }
                        }

                        if ($TipoAtributoAnt != $post['TipoAtributo']) {
                            foreach ($post['atributos'] as $key => $value) {
                                $ofertaAtributo = new OfertaAtributos();
                                $ofertaAtributo->BNF_Oferta_id = $id;
                                $ofertaAtributo->NombreAtributo = $value;
                                $ofertaAtributo->Stock = $post['stocks'][$key];
                                $ofertaAtributo->StockInicial = $post['stocks'][$key];
                                $ofertaAtributo->FechaVigencia = $post['vigencias'][$key];
                                $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                $ofertaAtributo->Eliminado = 0;
                                $atributo_id = $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);
                            }
                        } elseif ($TipoAtributoAnt == $post['TipoAtributo']) {
                            foreach ($post['atributosId'] as $key => $value) {
                                if ($this->getOfertaAtributosTable()->getIfExistById($id, $value) > 0 and !$copyState) {

                                    $ofertaAtributo = $this->getOfertaAtributosTable()
                                        ->getOfertaAtributosSearchById($id, $value);

                                    $ofertaAtributo->NombreAtributo = $post['atributos'][$key];
                                    $ofertaAtributo->Stock = $post['stocks'][$key];
                                    $ofertaAtributo->StockInicial = $post['stockIniciales'][$key];
                                    $ofertaAtributo->FechaVigencia = null;
                                    $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                    $ofertaAtributo->Eliminado = 0;
                                    $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);
                                } else {
                                    $ofertaAtributo = new OfertaAtributos();
                                    $ofertaAtributo->BNF_Oferta_id = $id;
                                    $ofertaAtributo->NombreAtributo = $post['atributos'][$key];
                                    $ofertaAtributo->Stock = $post['stocks'][$key];
                                    $ofertaAtributo->StockInicial = $post['stocks'][$key];
                                    $ofertaAtributo->FechaVigencia = null;
                                    $ofertaAtributo->DatoBeneficio = $post['beneficios'][$key];
                                    $ofertaAtributo->Eliminado = 0;
                                    $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);
                                }
                            }

                        }


                        foreach ($post['atributosId'] as $key => $value) {
                            foreach ($atributosIdAnt as $keyAnt => $valueAnt) {
                                if ($value == $valueAnt) {
                                    unset($atributosIdAnt[$keyAnt]);
                                }
                            }
                        }

                        foreach ($atributosIdAnt as $key => $value) {
                            $this->getOfertaAtributosTable()->deleteOfertaAtributosById($value);
                            $this->getCuponTable()->deleteCuponByAtributo($value);
                        }


                    }
                }
                #endregion

                #region Caducación manual de la Oferta
                if ($request->getPost()->Estado == $this::ESTADO_OFERTA_CADUCADO) {
                    //Finalizo Cupones
                    $this->getCuponTable()->updateXofertaFinalizado($id);
                    if ($post['TipoAtributo'] == "Split") {
                        $stockAnt = 0;
                        foreach ($post['atributos'] as $key => $value) {
                            if ($this->getOfertaAtributosTable()->getIfExist($id, $value) > 0 and !$copyState) {
                                $ofertaAtributo = $this->getOfertaAtributosTable()
                                    ->getOfertaAtributosSearch($id, $value);
                                $stockAnt += $ofertaAtributo->Stock;
                                $ofertaAtributo->Stock = 0;
                                $ofertaAtributo->StockInicial = 0;
                                $ofertaAtributo->Eliminado = 0;
                                $this->getOfertaAtributosTable()->saveOfertaAtributos($ofertaAtributo);
                            }
                        }
                    } else {
                        //Caduca la oferta
                        $oferta->id = $id;
                        $oferta->Stock = 0;
                        $oferta->StockInicial = 0;
                        $this->getOfertaTable()->saveOferta($oferta);
                    }
                    //Devuelve el stock a la bolsa
                    $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotal($tipoAnt, $empAnt);
                    $bolsaTotal->BolsaActual = $bolsaTotal->BolsaActual + $stockAnt;
                    $this->getBolsaTotalTable()->editBolsa($bolsaTotal);
                }
                #endregion

                #region Activar|Desactivar Oferta
                $estadoAct = (int)$oferta->Eliminado;
                if (($estadoAnt == 1 and $estadoAct == 0) or ($estadoAnt == 0 and $estadoAct == 1)) {
                    $this->eliminar($id, $estadoAct);
                }
                #endregion

                #region Actualizar Rubro
                if (!$copyState) {
                    $this->getOfertaRubroTable()->deleteRubro($id, $rubroData);
                }

                if ($this->getOfertaRubroTable()->getOfertaRubroExist($id, $datoRubro) > 0 and !$copyState) {
                    $ofertaRubro = $this->getOfertaRubroTable()->getOfertaRubroSeach($id, $datoRubro);
                    $ofertaRubro->Eliminado = '0';
                    $this->getOfertaRubroTable()->saveOfertaRubro($ofertaRubro);
                } else {
                    $ofertaRubro = new OfertaRubro();
                    $ofertaRubro->BNF_Rubro_id = $datoRubro;
                    $ofertaRubro->BNF_Oferta_id = $id;
                    $ofertaRubro->Eliminado = '0';
                    $this->getOfertaRubroTable()->saveOfertaRubro($ofertaRubro);
                }
                #endregion

                #region Actualizamos los Ubigeos
                if (count($datoDepartamento) > 0) {
                    if (!$copyState) {
                        $this->getOfertaUbigeoTable()->deleteAllUbigeos($id);
                    }

                    foreach ($datoDepartamento as $valor) {
                        if ($this->getOfertaUbigeoTable()->getOfertaUbigeoExist($id, $valor) > 0 and !$copyState) {
                            $ofertaUbigeo = $this->getOfertaUbigeoTable()->getOfertaUbigeoSeach($id, $valor);
                            $ofertaUbigeo->Eliminado = '0';
                            $this->getOfertaUbigeoTable()->saveOfertaUbigeo($ofertaUbigeo);
                        } else {
                            $ofertaUbigeo = new OfertaUbigeo();
                            $ofertaUbigeo->BNF_Ubigeo_id = $valor;
                            $ofertaUbigeo->BNF_Oferta_id = $id;
                            $ofertaUbigeo->Eliminado = '0';
                            $this->getOfertaUbigeoTable()->saveOfertaUbigeo($ofertaUbigeo);
                        }
                    }
                } else {
                    $this->getOfertaUbigeoTable()->deleteAllUbigeos($id);
                }
                #endregion

                #region Actualizamos los Segmentos
                if (count($datoSegmento) > 0) {
                    if (!$copyState) {
                        $this->getOfertaSegmentoTable()->deleteAllSegmentos($id);
                    }
                    foreach ($datoSegmento as $valor) {
                        if ($this->getOfertaSegmentoTable()->getOfertaSegmentoExist($id, $valor) > 0 and !$copyState) {
                            $ofertaSegmento = $this->getOfertaSegmentoTable()->getOfertaSegmentoSeach($id, $valor);
                            $ofertaSegmento->Eliminado = '0';
                            $this->getOfertaSegmentoTable()
                                ->saveOfertaSegmento($ofertaSegmento);
                        } else {
                            $ofertaSegmento = new OfertaSegmento();
                            $ofertaSegmento->BNF_Segmento_id = $valor;
                            $ofertaSegmento->BNF_Oferta_id = $id;
                            $ofertaSegmento->Eliminado = '0';
                            $this->getOfertaSegmentoTable()->saveOfertaSegmento($ofertaSegmento);
                        }
                    }
                } else {
                    $this->getOfertaSegmentoTable()->deleteAllSegmentos($id);
                }
                #endregion

                #region Actualizamos las Categorias
                if (count($datoCategoria) > 0) {
                    if (!$copyState) {
                        $this->getOfertaCategoriaUbigeoTable()->deleteAllCategoriaUbigeo($id);
                    }
                    foreach ($datoCategoria as $valor) {
                        if ($this->getOfertaCategoriaUbigeoTable()->getOfertaCategoriaUbigeoExist($id, $valor) > 0 and !$copyState) {
                            $categoriaUbigeo = $this->getCategoriaUbigeoTable()
                                ->getCategoriaUbigeoPais($valor, $datoPais);

                            $ofertaCategoriaUbigeo = $this->getOfertaCategoriaUbigeoTable()
                                ->getOfertaCategoriaUbigeoSeach($id, $categoriaUbigeo->id);
                            $ofertaCategoriaUbigeo->Eliminado = '0';
                            $this->getOfertaCategoriaUbigeoTable()->saveOfertaCategoriaUbigeo($ofertaCategoriaUbigeo);
                        } else {
                            $categoriaUbigeo = $this->getCategoriaUbigeoTable()
                                ->getCategoriaUbigeoPais($valor, $datoPais);

                            $ofertaCategoriaUbigeo = new OfertaCategoriaUbigeo();
                            $ofertaCategoriaUbigeo->BNF_CategoriaUbigeo_id = $categoriaUbigeo->id;
                            $ofertaCategoriaUbigeo->BNF_Oferta_id = $id;
                            $ofertaCategoriaUbigeo->Eliminado = '0';
                            $this->getOfertaCategoriaUbigeoTable()->saveOfertaCategoriaUbigeo($ofertaCategoriaUbigeo);
                        }
                    }
                } else {
                    $this->getOfertaCategoriaUbigeoTable()->deleteAllCategoriaUbigeo($id);
                }
                #endregion

                #region Actualizamos las Campañas
                if (count($datoCampania) > 0 and $datoCampania != "") {
                    if (!$copyState) {
                        $this->getOfertaCampaniaUbigeoTable()->deleteAllCampaniaUbigeo($id);
                    }
                    foreach ($datoCampania as $valor) {
                        if ($this->getOfertaCampaniaUbigeoTable()->getOfertaCampaniaUbigeoExist($id, $valor) > 0 and !$copyState) {
                            $campaniaPais = $this->getCampaniaUbigeoTable()
                                ->getCampaniaUbigeoPais($valor, $datoPais);

                            $ofertaCategoriaUbigeo = $this->getOfertaCampaniaUbigeoTable()
                                ->getOfertaCampaniaUbigeoSeach($id, $campaniaPais->id);
                            $ofertaCategoriaUbigeo->Eliminado = '0';
                            $this->getOfertaCampaniaUbigeoTable()->saveOfertaCampaniaUbigeo($ofertaCategoriaUbigeo);
                        } else {
                            $campaniaPais = $this->getCampaniaUbigeoTable()
                                ->getCampaniaUbigeoPais($valor, $datoPais);

                            $ofertaCategoriaUbigeo = new OfertaCampaniaUbigeo();
                            $ofertaCategoriaUbigeo->BNF_CampaniaUbigeo_id = $campaniaPais->id;
                            $ofertaCategoriaUbigeo->BNF_Oferta_id = $id;
                            $ofertaCategoriaUbigeo->Eliminado = '0';
                            $this->getOfertaCampaniaUbigeoTable()->saveOfertaCampaniaUbigeo($ofertaCategoriaUbigeo);
                        }
                    }
                } else {
                    $this->getOfertaCampaniaUbigeoTable()->deleteAllCampaniaUbigeo($id);
                }
                #endregion

                #region Agregar Imagenes Grabar denuevo
                if ($copyState) {
                    $path = './public/elements/oferta/';
                    $allImage = $this->getImagenTable()->getImagenOferta($ofertaAnt);

                    foreach ($allImage as $imagen) {
                        $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);
                        $ext = explode('.', $imagen->Nombre);

                        $manager = new ImageManager(array('driver' => 'imagick'));
                        $img = $manager->make($path . $imagen->Nombre);
                        $img2 = $manager->make($path . $imagen->Nombre);
                        $img3 = $manager->make($path . $imagen->Nombre);

                        $resize = new Resize();
                        $resize->rename($path, $img, $ext[1], $fileName, '');
                        $resize->rename($path, $img2, $ext[1], $fileName, '-medium');
                        $resize->rename($path, $img3, $ext[1], $fileName, '-large');

                        $nuevaImagen = new Imagen();
                        $nuevaImagen->BNF_Oferta_id = $id;
                        $nuevaImagen->Nombre = $fileName . "." . $ext[1];
                        $nuevaImagen->Principal = $imagen->Principal == 1 ? '1' : '0';
                        $nuevaImagen->Eliminado = 0;
                        $this->getImagenTable()->saveImagen($nuevaImagen);

                        $logger = new Logger;
                        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_OFERTA);
                        $message = "Imagen asignada a la oferta " . $id . ": " . $fileName . "." . $ext[1];
                        $logger->addWriter($writer);
                        $logger->log(Logger::INFO, $message);
                    }
                }
                #endregion

                #region Actualizamos Imagen
                if (is_object($request->getPost()->Imagen) || is_array($request->getPost()->Imagen)) {
                    foreach ($request->getPost()->Imagen as $key => $img) {
                        $imagenOferta = new Imagen();
                        $imagenOferta->Nombre = $img;
                        $imagenOferta->BNF_Oferta_id = $id;
                        if ($key == $principal) {
                            $imagenOferta->Principal = '1';
                            $this->getImagenTable()->noprincipalImagen($id);
                        } else {
                            $imagenOferta->Principal = '0';
                        }
                        $this->getImagenTable()->saveImagen($imagenOferta);

                        $logger = new Logger;
                        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_OFERTA);
                        $message = "Imagen asignada a la oferta " . $id . ": " . $img;
                        $logger->addWriter($writer);
                        $logger->log(Logger::INFO, $message);
                    }
                }
                #endregion

                #region Actualizar Busqueda
                $busqueda = new Busqueda();
                $busqueda->BNF_Oferta_id = $id;
                $busqueda->TipoOferta = 1;
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
                #endregion

                #region Actualizar formulario
                if ($post['Tipo'] == $this::TIPO_OFERTA_LEAD) {
                    if ($copyState) {
                        $form_imput = $request->getPost()->form_imput;
                        $form_imput_req = $request->getPost()->form_imput_req;
                        $ofertaformulariotable = $this->serviceLocator->get('Oferta\Model\Table\OfertaFormularioTable');
                        $ofertaformulario = new OfertaFormulario();
                        $ofertaformulario->BNF_Oferta_id = $id;
                        $form_array = array();
                        foreach ($formulario as $dato) {
                            $form_array[$dato->id] = $dato->id;
                        }
                        //Agregamos el campo CorreoContacto de Lead
                        if (isset($request->getPost()->CorreoContacto)) {
                            $ofertaformulario->BNF_Formulario_id = 12;
                            $ofertaformulario->Descripcion = $request->getPost()->CorreoContacto;
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = '0';
                            $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                            unset($form_array[12]);
                        }
                        //Agregamos el banner de Lead
                        if (count($request->getPost()->banner) <= 1) {
                            $path = './public/elements/banners/';
                            $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);
                            $ext = explode('.', $request->getPost()->banner);

                            $manager = new ImageManager(array('driver' => 'imagick'));
                            $img = $manager->make($path . $request->getPost()->banner);

                            $resize = new Resize();
                            $resize->rename($path, $img, $ext[1], $fileName, '');

                            $ofertaformulario->BNF_Formulario_id = 1;
                            $ofertaformulario->Descripcion = $fileName . "." . $ext[1];
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = '0';
                            $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                            unset($form_array[1]);

                            $logger = new Logger;
                            $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_OFERTA);
                            $message = "Banner asignado a la oferta " . $id . ": " . $request->getPost()->banner;
                            $logger->addWriter($writer);
                            $logger->log(Logger::INFO, $message);
                        }
                        //Agregamos el mensaje de Lead
                        if (isset($request->getPost()->textobanner)) {
                            $ofertaformulario->BNF_Formulario_id = 13;
                            $ofertaformulario->Descripcion = $request->getPost()->textobanner;
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = '0';
                            $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                            unset($form_array[13]);
                        }
                        //Agregamos los campos seleccionados del formulario lead
                        foreach ($form_imput as $dato) {
                            unset($form_array[$dato]);
                            $ofertaformulario->BNF_Formulario_id = $dato;
                            $ofertaformulario->Descripcion = null;
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = (in_array($dato, $form_imput_req)) ? '1' : '0';
                            $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                        }
                        //Agregamos los campos desactivados del formulario lead
                        foreach ($form_array as $dato) {
                            $ofertaformulario->BNF_Formulario_id = $dato;
                            $ofertaformulario->Descripcion = null;
                            $ofertaformulario->Activo = '0';
                            $ofertaformulario->Requerido = (in_array($dato, $form_imput_req)) ? '1' : '0';
                            $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                        }
                        if ($form_imput == null) {
                            foreach ($form_array as $dato) {
                                $ofertaformulario->BNF_Formulario_id = $dato;
                                $ofertaformulario->Descripcion = null;
                                $ofertaformulario->Activo = '1';
                                $ofertaformulario->Requerido = (in_array($dato, $form_imput_req)) ? '1' : '0';
                                $ofertaformulariotable->saveOfertaFormulario($ofertaformulario);
                            }
                        }
                    } else {
                        $form_imput = $request->getPost()->form_imput;
                        $form_imput_req = $request->getPost()->form_imput_req;
                        $ofertaformulariotable = $this->serviceLocator->get('Oferta\Model\Table\OfertaFormularioTable');
                        $ofertaformulario = new OfertaFormulario();
                        $ofertaformulario->BNF_Oferta_id = $id;
                        $ofertaformulariotable->setActivo($id, '0');

                        $formulario = $ofertaformulariotable->getOfertaFormularioXOfertaData($id);
                        $form_array = array();
                        foreach ($formulario as $dato) {
                            $form_array[$dato->id] = $dato->id;
                        }

                        //Actualizamos el CorreoContacto de Lead
                        if (isset($request->getPost()->CorreoContacto)) {
                            unset($form_array[$request->getPost()->CorreoContacto_id]);
                            $ofertaformulario->id = $request->getPost()->CorreoContacto_id;
                            $ofertaformulario->Descripcion = $request->getPost()->CorreoContacto;
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = '0';
                            $ofertaformulariotable->updateOfertaFormulario($ofertaformulario);
                        }
                        //Actualizamos el Banner de Lead
                        if (count($request->getPost()->banner) <= 1) {
                            unset($form_array[$request->getPost()->banner_id]);
                            $ofertaformulario->id = $request->getPost()->banner_id;
                            $ofertaformulario->Descripcion = $request->getPost()->banner;
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = '0';
                            $ofertaformulariotable->updateOfertaFormulario($ofertaformulario);

                            $logger = new Logger;
                            $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_OFERTA);
                            $message = "Banner asignado a la oferta " . $id . ": " . $request->getPost()->banner;
                            $logger->addWriter($writer);
                            $logger->log(Logger::INFO, $message);
                        }
                        //Actualizamos el mensaje de Lead
                        if (isset($request->getPost()->textobanner)) {
                            unset($form_array[$request->getPost()->textobanner_id]);
                            $ofertaformulario->id = $request->getPost()->textobanner_id;
                            $ofertaformulario->Descripcion = $request->getPost()->textobanner;
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = '0';
                            $ofertaformulariotable->updateOfertaFormulario($ofertaformulario);
                        }
                        //Actualizamos los campos seleccionados del formulario Lead
                        foreach ($form_imput as $dato) {
                            unset($form_array[$dato]);
                            $ofertaformulario->id = $dato;
                            $ofertaformulario->Descripcion = null;
                            $ofertaformulario->Activo = '1';
                            $ofertaformulario->Requerido = (in_array($dato, $form_imput_req)) ? '1' : '0';
                            $ofertaformulariotable->updateOfertaFormulario($ofertaformulario);
                        }
                        //Agregamos los campos desactivados del formulario lead
                        foreach ($form_array as $dato) {
                            $ofertaformulario->id = $dato;
                            $ofertaformulario->Descripcion = null;
                            $ofertaformulario->Activo = '0';
                            $ofertaformulario->Requerido = (in_array($dato, $form_imput_req)) ? '1' : '0';
                            $ofertaformulariotable->updateOfertaFormulario($ofertaformulario);
                        }
                        if ($form_imput == null) {
                            $ofertaformulariotable->setActivo($id, '1');
                        }
                    }
                }
                #endregion

                #region Actualizacion de Tarjetas
                $tarjetasOfertaTable->disabledAllTarjetasOferta($id);
                if (is_object($tarjetasAsignar) || is_array($tarjetasAsignar)) {
                    foreach ($tarjetasAsignar as $tarjeta) {
                        $tarjetaOfertaRecovered = $tarjetasOfertaTable->getTarjetasOfertaData($id, $tarjeta);
                        if ($tarjetaOfertaRecovered) {
                            $tarjetasOferta = new TarjetasOferta();
                            $tarjetasOferta->id = $tarjetaOfertaRecovered->id;
                            $tarjetasOferta->BNF_Tarjetas_id = $tarjeta;
                            $tarjetasOferta->BNF_Oferta_id = $id;
                            $tarjetasOferta->Eliminado = 0;
                            $tarjetasOfertaTable->saveTarjetasOferta($tarjetasOferta);
                        } else {
                            $tarjetasOferta = new TarjetasOferta();
                            $tarjetasOferta->BNF_Tarjetas_id = $tarjeta;
                            $tarjetasOferta->BNF_Oferta_id = $id;
                            $tarjetasOferta->Eliminado = 0;
                            $tarjetasOfertaTable->saveTarjetasOferta($tarjetasOferta);
                        }
                    }
                }
                #endregion

                $this->flashMessenger()->addMessage('Oferta Modificada Correctamente');
                return $this->redirect()->toRoute('oferta');
            } else {
                $confirm[] = 'No se Registro, revisar los datos ingresados';
                $type = "danger";

                if (!$approvedTipo) {
                    $form->get('TipoAtributo')->setMessages(array('No se puede cambiar el tipo de atributos o tipo de oferta, porque la oferta tiene descargas'));
                }

                if (!$approvedStockSplit) {
                    $errorStockSplit = $this::ERROR_STOCK_SPLIT;
                }

                if ($request->getPost()->TipoAtributo == "Split") {
                    $totalAtributos = count($request->getPost()->atributos);
                    //Datos
                    $atributosId = $this->generarArreglosJS($request->getPost()->atributosId);
                    $atributos = $this->generarArreglosJS($request->getPost()->atributos);
                    $stocks = $this->generarArreglosJS($request->getPost()->stocks);
                    $stockIniciales = $this->generarArreglosJS($request->getPost()->stockIniciales);
                    $vigencias = $this->generarArreglosJS($request->getPost()->vigencias);
                    $beneficios = $this->generarArreglosJS($request->getPost()->beneficios);
                    //Mensajes de Error
                    $atributosMessage = $this->generarArreglosJS($messageAttrib["atributos"]);
                    $stocksMessage = $this->generarArreglosJS($messageAttrib["stocks"]);
                    $vigenciasMessage = $this->generarArreglosJS($messageAttrib["vigencias"]);
                    $beneficiosMessage = $this->generarArreglosJS($messageAttrib["beneficios"]);
                }
            }
        }

        if ($imagenesXAsignar == null) {
            $imagenesXAsignar = array();
        }

        if ($copyState) {
            $stockAnt = 0;
            $stockIniciales = '';
            $stockAnterior = '';
        }

        if ($oferta['TipoEspecial'] == 1 && $confirm == null) {
            $confirm[] = 'No se puede modificar el stock debido a que esta oferta cuenta con Código de Barras';
            $type = 'info';
        }


        $view = new ViewModel();
        $view->setVariables(
            array(
                'beneficios' => 'active',
                'oferta' => 'active',
                'oadd' => 'active',
                'id' => $id,
                'form' => $form,
                'stockant' => $stockAnt,
                'estado' => $estadoAnt,
                'stockInicial' => $stockInicial,
                'imagenes' => $imagenData,
                'confirm' => $confirm,
                'type' => $type,
                'ofertas' => null,
                'imagenesXAsignar' => $imagenesXAsignar,
                'imagenebanner' => $imagenesBanner,
                'menssage' => $mensajes,
                'form_config' => $form_config,
                'empresainactiva' => $empresaInactiva,
                'id_empresa' => $id_empresa,
                'camposXAsignar' => $camposXAsignar,
                'camposXAsignarReq' => $camposXAsignarReq,
                'tarjetas' => $tarjetas,
                'tarjetasAsignar' => $tarjetasAsignar,
                'totalAtributos' => $totalAtributos,
                'atributosId' => $atributosId,
                'atributos' => $atributos,
                'stocks' => $stocks,
                'stockIniciales' => $stockIniciales,
                'stockAnterior' => $stockAnterior,
                'datoBeneficios' => $beneficios,
                'vigencias' => $vigencias,
                'atributosMessage' => $atributosMessage,
                'stocksMessage' => $stocksMessage,
                'vigenciasMessage' => $vigenciasMessage,
                'beneficiosMessage' => $beneficiosMessage,
                'errorStockSplit' => $errorStockSplit,
                'slug' => $slug,
                'config' => $config,
                'bolsa' => $bolsa->BolsaActual,
                'TipoOferta' => $oferta['Tipo'],
                'TipoEspecial' => $oferta['TipoEspecial']
            )
        );

        if ($copyState) {
            $form->get('submit')->setAttribute('value', 'Guardar');
            $view->setTemplate('oferta/oferta/copy');
        }

        return $view;
    }

    public function deleteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $val = (int)$this->getRequest()->getPost('val');
        $id = (int)$this->getRequest()->getPost('id');

        $this->eliminar($id, $val);

        return json_encode(array('status' => 200));
    }

    public function assignAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $razon = null;
        $ruc = null;

        $datos = new BuscarEmpresaData($this);

        $form = new BuscarEmpresaForm('empresa', $datos->getFormData());

        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $this->getRequest()->getPost()->toArray();
            $validate = new BuscarEmpresaFilter();

            $form->setInputFilter(
                $validate->getInputFilter($datos->getFilterData(), $post)
            );

            $form->setData($post);

            if ($form->isValid()) {
                $razon = ((int)($request->getPost()->Empresa)) ? $request->getPost()->Empresa : null;
                $ruc = (($request->getPost()->Ruc) != "") ? $request->getPost()->Ruc : null;
            }
        }

        $comboempnorm = $this->getempnormal($razon, $ruc);
        $comboempesp = $this->getempcliente($razon, $ruc);
        $id = (int)$this->params()->fromRoute('id', null);

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'oferta' => 'active',
                'oassign' => 'active',
                'form' => $form,
                'normal' => $comboempnorm,
                'especial' => $comboempesp,
                'id' => $id,
            )
        );
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        //return $this->redirect()->toRoute('oferta');
        $i = 2;
        $empresa_id = (int)$this->identity()->BNF_Empresa_id;
        $type_user = $this->identity()->TipoUsuario;
        if ($type_user == 'super' && $empresa_id != 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        } elseif ($type_user == 'oferta' && $empresa_id == 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $resultado = $this->getOfertaTable()->getReports($empresa_id);
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte_Ofertas")
                ->setSubject("Ofertas")
                ->setDescription("Documento Listado de Ofertas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Ofertas");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:AC1' . $registros);
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
            $objPHPExcel->getActiveSheet()->getStyle('A1:AD1' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:AD1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Tipo de Oferta')
                ->setCellValue('C1', 'Empresa Proveedora')
                ->setCellValue('D1', 'Nombre')
                ->setCellValue('E1', 'Titulo')
                ->setCellValue('F1', 'Titulo Corto')
                ->setCellValue('G1', 'Subtitulo')
                ->setCellValue('H1', 'Tipo de Beneficio')
                ->setCellValue('I1', 'Dato Beneficio')
                ->setCellValue('J1', 'Descripcion')
                ->setCellValue('K1', 'Condiciones de Uso')
                ->setCellValue('L1', 'Direccion')
                ->setCellValue('M1', 'Telefono')
                ->setCellValue('N1', 'Premium')
                ->setCellValue('O1', 'Distrito')
                ->setCellValue('P1', 'Fecha Fin de Vigencia')
                ->setCellValue('Q1', 'Fecha Inicio de Publicacion')
                ->setCellValue('R1', 'Fecha Fin de Publicacion')
                ->setCellValue('S1', 'Stock')
                ->setCellValue('T1', 'Correo')
                ->setCellValue('U1', 'Estado')
                ->setCellValue('V1', 'Descarga Maxima por Día')
                ->setCellValue('W1', 'Fecha de Creacion')
                ->setCellValue('X1', 'Fecha de Actualizacion')
                ->setCellValue('Y1', 'Eliminado')
                ->setCellValue('Z1', 'Campañas')
                ->setCellValue('AA1', 'Categorias')
                ->setCellValue('AB1', 'Segmentos')
                ->setCellValue('AC1', 'Pais')
                ->setCellValue('AD1', 'Rubro');

            foreach ($resultado as $registro) {
                $campaniadata = "";
                $categoriadata = "";
                $segmentodata = "";
                $ubigeo = "";
                $rubro = "";
                $Empresaprov = "";

                try {
                    $ofertacategoria = $this->getOfertaCategoriaUbigeoTable()
                        ->getOfertaCategoriaUbigeos($registro->id);
                    $c = 0;
                    foreach ($ofertacategoria as $valor) {
                        $c = $c + 1;
                        if ($c == 1) {
                            $categoriadata = $valor->Nombre;
                        } else {
                            $categoriadata = $categoriadata . " - " . $valor->Nombre;
                        }
                    }
                } catch (\Exception $ex) {
                    $categoriadata = "";
                }

                try {
                    $ofertacampania = $this->getOfertaCampaniaUbigeoTable()
                        ->getOfertaCampaniaUbigeos($registro->id);
                    $c = 0;
                    foreach ($ofertacampania as $valor) {
                        $c = $c + 1;
                        if ($c == 1) {
                            $campaniadata = $valor->Nombre;
                        } else {
                            $campaniadata = $campaniadata . " - " . $valor->Nombre;
                        }
                    }
                } catch (\Exception $ex) {
                    $campaniadata = "";
                }

                try {
                    $ofertasegmento = $this->getOfertaSegmentoTable()
                        ->getOfertaSegmentosName($registro->id);
                    $c = 0;
                    foreach ($ofertasegmento as $valor) {
                        $c = $c + 1;
                        if ($c == 1) {
                            $segmentodata = $valor->Nombre;
                        } else {
                            $segmentodata = $segmentodata . " - " . $valor->Nombre;
                        }
                    }
                } catch (\Exception $ex) {
                    $segmentodata = "";
                }

                try {
                    $ofertaUbigeo = $this->getOfertaUbigeoTable()
                        ->getOfertaUbigeosPais($registro->id);
                    $ubigeo = $ofertaUbigeo->NombrePais;
                } catch (\Exception $ex) {
                    $ubigeo = "";
                }

                try {
                    $Empresa = $this->getEmpresaTable()
                        ->getEmpresa($registro->BNF_BolsaTotal_Empresa_id);
                    $Empresaprov = $Empresa->NombreComercial;
                } catch (\Exception $ex) {
                    $Empresaprov = "";
                }

                try {
                    $ofertaRubro = $this->getOfertaRubroTable()
                        ->getOfertaRubrosName($registro->id);
                    foreach ($ofertaRubro as $valor) {
                        $rubro = $valor->Nombre;
                    }
                } catch (\Exception $ex) {
                    $rubro = "";
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->TipoOferta)
                    ->setCellValue('C' . $i, $Empresaprov)
                    ->setCellValue('D' . $i, $registro->Nombre)
                    ->setCellValue('E' . $i, $registro->Titulo)
                    ->setCellValue('F' . $i, $registro->TituloCorto)
                    ->setCellValue('G' . $i, $registro->SubTitulo)
                    ->setCellValue('H' . $i, $registro->BNF_TipoBeneficio_id)
                    ->setCellValue('I' . $i, $registro->DatoBeneficio)
                    ->setCellValue('J' . $i, strip_tags($registro->Descripcion))
                    ->setCellValue('K' . $i, strip_tags($registro->CondicionesUso))
                    ->setCellValue('L' . $i, $registro->Direccion)
                    ->setCellValue('M' . $i, $registro->Telefono)
                    ->setCellValue('N' . $i, ((int)$registro->Premium == 0) ? 'No' : 'Si')
                    ->setCellValue('O' . $i, $registro->Distrito)
                    ->setCellValue('P' . $i, $registro->FechaFinVigencia)
                    ->setCellValue('Q' . $i, $registro->FechaInicioPublicacion)
                    ->setCellValue('R' . $i, $registro->FechaFinPublicacion)
                    ->setCellValue('S' . $i, $registro->Stock)
                    ->setCellValue('T' . $i, $registro->Correo)
                    ->setCellValue('U' . $i, $registro->Estado)
                    ->setCellValue('V' . $i, $registro->DescargaMaximaDia)
                    ->setCellValue('W' . $i, $registro->FechaCreacion)
                    ->setCellValue('X' . $i, $registro->FechaActualizacion)
                    ->setCellValue('Y' . $i, ((int)$registro->Eliminado == 0) ? 'Activo' : 'Inactivo')
                    ->setCellValue('Z' . $i, $campaniadata)
                    ->setCellValue('AA' . $i, $categoriadata)
                    ->setCellValue('AB' . $i, $segmentodata)
                    ->setCellValue('AC' . $i, $ubigeo)
                    ->setCellValue('AD' . $i, $rubro);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Ofertas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportNormalAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $i = 2;
        $resultado = $this->getOfertaEmpresaClienteTable()->getReportsNormal();
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte_Ofertas")
                ->setSubject("Ofertas")
                ->setDescription("Documento Listado de Ofertas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Ofertas");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:G1' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

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
            $objPHPExcel->getActiveSheet()->getStyle('A1:GI' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Oferta')
                ->setCellValue('C1', 'Empresa')
                ->setCellValue('D1', 'Numero de Cupones')
                ->setCellValue('E1', 'Fecha de Creacion')
                ->setCellValue('F1', 'Fecha de Actualizacion')
                ->setCellValue('G1', 'Eliminado');

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->BNF_Oferta_id)
                    ->setCellValue('C' . $i, $registro->BNF_Empresa_id)
                    ->setCellValue('D' . $i, (int)$registro->NumeroCupones)
                    ->setCellValue('E' . $i, $registro->FechaCreacion)
                    ->setCellValue('F' . $i, $registro->FechaActualizacion)
                    ->setCellValue('G' . $i, ((int)$registro->Eliminado == 0) ? 'Activo' : 'Inactivo');
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="OfertasEmpNormal.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportEspecialAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $i = 2;
        $resultado = $this->getOfertaEmpresaClienteTable()->getReportsEspecial();
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte_Ofertas")
                ->setSubject("Ofertas")
                ->setDescription("Documento Listado de Ofertas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Ofertas");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:G1' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

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
            $objPHPExcel->getActiveSheet()->getStyle('A1:GI' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Oferta')
                ->setCellValue('C1', 'Empresa')
                ->setCellValue('D1', 'Numero de Cupones')
                ->setCellValue('E1', 'Fecha de Creacion')
                ->setCellValue('F1', 'Fecha de Actualizacion')
                ->setCellValue('G1', 'Eliminado');

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->BNF_Oferta_id)
                    ->setCellValue('C' . $i, $registro->BNF_Empresa_id)
                    ->setCellValue('D' . $i, (int)$registro->NumeroCupones)
                    ->setCellValue('E' . $i, $registro->FechaCreacion)
                    ->setCellValue('F' . $i, $registro->FechaActualizacion)
                    ->setCellValue('G' . $i, ((int)$registro->Eliminado == 0) ? 'Activo' : 'Inactivo');
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="OfertasEmpEspecial.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function getprovAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $bolsa = array('1' => 0, '2' => 0, '3' => 0);
        $tipo = array();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $val = $post_data['opt'];
            if ($this->getPaqueteEmpresaProveedorTable()->getTotalPaqueteEmpresas($id, $val) > 0) {
                //Obtenemos El total de la bolsa
                $bolsaTotal = $this->getBolsaTotalTable()->getBolsaTotalEmpresa($id);
                foreach ($bolsaTotal as $valores) {
                    $bolsa[$valores->BNF_TipoPaquete_id] = (int)$valores->BolsaActual;
                    $tipopaquete = $this->getTipoPaqueteTable()->getTipoPaquete($valores->BNF_TipoPaquete_id);
                    $tipo[$tipopaquete->id] = $tipopaquete->NombreTipoPaquete;
                }

                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'valores' => $bolsa,
                            'tipos' => $tipo
                        )
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false)));
            }
        }
        return $response;
    }

    public function getdepaAction()
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

    public function assignnormalAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $empresas = array();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['oferta'];
            $emp = $post_data['normal'];
            if ($this->getOfertaTable()->getOfertaExits($id) > 0) {
                //Asignacion de Empresas Normales
                foreach ($emp as $empvalue) {
                    //Verificamos si existe la empresas
                    if ($this->getEmpresaTable()->getEmpresasClienteNormExist($empvalue)) {
                        //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                        if ($this->getOfertaEmpresaClienteTable()
                                ->getOfertaEmpresaClienteExist($id, $empvalue) > 0
                        ) {
                            $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                                ->getOfertaEmpresaClienteSeach($id, $empvalue);
                            $ofertaEmpCli->Eliminado = '0';
                            $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                        } else {
                            $ofertaEmpCli = new OfertaEmpresaCliente();
                            $ofertaEmpCli->BNF_Empresa_id = $empvalue;
                            $ofertaEmpCli->BNF_Oferta_id = $id;
                            $ofertaEmpCli->Eliminado = '0';
                            $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                        }
                        $empresas[] = $empvalue;
                    }
                }
                $this->flashMessenger()->addSuccessMessage('Asignacion Completa.');
            } else {
                $this->flashMessenger()->addErrorMessage('La Oferta no existe.');
            }
        }
        return $this->redirect()->toRoute('oferta', array('action' => 'assign', 'id' => $id));
    }

    public function assignnormaltodosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $empresas = array();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $emp = $post_data['emp'];
            $opt = $post_data['opt'];
            if ($this->getOfertaTable()->getOfertaExits($id) > 0) {
                //Asignacion de Empresas Normales
                foreach ($emp as $empvalue) {
                    //Verificamos si existe la empresas
                    if ($this->getEmpresaTable()->getEmpresasClienteNormExist($empvalue)) {
                        //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                        if ($this->getOfertaEmpresaClienteTable()
                                ->getOfertaEmpresaClienteExist($id, $empvalue) > 0
                        ) {
                            $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                                ->getOfertaEmpresaClienteSeach($id, $empvalue);
                            if ($opt == "quitN") {
                                $ofertaEmpCli->Eliminado = '1';
                            } elseif ($opt == "asigN") {
                                $ofertaEmpCli->Eliminado = '0';
                            }
                            $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                        } else {
                            $ofertaEmpCli = new OfertaEmpresaCliente();
                            $ofertaEmpCli->BNF_Empresa_id = $empvalue;
                            $ofertaEmpCli->BNF_Oferta_id = $id;
                            $ofertaEmpCli->Eliminado = '0';
                            $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                        }
                        $empresas[] = $empvalue;
                    }
                }
                $response->setContent(
                    Json::encode(
                        array('response' => true, 'message' => 'Asignacion Completa.', 'values' => $empresas)
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'La Oferta no existe.')));
            }
        }
        return $response;
    }

    public function assignespecialtotalAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $empresas = array();
        $id = null;
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $emp = $post_data['emp'];
            $opt = $post_data['opt'];
            if ($this->getOfertaTable()->getOfertaExits($id) > 0) {
                //Asignacion de Empresas Especial
                foreach ($emp as $empvalue) {
                    //Verificamos si existe la empresas
                    if ($this->getEmpresaTable()->getEmpresasClienteEspExist($empvalue)) {
                        //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                        if ($this->getOfertaEmpresaClienteTable()
                                ->getOfertaEmpresaClienteExist($id, $empvalue) > 0
                        ) {
                            $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                                ->getOfertaEmpresaClienteSeach($id, $empvalue);
                            if ($opt == "quitE") {
                                $ofertaEmpCli->Eliminado = '1';
                            } elseif ($opt == "asigE") {
                                $ofertaEmpCli->Eliminado = '0';
                            }
                            $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                        } else {
                            $ofertaEmpCli = new OfertaEmpresaCliente();
                            $ofertaEmpCli->BNF_Empresa_id = $empvalue;
                            $ofertaEmpCli->BNF_Oferta_id = $id;
                            $ofertaEmpCli->Eliminado = '0';
                            $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                        }
                        //Verificamos Relacion de Subgrupos
                        $subgrupos = $this->getSubgrupoTable()->getSubgruposEmpresa($empvalue);
                        foreach ($subgrupos as $subgrupo) {
                            //Verificamos si Tiene Relacion de Oferta Subgrupo
                            if ($this->getOfertaSubgrupoTable()->getOfertaSubgrupoExist($id, $subgrupo->id) > 0) {
                                $ofertaSubgrupo = $this->getOfertaSubgrupoTable()
                                    ->getOfertaSubgrupoSeach($id, $subgrupo->id);
                                if ($opt == "quitE") {
                                    $ofertaSubgrupo->Eliminado = '1';
                                } elseif ($opt == "asigE") {
                                    $ofertaSubgrupo->Eliminado = '0';
                                }
                                $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                            } else {
                                $ofertaSubgrupo = new OfertaSubgrupo();
                                $ofertaSubgrupo->BNF_Subgrupo_id = $subgrupo->id;
                                $ofertaSubgrupo->BNF_Oferta_id = $id;
                                $ofertaSubgrupo->Eliminado = '0';
                                $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                            }
                        }
                        $empresas[] = $empvalue;
                    }
                }
                $response->setContent(
                    Json::encode(
                        array('response' => true, 'message' => 'Asignacion Completa.', 'values' => $empresas)
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'La Oferta no existe.')));
            }
        }
        return $response;
    }

    public function assignespecialAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $empresas = array();
        $id = null;
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['oferta'];
            $emp = $post_data['especial'];
            if ($this->getOfertaTable()->getOfertaExits($id) > 0) {
                //Asignacion de Empresas Especial
                foreach ($emp as $empvalue) {
                    //Verificamos si existe la empresas
                    if ($this->getEmpresaTable()->getEmpresasClienteEspExist($empvalue)) {
                        //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                        if ($this->getOfertaEmpresaClienteTable()
                                ->getOfertaEmpresaClienteExist($id, $empvalue) > 0
                        ) {
                            $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                                ->getOfertaEmpresaClienteSeach($id, $empvalue);
                            $ofertaEmpCli->Eliminado = '0';
                            $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                        } else {
                            $ofertaEmpCli = new OfertaEmpresaCliente();
                            $ofertaEmpCli->BNF_Empresa_id = $empvalue;
                            $ofertaEmpCli->BNF_Oferta_id = $id;
                            $ofertaEmpCli->Eliminado = '0';
                            $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                        }
                        //Verificamos Relacion de Subgrupos
                        $subgrupos = $this->getSubgrupoTable()->getSubgruposEmpresa($empvalue);
                        foreach ($subgrupos as $subgrupo) {
                            //Verificamos si Tiene Relacion de Oferta Subgrupo
                            if ($this->getOfertaSubgrupoTable()->getOfertaSubgrupoExist($id, $subgrupo->id) > 0) {
                                $ofertaSubgrupo = $this->getOfertaSubgrupoTable()
                                    ->getOfertaSubgrupoSeach($id, $subgrupo->id);
                                $ofertaSubgrupo->Eliminado = '0';
                                $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                            } else {
                                $ofertaSubgrupo = new OfertaSubgrupo();
                                $ofertaSubgrupo->BNF_Subgrupo_id = $subgrupo->id;
                                $ofertaSubgrupo->BNF_Oferta_id = $id;
                                $ofertaSubgrupo->Eliminado = '0';
                                $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                            }
                        }
                        $empresas[] = $empvalue;
                    }
                }
                $this->flashMessenger()->addSuccessMessage('Asignacion Completa.');
            } else {
                $this->flashMessenger()->addErrorMessage('La Oferta no existe.');
            }
        }

        return $this->redirect()->toRoute('oferta', array('action' => 'assign', 'id' => $id));
    }

    public function assignespecialsubAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $empresas = array();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $empvalue = $post_data['emp'];
            $sub = $post_data['sub'];
            unset($sub[0]);

            if ($this->getOfertaTable()->getOfertaExits($id) > 0) {
                //Verificamos si existe la empresas
                if ($this->getEmpresaTable()->getEmpresasClienteEspExist($empvalue)) {
                    //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                    if ($this->getOfertaEmpresaClienteTable()->getOfertaEmpresaClienteExist($id, $empvalue) > 0) {
                        $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                            ->getOfertaEmpresaClienteSeach($id, $empvalue);
                        $ofertaEmpCli->Eliminado = '0';
                        $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                    } else {
                        $ofertaEmpCli = new OfertaEmpresaCliente();
                        $ofertaEmpCli->BNF_Empresa_id = $empvalue;
                        $ofertaEmpCli->BNF_Oferta_id = $id;
                        $ofertaEmpCli->Eliminado = '0';
                        $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                    }

                    if (!empty($sub)) {
                        foreach ($sub as $key => $val) {
                            //Verificamos Relacion de Subgrupos
                            if ($this->getSubgrupoTable()->getRelSubgrupoEmpresa($empvalue, $key)) {
                                //Verificamos si Tiene Relacion de Oferta Subgrupo
                                if ($this->getOfertaSubgrupoTable()->getOfertaSubgrupoExist($id, $key) > 0) {
                                    $ofertaSubgrupo = $this->getOfertaSubgrupoTable()
                                        ->getOfertaSubgrupoSeach($id, $key);
                                    $ofertaSubgrupo->Eliminado = $val;
                                    $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                                } else {
                                    $ofertaSubgrupo = new OfertaSubgrupo();
                                    $ofertaSubgrupo->BNF_Subgrupo_id = $key;
                                    $ofertaSubgrupo->BNF_Oferta_id = $id;
                                    $ofertaSubgrupo->Eliminado = '0';
                                    $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                                }
                            }
                        }
                    }
                    $empresas[] = $empvalue;
                }
                $response->setContent(
                    Json::encode(
                        array('response' => true, 'message' => 'Asignacion Completa.', 'values' => $empresas)
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'La Oferta no existe.')));
            }
        }
        return $response;
    }

    public function loadassignAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $normal = array();
        $especial = array();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            if ($this->getOfertaEmpresaClienteTable()->getOfertaEmpresaClienteTotal($id) > 0) {
                //Obtenemos los datos de Empresa Cliente Normal
                $empNormal = $this->getOfertaEmpresaClienteTable()->getOfertaEmpresaClienteNormal($id);
                foreach ($empNormal as $valores) {
                    $normal[] = $valores->BNF_Empresa_id;
                }

                //Obtenemos los datos de Empresa Cliente Normal
                $empEspecial = $this->getOfertaEmpresaClienteTable()->getOfertaEmpresaClienteEspecial($id);
                foreach ($empEspecial as $valores) {
                    $especial[] = $valores->BNF_Empresa_id;
                }

                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'normal' => $normal,
                            'especial' => $especial,
                            'message' => 'Datos de Oferta.'
                        )
                    )
                );
            } else {
                $response->setContent(
                    Json::encode(
                        array('response' => false, 'message' => 'No hay Asignaciones.')
                    )
                );
            }
        }
        return $response;
    }

    public function loadempAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $subgrupos = array();
        $active = array();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $oferta = (int)$post_data['ofert'];
            if ($this->getEmpresaTable()->getEmpresa($id)) {
                //Obtenemos los datos de Empresa
                $empEsp = $this->getEmpresaTable()->getEmpresa($id);
                $empresa = array(
                    'id' => $empEsp->id,
                    'nombre' => $empEsp->NombreComercial,
                    'tipo' => $empEsp->ClaseEmpresaCliente);
                //obtenemos los subgrupos
                $subgrupo = $this->getSubgrupoTable()->getSubgruposEmpresa($id);
                foreach ($subgrupo as $valores) {
                    $subgrupos[$valores->id] = $valores->Nombre;
                }
                //Obtenemos las Relaciones con Oferta
                $ofertaSub = $this->getOfertaSubgrupoTable()->getOfertaSubgruposEmp($oferta, $id);
                foreach ($ofertaSub as $valores) {
                    $active[] = $valores->BNF_Subgrupo_id;
                }

                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'empresa' => $empresa,
                            'subgrupos' => $subgrupos,
                            'active' => $active,
                            'message' => 'Datos de Empresa.'
                        )
                    )
                );
            } else {
                $response->setContent(
                    Json::encode(
                        array('response' => false, 'message' => 'Empresa no Existe.')
                    )
                );
            }
        }
        return $response;
    }

    public function deletenormalAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $empresas = array();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $opt = $post_data['opt'];
            $emp = $post_data['emp'];
            if ($this->getOfertaTable()->getOfertaExits($id) > 0) {
                if ($opt == "quitN") {
                    //Eliminacion de Empresas Normales
                    foreach ($emp as $empvalue) {
                        //Verificamos si existe la empresas
                        if ($this->getEmpresaTable()->getEmpresasClienteNormExist($empvalue)) {
                            //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                            if ($this->getOfertaEmpresaClienteTable()
                                    ->getOfertaEmpresaClienteExist($id, $empvalue) > 0
                            ) {
                                $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                                    ->getOfertaEmpresaClienteSeach($id, $empvalue);
                                $ofertaEmpCli->Eliminado = '1';
                                $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                            }
                            $empresas[] = $empvalue;
                        }
                    }
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => true,
                                'message' => 'Eliminacion Completa.',
                                'values' => $empresas
                            )
                        )
                    );
                }
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'La Oferta no existe.')));
            }
        }
        return $response;
    }

    public function deleteespecialAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $empresas = array();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $opt = $post_data['opt'];
            $emp = $post_data['emp'];
            if ($this->getOfertaTable()->getOfertaExits($id) > 0) {
                if ($opt == "quitE") {
                    //Eliminacion de Empresas Especial
                    foreach ($emp as $empvalue) {
                        //Verificamos si existe la empresas
                        if ($this->getEmpresaTable()->getEmpresasClienteEspExist($empvalue)) {
                            //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                            if ($this->getOfertaEmpresaClienteTable()
                                    ->getOfertaEmpresaClienteExist($id, $empvalue) > 0
                            ) {
                                $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                                    ->getOfertaEmpresaClienteSeach($id, $empvalue);
                                $ofertaEmpCli->Eliminado = '1';
                                $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                            }
                            //Verificamos Relacion de Subgrupos
                            $subgrupos = $this->getSubgrupoTable()->getSubgruposEmpresa($empvalue);
                            foreach ($subgrupos as $subgrupo) {
                                //Verificamos si Tiene Relacion de Oferta Subgrupo
                                if ($this->getOfertaSubgrupoTable()->getOfertaSubgrupoExist($id, $subgrupo->id) > 0) {
                                    $ofertaSubgrupo = $this->getOfertaSubgrupoTable()
                                        ->getOfertaSubgrupoSeach($id, $subgrupo->id);
                                    $ofertaSubgrupo->Eliminado = '1';
                                    $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                                }
                            }
                            $empresas[] = $empvalue;
                        }
                    }
                    $response->setContent(
                        Json::encode(
                            array('response' => true, 'message' => 'Eliminacion Completa.', 'values' => $empresas)
                        )
                    );
                }
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'La Oferta no existe.')));
            }
        }
        return $response;
    }

    public function saveImageAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resize_bool['oferta_img'] = false;
        $resize_bool['oferta_medium'] = false;
        $resize_bool['oferta_large'] = false;
        $path = './public/elements/oferta/';

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
                $resize->rename($path, $img3, $ext, $fileName, '-large');
                $guardado = true;
            } catch (\Exception $e) {
                $guardado = false;
            }
        }

        if ($guardado) {
            $logger = new Logger;
            $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_TEMP);
            $message = "Imagen guardada temporalmente: " . $fileName . "." . $ext;
            $logger->addWriter($writer);
            $logger->log(Logger::INFO, $message);

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

    /*Borrar Imagenes Temporales*/
    public function deleteImageAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $img = $this->getRequest()->getPost('val');
        $ext = $this->getRequest()->getPost('ext');
        $fullpath = './public/elements/oferta/' . $img;

        if (file_exists($fullpath)) {
            unlink($fullpath);
            $fullpath2 = str_replace('.' . $ext, '', $fullpath) . '-medium' . '.' . $ext;
            unlink($fullpath2);
            $fullpath3 = str_replace('.' . $ext, '', $fullpath) . '-large' . '.' . $ext;
            unlink($fullpath3);

            $logger = new Logger;
            $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_TEMP_DELETE);
            $message = "Imagen eliminada: " . $img;
            $logger->addWriter($writer);
            $logger->log(Logger::INFO, $message);

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

    /*Borrar Imagenes Guardadas*/
    public function deleteImagenAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $id = (int)$this->getRequest()->getPost('id');
        $response = $this->getResponse();

        $oferta_id = $this->getImagenTable()->getImagen($id)->BNF_Oferta_id;
        $count = $this->getImagenTable()->getImagenOferta($oferta_id);

        if (count($count) > 1) {
            if ($this->getImagenTable()->getImagen($id)->Principal == 0) {
                $dato = $this->getImagenTable()->deleteImagen($id);

                $trozos = explode(".", $dato);
                $ext = end($trozos);

                $fullpath = './public/elements/oferta/' . $dato;
                if (file_exists($fullpath)) {
                    unlink($fullpath);
                    $fullpath2 = str_replace('.' . $ext, '', $fullpath) . '-medium' . '.' . $ext;
                    unlink($fullpath2);
                    $fullpath3 = str_replace('.' . $ext, '', $fullpath) . '-large' . '.' . $ext;
                    unlink($fullpath3);

                    $logger = new Logger;
                    $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_OFERTA_DELETE);
                    $message = "Imagen eliminada de la oferta " . $oferta_id . ": " . $dato;
                    $logger->addWriter($writer);
                    $logger->log(Logger::INFO, $message);

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

    public function pricipalImageAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $oferta_id = (int)$this->getRequest()->getPost('oferta_id');
        $id = (int)$this->getRequest()->getPost('id');

        $dato = $this->getImagenTable()->principalImagen($id, $oferta_id);

        $response = $this->getResponse();/**/
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

    public function saveBannerAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $guardado = false;
        $path = './public/elements/banners/';
        $response = $this->getResponse();
        $ext = $this->getRequest()->getPost('ext');
        $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);

        $manager = new ImageManager(array('driver' => 'imagick'));
        $img = $manager->make($_FILES['val']['tmp_name']);

        $config = $this->getServiceLocator()->get('Config');

        $resize = new Resize();

        $resize_bool['banner_lead'] = $resize->isResize($img, $config, 'banner_lead');

        try {
            $resize->rename($path, $img, $ext, $fileName, '');
            $guardado = true;
        } catch (\Exception $ex) {
            echo $ex;
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

    public function deleteBannerAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $eliminado = false;
        $response = $this->getResponse();
        $img = $this->getRequest()->getPost('val');
        $id_oferta = (int)$this->getRequest()->getPost('id_oferta');
        $fullpath = './public/elements/banners/' . $img;

        if ($id_oferta == 0) {
            if (file_exists($fullpath)) {
                unlink($fullpath);
                $eliminado = true;
            }
        } else {
            try {
                $ofertaformulariotable = $this->serviceLocator->get('Oferta\Model\Table\OfertaFormularioTable');
                $ofertaformulario = new OfertaFormulario();
                $ofertaformulario->BNF_Oferta_id = $id_oferta;
                $ofertaformulario->BNF_Formulario_id = 1;
                $ofertaformulario->Descripcion = null;
                $ofertaformulario->Activo = '0';
                $ofertaformulariotable->updateOfertaFormularioXOferta($ofertaformulario);

                if (file_exists($fullpath)) {
                    unlink($fullpath);
                }

                $logger = new Logger;
                $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_IMAGE_OFERTA_DELETE);
                $message = "Banner eliminado de la oferta " . $id_oferta . ": " . $img;
                $logger->addWriter($writer);
                $logger->log(Logger::INFO, $message);
                $eliminado = true;
            } catch (\Exception $e) {
                $eliminado = false;
            }
        }

        if ($eliminado) {
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

    public function eliminar($id, $val)
    {
        $this->getOfertaTable()->deleteOferta($id, $val);
        $this->getOfertaEmpresaClienteTable()->deleteOfertaEmpresaCliente($id, $val);

        if ($val == 1) {
            $oferta = $this->getOfertaTable()->getOferta($id);
            $bolsaTotalNueva = $this->getBolsaTotalTable()->getBolsaTotal(
                $oferta->BNF_BolsaTotal_TipoPaquete_id,
                $oferta->BNF_BolsaTotal_Empresa_id
            );
            $bolsaTotalNueva->BolsaActual = $bolsaTotalNueva->BolsaActual + (int)$oferta->Stock;
            $this->getBolsaTotalTable()->editBolsa($bolsaTotalNueva);
        } elseif ($val == 0) {
            $oferta = $this->getOfertaTable()->getOferta($id);
            $bolsaTotalNueva = $this->getBolsaTotalTable()->getBolsaTotal(
                $oferta->BNF_BolsaTotal_TipoPaquete_id,
                $oferta->BNF_BolsaTotal_Empresa_id
            );
            $bolsaTotalNueva->BolsaActual = $bolsaTotalNueva->BolsaActual - (int)$oferta->Stock;
            $this->getBolsaTotalTable()->editBolsa($bolsaTotalNueva);
        }
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
        $cadena = trim($cadena);
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

    public function verificarImagen($imagen)
    {
        $config = $this->getServiceLocator()->get('Config');
        $upload = new UploadFile();
        $size = new Size(array('max' => $config['size_file_upload']));
        $extension = new Extension(array('jpg', 'png'));

        if ($upload->isValid($imagen)) {
            if ($size->isValid($imagen)) {
                if (!$extension->isValid($imagen)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function validarCampos($request)
    {

        $approved = false;
        $messages = array();

        $validStock = new Step(array('baseValue' => 0, 'step' => 1));
        $validNotEmpty = new NotEmpty(NotEmpty::ALL);
        $validDate = new Date(array('format' => 'Y-m-d'));

        $atributos = $request->getPost()->atributos;
        $stock = $request->getPost()->stocks;

        $vigencia = ((int)$request->getPost()->Tipo == $this::TIPO_OFERTA_LEAD) ? $request->getPost()->atributos :
            $request->getPost()->vigencias;


        $beneficio = $request->getPost()->beneficios;

        if (count($atributos) == count($stock) and count($stock) == count($vigencia)
            and count($beneficio) == count($vigencia)
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
            if ((int)$request->getPost()->Tipo != $this::TIPO_OFERTA_LEAD) {
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
            }
            //Validar Beneficios
            $beneficiosState = true;
            if (is_array($beneficio) || is_object($beneficio)) {
                foreach ($beneficio as $value) {
                    if (!$validNotEmpty($value)) {
                        $messages['beneficios'][] = "El campo no puede quedar vacío.";
                        $beneficiosState = false;
                    } else {
                        $messages['beneficios'][] = "";
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
            if ($atributosState and $stockState and $vigenciaState and $accionState and $beneficiosState) {
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

    public function getDescargasByAtributoAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $id = $this->getRequest()->getPost('id');
        $has_downloads = $this->getCuponTable()->hasCuponDescargasByAtribuo($id);
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