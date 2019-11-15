<?php

namespace Application\Controller;

use Application\Cache\CacheManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Zend\Debug\Debug;

class TiendaController extends AbstractActionController
{
    protected $empresatable;
    protected $ubigeotable;
    protected $categoriatable;
    protected $ofertatable;
    protected $clientetable;

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

    public function getAfiliadosTotal($segmento, $ubigeo_id, $empresa, $subgrupo)
    {
        $this->ofertatable = $this->serviceLocator->get('Application\Model\Table\OfertaTable');
        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Empresas']['getAfiliados']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];
        $keyAFIL = __CLASS__ . __FUNCTION__ . $segmento . $ubigeo_id . $empresa . $subgrupo;
        if ($cache->hasItem($keyAFIL) and $cacheStatus == true) {
            $logos = $cache->getItem($keyAFIL);
        } else {
            $logos = $this->ofertatable
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

        $data = $empresas_afiliadas;
        return $data;
    }

    public function indexAction()
    {
        $ubigeo_id = $this->getUbigeo();
        $pais = 1;

        $this->empresatable = $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
        $this->ubigeotable = $this->serviceLocator->get('Application\Model\UbigeoTable');
        $this->categoriatable = $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
        $this->ofertatable = $this->serviceLocator->get('Application\Model\Table\OfertaTable');
        $this->clientetable = $this->serviceLocator->get('Auth\Model\Table\ClienteTable');

        $url = 'home';
        $slug = $this->params()->fromRoute('opt', 0);

        //varibles globales
        $config = $this->getServiceLocator()->get('Config');

        $dni = isset($this->identity()['NumeroDocumento']) ?$this->identity()['NumeroDocumento'] :null;
        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $segmento = isset($this->identity()['segmento']) ?$this->identity()['segmento'] :null;
        $subgrupo = isset($this->identity()['subgrupo']) ?$this->identity()['subgrupo'] :null;

        //ubicacion
        $ubigeos = $this->ubigeotable->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        //empresas afiliadas
        $data = $this->getAfiliadosTotal($segmento, $ubigeo_id, $empresa, $subgrupo);

        //categoias
        $categorias = $this->categoriatable->getBuscarCategoriaXPais($pais);
        $categoriasfooter = $this->categoriatable->getBuscarCategoriaXPais($pais);
        $catotros = $this->categoriatable->getBuscarCatOtros($pais);

        return new ViewModel(
            array(
                'url' => $url,
                'slug' => $slug,
                'category' => 'tienda',
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'cliente' => $dni,
                'imgemp' => $img,
                'ubigeo' => $ubigeo,
                'data' => $data,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'ubigeo_id' => $ubigeo_id,
                'afiliadas' => true,
                'tiendas' => 'tiendas',
                'catotros' => $catotros,
            )
        );
    }
}
