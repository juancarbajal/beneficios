<?php

namespace Application\Controller;

use Application\Cache\CacheManager;
use Application\Form\BusquedaForm;
use Application\Service\MobileDetect;
use Zend\I18n\Validator\Alpha;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Zend\Debug\Debug;

class BusquedaController extends AbstractActionController
{
    protected $ofertaTable;
    protected $empresaTable;
    protected $clienteTable;
    protected $ofertaEmpresaCliente;
    protected $ubigeoTable;
    protected $categoriaTable;
    protected $ofertaUbigeoTable;
    protected $bannersCategoriaTable;

    const PAIS_DEFAULT = 1;

    #region ObjectTables
    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaTable');
    }

    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosTable');
    }
    public function getOfertaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
    }

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\ClienteTable');
    }

    public function getOfertaEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaEmpresaClienteTable');
    }

    public function getUbigeoTable()
    {
        return $this->serviceLocator->get('Application\Model\UbigeoTable');
    }

    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
    }

    public function getOfertaUbigeoTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaUbigeoTable');
    }

    public function getBannersCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\BannersCategoriaTable');
    }
    #endregion

    #region Inicializacion
    public function getAfiliados($segmento = 0, $ubigeo_id, $empresa, $subgrupo = 0)
    {
        $data = array();
        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Empresas']['getAfiliados']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];
        $keyAFIL = __CLASS__ . __FUNCTION__ . $segmento . $ubigeo_id . $empresa . $subgrupo;
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
    #endregion

    public function indexAction()
    {
        $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;
        $ubigeo_id = $this->getUbigeo();

        $datobusqueda = null;
        $typebusqueda = 1;
        $ofertas = null;
        $select = -1;
        $categoria = 1;
        $premium = 0;
        $destacados = 0;
        $novedades = 0;

        $catotros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = (int)$request->getPost()->oferta_id;

            if ($request->getPost()->ubigeo != null) {
                $ubigeo_id = $request->getPost()->ubigeo;
            } else {
                $ubigeo_id = $request->getPost()->ubigeo_id;
                $this->setUbigeo($ubigeo_id);
            }

            $datobusqueda = $request->getPost()->search;
            $premium = (int)$request->getPost()->premium;
            $destacados = (int)$request->getPost()->destacados;
            $novedades = (int)$request->getPost()->novedades;
            $categoria = ((int)$request->getPost()->categoria_id != 0) ? (int)$request->getPost()->categoria_id : 1;
            $category = $request->getPost()->category;
            $tipoOferta = $request->getPost()->tipoOferta;

            if ($premium == 1) {
                $select = 1;
            }
            if ($destacados == 1) {
                $select = 2;
            }
            if ($novedades == 1) {
                $select = 3;
            }

            if ($id != 0) {
                if ($tipoOferta == 1) {
                    return $this->redirect()->toRoute(
                        'coupon',
                        array('coupon' => $this->getOfertaTable()->getOferta($id)->Slug, 'val' => $category)
                    );
                } elseif ($tipoOferta == 2) {
                    return $this->redirect()->toRoute(
                        'coupon-puntos',
                        array('coupon' => $this->getOfertaPuntosTable()->getOfertaPuntos($id)->Slug, 'val' => $category)
                    );
                } elseif ($tipoOferta == 0) {
                    return $this->redirect()->toRoute(
                        'company',
                        array('comp' => $this->getEmpresaTable()->getEmpresa($id)->Slug)
                    );
                } elseif($tipoOferta == 3) {
                    return $this->redirect()->toRoute(
                        'coupon-premios',
                        array('coupon' => $this->getOfertaPremiosTable()->getOfertaPremios($id)->Slug, 'val' => $category)
                    );
                }
            } else {
                $typebusqueda = 2;
            }
        }

        $segmentos_puntos = $this->identity()['segmentos_puntos'];
        $segmentos_premios = $this->identity()['segmentos_premios'];
        $puntos = $this->identity()['exist_puntos'];
        $premios = $this->identity()['exist_premios'];
        $ofertas = $this->getOfertaEmpresaClienteTable()->getImagenOfertaXName(
            $datobusqueda,
            $premium,
            $destacados,
            $novedades,
            $ubigeo_id,
            $empresa,
            $segmento,
            $subgrupo,
            0,
            $segmentos_puntos,
            $puntos,
            $premios,
            $segmentos_premios
        );

        //ubicacion
        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        //categoias
        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);

        //empresas afiliadas
        $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);

        $categoriadata = $this->getCategoriaTable()->getCategoria($categoria);
        $url = 'resultado/home';
        if ($categoriadata->Slug != 'home') {
            $url = 'resultado/' . $categoriadata->Slug;
        }

        //varibles globales
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

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => $url,
                'slug' => 'cat',
                'url_slug' => $categoriadata->Slug,
                'category' => 'busqueda',
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rofertasP' => $config['images']["ofertas-puntos"],
                'rofertasPR' => $config['images']["ofertas-premios"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'nombre' => $datobusqueda,
                'cliente' => $dni,
                'imgemp' => $img,
                'ubigeo' => $ubigeo,
                'data' => $data,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'typebusqueda' => $typebusqueda,
                'ofertas' => $ofertas,
                'ubigeo_id' => $ubigeo_id,
                'select' => $select,
                'campania_id' => null,
                'categoria_id' => $categoria,
                'catotros' => $catotros,
                'premium' => $premium,
                'destacados' => $destacados,
                'novedades' => $novedades,
                'total' => $totalOEPFilter,
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('application/busqueda/index-mobile');
        }
        return $view;
    }

    public function ofertaNameAction()
    {
        $empresa = $this->identity()['Empresa'];
        $segmento = isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0;
        $subgrupo = isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0;

        $data = array();
        $result = false;
        $response = $this->getResponse();
        $parametros = $this->params()->fromQuery();
        $ubigeo_id = $parametros['id'];
        $name = $parametros['val'];

        $validador_alpha = new Alpha(array('allowWhiteSpace' => true));

        $segmentos_puntos = $this->identity()['segmentos_puntos'];
        $segmentos_premios = $this->identity()['segmentos_premios'];
        $puntos = $this->identity()['exist_puntos'];
        $premios = $this->identity()['exist_premios'];

        if (is_int((int)$ubigeo_id) > 0 and $validador_alpha->isValid($name)) {
            $ofertas = $this->getOfertaEmpresaClienteTable()->busquedaOferta(
                $name,
                $ubigeo_id,
                $empresa,
                $segmento,
                $subgrupo,
                0,
                $segmentos_puntos,
                $puntos,
                $premios,
                $segmentos_premios
            );

            if (count($ofertas) == 0) {
                $result = false;
            }

            foreach ($ofertas as $dato) {
                $dato = (object)$dato;
                $result = true;
                $data[] = array('id' => $dato->idOferta, 'value' => $dato->Titulo, 'tipo' => $dato->TipoOferta);
            }
        }

        $response->setContent(
            Json::encode(
                array(
                    'data' => $data,
                    'result' => $result
                )
            )
        );

        return $response;
    }

    public function ofertaUbigeoAction()
    {
        $data = array();
        $response = $this->getResponse();
        $ofertas = $this->getOfertaUbigeoTable()->getOfertaUbigeo(
            $this::PAIS_DEFAULT,
            $this->identity()['Empresa'],
            isset($this->identity()['segmento']) ? $this->identity()['segmento'] : 0,
            isset($this->identity()['subgrupo']) ? $this->identity()['subgrupo'] : 0
        );
        foreach ($ofertas as $dato) {
            $data[$dato->id] = $dato->Nombre;
        }

        $response->setContent(
            Json::encode(
                array(
                    'data' => $data
                )
            )
        );

        return $response;
    }
}
