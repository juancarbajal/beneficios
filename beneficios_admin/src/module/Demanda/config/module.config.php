<?php

namespace Demanda;

return array(
    'controllers' => array(
        'invokables' => array(
            'Demanda\Controller\Demanda' => Controller\DemandaController::class,
            'Demanda\Controller\DemandaPremios' => Controller\DemandaPremiosController::class,
        ),
    ),

    'router' => array(
        'routes' => array(
            'demandas-ofertas' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/demandas-ofertas[/:action][/id/:id][/fecha/:val][/categoria/:val2][/order_by/:order_by]' .
                        '[/:order][/p/:page][/search[/:q1][/:q2][/:q3]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+',
                        'val' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'val2' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'q3' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Demanda\Controller\Demanda',
                        'action' => 'index',
                    ),
                ),
            ),
            'demandas-ofertas-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/demandas-ofertas-premios[/:action][/id/:id][/fecha/:val][/categoria/:val2][/order_by/:order_by]' .
                        '[/:order][/p/:page][/search[/:q1][/:q2][/:q3]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+',
                        'val' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'val2' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'q3' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Demanda\Controller\DemandaPremios',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-demandas-ofertas' => __DIR__ . '/../view/layout/slidePaginator.phtml',
            'paginator-demandas-ofertas-premios' => __DIR__ . '/../view/layout/slidePaginatorPremios.phtml',
            'mail-demandas-ofertas' => __DIR__ . '/../view/mail/mail.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
