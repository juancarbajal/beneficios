<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Usuario\Controller\Usuario' => 'Usuario\Controller\UsuarioController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'usuario' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/usuario[/:action][/:id[/:val]][/order_by/:order_by][/:order][/p/:page]' .
                        '[/search[/:q1][/:q2]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Usuario\Controller\Usuario',
                        'action' => 'index',
                    ),
                )
            )
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-usuario' => __DIR__ . '/../view/layout/slidePaginator.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
