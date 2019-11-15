<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 14/12/15
 * Time: 05:22 PM
 */

return array(
    'cache_status' => false,
    'items' => array(
        'Empresas' => array(
            'getAfiliados' => array('ttl' => 3600),
        ),
        'Ofertas' => array(
            'indexAction' => array('ttl' => 3600),
            'categoriaAction' => array('ttl' => 3600),
            'campaniaAction' => array('ttl' => 3600),
            'companyAction' => array('ttl' => 3600),
            'loadOfertaCategoryAction' => array('ttl' => 3600),
        )
    ),
    'connection_cache' => array(
        'server' => '127.0.0.1',
        'port' => 11211
    )
);