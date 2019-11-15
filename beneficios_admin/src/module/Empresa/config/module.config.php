<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Empresa\Controller\Empresa' => 'Empresa\Controller\EmpresaController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'empresa' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/empresa[/:action][/:id[/:val[/:tipo]]][/order_by/:order_by]' .
                        '[/:order][/p/:page][/search[/:q1][/:q2]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+',
                        'val' => '(0|1)',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Empresa\Controller\Empresa',
                        'action' => 'index',
                    ),
                )
            )
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-empresa' => __DIR__ . '/../view/layout/slidePaginator.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
