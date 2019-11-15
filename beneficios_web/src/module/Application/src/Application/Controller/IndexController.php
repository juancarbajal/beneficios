<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Cache\CacheManager;
use Application\Model\Cupon;
use Application\Model\OfertaEmpresaCliente;
use Application\Service\MobileDetect;
use DOMPDFModule\View\Model\PdfModel;
use Zend\Http\Header\SetCookie;
use Zend\Json\Json;
use Zend\Mime\Mime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Debug\Debug;
use Picqer\Barcode\BarcodeGeneratorPNG;

class IndexController extends AbstractActionController
{
    const PAIS_DEFAULT = 1;
    const CATEGORIA_DEFAULT = 1;
    const OFETAS_PREMIUN = 1;
    const OFETAS_NOVADADES = 2;
    const OFETAS_DESTACADOS = 3;
    const OFETAS_RESTANTES = 0;
    const NOT_OFFSET = -1;
    const TIPO_CATEGORIA = 1;
    const TIPO_CAMPANIA = 2;
    const TIPO_TIENDA = 3;
    const OFERTA_TIPO_SPLIT = "Split";

    #region ObjectTables
    public function getBannerTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\BannerTable');
    }

    public function getBannersCampaniasTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\BannersCampaniasTable');
    }

    public function getBannersCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\BannersCategoriaTable');
    }

    public function getBannersTiendaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\BannersTiendaTable');
    }

    public function getGaleriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\GaleriaTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
    }

    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaTable');
    }

    public function getCuponTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CuponTable');
    }

    public function getConfiguracionesTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\ConfiguracionesTable');
    }

    public function getOfertaEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaEmpresaClienteTable');
    }

    public function getOfertaUbigeoTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaUbigeoTable');
    }

    public function getLayoutCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\LayoutCategoriaTable');
    }

    public function getLayoutCampaniaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\LayoutCampaniaTable');
    }

    public function getLayoutTiendaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\LayoutTiendaTable');
    }

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\ClienteTable');
    }

    public function getUbigeoTable()
    {
        return $this->serviceLocator->get('Application\Model\UbigeoTable');
    }

    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CampaniaTable');
    }

    public function getPreguntasTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\PreguntasTable');
    }

    public function getTarjetasOfertaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\TarjetasOfertaTable');
    }

    public function getLayoutCategoriaPosicionTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\LayoutCategoriaPosicionTable');
    }

    public function getLayoutCampaniaPosicionTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\LayoutCampaniaPosicionTable');
    }

    public function getLayoutTiendaPosicionTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\LayoutTiendaPosicionTable');
    }

    public function getOfertaAtributosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaAtributosTable');
    }

    public function getOfertaCodigoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaCuponCodigoTable');
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
                $data[] = @$empresas_afiliadas[$rand + $i];
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

            $dni = null;
            $img = null;

            $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
            $empresa = $this->identity()['Empresa'];
            $img = $this->identity()['logo'];
            $puntos = $this->identity()['exist_puntos'];
            $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
            $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;

            $catOtros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

            //Datos de Ubigeo
            $ubigeo_id = $this->getUbigeo();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $ubigeo_id = $request->getPost()->ubigeo;
                $this->setUbigeo($ubigeo_id);
            }
            $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
            $ubigeo = $ubigeos->Nombre;

            $config = $this->getServiceLocator()->get('Config');
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
                $totalOEP = $this->getOfertaEmpresaClienteTable()->totalOfertasEP($empresa);
                foreach ($totalOEP as $value) {
                    $totalOEPFilter[$value["SlugEmpresa"]] = $value["totalOfertasEP"];
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
                $ofertasPRM = $this->getOfertaEmpresaClienteTable()
                    ->getOrdenamientoOfertas(
                        $ubigeo_id,
                        $empresa,
                        $segmento,
                        $this::CATEGORIA_DEFAULT,
                        $subgrupo,
                        null,
                        null,
                        $this::NOT_OFFSET,
                        $this::OFETAS_PREMIUN,
                        null
                    );
                $ofertasPRM = $ofertasPRM->toArray();

                $cache->setItem($keyPRM, $ofertasPRM);
            }

            foreach ($ofertasPRM as $dato) {
                $convert = new OfertaEmpresaCliente();
                $convert->exchangeArray($dato);
                array_push($ofertasPRM_L, $convert);
                array_push($ofertasPRM_ID, $convert->idOferta);
            }

            //Ofertas Novedades
            $ofertasNOV_L = array();
            $ofertasNOV_ID = array();
            $keyNOV = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento .
                $this::CATEGORIA_DEFAULT . $subgrupo . "2";
            if ($cache->hasItem($keyNOV) and $cacheStatus == true) {
                $ofertasNOV = $cache->getItem($keyNOV);
            } else {
                $ofertasNOV = $this->getOfertaEmpresaClienteTable()
                    ->getOrdenamientoOfertas(
                        $ubigeo_id,
                        $empresa,
                        $segmento,
                        $this::CATEGORIA_DEFAULT,
                        $subgrupo,
                        null,
                        null,
                        $this::NOT_OFFSET,
                        $this::OFETAS_NOVADADES,
                        null
                    );
                $ofertasNOV = $ofertasNOV->toArray();
                $cache->setItem($keyNOV, $ofertasNOV);
            }

            foreach ($ofertasNOV as $dato) {
                $convert = new OfertaEmpresaCliente();
                $convert->exchangeArray($dato);
                array_push($ofertasNOV_L, $convert);
                array_push($ofertasNOV_ID, $convert->idOferta);
            }

            //ofertas Destacadas
            $ofertasDEST_L = array();
            $ofertasDEST_ID = array();
            $keyDEST = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento .
                $this::CATEGORIA_DEFAULT . $subgrupo . "3";
            if ($cache->hasItem($keyDEST) and $cacheStatus == true) {
                $ofertasDEST = $cache->getItem($keyDEST);
            } else {
                $ofertasDEST = $this->getOfertaEmpresaClienteTable()
                    ->getOrdenamientoOfertas(
                        $ubigeo_id,
                        $empresa,
                        $segmento,
                        $this::CATEGORIA_DEFAULT,
                        $subgrupo,
                        null,
                        null,
                        $this::NOT_OFFSET,
                        $this::OFETAS_DESTACADOS,
                        null
                    );
                $ofertasDEST = $ofertasDEST->toArray();
                $cache->setItem($keyDEST, $ofertasDEST);
            }

            foreach ($ofertasDEST as $dato) {
                $convert = new OfertaEmpresaCliente();
                $convert->exchangeArray($dato);
                array_push($ofertasDEST_L, $convert);
                array_push($ofertasDEST_ID, $convert->idOferta);
            }

            $arrayMerge_L = array_merge($ofertasPRM_L, $ofertasNOV_L, $ofertasDEST_L);
            $arrayMerge_L = array_unique($arrayMerge_L);

            $layout = $this->getLayoutCategoriaTable()->getLayoutCategoria($this::CATEGORIA_DEFAULT, $empresa);
            if (count($layout) == 0) {
                $layout = $this->getLayoutCategoriaTable()->getLayoutCategoria($this::CATEGORIA_DEFAULT);
            }

            $resultadosOrden = $this->generarOrdenamiento($layout, $arrayMerge_L, $this::TIPO_CATEGORIA);

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

            //empresas afiliadas
            $data = $this->getAfiliados($segmento = 0, $ubigeo_id, $empresa, $subgrupo = 0);

            //galeria de Imagenes
            $galeria = $this->getGaleriaTable()->getGalerias($empresa);

            //Banners Home
            $banners = $this->getBannersCategoriaTable()->getBannerCategoriaAll(1, $empresa);
            $array = array();
            foreach ($banners as $value) :
                $array[$value->BNF_Banners_id] = array('image' => $value->Imagen, 'link' => $value->Url);
            endforeach;
            $banners = $array;

            $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
            $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
            $categories = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);

            $category = null;
            foreach ($categories as $key => $dato) {
                if ($key == 0) {
                    $category = $dato->Slug;
                }
            }

            if (isset($this->getRequest()->getHeaders()->get('Cookie')->modal)) {
                $modal = true;
            } else {
                $modal = false;
                $cookie = new SetCookie('modal', '1', time() + $config['time_cookie']); // now + 1 year
                $response = $this->getResponse()->getHeaders();
                $response->addHeader($cookie);
            }

            $view = new ViewModel();
            $view->setVariables(
                array(
                    'url' => 'home',
                    'category' => $category,
                    'router' => 'application',
                    'total' => $totalOEPFilter,
                    'rlogos' => $config['images']["logos"],
                    'rofertas' => $config['images']["ofertas"],
                    'rgaleria' => $config['images']["galeria"],
                    'rbanners' => $config['images']["banners"],
                    'galeria' => $galeria,
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
                    'url_slug' => 'home',
                    'catotros' => $catOtros,
                    'offset' => $offset,
                    'modal' => $modal,
                    'notin' => $noCargar
                )
            );

            $mobile = new MobileDetect();
            if ($mobile->isMobile() == 1) {
                $view->setTemplate('application/index/index-mobile');
            }
            return $view;
    }

    public function categoriaAction()
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

        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;

        //buscar datos de categoria segun el nombre recibido
        $category = $this->params()->fromRoute('cat', null);
        $cat = $this->getCategoriaTable()->getBuscarCategoria($category);
        if ($cat == false) {
            return $this->redirect()->toRoute('404');
        }

        $catOtros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        //Ubigeo
        $ubigeo_id = $this->getUbigeo();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $this->setUbigeo($ubigeo_id);
        }
        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Ofertas']['categoriaAction']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];

        //Consulta Total de Ofertas de EP
        $totalOEPFilter = array();
        $keyEOP = __CLASS__ . __FUNCTION__ . $empresa;
        if ($cache->hasItem($keyEOP) and $cacheStatus == true) {
            $totalOEPFilter = $cache->getItem($keyEOP);
        } else {
            $totalOEP = $this->getOfertaEmpresaClienteTable()->totalOfertasEP($empresa);
            foreach ($totalOEP as $value) {
                $totalOEPFilter[$value["SlugEmpresa"]] = $value["totalOfertasEP"];
            }
            $cache->setItem($keyEOP, $totalOEPFilter);
        }

        //ofertas Premiun
        $ofertasPRM_L = array();
        $ofertasPRM_ID = array();
        $keyPRM = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento . $cat->id . $subgrupo . "1";
        if ($cache->hasItem($keyPRM) and $cacheStatus == true) {
            $ofertasPRM = $cache->getItem($keyPRM);
        } else {
            $ofertasPRM = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    $cat->id,
                    $subgrupo,
                    null,
                    null,
                    $this::NOT_OFFSET,
                    $this::OFETAS_PREMIUN,
                    null
                );
            $ofertasPRM = $ofertasPRM->toArray();
            $cache->setItem($keyPRM, $ofertasPRM);
        }

        foreach ($ofertasPRM as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasPRM_L, $convert);
            array_push($ofertasPRM_ID, $convert->idOferta);
        }

        //Ofertas Novedades
        $ofertasNOV_L = array();
        $ofertasNOV_ID = array();
        $keyNOV = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento . $cat->id . $subgrupo . "2";
        if ($cache->hasItem($keyNOV) and $cacheStatus == true) {
            $ofertasNOV = $cache->getItem($keyNOV);
        } else {
            $ofertasNOV = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    $cat->id,
                    $subgrupo,
                    null,
                    null,
                    $this::NOT_OFFSET,
                    $this::OFETAS_NOVADADES,
                    null
                );
            $ofertasNOV = $ofertasNOV->toArray();
            $cache->setItem($keyNOV, $ofertasNOV);
        }

        foreach ($ofertasNOV as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasNOV_L, $convert);
            array_push($ofertasNOV_ID, $convert->idOferta);
        }

        //ofertas Destacadas
        $ofertasDEST_L = array();
        $ofertasDEST_ID = array();

        $keyDEST = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento . $cat->id . $subgrupo . "3";
        if ($cache->hasItem($keyDEST) and $cacheStatus == true) {
            $ofertasDEST = $cache->getItem($keyDEST);
        } else {
            $ofertasDEST = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    $cat->id,
                    $subgrupo,
                    null,
                    null,
                    $this::NOT_OFFSET,
                    $this::OFETAS_DESTACADOS,
                    null
                );
            $ofertasDEST = $ofertasDEST->toArray();
            $cache->setItem($keyDEST, $ofertasDEST);
        }

        foreach ($ofertasDEST as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasDEST_L, $convert);
            array_push($ofertasDEST_ID, $convert->idOferta);
        }

        $arrayMerge_L = array_merge($ofertasPRM_L, $ofertasNOV_L, $ofertasDEST_L);
        $arrayMerge_L = array_unique($arrayMerge_L);

        $layout = $this->getLayoutCategoriaTable()->getLayoutCategoria($cat->id, $empresa);
        if (count($layout) == 0) {
            $layout = $this->getLayoutCategoriaTable()->getLayoutCategoria($cat->id);
        }

        $resultadosOrden = $this->generarOrdenamiento($layout, $arrayMerge_L, $this::TIPO_CATEGORIA);

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

        //empresas afiliadas
        $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);

        $descripcion = $cat->Nombre;
        //Banners de Categoria
        $banners = $this->getBannersCategoriaTable()->getBannerCategoriaAll($cat->id, $empresa);
        $array = array();
        foreach ($banners as $value) :
            $array[$value->BNF_Banners_id] = array('image' => $value->Imagen, 'link' => $value->Url);
        endforeach;
        $banners = $array;

        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'category/' . $category,
                'router' => 'category',
                'slug' => 'cat',
                'category' => $category,
                'total' => $totalOEPFilter,
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'ubigeo' => $ubigeo,
                'ofertas_premiun' => $ofertas_premium,
                'ofertas_descubre' => $ofertas_descubre,
                'ofertas_restantes' => $ofertas_restantes,
                'meses' => $meses,
                'data' => $data,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'imgemp' => $img,
                'url_slug' => $category,
                'banners' => $banners,
                'descripcion' => $descripcion,
                'ubigeo_id' => $ubigeo_id,
                'categoria_id' => $cat->id,
                'catotros' => $catOtros,
                'offset' => $offset,
                'notin' => $noCargar,
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('application/index/categoria-mobile');
        }
        return $view;
    }

    public function campaniaAction()
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

        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;

        //buscar datos de campania segun el nombre recibido
        $campaign = $this->params()->fromRoute('camp', 0);
        $camp = $this->getCampaniaTable()->getBuscarCampania($campaign);
        if ($camp == false) {
            return $this->redirect()->toRoute('404');
        }

        $catOtros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        //Ubigeo
        $ubigeo_id = $this->getUbigeo();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $this->setUbigeo($ubigeo_id);
        }
        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Ofertas']['campaniaAction']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];

        //Consulta Total de Ofertas de EP
        $totalOEPFilter = array();
        $keyEOP = __CLASS__ . __FUNCTION__ . $empresa;
        if ($cache->hasItem($keyEOP) and $cacheStatus == true) {
            $totalOEPFilter = $cache->getItem($keyEOP);
        } else {
            $totalOEP = $this->getOfertaEmpresaClienteTable()->totalOfertasEP($empresa);
            foreach ($totalOEP as $value) {
                $totalOEPFilter[$value["SlugEmpresa"]] = $value["totalOfertasEP"];
            }
            $cache->setItem($keyEOP, $totalOEPFilter);
        }

        //ofertas Premiun
        $ofertasPRM_L = array();
        $ofertasPRM_ID = array();
        $keyPRM = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento . "0" . $subgrupo . $camp->id . "1";
        if ($cache->hasItem($keyPRM) and $cacheStatus == true) {
            $ofertasPRM = $cache->getItem($keyPRM);
        } else {
            $ofertasPRM = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    null,
                    $subgrupo,
                    $camp->id,
                    null,
                    $this::NOT_OFFSET,
                    $this::OFETAS_PREMIUN,
                    null
                );
            $ofertasPRM = $ofertasPRM->toArray();
            $cache->setItem($keyPRM, $ofertasPRM);
        }

        foreach ($ofertasPRM as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasPRM_L, $convert);
            array_push($ofertasPRM_ID, $convert->idOferta);
        }

        //Ofertas Novedades
        $ofertasNOV_L = array();
        $ofertasNOV_ID = array();
        $keyNOV = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento . "0" . $subgrupo . $camp->id . "2";
        if ($cache->hasItem($keyNOV) and $cacheStatus == true) {
            $ofertasNOV = $cache->getItem($keyNOV);
        } else {
            $ofertasNOV = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    null,
                    $subgrupo,
                    $camp->id,
                    null,
                    $this::NOT_OFFSET,
                    $this::OFETAS_NOVADADES,
                    null
                );
            $ofertasNOV = $ofertasNOV->toArray();
            $cache->setItem($keyNOV, $ofertasNOV);
        }

        foreach ($ofertasNOV as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasNOV_L, $convert);
            array_push($ofertasNOV_ID, $convert->idOferta);
        }

        //ofertas Destacadas
        $ofertasDEST_L = array();
        $ofertasDEST_ID = array();
        $keyDEST = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa . $segmento . "0" . $subgrupo . $camp->id . "3";
        if ($cache->hasItem($keyDEST) and $cacheStatus == true) {
            $ofertasDEST = $cache->getItem($keyDEST);
        } else {
            $ofertasDEST = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    null,
                    $subgrupo,
                    $camp->id,
                    null,
                    $this::NOT_OFFSET,
                    $this::OFETAS_DESTACADOS,
                    null
                );
            $ofertasDEST = $ofertasDEST->toArray();
            $cache->setItem($keyDEST, $ofertasDEST);
        }

        foreach ($ofertasDEST as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasDEST_L, $convert);
            array_push($ofertasDEST_ID, $convert->idOferta);
        }

        $arrayMerge_L = array_merge($ofertasPRM_L, $ofertasNOV_L, $ofertasDEST_L);
        $arrayMerge_L = array_unique($arrayMerge_L);

        $layout = $this->getLayoutCampaniaTable()->getLayoutCampania($camp->id, $empresa);
        if (count($layout) == 0) {
            $layout = $this->getLayoutCampaniaTable()->getLayoutCampania($camp->id);
        }

        $resultadosOrden = $this->generarOrdenamiento($layout, $arrayMerge_L, $this::TIPO_CAMPANIA);

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

        //empresas afiliadas
        $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);

        $descripcion = $camp->Nombre;

        //Banners de Campanias
        $banners = $this->getBannersCampaniasTable()->getBannerCampaniaAll($camp->id, $empresa);
        $array = array();
        foreach ($banners as $value) :
            $array[$value->BNF_Banners_id] = array('image' => $value->Imagen, 'link' => $value->Url);
        endforeach;
        $banners = $array;

        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'campaign/' . $campaign,
                'router' => 'campaign',
                'slug' => 'camp',
                'category' => 'campaign',
                'total' => $totalOEPFilter,
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'ubigeo' => $ubigeo,
                'ofertas_premiun' => $ofertas_premium,
                'ofertas_descubre' => $ofertas_descubre,
                'ofertas_restantes' => $ofertas_restantes,
                'meses' => $meses,
                'data' => $data,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'imgemp' => $img,
                'url_slug' => $campaign,
                'banners' => $banners,
                'descripcion' => $descripcion,
                'ubigeo_id' => $ubigeo_id,
                'campania_id' => $camp->id,
                'catotros' => $catOtros,
                'offset' => $offset,
                'notin' => $noCargar,
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('application/index/campania-mobile');
        }
        return $view;
    }

    public function companyAction()
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

        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;

        $catOtros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        //buscar datos de Empresa
        $company = $this->params()->fromRoute('comp', 0);
        $empresas = $this->getEmpresaTable()->getEmpresaSlug($company);
        if ($empresas == false) {
            return $this->redirect()->toRoute('404');
        }

        //Ubigeo
        $ubigeo_id = $this->getUbigeo();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $this->setUbigeo($ubigeo_id);
        }
        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Ofertas']['companyAction']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];

        //Consulta Total de Ofertas de EP
        $totalOEPFilter = array();
        $keyEOP = __CLASS__ . __FUNCTION__ . $empresa;
        if ($cache->hasItem($keyEOP) and $cacheStatus == true) {
            $totalOEPFilter = $cache->getItem($keyEOP);
        } else {
            $totalOEP = $this->getOfertaEmpresaClienteTable()->totalOfertasEP($empresa);
            foreach ($totalOEP as $value) {
                $totalOEPFilter[$value["SlugEmpresa"]] = $value["totalOfertasEP"];
            }
            $cache->setItem($keyEOP, $totalOEPFilter);
        }

        //ofertas Premiun
        $ofertasPRM_L = array();
        $ofertasPRM_ID = array();

        $keyPRM = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa .
            $segmento . "0" . $subgrupo . "0" . $empresas->id . "1";
        if ($cache->hasItem($keyPRM) and $cacheStatus == true) {
            $ofertasPRM = $cache->getItem($keyPRM);
        } else {
            $ofertasPRM = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    null,
                    $subgrupo,
                    null,
                    $empresas->id,
                    $this::NOT_OFFSET,
                    $this::OFETAS_PREMIUN,
                    null
                );
            $ofertasPRM = $ofertasPRM->toArray();
            $cache->setItem($keyPRM, $ofertasPRM);
        }

        foreach ($ofertasPRM as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasPRM_L, $convert);
            array_push($ofertasPRM_ID, $convert->idOferta);
        }

        //Ofertas Novedades
        $ofertasNOV_L = array();
        $ofertasNOV_ID = array();
        $keyNOV = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa .
            $segmento . "0" . $subgrupo . "0" . $empresas->id . "2";
        if ($cache->hasItem($keyNOV) and $cacheStatus == true) {
            $ofertasNOV = $cache->getItem($keyNOV);
        } else {
            $ofertasNOV = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    null,
                    $subgrupo,
                    null,
                    $empresas->id,
                    $this::NOT_OFFSET,
                    $this::OFETAS_NOVADADES,
                    null
                );
            $ofertasNOV = $ofertasNOV->toArray();
            $cache->setItem($keyNOV, $ofertasNOV);
        }

        foreach ($ofertasNOV as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasNOV_L, $convert);
            array_push($ofertasNOV_ID, $convert->idOferta);
        }

        //ofertas Destacadas
        $ofertasDEST_L = array();
        $ofertasDEST_ID = array();
        $keyDEST = __CLASS__ . __FUNCTION__ . $ubigeo_id . $empresa .
            $segmento . "0" . $subgrupo . "0" . $empresas->id . "3";
        if ($cache->hasItem($keyDEST) and $cacheStatus == true) {
            $ofertasDEST = $cache->getItem($keyDEST);
        } else {
            $ofertasDEST = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo_id,
                    $empresa,
                    $segmento,
                    null,
                    $subgrupo,
                    null,
                    $empresas->id,
                    $this::NOT_OFFSET,
                    $this::OFETAS_DESTACADOS,
                    null
                );
            $ofertasDEST = $ofertasDEST->toArray();
            $cache->setItem($keyDEST, $ofertasDEST);
        }

        foreach ($ofertasDEST as $dato) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($dato);
            array_push($ofertasDEST_L, $convert);
            array_push($ofertasDEST_ID, $convert->idOferta);
        }

        //data notin
        $arrayMerge_L = array_merge($ofertasPRM_L, $ofertasNOV_L, $ofertasDEST_L);
        $arrayMerge_L = array_unique($arrayMerge_L);

        $layout = $this->getLayoutTiendaTable()->getLayoutTienda($empresa);
        if (count($layout) == 0) {
            $layout = $this->getLayoutTiendaTable()->getLayoutTienda();
        }

        $resultadosOrden = $this->generarOrdenamiento($layout, $arrayMerge_L, $this::TIPO_TIENDA);

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

        //empresas afiliadas
        $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);

        $descripcion = $empresas->NombreComercial;

        //Banners de Tienda
        $banners = $this->getBannersTiendaTable()->getBanners($empresa);
        $array = array();
        foreach ($banners as $value) :
            $array[$value->BNF_Banners_id] = array('image' => $value->Imagen, 'link' => $value->Url);
        endforeach;
        $banners = $array;

        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'company/' . $company,
                'router' => 'company',
                'slug' => 'comp',
                'category' => 'company',
                'total' => $totalOEPFilter,
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'ubigeo' => $ubigeo,
                'ofertas_premiun' => $ofertas_premium,
                'ofertas_descubre' => $ofertas_descubre,
                'ofertas_restantes' => $ofertas_restantes,
                'meses' => $meses,
                'data' => $data,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'imgemp' => $img,
                'url_slug' => $company,
                'banners' => $banners,
                'descripcion' => $descripcion,
                'ubigeo_id' => $ubigeo_id,
                'catotros' => $catOtros,
                'company_id' => $empresas->id,
                'offset' => $offset,
                'notin' => $noCargar,
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('application/index/campania-mobile');
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

        $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;
        $email = isset($this->identity()['email']) ? $this->identity()['email'] : null;
        $idCliente = isset($this->identity()['id']) ? $this->identity()['id'] : null;

        $ubigeo_id = $this->getUbigeo();
        $ofertas = null;
        $categoria = null;
        $company = null;
        $tarjetasData = array();
        $coupon = $this->params()->fromRoute('coupon', 0);
        $category = $this->params()->fromRoute('val', null);

        //Verificar la categoria
        $datosCategoria = $this->getCategoriaTable()->getBuscarCategoria($category);
        if (!is_object($datosCategoria)) {
            $idCategoria = substr($category, 0, 3);
            if (!in_array($idCategoria, $array)) {
                return $this->redirect()->toRoute('404');
            }
        }

        $configuraciones = $this->getConfiguracionesTable()->fetchAll();
        $conf = array();
        foreach ($configuraciones as $dat) {
            $conf[$dat->Campo] = $dat->Atributo;
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $this->setUbigeo($ubigeo_id);
        }

        $catOtros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        //buscar datos del Cupon
        $cupon = $this->getOfertaEmpresaClienteTable()->getCuponOferta($empresa, $dni, $subgrupo, $coupon);
//        var_dump($cupon);exit;
        if (!is_object($cupon)) {
            return $this->redirect()->toRoute('404', array('opt' => $coupon));
        }

        //Oferta tipo lead
        if ($cupon->TipoOferta == 3) {
            return $this->redirect()->toRoute('lead', array('opt' => $coupon, 'val' => $category));
        }

        //datos Atributos
        $atributosData = $this->getOfertaAtributosTable()->getAllOfertaAtributos($cupon->idOferta);

        //imagenes de cupon
        $imgCupon = $this->getOfertaTable()->getImagenCupon($cupon->idOferta);
        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        //ofertas
        $get = $this->getRequest()->getHeader('REFERER');
        if ($get == false) {
            $categoria = $cupon->idCategoria;
            $ofertas = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas($ubigeo_id, $empresa, $segmento, $categoria, $subgrupo);
        } else {
            $redirectUrl = $this->getRequest()->getHeader('REFERER')->uri()->getPath();
            $redirectUrl = explode('/', $redirectUrl);

            if ($redirectUrl[1] == 'home') {
                $categoria = $this::CATEGORIA_DEFAULT;
                $ofertas = $this->getOfertaEmpresaClienteTable()
                    ->getOrdenamientoOfertas($ubigeo_id, $empresa, $segmento, $categoria, $subgrupo);
            } elseif ($redirectUrl[1] == 'category') {
                $categoria = $this->getCategoriaTable()->getBuscarCategoria($redirectUrl[2]);
                $ofertas = $this->getOfertaEmpresaClienteTable()
                    ->getOrdenamientoOfertas($ubigeo_id, $empresa, $segmento, $categoria->id, $subgrupo);
            } elseif ($redirectUrl[1] == 'campaign') {
                $categoria = $cupon->idCategoria;
                $ofertas = $this->getOfertaEmpresaClienteTable()
                    ->getOrdenamientoOfertas($ubigeo_id, $empresa, $segmento, $categoria, $subgrupo);
            } elseif ($redirectUrl[1] == 'company') {
                $company = $this->getEmpresaTable()->getEmpresaSlug($cupon->SlugEmpresa);
                $ofertas = $this->getOfertaEmpresaClienteTable()
                    ->getOrdenamientoOfertas($ubigeo_id, $empresa, $segmento, null, $subgrupo, null, $company->id);
            } else {
                $categoria = $cupon->idCategoria;
                $ofertas = $this->getOfertaEmpresaClienteTable()
                    ->getOrdenamientoOfertas($ubigeo_id, $empresa, $segmento, $categoria, $subgrupo);
            }
        }

        $ofertas_relacionadas = array();
        foreach ($ofertas as $oferta) {
            if ($oferta->idOferta != $cupon->idOferta) {
                array_push($ofertas_relacionadas, $oferta);
            }
        }

        //empresas afiliadas
        $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);
        $descripcion = $cupon;

        $config = $this->getServiceLocator()->get('Config');
        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);

        $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
        if ($config["empresas_especiales"][0] == $datosEmpresa->SubDominio) {
            $ofertaData = $this->getOfertaTable()->getOfertaBySlug($coupon);
            $tarjetasData = $this->getTarjetasOfertaTable()->getAllTarjetasOferta($ofertaData->id);
        }

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'coupon/' . $coupon,
                'router' => 'coupon',
                'slug' => 'comp',
                'category' => $category,
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'ofertas_relacionadas' => $ofertas_relacionadas,
                'cupon' => $cupon,
                'ubigeo' => $ubigeo,
                'imgCupon' => $imgCupon,
                'empresaID' => $empresa,
                'clienteID' => $idCliente,
                'meses' => $meses,
                'data' => $data,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'imgemp' => $img,
                'url_slug' => $coupon,
                'descripcion' => $descripcion,
                'ubigeo_id' => $ubigeo_id,
                'conf' => $conf,
                'email_user' => $email,
                'catotros' => $catOtros,
                'tarjetas' => $tarjetasData,
                'atributosData' => $atributosData,
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('application/index/cupon-mobile');
        }
        return $view;
    }

    public function loadOfertaCategoryAction()
    {
        $empresa = $this->identity()['Empresa'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;
        $flagcheckboxLogo=$this->identity()['flagcheckboxLogo'];


        $response = $this->getResponse();
        $categoria = ($this->getRequest()->getPost('categoria') != '')
            ? $this->getRequest()->getPost('categoria') : null;
        $ubigeo = $this->getRequest()->getPost('ubigeo');
        $campaign = ($this->getRequest()->getPost('campaign') != '') ? $this->getRequest()->getPost('campaign') : null;
        $company = ($this->getRequest()->getPost('company') != '') ? $this->getRequest()->getPost('company') : null;
        $offset = (int)$this->getRequest()->getPost('offset');
        $notin = explode(',', $this->getRequest()->getPost('notin'));
        $category = $this->getRequest()->getPost('categories');

        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Ofertas']['loadOfertaCategoryAction']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];
        $keyLOAD = __CLASS__ . __FUNCTION__ . $ubigeo . $empresa . $segmento .
            $categoria . $subgrupo . $campaign . $company . $offset;
        if ($cache->hasItem($keyLOAD) and $cacheStatus == true) {
            $result = $cache->getItem($keyLOAD);
        } else {
            $result = $this->getOfertaEmpresaClienteTable()
                ->getOrdenamientoOfertas(
                    $ubigeo,
                    $empresa,
                    $segmento,
                    $categoria,
                    $subgrupo,
                    $campaign,
                    $company,
                    $offset,
                    0,
                    $notin
                );
            $result = $result->toArray();
            $cache->setItem($keyLOAD, $result);
        }

        $ofertas = array();
        foreach ($result as $data) {
            $convert = new OfertaEmpresaCliente();
            $convert->exchangeArray($data);
            array_push($ofertas, $convert);
        }

        $response->setContent(
            Json::encode(
                array(
                    'ofertas' => $ofertas,
                    'category' => $category,
                    'flagcheckboxLogo'=>$flagcheckboxLogo
                )
            )
        );
        return $response;
    }

    public function loadOfertaSearchAction()
    {
        $response = $this->getResponse();
        $ubigeo = $this->getRequest()->getPost('ubigeo');
        $offset = (int)$this->getRequest()->getPost('offset');
        $nombre = ($this->getRequest()->getPost('nombre') != null) ?
            $this->getRequest()->getPost('nombre') : null;
        $premium = (int)$this->getRequest()->getPost('premium');
        $destacados = (int)$this->getRequest()->getPost('destacados');
        $novedades = (int)$this->getRequest()->getPost('novedades');
        $flagcheckboxLogo=isset($this->identity()['flagcheckboxLogo']) ? $this->identity()['flagcheckboxLogo'] : 0;

        $empresa = $this->identity()['Empresa'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;

        $segmentos_puntos = $this->identity()['segmentos_puntos'];
        $puntos = $this->identity()['exist_puntos'];
        $result = $this->getOfertaEmpresaClienteTable()
            ->getImagenOfertaXName(
                $nombre,
                $premium,
                $destacados,
                $novedades,
                $ubigeo,
                $empresa,
                $segmento,
                $subgrupo,
                $offset,
                $segmentos_puntos,
                9,
                $puntos
            );

        $ofertas = array();
        foreach ($result as $data) {
            array_push($ofertas, (object)$data);
        }

        $response->setContent(
            Json::encode(
                array(
                    'ofertas' => $ofertas,

                )
            )
        );

        return $response;
    }

    public function envioCuponAction()
    {
        $data = array();
        $mensaje = null;
        $res = null;
        $mostrar = "";
        $number = "";
        $id_correoCliente = null;
        $dataAtributo = null;
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
        $atributo = (int)$this->getRequest()->getPost('atributo');
        $cat = $this->getCategoriaTable()->getBuscarCategoria($slug_cat);
        $idCategoria = (!$cat) ? substr($slug_cat, 0, 3) : $cat->id;
        $oferta = $this->getOfertaTable()->getOferta($idOferta);

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

        if ($idCliente != '') {
            $clienteCorreotable = $this->serviceLocator->get('Auth\Model\Table\ClienteCorreoTable');

            $session = new SessionContainer('auth');
            $data_user = $session->offsetGet('storage');
            $data_user['email'] = $email;
            $session->offsetSet('storage', $data_user);

            $clienteCorreo = $clienteCorreotable->buscarCorreo($email, $idCliente);
            if ($clienteCorreo == false) {
                $id_correoCliente = $clienteCorreotable->saveCorreo($idCliente, $email);
            } else {
                $id_correoCliente = $clienteCorreo->id;
                $clienteCorreotable->updateCorreo($clienteCorreo->id);
            }
        }

        $maxDescargas = $oferta->DescargaMaximaDia;
        $idOEC = $this->getOfertaEmpresaClienteTable()->getOfertaEmpresaCliente($idOferta, $idEmpresa);
        $resultado = $this->getCuponTable()->verifyLimit($idOferta, $idOEC->id, $idCliente, $atributo);

        if ($resultado < $maxDescargas) {
            if ($oferta->BNF_BolsaTotal_TipoPaquete_id == 1) { //Oferta Tipo Descarga
                $cupon = $this->getCuponTable()->getCuponValid($idOferta, $atributo);
                if ($cupon == false) {
                    $res = false;
                    $mensaje = 'error';
                } else {
                    $resultadoCupon = $this->getCuponTable()
                        ->updateCupon(
                            $idOEC->id,
                            $this->identity()['Empresa'],
                            $idCliente,
                            $cupon->id,
                            $idCategoria,
                            $email
                        );

                    if ($resultadoCupon) {
                        $res = true;
                        $mensaje = 'cupon generado';
                        $logoCliente = $this->getEmpresaTable()->getEmpresa($idEmpresa)->Logo;
                        if ($oferta->TipoAtributo == $this::OFERTA_TIPO_SPLIT) {
                            $dataAtributo = $this->getOfertaAtributosTable()->getOfertaAtributos($atributo);
                            $dataAttr['Stock'] = (int)$dataAtributo->Stock - 1;
                            $this->getOfertaAtributosTable()
                                ->updateOfertaAtributos($dataAttr, $oferta->id, $atributo);

                            if ($dataAttr['Stock'] == 0) {
                                $totalAtributos = $this->getOfertaAtributosTable()->getTotalHabilitados($oferta->id);
                                if ($totalAtributos == 0) {
                                    $data['Estado'] = 'Caducado';
                                    $this->getOfertaTable()->updateOferta($data, $oferta->id);
                                }
                            }
                        } else {
                            $data['Stock'] = (int)$oferta->Stock - 1;
                            if ($data['Stock'] == 0) {
                                $data['Estado'] = 'Caducado';
                            }
                            $this->getOfertaTable()->updateOferta($data, $oferta->id);
                        }

                        $ofertaDetalle = $this->getOfertaEmpresaClienteTable()->getOfertaDetalle($idOferta);
                        $codigoCupon = $this->generatePDF($ofertaDetalle, $logoCliente, $email, $cupon->id, $dataAtributo);

                        $this->getCuponTable()->setCuponCode($codigoCupon, $cupon->id);

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

                        $this->enviarCorreo($oferta->BNF_BolsaTotal_Empresa_id);
                    }
                }
            } else { //Oferta Tipo Presencia
                if ($idOEC == false) {
                    $res = false;
                    $mensaje = 'error';
                } else {
                    $cupon = new Cupon();
                    if ($idCliente != '') {
                        $cupon->BNF_Cliente_id = $idCliente;
                    }
                    $cupon->BNF_Oferta_id = $idOferta;
                    $cupon->BNF_OfertaEmpresaCliente_id = $idOEC->id;
                    $cupon->BNF_Empresa_id = $this->identity()['Empresa'];
                    $cupon->EstadoCupon = "Generado";
                    $cupon->BNF_Categoria_id = $idCategoria;
                    if ($id_correoCliente != null) {
                        $cupon->BNF_ClienteCorreo_id = $id_correoCliente;
                    }

                    if (!empty($atributo)) {
                        $cupon->BNF_Oferta_Atributo_id = $atributo;
                        $dataAtributo = $this->getOfertaAtributosTable()->getOfertaAtributos($atributo);
                    }

                    $idCupon = $this->getCuponTable()->saveCupon($cupon);

                    $res = true;
                    $mensaje = 'cupon generado';

                    $logoCliente = $this->getEmpresaTable()->getEmpresa($idEmpresa)->Logo;
                    $ofertaDetalle = $this->getOfertaEmpresaClienteTable()->getOfertaDetalle($idOferta);
                    $codigoCupon = $this->generatePDF($ofertaDetalle, $logoCliente, $email, $idCupon, $dataAtributo);

                    $this->getCuponTable()->setCuponCode($codigoCupon, $idCupon);

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

                    $this->enviarCorreo($oferta->BNF_BolsaTotal_Empresa_id);
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
                    'number' => $number,
                    'response' => $res,
                    'session' => true
                )
            )
        );

        return $response;
    }

    public function generatePDF($ofertaDetalle, $logoCliente, $email, $idCupon, $atributo)
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

        $tarjetasData = array();
        $empresa = $this->identity()['Empresa'];
        $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
        if ($config["empresas_especiales"][0] == $datosEmpresa->SubDominio) {
            $tarjetasData = $this->getTarjetasOfertaTable()->getAllTarjetasOferta($ofertaDetalle->idOferta);
        }

        #region Generar Código
        if ($ofertaDetalle->TipoEspecial == 1) {
            //generar codigo de barrar
            $codigoCupon = $this->getOfertaCodigoTable()->getByOferta($ofertaDetalle->idOferta);
            $generator = new BarcodeGeneratorPNG();
            $codigoCuponPDF = '<img style="width: 250px;height: 100px;" src="data:image/png;base64,' .
                base64_encode($generator->getBarcode($codigoCupon->Codigo, $generator::TYPE_CODE_128, 2, 20)) . '">' .
                $codigoCupon->Codigo;
            $this->getOfertaCodigoTable()->update($codigoCupon->id, ['Estado' => '1']);
            $count = $this->getOfertaCodigoTable()->getCantByOferta($ofertaDetalle->idOferta);
            if ($count == 0) {
                $this->getOfertaTable()->updateOferta(['TipoEspecial' => '0'], $ofertaDetalle->idOferta);
            }
            $codigoCupon = $codigoCupon->Codigo;
        } else {
            $campania = ($ofertaDetalle->idCampania != null) ? $ofertaDetalle->idCampania : '';
            $random = $this->generateRandomString();
            $codigoCupon = $idCupon . $campania . $random;
            $codigoCuponPDF = $codigoCupon;
        }
        #endregion

        if (is_object($atributo)) {
            $date = new \DateTime($atributo->FechaVigencia);
            $mes = $date->format('m');
            $atributo->FechaVigencia = "<br><em>Cupón válido hasta el " .
                $date->format('d') . " de " . $meses[$mes - 1] . "</em>";
        }

        if ($ofertaDetalle->vigencia != null) {
            $date = new \DateTime($ofertaDetalle->vigencia);
            $mes = $date->format('m');
            $ofertaDetalle->vigencia = "<br><em>Cupón válido hasta el " .
                $date->format('d') . " de " . $meses[$mes - 1] . "</em>";
        }

        $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
        $dni = ($dni != null) ? 'Número de Documento: ' . $dni : '';
        $pdf->setOption('filename', 'documentoPdf');
        $pdfView = new ViewModel($pdf);
        $pdfView->setTerminal(true)
            ->setTemplate('Application/index/pdf')
            ->setVariables(
                array(
                    'cupon' => $ofertaDetalle,
                    'config' => $config,
                    'logoCliente' => $logoCliente,
                    'codigoCupon' => $codigoCuponPDF,
                    'conf' => $conf,
                    'dni' => $dni,
                    'baseurl' => $baseurl,
                    'tarjetas' => $tarjetasData,
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
            ->addFrom('weeareroialty@gmail.com', 'weeare')
            ->setSubject('Cupón de descuento "' . $oferta->nombreEmpresa . '"');

        $html = '<b>Empieza a disfrutar de tus Beneficios…</b>';

        $datoBeneficio = (is_object($atributo)) ? $atributo->DatoBeneficio : $oferta->datoBeneficio;
        $datoHtml = '';
        if ($datoBeneficio != null) {
            if ($oferta->idTipoBeneficio == 1) {
                $datoHtml =  $datoBeneficio . '% Descuento ';
            } elseif ($oferta->idTipoBeneficio == 2) {
                $datoHtml  = 'S/.' . $datoBeneficio . ' Descuento ';
            } else
                  $datoHtml = str_replace(' por', '', $datoBeneficio) . ' ';
            /*elseif ($oferta->idTipoBeneficio == 3) {
                $datoHtml = $datoBeneficio . ' en';
            } else {
                $datoHtml = $datoBeneficio;
                }*/
        }
        
        $html = '<p>Disfruta de tu cupón <b>' . $datoHtml .' ' . $oferta->TituloOferta . '</b></p>';
/*           ' <p>Muchas gracias por tu descarga del cupón de descuento: <b>"';

        $datoBeneficio = (is_object($atributo)) ? $atributo->DatoBeneficio : $oferta->datoBeneficio;

        if ($datoBeneficio != null) {
            if ($oferta->idTipoBeneficio == 1) {
                $html .= '-' . $datoBeneficio . '% Descuento';
            } elseif ($oferta->idTipoBeneficio == 2) {
                $html .= '-S/.' . $datoBeneficio . ' Descuento';
            } elseif ($oferta->idTipoBeneficio == 3) {
                $html .= $datoBeneficio . ' en';
            } else {
                $html .= $datoBeneficio;
            }
        }
*/
        /*$html .= ' ' . ((is_object($atributo)) ? $atributo->NombreAtributo : $oferta->TituloOferta) . '".</b>
            <p>Para poder disfrutar de tu beneficio debes imprimir o mostrar desde tu smartphone el cupón descargado y
             presentarlo en el establecimiento que brinda el descuento. Revisa bien los términos y condiciones de
              cada beneficio para que puedas disfrutarlo sin problemas. </p><br>
            <p>El equipo de Beneficios.pe</p>';*/
        $html .= '<p>Para utilizarlo, presenta tu código de descuento adjunto o número de documento de identidad en el comercio al momento de pagar el consumo.</p>'
              . '<p>Weeare</p>';

        $htmlBody = new MimePart($html);
        $htmlBody->type = "text/html";

        //$attachment = new MimePart($pdf);
        $attachment = new MimePart(fopen($pdf, 'r'));
        $attachment->type = 'application/pdf';
        $attachment->filename = 'beneficios_' . $oferta->nombreEmpresa . '_' . $idCupon . '.pdf';
        $attachment->encoding = Mime::ENCODING_BASE64;
        $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;

        $body = new MimeMessage();
        $body->setParts(array($htmlBody, $attachment));

        $message->setBody($body);

        $transport->send($message);
    }

    public function condicionesAction()
    {
        $id = (int)$this->params()->fromRoute('val', null);
        if ($id == 0) {
            return $this->redirect()->toRoute('404');
        }

        $pagina = "";
        $slug = "";
        try {
            $contenido = $this->getOfertaTable()->getOferta($id);

            if ($contenido) {
                $pagina = $contenido->CondicionesDelivery;
                $slug = $contenido->Slug;
            }
        } catch (\Exception $ex) {
            $pagina = "";
        }

        $catotros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;

        $ubigeo_id = $this->getUbigeo();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $this->setUbigeo($ubigeo_id);
        }
        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);
        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);

        $config = $this->getServiceLocator()->get('Config');

        return new ViewModel(
            array(
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'cliente' => $dni,
                'imgemp' => $img,
                'ubigeo' => $ubigeo,
                'data' => $data,
                'slug' => $slug,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'ubigeo_id' => $ubigeo_id,
                'categoria_id' => $this::CATEGORIA_DEFAULT,
                'url_slug' => 'home',
                'catotros' => $catotros,
                'contenido' => $pagina
            )
        );
    }

    public function registrarRespuestaAction()
    {
        $response = $this->getResponse();

        if ($this->getRequest()->isPost()) {
            $answer = $this->getRequest()->getPost('answer');
            $idCliente = $this->getRequest()->getPost('client');
            $question = $this->getRequest()->getPost('question');

            $session = new SessionContainer('auth');
            $data_user = $session->offsetGet('storage');
            if ($question == 'pregunta_01')
                $data_user['Nombre'] = $answer;
            if ($question == 'pregunta_02')
                $data_user['Apellido'] = $answer;
            $session->offsetSet('storage', $data_user);

            if ($this->getClienteTable()->getCliente($idCliente) != false) {
                $question = ucwords(str_replace('_', '', $question));
                $data[$question] = trim($answer);
                $data['Fecha' . $question] = date("Y-m-d H:i:s");

                if ($this->getPreguntasTable()->saveRespuestas($idCliente, $data)) {
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => true,
                                'NomSession' => $this->identity()["Nombre"] . ' ' . $this->identity()["Apellido"]
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
            } else {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false
                        )
                    )
                );
            }
        }

        return $response;
    }

    public function enviarCorreo($empresa_id)
    {
        $config = $this->getServiceLocator()->get('Config');
        $data_empresa = $this->getEmpresaTable()->getEmpresa($empresa_id);
        $email_empresa = $data_empresa->CorreoPersonaAtencion;

        $total = (int)$this->getConfiguracionesTable()->getConfig('total_redimidos')->Atributo;
        $actual = $this->getCuponTable()->getTotalRedimidosEmpresa($empresa_id);
        if ($total == $actual) {
            $mensaje_cuerpo = $this->getConfiguracionesTable()->getConfig('mensaje_proveedor')->Atributo;
            $transport = $this->getServiceLocator()->get('mail.transport');
            $renderer = $this->getServiceLocator()->get('ViewRenderer');
            $content = $renderer->render(
                'Application/mail/proveedor',
                array(
                    'empresa' => $data_empresa->NombreComercial,
                    'config' => $config,
                    'mensaje_cuerpo' => $mensaje_cuerpo,
                )
            );

            $message_email = new Message();
            $message_email->addTo($email_empresa)
                ->addFrom('weeareroialty@gmail.com', 'weeare')
                ->setSubject('Informe de Consumo');

            $htmlBody = new MimePart($content);
            $htmlBody->type = "text/html";
            $body = new MimeMessage();
            $body->setParts(array($htmlBody));
            $message_email->setBody($body);
            $transport->send($message_email);
        }
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
                    array_push($ofertasNOT, $ofertasA[$i]->idOferta);
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
                array_push($ofertasNOT, $ofertasA[$i]->idOferta);
                $contOM++;
            }
        }

        for ($i = ($cantOP + 9); $i < ($cantOP + 18); $i++) {
            if (isset($ofertasA[$i])) {
                array_push($ofertas_restantes, $ofertasA[$i]);
                array_push($ofertasNOT, $ofertasA[$i]->idOferta);
                $contOM++;
            }
        }
        return array($ofertas_premium, $ofertas_descubre, $ofertas_restantes, $ofertasNOT, $cantOP, $contOM);
    }

    public function generarOrdenamiento($layout, $arrayMerge_L, $tipo)
    {
        $ofertasLayoutFila01 = array();
        $ofertasLayoutFila02 = array();
        $ofertasLayoutFila03 = array();

        $orden = array();
        $datosPosicion = array();
        foreach ($layout as $value) {
            //Recuperamos el Posicionamiento segun el layout
            if ($tipo == $this::TIPO_CATEGORIA) {
                $datosPosicion = $this->getLayoutCategoriaPosicionTable()->getLayoutCategoriaPosicion($value->id);
            } elseif ($tipo == $this::TIPO_CAMPANIA) {
                $datosPosicion = $this->getLayoutCampaniaPosicionTable()->getLayoutCampaniaPosicion($value->id);
            } elseif ($tipo == $this::TIPO_TIENDA) {
                $datosPosicion = $this->getLayoutTiendaPosicionTable()->getLayoutTiendaPosicion($value->id);
            }

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
                    $ofertasLayoutFila01[$data["Index"]] = $data["BNF_Oferta_id"];
                } elseif ($value->fila == 2) {
                    $ofertasLayoutFila02[$data["Index"]] = $data["BNF_Oferta_id"];
                } elseif ($value->fila == 3) {
                    $ofertasLayoutFila03[$data["Index"]] = $data["BNF_Oferta_id"];
                }
            }

            $orden[] = (object)array(
                'TipoLayout' => $value->TipoLayout,
                'fila' => $value->fila
            );
        }

        $ofertasA = array();
        foreach ($arrayMerge_L as $value) {
            if (in_array($value->idOferta, $ofertasLayoutFila01)) {
                $clave = array_search($value->idOferta, $ofertasLayoutFila01);
                $ofertasLayoutFila01[$clave] = $value;
            } elseif (in_array($value->idOferta, $ofertasLayoutFila02)) {
                $clave = array_search($value->idOferta, $ofertasLayoutFila02);
                $ofertasLayoutFila02[$clave] = $value;
            } elseif (in_array($value->idOferta, $ofertasLayoutFila03)) {
                $clave = array_search($value->idOferta, $ofertasLayoutFila03);
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
}
