<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Auth' => 'Auth\Controller\AuthController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Auth',
                        'action'     => 'index',
                    ),
                ),
            ),
            'login' => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Auth',
                        'action'     => 'logout',
                    ),
                ),
            ),
            'validate' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '[/:action][/:val]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Auth',
                        'action'     => 'validate',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'aliases' => array(
            'Zend\Authentication\AuthenticationService' => 'auth_service',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'auth' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'auth/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'auth/layout/mobile' => __DIR__ . '/../view/mobile/layout.phtml',
        ),
    ),
);
