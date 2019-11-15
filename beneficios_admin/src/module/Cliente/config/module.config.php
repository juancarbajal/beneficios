<?php
namespace Cliente;

return array(
    'controllers' => array(
        'invokables' => array(
            'Cliente\Controller\FinalUser' => Controller\FinalUserController::class,
            'Cliente\Controller\FinalUserLoad' => Controller\FinalUserLoadController::class,
        ),
    ),

    'router' => array(
        'routes' => array(
            'cliente' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cliente[/:action][/:id][/cliente-search/:cliente][/empresa-search/:empresa]'
                        . '[/order_by/:order_by][/:order][/p/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'cliente' => '[a-zA-Z0-9_-]*',
                        'empresa' => '[0-9]+|all',
                        'page' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Cliente\Controller\FinalUser',
                        'action' => 'index',
                    ),
                )
            ),
            'cliente-load' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cliente-load[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Cliente\Controller\FinalUserLoad',
                        'action' => 'load',
                    ),
                )
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-slide' => __DIR__ . '/../view/layout/slidePaginator.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
