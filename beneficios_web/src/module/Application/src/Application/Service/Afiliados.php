<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 26/07/16
 * Time: 11:59 PM
 */

namespace Application\Service;


use Application\Cache\CacheManager;

class Afiliados
{
    protected $serviceLocator;

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo)
    {
        $data = array();
        $config = $this->serviceLocator->get('Config');
        $ttl = $config['items']['Empresas']['getAfiliados']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];
        $keyAFIL = "empafil-" . $segmento . $ubigeo_id . $empresa . $subgrupo;
        if ($cache->hasItem($keyAFIL) and $cacheStatus == true) {
            $logos = $cache->getItem($keyAFIL);
        } else {
            $ofetaTable = $this->serviceLocator->get('Application\Model\Table\OfertaTable');
            $logos = $ofetaTable
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
}