<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Categoria\Controller\Categoria' => 'Categoria\Controller\CategoriaController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'categoria' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/categoria[/:action][/:id][/order_by/:order_by][/:order][/p/:page]'
                        . '[/search[/:q1][/:q2]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Categoria\Controller\Categoria',
                        'action' => 'index',
                    ),
                )
            )
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-categoria' => __DIR__ . '/../view/layout/slidePaginator.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
