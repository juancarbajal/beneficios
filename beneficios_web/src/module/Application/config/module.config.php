<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'application' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/home[/:action][/:val]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                )
            ),
            'category' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/category[/:cat][/:val]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'categoria',
                    ),
                )
            ),
            'campaign' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/campaign[/:camp][/:action][/:val]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'campania',
                    ),
                )
            ),
            'company' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/company[/:comp][/:action][/:val]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'company',
                    ),
                )
            ),
            'coupon' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '[/:val]/coupon[/:coupon]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'coupon',
                    ),
                )
            ),
            'resultado' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/resultado[/:opt][/:action][/:val]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Busqueda',
                        'action' => 'index',
                    ),
                )
            ),
            'pdf' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/pdf',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'generatePDF',
                    ),
                ),
            ),
            'lead' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '[/:val]/lead[/:opt]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Lead',
                        'action' => 'index',
                    ),
                )
            ),
            '404' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/404',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Caducada',
                        'action' => 'index',
                    ),
                )
            ),
            'tiendas' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/tiendas',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Tienda',
                        'action' => 'index',
                    ),
                )
            ),
            'condiciones' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/condiciones[/:val]',
                    'constraints' => array(
                        'val' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'condiciones',
                    ),
                )
            ),
            'puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/puntos[/:action][/:val]',
                    'constraints' => array(
                        'val' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Puntos',
                        'action' => 'index',
                    ),
                )
            ),
            'coupon-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '[/:val]/coupon-puntos[/:coupon]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Puntos',
                        'action' => 'coupon',
                    ),
                )
            ),
            'pre-vista-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '[/:val]/pre-vista-puntos[/:coupon]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\PreVista',
                        'action' => 'coupon',
                    ),
                )
            ),
            'pre-vista' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '[/:val]/pre-vista[/:coupon]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\PreVista',
                        'action' => 'normal',
                    ),
                )
            ),
            'pre-vista-lead' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '[/:val]/pre-vista-lead[/:coupon]',
                    'constraints' => array(
                        'val' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\PreVista',
                        'action' => 'lead',
                    ),
                )
            ),
            'delivery' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/delivery[/:slug[/:id]]',
                    'constraints' => array(
                        'slug' =>  '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Delivery',
                        'action' => 'index',
                    ),
                )
            )
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
            'Application\Controller\Index' => Controller\IndexController::class,
            'Application\Controller\Busqueda' => Controller\BusquedaController::class,
            'Application\Controller\Lead' => Controller\LeadController::class,
            'Application\Controller\Caducada' => Controller\CaducadaController::class,
            'Application\Controller\Tienda' => Controller\TiendaController::class,
            'Application\Controller\Puntos' => Controller\PuntosController::class,
            'Application\Controller\PreVista' => Controller\PreVistaController::class,
            'Application\Controller\Delivery' => Controller\DeliveryController::class
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'mobile/layout' => __DIR__ . '/../view/mobile/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'error/errorcustom' => __DIR__ . '/../view/error/500.phtml',
            'Application/index/pdf' => __DIR__ . '/../view/application/index/pdf.phtml',
            'Application/puntos/pdf' => __DIR__ . '/../view/application/puntos/pdf.phtml',
            'Application/mail/lead' => __DIR__ . '/../view/mail/message_lead.phtml',
            'Application/mail/delivery' => __DIR__ . '/../view/mail/message_delivery.phtml',
            'Application/mail/proveedor' => __DIR__ . '/../view/mail/message_proveedor.phtml',
        ),
        'template_path_stack' => array(
            'application' => __DIR__ . '/../view',
        ),
    ),
);
