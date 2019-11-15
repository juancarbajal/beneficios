<?php

namespace Premios;

return array(
    'controllers' => array(
        'invokables' => array(
            'Premios\Controller\Campanias' => Controller\CampaniasController::class,
            'Premios\Controller\OfertaPremios' => Controller\OfertaPremiosController::class,
            'Premios\Controller\Asignaciones' => Controller\AsignacionesController::class,
            'Premios\Controller\Cancelaciones' => Controller\CancelarController::class,
            'Premios\Controller\ReportePremios' => Controller\ReportePremiosController::class,
            'Premios\Controller\OfertasTop' => Controller\OfertasTopController::class,
            'Premios\Controller\IngresosGlobales' => Controller\IngresosGlobalesController::class,
            'Premios\Controller\ProveedorCampanias' => Controller\ProveedorCampaniasController::class,
            'Premios\Controller\ProveedorOfertas' => Controller\ProveedorOfertasController::class
        ),
    ),

    'router' => array(
        'routes' => array(
            'campanias-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/campanias-premios[/:action][/:id][/:val][/order_by/:order_by]' .
                        '[/:order][/p/:page][/search[/:q1][/:q2]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'val' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\Campanias',
                        'action' => 'index',
                    ),
                )
            ),
            'ofertas-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ofertas-premios[/:action][/:id][/:val][/order_by/:order_by]' .
                        '[/:order][/p/:page][/search[/:q1][/:q2]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+',
                        'val' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\OfertaPremios',
                        'action' => 'index',
                    ),
                )
            ),
            'asignaciones-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/asignaciones-premios[/:action][/:id][/:val][/order_by/:order_by]' .
                        '[/:order][/p/:page][/search[/:q1][/:q2]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+',
                        'val' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\Asignaciones',
                        'action' => 'index',
                    ),
                )
            ),
            'cancelar-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cancelar-premios[/:action][/:id][/order_by/:order_by]' .
                        '[/:order][/p/:page][/search[/:q1][/:q2][/:q3][/:q4][/:q5]]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[0-9]+',
                        'q3' => '[0-9]+',
                        'q4' => '[0-9]+',
                        'q5' => '[a-zA-Z]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\Cancelaciones',
                        'action' => 'index',
                    ),
                )
            ),
            'reporte-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/reporte-premios[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\ReportePremios',
                        'action' => 'index',
                    ),
                )
            ),
            'ofertas-top-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ofertas-top-premios[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\OfertasTop',
                        'action' => 'index',
                    ),
                )
            ),
            'ingresos-globales-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ingresos-globales-premios[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)'
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\IngresosGlobales',
                        'action' => 'index',
                    ),
                )
            ),
            'proveedor-campanias-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/proveedor-campanias-premios[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)'
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\ProveedorCampanias',
                        'action' => 'index',
                    ),
                )
            ),
            'proveedor-ofertas-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/proveedor-ofertas-premios[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)'
                    ),
                    'defaults' => array(
                        'controller' => 'Premios\Controller\ProveedorOfertas',
                        'action' => 'index',
                    ),
                )
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-campanias-premios' => __DIR__ . '/../view/layout/slidePaginator.phtml',
            'paginator-ofertas-premios' => __DIR__ . '/../view/layout/slidePaginatorOferta.phtml',
            'paginator-asignaciones-premios' => __DIR__ . '/../view/layout/slidePaginatorAsignacion.phtml',
            'paginator-cancelaciones-premios' => __DIR__ . '/../view/layout/slidePaginatorCancelaciones.phtml',
            'mail-asignaciones-clasico' => __DIR__ . '/../view/mail/clasico.phtml',
            'mail-asignaciones-personalizado' => __DIR__ . '/../view/mail/personalizado.phtml',
            'mail-asignaciones-premios-admin' => __DIR__ . '/../view/mail/asignacion.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
