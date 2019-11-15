<?php

namespace Paquete;

return array(
    'controllers' => array(
        'invokables' => array(
            'Paquete\Controller\Paquete' => Controller\PaqueteController::class,
            'Paquete\Controller\PaquetesComprados' => Controller\PaquetesCompradosController::class,
        ),
    ),

    'router' => array(
        'routes' => array(
            'paquete' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/paquete[/:action][/:id[/:val]][/order_by/:order_by][/:order][/p/:page]'
                        . '[/search[/:q1][/:q2][/:q3][/:q4][/:q5]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'val' => '(0|1)',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[0-9]+',
                        'q3' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'q4' => '(\d{4}-\d{2}-\d{2})|0',
                        'q5' => '(\d{4}-\d{2}-\d{2})|0'
                    ),
                    'defaults' => array(
                        'controller' => 'Paquete\Controller\Paquete',
                        'action' => 'index',
                    ),
                )
            ),

            'paquetes-comprados' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/paquetes-comprados[/:action][/:val][/p/:page][/paq/:q1][/fact/:q2]' .
                        '[/fini/:q3][/ffin/:q4]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Paquete\Controller\PaquetesComprados',
                        'action' => 'index',
                    ),
                )
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-paquete' => __DIR__ . '/../view/layout/slidePaginator.phtml',
            'paginator-assing' => __DIR__ . '/../view/layout/slidePaginatorAssing.phtml',
            'paginator-paqcomp' => __DIR__ . '/../view/layout/slidePaginatorPC.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
