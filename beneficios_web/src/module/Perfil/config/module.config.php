<?php
namespace Perfil;

return array(
    'router' => array(
        'routes' => array(
            'perfil' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/perfil[/:action][/p/:page]',
                    'constraints' => array(
                        'val' => '[0-9]*',
                        'page' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Perfil\Controller\Perfil',
                        'action' => 'index',
                    ),
                )
            ),
            'perfil-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/perfil/puntos[/:action][/p/:page]',
                    'constraints' => array(
                        'val' => '[0-9]*',
                        'page' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Perfil\Controller\PerfilPuntos',
                        'action' => 'index',
                    ),
                )
            ),
            'perfil-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/perfil/premios[/:action][/p/:page]',
                    'constraints' => array(
                        'val' => '[0-9]*',
                        'page' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Perfil\Controller\PerfilPremios',
                        'action' => 'index',
                    ),
                )
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Perfil\Controller\Perfil' => Controller\PerfilController::class,
            'Perfil\Controller\PerfilPuntos' => Controller\PerfilPuntosController::class,
            'Perfil\Controller\PerfilPremios' => Controller\PerfilPremiosController::class,
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'paginator-puntos' => __DIR__ . '/../view/layout/slidePaginatorPuntos.phtml',
            'paginator-descar' => __DIR__ . '/../view/layout/slidePaginatorDescar.phtml',
            'paginator-vigentes' => __DIR__ . '/../view/layout/slidePaginatorVigentes.phtml',
            'paginator-utilizados' => __DIR__ . '/../view/layout/slidePaginatorUtilizados.phtml',
            'paginator-vigentes-premios' => __DIR__ . '/../view/layout/slidePaginatorVigentesPremios.phtml',
            'paginator-utilizados-premios' => __DIR__ . '/../view/layout/slidePaginatorUtilizadosPremios.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);