<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Rubro\Controller\Rubro' => 'Rubro\Controller\RubroController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'rubro' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rubro[/:action][/:id[/:val]][/order_by/:order_by][/:order][/p/:page][/search[/:q1]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Rubro\Controller\Rubro',
                        'action' => 'index',
                    ),
                )
            )
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-rubro' => __DIR__ . '/../view/layout/slidePaginator.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
