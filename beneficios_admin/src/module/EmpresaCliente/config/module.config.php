<?php
return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/no-access',
                    'defaults' => array(
                        '__NAMESPACE__' => 'EmpresaCliente\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                ),
            ),
            'oauth' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/oauth',
                    'defaults' => array(
                        'controller' => 'EmpresaCliente\Controller\Index',
                        'action' => 'oauth',
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
//    'translator' => array(
//        'locale' => 'en_US',
//        'translation_file_patterns' => array(
//            array(
//                'type'     => 'gettext',
//                'base_dir' => __DIR__ . '/../language',
//                'pattern'  => '%s.mo',
//            ),
//        ),
//    ),
    'controllers' => array(
        'invokables' => array(
            'EmpresaCliente\Controller\Index' => 'EmpresaCliente\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'error/404_login' => __DIR__ . '/../view/error/404_login.phtml',
            'error/404_logout' => __DIR__ . '/../view/error/404_logout.phtml',
            'error/500' => __DIR__ . '/../view/layout/500.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'layout' => 'layout/layout',
        'display_exceptions' => true,
        'exception_template' => 'error/index',
        'display_not_found_reason' => true,
        'not_found_template' => 'error/404',
        'doctype' => 'HTML5',
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
);
