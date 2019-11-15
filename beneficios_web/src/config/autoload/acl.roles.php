<?php

return array(
    'guest' => array(
        "allow" => array(
            'login',
            'validate',
            'pre-vista-puntos',
            'pre-vista',
            'pre-vista-premios',
            'zftool-create-module',
            'zftool-create-controller',
            'pre-vista-lead',
        ),
        "deny" => array()
    ),
    'cliente' => array(
        "allow" => array(
            'home',
            'application',
            'category',
            'campaign',
            'company',
            'coupon',
            'resultado',
            'lead',
            '404',
            'tiendas',
            'condiciones',
            'puntos',
            'perfil',
            'coupon-puntos',
            'pre-vista-puntos',
            'pre-vista',
            'premios',
            'coupon-premios',
            'pre-vista-premios',
            'perfil-puntos',
            'perfil-premios',
            'pre-vista-lead',
            'delivery'
        ),
        "deny" => array()
    ),
    'admin' => array(
        "allow" => array(
            'usuario',
            'empresa',
            'paquete',
            'cliente',
            'logout',
            'rubro',
            'categoria',
            'campania',
            'oferta',
            'ordenamiento',
            'puntos',
            'perfil',
            'pre-vista-puntos',
            'pre-vista',
            'premios',
            'pre-vista-premios',
            'perfil-puntos',
            'perfil-premios',
            'pre-vista-lead',
            'delivery'
        ),
        "deny" => array()
    ),
    'proveedor' => array(
        "allow" => array(
            'usuario',
            'empresa',
            'paquete',
            'cliente',
            'logout',
            'rubro',
            'categoria',
            'campania',
            'oferta',
            'ordenamiento',
            'pre-vista-puntos',
            'pre-vista',
            'pre-vista-premios',
            'pre-vista-lead',
            'delivery'
        ),
        'deny' => array()
    ),
    'finanzas' => array(
        "allow" => array(
            'usuario',
            'empresa',
            'paquete',
            'cliente',
            'logout',
            'rubro',
            'categoria',
            'campania',
            'oferta',
            'ordenamiento',
            'pre-vista-puntos',
            'pre-vista',
            'pre-vista-lead',
            'delivery'
        ),
        "deny" => array()
    ),
    'demanda' => array(
        "allow" => array(
            'usuario',
            'empresa',
            'paquete',
            'cliente',
            'logout',
            'rubro',
            'categoria',
            'campania',
            'oferta',
            'ordenamiento',
            'pre-vista-puntos',
            'pre-vista',
            'pre-vista-premios',
            'pre-vista-lead',
            'delivery'
        ),
        "deny" => array()
    )
);
