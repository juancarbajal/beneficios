<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Ordenamiento\Controller\Ordenamiento' => 'Ordenamiento\Controller\OrdenamientoController',
            'Ordenamiento\Controller\Banner' => 'Ordenamiento\Controller\BannerController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'ordenamiento' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ordenamiento[/:action][/:id[/:val[/:type]]][/order_by/:order_by][/:order][/p/:page]'
                        . '[/search[/:q1]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Ordenamiento\Controller\Ordenamiento',
                        'action' => 'index',
                    ),
                )
            ),
            'banners' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/banners[/:action][/:id[/:val[/:emp[/:type]]]][/order_by/:order_by][/:order][/p/:page]'
                        . '[/search[/:q1]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Ordenamiento\Controller\Banner',
                        'action' => 'index',
                    ),
                )
            )
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-ordenamiento' => __DIR__ . '/../view/layout/slidePaginator.phtml',
            'paginator-banner' => __DIR__ . '/../view/layout/slideBanner.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
