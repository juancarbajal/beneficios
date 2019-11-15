<?php

namespace Application\Controller;

use Application\Model\AsignacionEstadoLog;
use Application\Model\CuponPuntosAsignacion;
use Application\Model\CuponPuntosLog;
use Application\Model\OfertaPuntos;
use Application\Model\OfertaPuntosDelivery;
use Perfil\Services\Puntos;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Application\Cache\CacheManager;
use Application\Service\MobileDetect;
use DOMPDFModule\View\Model\PdfModel;
use Zend\Mime\Mime;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class PuntosController extends AbstractActionController
{
    const PAIS_DEFAULT = 1;
    const CATEGORIA_DEFAULT = 9;
    const OFETAS_PREMIUN = 1;
    const OFETAS_NOVADADES = 2;
    const OFETAS_DESTACADOS = 3;
    const OFETAS_RESTANTES = 0;
    const NOT_OFFSET = -1;
    const TIPO_CATEGORIA = 1;
    const TIPO_CAMPANIA = 2;
    const TIPO_TIENDA = 3;
    const OFERTA_TIPO_SPLIT = "Split";
    const OFERTA_TIPO_UNICO = "Unico";
    const OPERACION_APLICAR = "Aplicar";
    const ROUTER = 'coupon-puntos';

    #region ObjectTables
    public function getBannersCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\BannersCategoriaTable');
    }

    public function getGaleriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\GaleriaTable');
    }

    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
    }

    public function getUbigeoTable()
    {
        return $this->serviceLocator->get('Application\Model\UbigeoTable');
    }

    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaTable');
    }

    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosTable');
    }

    public function getLayoutPuntosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\LayoutPuntosTable');
    }

    public function getLayoutPuntosPosicionTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\LayoutPuntosPosicionTable');
    }

    public function getConfiguracionesTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\ConfiguracionesTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
    }

    public function getPreguntasTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\PreguntasTable');
    }

    public function getCuponPuntosTable()
    {
        return $this->serviceLocator->get('Perfil\Model\Table\CuponPuntosTable');
    }

    public function getAsignacionTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\AsignacionTable');
    }

    public function getOfertaPuntosAtributosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosAtributosTable');
    }

    public function getOfertaPuntosRubroTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosRubroTable');
    }

    public function getAsignacionEstadoLogTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\AsignacionEstadoLogTable');
    }

    public function getCuponPuntosLogTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CuponPuntosLogTable');
    }

    public function getCuponPuntosAsignacionTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CuponPuntosAsignacionTable');
    }

    public function getDeliveryPuntosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\DeliveryPuntosTable');
    }

    public function getOfertaPuntosDeliveryTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosDeliveryTable');
    }

    #endregion

    public function getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo)
    {
        $data = array();
        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Empresas']['getAfiliados']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];
        $keyAFIL = "empafil-" . $segmento . $ubigeo_id . $empresa . $subgrupo;
        if ($cache->hasItem($keyAFIL) and $cacheStatus == true) {
            $logos = $cache->getItem($keyAFIL);
        } else {
            $logos = $this->getOfertaTable()
                ->getLogoEmpresaXOferta($segmento, $ubigeo_id, $empresa, $subgrupo)
                ->toArray();
            $cache->setItem($keyAFIL, $logos);
        }

        $empresas_afiliadas = array();
        foreach ($logos as $dato) {
            $logo['logo'] = $dato['Nombre'];
            $logo['slug'] = $dato['Slug'];
            array_push($empresas_afiliadas, $logo);
        }

        $count = count($empresas_afiliadas);
        if ($count > 25) {
            $rand = rand(1, $count - 1);
            for ($i = 0; $i < 25; $i++) {
                $data[] = $empresas_afiliadas[$rand + $i];
                if ($rand + $i == $count - 1) {
                    $rand = -($i + 1);
                }
            }
        } else {
            $data = $empresas_afiliadas;
        }

        return $data;
    }

    public function getUbigeo()
    {
        return $this->identity()['ubigeo'];
    }

    public function setUbigeo($ubigeo)
    {
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        $data_user['ubigeo'] = $ubigeo;
        $session->offsetSet('storage', $data_user);
    }

    public function indexAction()
    {
        $meses = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        );

        //Datos de Ubigeo
        $ubigeo_id = $this->getUbigeo();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $this->setUbigeo($ubigeo_id);
        }
        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        //Datos generales
        $config = $this->getServiceLocator()->get('Config');
        $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $puntos = $this->identity()['exist_puntos'];
        $flagcheckboxMoney = $this->identity()['flagcheckboxMoney'];
        $segmentos_puntos = $this->identity()['segmentos_puntos'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;

        if (empty($puntos)) {
            return $this->redirect()->toRoute('application');
        }

        //empresas afiliadas
        $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);

        //categorias
        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $catotros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        $category = "puntos";

        //cache
        $ttl = $config['items']['Ofertas']['indexAction']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];

        //Consulta Total de Ofertas de EP
        $totalOEPFilter = array();
        $keyEOP = __CLASS__ . __FUNCTION__ . $empresa;
        if ($cache->hasItem($keyEOP) and $cacheStatus == true) {
            $totalOEPFilter = $cache->getItem($keyEOP);
        } else {
            $totalOEP = $this->getOfertaPuntosTable()->totalOfertasByEmpresas($empresa);
            foreach ($totalOEP as $value) {
                $totalOEPFilter[$value->SlugEmpresa] = $value->TotalOfertas;
            }
            $cache->setItem($keyEOP, $totalOEPFilter);
        }

        //ofertas Premiun
        $ofertasPRM_L = array();
        $ofertasPRM_ID = array();
        $keyPRM = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento .
            $this::CATEGORIA_DEFAULT . $subgrupo . "1";
        if ($cache->hasItem($keyPRM) and $cacheStatus == true) {
            $ofertasPRM = $cache->getItem($keyPRM);
        } else {
            $ofertasPRM = $this->getOfertaPuntosTable()->getOrdenamientoOfertas(
                $ubigeo_id,
                $empresa,
                $this::CATEGORIA_DEFAULT,
                null,
                $segmentos_puntos,
                $this::NOT_OFFSET,
                $this::OFETAS_PREMIUN,
                null
            );
            $ofertasPRM = $ofertasPRM->toArray();
            $cache->setItem($keyPRM, $ofertasPRM);
        }

        foreach ($ofertasPRM as $dato) {
            $convert = new OfertaPuntos();
            $convert->exchangeArray($dato);
            array_push($ofertasPRM_L, $convert);
            array_push($ofertasPRM_ID, $convert->id);
        }

        //Ofertas Novedades
        $ofertasNOV_L = array();
        $ofertasNOV_ID = array();
        $keyNOV = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento .
            $this::CATEGORIA_DEFAULT . $subgrupo . "2";
        if ($cache->hasItem($keyNOV) and $cacheStatus == true) {
            $ofertasNOV = $cache->getItem($keyNOV);
        } else {
            $ofertasNOV = $this->getOfertaPuntosTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $this::CATEGORIA_DEFAULT,
                    null,
                    $segmentos_puntos,
                    $this::NOT_OFFSET,
                    $this::OFETAS_NOVADADES,
                    null
                );
            $ofertasNOV = $ofertasNOV->toArray();
            $cache->setItem($keyNOV, $ofertasNOV);
        }

        foreach ($ofertasNOV as $dato) {
            $convert = new OfertaPuntos();
            $convert->exchangeArray($dato);
            array_push($ofertasNOV_L, $convert);
            array_push($ofertasNOV_ID, $convert->id);
        }

        //ofertas Destacadas
        $ofertasDEST_L = array();
        $ofertasDEST_ID = array();
        $keyDEST = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento .
            $this::CATEGORIA_DEFAULT . $subgrupo . "3";
        if ($cache->hasItem($keyDEST) and $cacheStatus == true) {
            $ofertasDEST = $cache->getItem($keyDEST);
        } else {
            $ofertasDEST = $this->getOfertaPuntosTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $this::CATEGORIA_DEFAULT,
                    null,
                    $segmentos_puntos,
                    $this::NOT_OFFSET,
                    $this::OFETAS_DESTACADOS,
                    null
                );
            $ofertasDEST = $ofertasDEST->toArray();
            $cache->setItem($keyDEST, $ofertasDEST);
        }

        foreach ($ofertasDEST as $dato) {
            $convert = new OfertaPuntos();
            $convert->exchangeArray($dato);
            array_push($ofertasDEST_L, $convert);
            array_push($ofertasDEST_ID, $convert->id);
        }

        $arrayMerge_L = array_merge($ofertasPRM_L, $ofertasNOV_L, $ofertasDEST_L);
        $arrayMerge_L = array_unique($arrayMerge_L);

        //Ordenamientos
        $layout = $this->getLayoutPuntosTable()->getLayoutPuntos($empresa);
        if (count($layout) == 0) {
            $layout = $this->getLayoutPuntosTable()->getLayoutPuntos();
        }

        $resultadosOrden = $this->generarOrdenamiento($layout, $arrayMerge_L);

        $ofertasOrdenadas = $resultadosOrden[0];
        $orden = $resultadosOrden[1];

        $ordenamiento = $this->generarOfertasOrdenadas($orden, $ofertasOrdenadas);

        $ofertas_premium = $ordenamiento[0];
        $ofertas_descubre = $ordenamiento[1];
        $ofertas_restantes = $ordenamiento[2];
        $ofertasNOT = $ordenamiento[3];
        $cantidad_OP = $ordenamiento[4];
        $cantidad_OM = $ordenamiento[5];

        $noCargar = $ofertasNOT;
        $total_mostrado = ($cantidad_OP + $cantidad_OM);
        $restante = count($ofertasOrdenadas);

        if ($total_mostrado < $restante) {
            $offset = 0;
        } else {
            $offset = $total_mostrado - $restante;
        }

        //Banners Home
        $banners = $this->getBannersCategoriaTable()->getBannerCategoriaAll(9, $empresa);
        $array = array();
        foreach ($banners as $value) :
            $array[$value->BNF_Banners_id] = array('image' => $value->Imagen, 'link' => $value->Url);
        endforeach;
        $banners = $array;

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'puntos',
                'url_slug' => 'puntos',
                'category' => $category,
                'router' => $this::ROUTER,
                'descripcion' => 'Puntos',
                'total' => $totalOEPFilter,
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas-puntos"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'banners' => $banners,
                'cliente' => $dni,
                'imgemp' => $img,
                'exist_puntos' => $puntos,
                'ubigeo' => $ubigeo,
                'ofertas_premiun' => $ofertas_premium,
                'ofertas_descubre' => $ofertas_descubre,
                'ofertas_restantes' => $ofertas_restantes,
                'data' => $data,
                'meses' => $meses,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'ubigeo_id' => $ubigeo_id,
                'categoria_id' => $this::CATEGORIA_DEFAULT,
                'catotros' => $catotros,
                'offset' => $offset,
                'notin' => $noCargar,
                'flagcheckboxMoney'=>$flagcheckboxMoney
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('application/puntos/index-mobile');
        }
        return $view;
    }

    public function couponAction()
    {
        $meses = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        );

        $array = array('bus', 'com', 'cam', 'tie');
        $ofertas = null;
        $company = null;

        //Datos generales
        $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $email = isset($this->identity()['email']) ? $this->identity()['email'] : null;
        $idCliente = isset($this->identity()['id']) ? $this->identity()['id'] : null;
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;
        $segmentos = $this->identity()['segmentos_puntos'];

        $ubigeo_id = $this->getUbigeo();
        $coupon = $this->params()->fromRoute('coupon', 0);
        $category = $this->params()->fromRoute('val', null);
        $cat = $this->getCategoriaTable()->getBuscarCategoria($category);

        $idCategoria = (!$cat) ? substr($category, 0, 3) : $cat->id;
        if (!$cat && !in_array($idCategoria, $array)) {
            return $this->redirect()->toRoute('404');
        }

        $configuraciones = $this->getConfiguracionesTable()->fetchAll();
        $conf = array();
        foreach ($configuraciones as $dat) {
            $conf[$dat->Campo] = $dat->Atributo;
        }

        $catotros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        //buscar datos del Cupon
        $cupon = $this->getOfertaPuntosTable()->getCuponOferta($empresa, $dni, $coupon, $segmentos);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $this->setUbigeo($ubigeo_id);
        }
        if ($cupon == false) {
            return $this->redirect()->toRoute('404', array('opt' => $coupon));
        }

        //imagenes de cupon
        $imgCupon = $this->getOfertaPuntosTable()->getImagenCupon($cupon->idOferta);
        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        //ofertas
        $ofertas = $this->getOfertaPuntosTable()
            ->getOrdenamientoOfertas($ubigeo_id, $empresa, $this::CATEGORIA_DEFAULT, null, $segmentos);


        $ofertas_relacionadas = array();
        foreach ($ofertas as $oferta) {
            if ($oferta->id != $cupon->idOferta) {
                array_push($ofertas_relacionadas, $oferta);
            }
        }

        //empresas afiliadas
        $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);

        //datos Cliente
        $clienteData = $this->getAsignacionTable()->getPuntosAsignados($dni);

        //datos Atributos
        $atributosData = $this->getOfertaPuntosAtributosTable()->getAllOfertaPuntosAtributos($cupon->idOferta);

        $config = $this->getServiceLocator()->get('Config');
        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);

        //Banners Home
        $banners = $this->getBannersCategoriaTable()->getBannerOferta(9, $empresa);
        $array = array();
        foreach ($banners as $value) :
            $array[$value->BNF_Banners_id] = array('image' => $value->Imagen, 'link' => $value->Url);
        endforeach;
        $banners = $array;

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'coupon-puntos/' . $coupon,
                'router' => $this::ROUTER,
                'slug' => 'ptos',
                'category' => $category,
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas-puntos"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'ubigeo' => $ubigeo,
                'ofertas_relacionadas' => $ofertas_relacionadas,
                'cupon' => $cupon,
                'imgCupon' => $imgCupon,
                'empresaID' => $empresa,
                'clienteID' => $idCliente,
                'meses' => $meses,
                'data' => $data,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'imgemp' => $img,
                'url_slug' => $coupon,
                'clienteData' => $clienteData,
                'atributosData' => $atributosData,
                'ubigeo_id' => $ubigeo_id,
                'conf' => $conf,
                'email_user' => $email,
                'catotros' => $catotros,
                'banners' => $banners,
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('application/puntos/cupon-mobile');
        }
        return $view;
    }

    public function envioCuponAction()
    {
        $data = array();
        $cant_puntos = 0;
        $mensaje = null;
        $res = null;
        $mostrar = "";
        $number = "";
        $id_correoCliente = null;
        $array = array('bus', 'com', 'cam', 'tie');
        $response = $this->getResponse();

        if (!isset($this->identity()['Empresa'])) {
            $response->setContent(
                Json::encode(
                    array(
                        'session' => false
                    )
                )
            );

            return $response;
        }

        $email = $this->getRequest()->getPost('email');
        $idOferta = $this->getRequest()->getPost('idOferta');
        $idEmpresa = $this->getRequest()->getPost('idEmpresa');
        $idCliente = $this->getRequest()->getPost('idCliente');
        $slug_cat = $this->getRequest()->getPost('slug_cat');
        $puntos = $this->getRequest()->getPost('puntos');
        $atributo = (int)$this->getRequest()->getPost('atributo');
        $delivery = $this->getRequest()->getPost('delivery');
        $cat = $this->getCategoriaTable()->getBuscarCategoria($slug_cat);
        $idCategoria = (!$cat) ? substr($slug_cat, 0, 3) : $cat->id;
        $oferta = $this->getOfertaPuntosTable()->getOfertaPuntos($idOferta);

        if (isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
        } else {
            $url = "http://" . $_SERVER['HTTP_HOST'];
        }

        $parsedUrl = parse_url($url);
        $host = explode('.', $parsedUrl['host']);
        $subdomain = $host[0];

        if (!$cat && !in_array($idCategoria, $array)) {
            return $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                        'status' => 404
                    )
                )
            );
        }

        $clienteTable = $this->serviceLocator->get('Auth\Model\Table\ClienteTable');
        $dataCliente = $clienteTable->getCliente($idCliente);

        $clienteCorreoTable = $this->serviceLocator->get('Auth\Model\Table\ClienteCorreoTable');
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        $data_user['email'] = $email;
        $session->offsetSet('storage', $data_user);

        $clienteCorreo = $clienteCorreoTable->buscarCorreo($email, $idCliente);
        if ($clienteCorreo == false) {
            $id_correoCliente = $clienteCorreoTable->saveCorreo($idCliente, $email);
        } else {
            $id_correoCliente = $clienteCorreo->id;
            $clienteCorreoTable->updateCorreo($clienteCorreo->id);
        }

        $maxDescargas = $oferta->DescargaMaxima;
        $resultado = $this->getCuponPuntosTable()->verifyLimit($idOferta, $idCliente, $atributo);
        $clienteData = $this->getAsignacionTable()->getPuntosAsignados($dataCliente->NumeroDocumento);
        $disponibles = $clienteData->TotalAsignados;

        if ($resultado < $maxDescargas) {
            $cupon = $this->getCuponPuntosTable()->getCuponValid($idOferta, $atributo);
            if ($cupon == false) {
                $res = false;
                $mensaje = 'error';
            } else {

                $datosDelivery = $this->getDeliveryPuntosTable()->getFormulario($idOferta);
                if ($datosDelivery->count() > 0) {
                    foreach ($datosDelivery as $item) {
                        if ($item->Requerido == 1 && empty($delivery[$item->Nombre_Campo])) {
                            $campo = !empty($item->Etiqueta_Campo) ? $item->Etiqueta_Campo : $item->Nombre_Campo;
                            $error = "El campo " . $campo . " es requerido";

                            return $response->setContent(
                                Json::encode(
                                    array(
                                        'response' => false,
                                        'session' => true,
                                        'status' => 200,
                                        'errorField' => $item->Nombre_Campo,
                                        'errorMessage' => $error
                                    )
                                )
                            );
                        }
                    }
                }

                $rubroOferta = $this->getOfertaPuntosRubroTable()->getOfertaPuntosRubroByOferta($oferta->id);

                $resultadoCupon = $this->getCuponPuntosTable()->updateCupon(
                    $this->identity()['Empresa'],
                    $idCliente,
                    $cupon->id,
                    $idCategoria,
                    $rubroOferta->BNF_Rubro_id,
                    $id_correoCliente,
                    $puntos,
                    $disponibles
                );

                if ($resultadoCupon) {
                    $precio = "";
                    $titulo = "";
                    $res = true;
                    $mensaje = 'cupon generado';
                    $empresa = $this->getEmpresaTable()->getEmpresa($idEmpresa);
                    $logoCliente = $empresa->Logo;
                    $dataAtributo = null;
                    if ($oferta->TipoPrecio == $this::OFERTA_TIPO_UNICO) {
                        $data['Stock'] = (int)$oferta->Stock - 1;
                        if ($data['Stock'] == 0) {
                            $data['Estado'] = 'Caducado';
                        }
                        $this->getOfertaPuntosTable()->updateOferta($data, $oferta->id);

                        $precio = $oferta->PrecioVentaPublico;
                        $titulo = $oferta->Titulo;
                    } elseif ($oferta->TipoPrecio == $this::OFERTA_TIPO_SPLIT) {
                        $dataAtributo = $this->getOfertaPuntosAtributosTable()->getOfertaPuntosAtributos($atributo);
                        $dataAttr['Stock'] = (int)$dataAtributo->Stock - 1;
                        $this->getOfertaPuntosAtributosTable()
                            ->updateOfertaPuntosAtributos($dataAttr, $oferta->id, $atributo);

                        $precio = $dataAtributo->PrecioVentaPublico;
                        $titulo = $dataAtributo->NombreAtributo;

                        if ($dataAttr['Stock'] == 0) {
                            $totalAtributos = $this->getOfertaPuntosAtributosTable()->getTotalHabilitados($oferta->id);
                            if ($totalAtributos == 0) {
                                $data['Estado'] = 'Caducado';
                                $this->getOfertaPuntosTable()->updateOferta($data, $oferta->id);
                            }
                        }
                    }

                    $titulo_oferta = "S/. " . $precio . " por " . $titulo;

                    #region asignacion
                    $puntosUsados = $puntos;
                    $saldo = $disponibles - $puntos;

                    $asignacionesData = $this->getAsignacionTable()->getAsignacionesCliente($dataCliente->id);
                    $idAsignacion = 0;
                    foreach ($asignacionesData as $asignacion) {
                        $restante = $asignacion->CantidadPuntosDisponibles - $puntos;
                        if ($restante >= 0) {
                            $asignacion->CantidadPuntosUsados = $asignacion->CantidadPuntosUsados + $puntos;
                            $asignacion->CantidadPuntosDisponibles = $asignacion->CantidadPuntosDisponibles - $puntos;
                            $this->getAsignacionTable()->updateAsignacion($asignacion);

                            $idAsignacion = $asignacion->id;

                            $asignacionEstadoLog = new AsignacionEstadoLog();
                            $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacion->id;
                            $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                            $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                            $asignacionEstadoLog->CantidadPuntos = (int)$asignacion->CantidadPuntos;
                            $asignacionEstadoLog->CantidadPuntosUsados = (int)$asignacion->CantidadPuntosUsados;
                            $asignacionEstadoLog->CantidadPuntosDisponibles = (int)$asignacion->CantidadPuntosDisponibles;
                            $asignacionEstadoLog->CantidadPuntosEliminados = (int)$asignacion->CantidadPuntosEliminados;
                            $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                            $asignacionEstadoLog->Operacion = $this::OPERACION_APLICAR;
                            $asignacionEstadoLog->Puntos = $puntos;
                            $asignacionEstadoLog->Motivo = "Aplicando Puntos";
                            $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                            //Guardar Puntos Utilizados
                            $cuponPuntosAsignacion = new CuponPuntosAsignacion();
                            $cuponPuntosAsignacion->BNF2_Cupon_Puntos_id = $cupon->id;
                            $cuponPuntosAsignacion->BNF2_Asignacion_Puntos_id = $asignacion->id;
                            $cuponPuntosAsignacion->PuntosUtilizados = $puntos;
                            $this->getCuponPuntosAsignacionTable()->save($cuponPuntosAsignacion);
                            break;
                        } else {
                            $residuo = $puntos - $asignacion->CantidadPuntosDisponibles;
                            $usadosCupon = $asignacion->CantidadPuntosDisponibles;
                            $asignacion->CantidadPuntosUsados = $asignacion->CantidadPuntosUsados + $usadosCupon;
                            $asignacion->CantidadPuntosDisponibles = 0;
                            $this->getAsignacionTable()->updateAsignacion($asignacion);

                            $idAsignacion = $asignacion->id;

                            $asignacionEstadoLog = new AsignacionEstadoLog();
                            $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacion->id;
                            $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                            $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                            $asignacionEstadoLog->CantidadPuntos = (int)$asignacion->CantidadPuntos;
                            $asignacionEstadoLog->CantidadPuntosUsados = (int)$asignacion->CantidadPuntosUsados;
                            $asignacionEstadoLog->CantidadPuntosDisponibles = (int)$asignacion->CantidadPuntosDisponibles;
                            $asignacionEstadoLog->CantidadPuntosEliminados = (int)$asignacion->CantidadPuntosEliminados;
                            $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                            $asignacionEstadoLog->Operacion = $this::OPERACION_APLICAR;
                            $asignacionEstadoLog->Puntos = $usadosCupon;
                            $asignacionEstadoLog->Motivo = "Aplicando Puntos";
                            $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                            //Guardar Puntos Utilizados
                            $cuponPuntosAsignacion = new CuponPuntosAsignacion();
                            $cuponPuntosAsignacion->BNF2_Cupon_Puntos_id = $cupon->id;
                            $cuponPuntosAsignacion->BNF2_Asignacion_Puntos_id = $asignacion->id;
                            $cuponPuntosAsignacion->PuntosUtilizados = $usadosCupon;
                            $this->getCuponPuntosAsignacionTable()->save($cuponPuntosAsignacion);

                            $puntos = $residuo;
                        }
                    }
                    #endregion

                    $ofertaDetalle = $this->getOfertaPuntosTable()->getOfertaDetalle($idOferta);

                    #region Delivery
                    $correo_delivery = $oferta->CorreoContactoDelivery;
                    $datos_delivery = [];
                    $datosDelivery = $this->getDeliveryPuntosTable()->getFormulario($idOferta);
                    if ($datosDelivery->count() > 0) {
                        foreach ($datosDelivery as $item) {
                            if (isset($delivery[$item->Nombre_Campo])) {
                                $ofertaDelivery = new OfertaPuntosDelivery();
                                $ofertaDelivery->BNF2_Delivery_Puntos_id = $item->id;
                                $ofertaDelivery->BNF2_Asignacion_Puntos_id = $idAsignacion;
                                $ofertaDelivery->BNF2_Oferta_Puntos_id = $idOferta;
                                $ofertaDelivery->BNF_Cliente_id = $idCliente;
                                $ofertaDelivery->BNF_Empresa_id = $idEmpresa;
                                $ofertaDelivery->Detalle = $delivery[$item->Nombre_Campo];
                                $this->getOfertaPuntosDeliveryTable()->saveOfertaPuntosDelivery($ofertaDelivery);

                                $datos_delivery[] = [
                                    'campo' => $item->Etiqueta_Campo,
                                    'valor' => $delivery[$item->Nombre_Campo]
                                ];
                            }
                        }

                        if ($empresa->SubDominio != $subdomain) {
                            $subdomain = "beneficios.pe";
                        }

                        $this->enviarMailDelivery(
                            $correo_delivery, $titulo_oferta, $dataAtributo, $datos_delivery, $subdomain, $puntosUsados, $precio
                        );
                    }
                    #endregion

                    $codigoCupon = $this->generatePDF(
                        $ofertaDetalle, $logoCliente, $email, $cupon->id, $puntosUsados, $saldo, $dataAtributo
                    );

                    $this->getCuponPuntosTable()->setCuponCode($codigoCupon, $cupon->id, $idAsignacion);
                    $cupon = $this->getCuponPuntosTable()->get($cupon->id);

                    $cuponPuntosLog = new CuponPuntosLog();
                    $cuponPuntosLog->BNF2_Cupon_Puntos_id = $cupon->id;
                    $cuponPuntosLog->CodigoCupon = $codigoCupon;
                    $cuponPuntosLog->EstadoCupon = "Generado";
                    $cuponPuntosLog->BNF2_Oferta_Puntos_id = $cupon->BNF2_Oferta_Puntos_id;
                    $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $cupon->BNF2_Oferta_Puntos_Atributos_id;
                    $cuponPuntosLog->BNF_Cliente_id = $cupon->BNF_Cliente_id;
                    $cuponPuntosLog->BNF_Usuario_id = null;
                    $cuponPuntosLog->Comentario = '';
                    $this->getCuponPuntosLogTable()->saveCuponPuntosLog($cuponPuntosLog);

                    $puntos = new Puntos($this->serviceLocator);
                    $cant_puntos = $puntos->updatePuntos($idCliente);

                    $clienteData = $this->getAsignacionTable()->getPuntosAsignados($dataCliente->NumeroDocumento);
                    $disponibles = $clienteData->TotalAsignados;

                    //Enviando la pregunta al cliente.
                    if ($idCliente != '') {
                        $preguntas = $this->getPreguntasTable()->getPreguntas($idCliente);

                        if ($preguntas != false) {
                            if ($preguntas->Pregunta01 == "") {
                                $number = 'pregunta_01';
                            } elseif ($preguntas->Pregunta02 == "") {
                                $number = 'pregunta_02';
                            } elseif ($preguntas->Pregunta03 == "") {
                                $number = 'pregunta_03';
                            } elseif ($preguntas->Pregunta04 == "") {
                                $number = 'pregunta_04';
                            } elseif ($preguntas->Pregunta05 == "") {
                                $number = 'pregunta_05';
                            } elseif ($preguntas->Pregunta06 == "") {
                                $number = 'pregunta_06';
                            } elseif ($preguntas->Pregunta07 == "") {
                                $number = 'pregunta_07';
                            } elseif ($preguntas->Pregunta08 == "") {
                                $number = 'pregunta_08';
                            } elseif ($preguntas->Pregunta09 == "") {
                                $number = 'pregunta_09';
                            } elseif ($preguntas->Pregunta10 == "") {
                                $number = 'pregunta_10';
                            }

                            if ($number != "") {
                                $config = $this->getServiceLocator()->get('Config');
                                $mostrar = $config['preguntas'][$number];
                            }
                        }
                    }
                }
            }
        } else {
            $res = false;
            $mensaje = 'fuera de limite';
        }

        $response->setContent(
            Json::encode(
                array(
                    'data' => $mensaje,
                    'question' => $mostrar,
                    'disponibles' => $disponibles,
                    'number' => $number,
                    'response' => $res,
                    'puntos' => $cant_puntos,
                    'session' => true
                )
            )
        );

        return $response;
    }

    public function generatePDF($ofertaDetalle, $logoCliente, $email, $idCupon, $puntos, $disponibles, $atributo)
    {
        $meses = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        );

        $uri = $this->getRequest()->getUri();
        $scheme = $uri->getScheme();
        $host = $uri->getHost();
        $baseurl = sprintf('%s://%s', $scheme, $host);
        $pdf = new PdfModel();
        $config = $this->getServiceLocator()->get('Config');
        $configuraciones = $this->getConfiguracionesTable()->fetchAll();
        $conf = array();
        foreach ($configuraciones as $dat) {
            $conf[$dat->Campo] = $dat->Atributo;
        }

        $campania = ($ofertaDetalle->idCampania != null) ? $ofertaDetalle->idCampania : '';
        $random = $this->generateRandomString();
        $codigoCupon = $idCupon . $campania . $random;

        if (is_object($atributo)) {
            $date = new \DateTime($atributo->FechaVigencia);
            $mes = $date->format('m');
            $atributo->FechaVigencia = "<br><em>Cupón válido hasta el " .
                $date->format('d') . " de " . $meses[$mes - 1] . "</em>";
        }

        if ($ofertaDetalle->FechaVigencia != null) {
            $date = new \DateTime($ofertaDetalle->FechaVigencia);
            $mes = $date->format('m');
            $ofertaDetalle->FechaVigencia = "<br><em>Cupón válido hasta el " .
                $date->format('d') . " de " . $meses[$mes - 1] . "</em>";
        }

        $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
        $dni = ($dni != null) ? 'Número de Documento: ' . $dni : '';
        $pdf->setOption('filename', 'documentoPdf');
        $pdfView = new ViewModel($pdf);
        $pdfView->setTerminal(true)
            ->setTemplate('Application/puntos/pdf')
            ->setVariables(
                array(
                    'cupon' => $ofertaDetalle,
                    'config' => $config,
                    'logoCliente' => $logoCliente,
                    'codigoCupon' => $codigoCupon,
                    'conf' => $conf,
                    'dni' => $dni,
                    'baseurl' => $baseurl,
                    'disponibles' => $disponibles,
                    'puntos' => $puntos,
                    'atributo' => $atributo
                )
            );
        $html = $this->getServiceLocator()->get('viewpdfrenderer')->getHtmlRenderer()->render($pdfView);
        $eng = $this->getServiceLocator()->get('viewpdfrenderer')->getEngine();

        $eng->load_html($html);
        $eng->render();
        $pdfCode = $eng->output();
        file_put_contents("public/elements/" . $codigoCupon . ".pdf", $pdfCode);
        $pdf_file = $baseurl . "/elements/" . $codigoCupon . ".pdf";
        $this->sendMailCupon($pdf_file, $ofertaDetalle, $email, $idCupon, $atributo);
        return $codigoCupon;
    }

    public function generateRandomString($length = 5)
    {
        $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function sendMailCupon($pdf, $oferta, $email, $idCupon, $atributo)
    {
        $transport = $this->getServiceLocator()->get('mail.transport');

        $message = new Message();
        $message->addTo($email)
            ->addFrom('weeareroialty@gmail.com', 'Weeare')
            ->setSubject('Cupón gratis de "' . $oferta->Empresa . '"');

$precio=((is_object($atributo)) ? $atributo->PrecioVentaPublico : $oferta->PrecioVentaPublico);

    $precioFInal=($this->identity()['flagcheckboxMoney'])?$precio." puntos por ":"S/. ".$precio." por ";

        $html = '<b>Empieza a disfrutar de tus Beneficios…</b>
            <p>Muchas gracias por tu descarga del cupón de descuento: <b>"' .
            $precioFInal .
            ((is_object($atributo)) ? $atributo->NombreAtributo : $oferta->TituloCorto) . '".</b>
            <p>Para poder disfrutar de tu beneficio debes imprimir o mostrar desde tu smartphone el cupón descargado y
             presentarlo en el establecimiento que brinda el descuento. Revisa bien los términos y condiciones de
              cada beneficio para que puedas disfrutarlo sin problemas. </p><br>
            <p>El equipo de Beneficios.pe</p>';

        $htmlBody = new MimePart($html);
        $htmlBody->type = "text/html";

        //$attachment = new MimePart($pdf);
        $attachment = new MimePart(fopen($pdf, 'r'));
        $attachment->type = 'application/pdf';
        $attachment->filename = 'beneficios_' . $oferta->Empresa . '_' . $idCupon . '.pdf';
        $attachment->encoding = Mime::ENCODING_BASE64;
        $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;

        $body = new MimeMessage();
        $body->setParts(array($htmlBody, $attachment));

        $message->setBody($body);

        $transport->send($message);
    }

    public function enviarMailDelivery($correo, $oferta, $atributo, $datos_delivery, $subdomain, $ptos_usados, $precio)
    {
        $transport = $this->getServiceLocator()->get('mail.transport');
        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        $content = $renderer->render('Application/mail/delivery',
            ['contenido' => array(
                'oferta' => $oferta,
                'atributo' => $atributo,
                'delivery' => $datos_delivery,
                'subdomain' => $subdomain,
                'precio' => $precio,
                'ptos_usados' => $ptos_usados
            )]
        );

        $messageemail = new Message();

        $messageemail->addTo($correo)
            ->addFrom('weeareroialty@gmail.com', 'weeare')
            ->setSubject("Delivery puntos - Registro de Cliente");

        $htmlBody = new MimePart($content);
        $htmlBody->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlBody));
        $messageemail->setBody($body);
        $transport->send($messageemail);
    }

    public function generarOfertasOrdenadas($layout, $ofertasA)
    {
        $ofertas_premium = array();
        $ofertas_descubre = array();
        $ofertas_restantes = array();
        $ofertasNOT = array();
        $cantOP = 0;
        $contOM = 0;
        $j = 0;

        foreach ($layout as $value) {
            $ListOferta = array();
            $fila = array();
            $cant = $value->TipoLayout;
            $fila['tipo'] = $cant;
            for ($i = $j; $i < ($j + $cant); $i++) {
                if (is_object($ofertasA[$i])) {
                    array_push($ListOferta, $ofertasA[$i]);
                    array_push($ofertasNOT, $ofertasA[$i]->id);
                }
            }
            $j = ($j + $cant);
            $cantOP = $cantOP + $cant;
            $fila['ofertas'] = $ListOferta;
            array_push($ofertas_premium, $fila);
        }

        for ($i = $cantOP; $i < ($cantOP + 9); $i++) {
            if (isset($ofertasA[$i])) {
                array_push($ofertas_descubre, $ofertasA[$i]);
                array_push($ofertasNOT, $ofertasA[$i]->id);
                $contOM++;
            }
        }

        for ($i = ($cantOP + 9); $i < ($cantOP + 18); $i++) {
            if (isset($ofertasA[$i])) {
                array_push($ofertas_restantes, $ofertasA[$i]);
                array_push($ofertasNOT, $ofertasA[$i]->id);
                $contOM++;
            }
        }
        return array($ofertas_premium, $ofertas_descubre, $ofertas_restantes, $ofertasNOT, $cantOP, $contOM);
    }

    public function generarOrdenamiento($layout, $arrayMerge_L)
    {
        $ofertasLayoutFila01 = array();
        $ofertasLayoutFila02 = array();
        $ofertasLayoutFila03 = array();

        $orden = array();
        foreach ($layout as $value) {
            $datosPosicion = $this->getLayoutPuntosPosicionTable()->getLayoutPuntosPosicion($value->id);

            //Inicializando Contenedores de Ofertas
            if ($value->fila == 1) {
                for ($i = 1; $i <= $value->TipoLayout; $i++) {
                    $ofertasLayoutFila01[$i] = null;
                }
            } elseif ($value->fila == 2) {
                for ($i = 1; $i <= $value->TipoLayout; $i++) {
                    $ofertasLayoutFila02[$i] = null;
                }
            } elseif ($value->fila == 3) {
                for ($i = 1; $i <= $value->TipoLayout; $i++) {
                    $ofertasLayoutFila03[$i] = null;
                }
            }

            //Guardamos los Id del Posicionamiento en el orden segun su fila
            foreach ($datosPosicion as $data) {
                if ($value->fila == 1) {
                    $ofertasLayoutFila01[$data["Index"]] = $data["BNF2_Oferta_Puntos_id"];
                } elseif ($value->fila == 2) {
                    $ofertasLayoutFila02[$data["Index"]] = $data["BNF2_Oferta_Puntos_id"];
                } elseif ($value->fila == 3) {
                    $ofertasLayoutFila03[$data["Index"]] = $data["BNF2_Oferta_Puntos_id"];
                }
            }

            $orden[] = (object)array(
                'TipoLayout' => $value->TipoLayout,
                'fila' => $value->fila
            );
        }

        $ofertasA = array();
        foreach ($arrayMerge_L as $value) {
            if (in_array($value->id, $ofertasLayoutFila01)) {
                $clave = array_search($value->id, $ofertasLayoutFila01);
                $ofertasLayoutFila01[$clave] = $value;
            } elseif (in_array($value->id, $ofertasLayoutFila02)) {
                $clave = array_search($value->id, $ofertasLayoutFila02);
                $ofertasLayoutFila02[$clave] = $value;
            } elseif (in_array($value->id, $ofertasLayoutFila03)) {
                $clave = array_search($value->id, $ofertasLayoutFila03);
                $ofertasLayoutFila03[$clave] = $value;
            } else {
                $ofertasA[] = $value;
            }
        }

        if (!empty($ofertasA)) {
            for ($j = 1; $j < count($ofertasLayoutFila01) + 1; $j++) {
                if (!is_object($ofertasLayoutFila01[$j]) and isset($ofertasA[0])) {
                    $ofertasLayoutFila01[$j] = $ofertasA[0];
                    array_shift($ofertasA);
                }
            }
        }

        if (!empty($ofertasA)) {
            for ($j = 1; $j < count($ofertasLayoutFila02) + 1; $j++) {
                if (!is_object($ofertasLayoutFila02[$j]) and isset($ofertasA[0])) {
                    $ofertasLayoutFila02[$j] = $ofertasA[0];
                    array_shift($ofertasA);
                }
            }
        }

        if (!empty($ofertasA)) {
            for ($j = 1; $j < count($ofertasLayoutFila03) + 1; $j++) {
                if (!is_object($ofertasLayoutFila03[$j]) and isset($ofertasA[0])) {
                    $ofertasLayoutFila03[$j] = $ofertasA[0];
                    array_shift($ofertasA);
                }
            }
        }

        return array(
            array_merge($ofertasLayoutFila01, $ofertasLayoutFila02, $ofertasLayoutFila03, $ofertasA),
            (object)$orden
        );
    }

    public function loadOfertaPuntosAction()
    {
        $empresa = $this->identity()['Empresa'];
        $segmentos = $this->identity()['segmentos_puntos'];
        $response = $this->getResponse();
        $categoria = ($this->getRequest()->getPost('categoria') != '')
            ? $this->getRequest()->getPost('categoria') : null;
        $ubigeo = $this->getRequest()->getPost('ubigeo');
        $campaign = ($this->getRequest()->getPost('campaign') != '') ? $this->getRequest()->getPost('campaign') : null;
        $company = ($this->getRequest()->getPost('company') != '') ? $this->getRequest()->getPost('company') : null;
        $offset = (int)$this->getRequest()->getPost('offset');
        $notin = explode(',', $this->getRequest()->getPost('notin'));
        $flagcheckboxLogo=$this->identity()['flagcheckboxLogo'];

        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Ofertas']['loadOfertaCategoryAction']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];
        $keyLOAD = __CLASS__ . __FUNCTION__ . $ubigeo . $categoria . $campaign . $company . $offset;
        if ($cache->hasItem($keyLOAD) and $cacheStatus == true) {
            $result = $cache->getItem($keyLOAD);
        } else {
            $result = $this->getOfertaPuntosTable()
                ->getOrdenamientoOfertas(
                    $ubigeo,
                    $empresa,
                    $categoria,
                    $campaign,
                    $segmentos,
                    $offset,
                    0,
                    $notin
                );
            $result = $result->toArray();
            $cache->setItem($keyLOAD, $result);
        }

        $ofertas = array();
        foreach ($result as $data) {
            $convert = new OfertaPuntos();
            $convert->exchangeArray($data);
            array_push($ofertas, $convert);
        }

        $response->setContent(
            Json::encode(
                array(
                    'ofertas' => $ofertas,
                    'category' => 'puntos',
                    'flagcheckboxLogo'=>$flagcheckboxLogo
                )
            )
        );
        return $response;
    }

    public function closeModalAction()
    {
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        $data_user['modal_puntos'] = false;
        $session->offsetSet('storage', $data_user);

        $response = $this->getResponse();
        $response->setContent(
            Json::encode(
                array(
                    'result' => 'ok'
                )
            )
        );
        return $response;
    }
}
