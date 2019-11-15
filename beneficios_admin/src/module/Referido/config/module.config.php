<?php
namespace Referido;

return array(
    'controllers' => array(
        'invokables' => array(
            'Referido\Controller\FinalUser' => Controller\FinalUserController::class,
            'Referido\Controller\ClienteLanding' => Controller\ClienteLandingController::class,
            'Referido\Controller\FinalUserLoad' => Controller\FinalUserLoadController::class,
            'Referido\Controller\Configuracion' => Controller\RefConfigController::class,
            'Referido\Controller\FinalUserLoadSpecialist' => Controller\FinalUserLoadSpecialistController::class
        ),
    ),

    'router' => array(
        'routes' => array(
            'referido' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/referido[/:action][/cliente[/:cliente]][/inicio[/:fecha_ini]][/fin[/:fecha_fin]]' .
                        '[/order_by/:order_by][/:order][/p/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*',
                        'cliente' => '[a-zA-Z0-9]*',
                        'fecha_ini' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'fecha_fin' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                    ),
                    'defaults' => array(
                        'controller' => 'Referido\Controller\FinalUser',
                        'action' => 'index',
                    ),
                )
            ),
            'cliente-landing' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cliente-landing[/:action][/cliente[/:cliente]][/inicio[/:fecha_ini]][/fin[/:fecha_fin]]' .
                        '[/order_by/:order_by][/:order][/p/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*',
                        'cliente' => '[a-zA-Z0-9]*',
                        'fecha_ini' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'fecha_fin' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                    ),
                    'defaults' => array(
                        'controller' => 'Referido\Controller\ClienteLanding',
                        'action' => 'index',
                    ),
                )
            ),
            'referido-load' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/referido-load[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Referido\Controller\FinalUserLoad',
                        'action' => 'load',
                    ),
                )
            ),






            'referido-load-specialist' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/referido-load-specialist[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Referido\Controller\FinalUserLoadSpecialist',
                        'action' => 'load-specialist',
                    ),
                )
            ),




            'referido-config' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/referido-configuracion[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Referido\Controller\Configuracion',
                        'action' => 'index',
                    ),
                )
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-slide-colaborador' => __DIR__ . '/../view/layout/slidePaginator.phtml',
            'paginator-slide-referido' => __DIR__ . '/../view/layout/slidePaginatorReferido.phtml',
            'mail-notificacion-puntos' => __DIR__ . '/../view/mail/notificacion-puntos.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
