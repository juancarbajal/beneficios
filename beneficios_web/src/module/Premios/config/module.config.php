<?php
namespace Premios;

return array(
    'router' => array(
        'routes' => array(
            'premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/premios[/:action][/:val]',
                    'constraints' => array(
                        'val' => '[0-9]*',
                        'page' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\Premios',
                        'action' => 'index',
                    ),
                )
            ),
            'coupon-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '[/:val]/coupon-premios[/:coupon]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\Premios',
                        'action' => 'coupon',
                    ),
                )
            ),
            'pre-vista-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/pre-vista-premios[/:coupon]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\PreVista',
                        'action' => 'coupon',
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
            'Premios\Controller\Premios' => Controller\PremiosController::class,
            'Premios\Controller\PreVista' => Controller\PreVistaController::class
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);