<?php

namespace Oferta;

return array(
    'controllers' => array(
        'invokables' => array(
            'Oferta\Controller\Oferta' => Controller\OfertaController::class,
            'Oferta\Controller\OfertaConsumidas' => Controller\OfertaConsumidasController::class,
            'Oferta\Controller\OfertaBlock' => Controller\OfertaBlockController::class,
            'Oferta\Controller\RegistrarLead' => Controller\RegistrarLeadController::class,
            'Oferta\Controller\Codigo' => Controller\CodigoController::class,
        ),
    ),

    'router' => array(
        'routes' => array(
            'oferta' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/oferta[/:action][/:id[/:val]][/order_by/:order_by][/:order][/p/:page]' .
                        '[/empresa/:q1][/tipo/:q2][/rubro/:q3][/category/:q4][/campaign/:q5]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[0-9]+',
                        'q3' => '[0-9]+',
                        'q4' => '[0-9]+',
                        'q5' => '[0-9]+',
                        'q6' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Oferta\Controller\Oferta',
                        'action' => 'index',
                    ),
                )
            ),
            'oferta-bloque' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/oferta-bloque',
                    'defaults' => array(
                        'controller' => 'Oferta\Controller\OfertaBlock',
                        'action' => 'index',
                    ),
                )
            ),
            'paquete-oferta' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/paquete-oferta[/:action][/:val][/p/:page][/oferta/:q1][/est/:q2]' .
                        '[/fini/:q3][/ffin/:q4]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Oferta\Controller\OfertaConsumidas',
                        'action' => 'index',
                    ),
                )
            ),
            'registrar-lead' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/registrar-lead[/:action][/:val]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Oferta\Controller\RegistrarLead',
                        'action' => 'index',
                    ),
                )
            ),
            'registrar-codigos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/registrar-codigos[/:action][/:val]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Oferta\Controller\Codigo',
                        'action' => 'index',
                    ),
                )
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-oferta' => __DIR__ . '/../view/layout/slidePaginator.phtml',
            'paginator-offcons' => __DIR__ . '/../view/layout/slidePaginatorOC.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
