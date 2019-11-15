<?php

namespace Reportes;

return array(
    'controllers' => array(
        'invokables' => array(
            'Reportes\Controller\Reporte' => Controller\ReporteController::class,
            'Reportes\Controller\ReporteUso' => Controller\ReporteUsoController::class,
            'Reportes\Controller\OfertasPublicadas' => Controller\OfertasPublicadasController::class,
            'Reportes\Controller\ReporteDescargas' => Controller\ReporteDescargasController::class,
            'Reportes\Controller\OfertaDescargas' => Controller\OfertaDescargasController::class,
        ),
    ),

    'router' => array(
        'routes' => array(
            'reportes' => array(
                'type' => 'segment',
                'options' => array(
                    'route' =>
                        '/reportes[/:action][/:fini][/:ffin]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Reportes\Controller\Reporte',
                        'action' => 'index',
                    ),
                )
            ),
            'reporte-dni' => array(
                'type' => 'segment',
                'options' => array(
                    'route' =>
                        '/reportes[/:action][/order_by/:order_by][/:order][/p/:page][/search[/:q1]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Reportes\Controller\Reporte',
                        'action' => 'index',
                    ),
                )
            ),
            'reporte-uso' => array(
                'type' => 'segment',
                'options' => array(
                    'route' =>
                        '/reporte-uso[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Reportes\Controller\ReporteUso',
                        'action' => 'index',
                    ),
                )
            ),
            'reporte-ofertas' => array(
                'type' => 'segment',
                'options' => array(
                    'route' =>
                        '/reporte-ofertas[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Reportes\Controller\OfertasPublicadas',
                        'action' => 'index',
                    ),
                )
            ),
            'reporte-descarga' => array(
                'type' => 'segment',
                'options' => array(
                    'route' =>
                        '/reporte-descarga[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Reportes\Controller\reporteDescargas',
                        'action' => 'index',
                    ),
                )
            ),
            'reporte-oferta-descarga' => array(
                'type' => 'segment',
                'options' => array(
                    'route' =>
                        '/reporte-oferta-descarga[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Reportes\Controller\OfertaDescargas',
                        'action' => 'index',
                    ),
                )
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-reporte-dni' => __DIR__ . '/../view/layout/slidePaginator.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
