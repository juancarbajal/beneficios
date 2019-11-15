<?php

namespace Puntos;

return array(
    'controllers' => array(
        'invokables' => array(
            'Puntos\Controller\Campanias' => Controller\CampaniasController::class,
            'Puntos\Controller\OfertaPuntos' => Controller\OfertaPuntosController::class,
            'Puntos\Controller\Asignaciones' => Controller\AsignacionesController::class,
            'Puntos\Controller\Cancelaciones' => Controller\CancelarController::class,
            'Puntos\Controller\ReportePuntos' => Controller\ReportePuntosController::class,
            'Puntos\Controller\ReporteRedimidos' => Controller\ReporteRedimidosController::class,

            'Puntos\Controller\ReportePagados' => Controller\ReportePagadosController::class,

            'Puntos\Controller\OfertasTop' => Controller\OfertasTopController::class,
            'Puntos\Controller\IngresosGlobales' => Controller\IngresosGlobalesController::class,
            'Puntos\Controller\ProveedorCampanias' => Controller\ProveedorCampaniasController::class,
            'Puntos\Controller\ProveedorOfertas' => Controller\ProveedorOfertasController::class,
            'Puntos\Controller\DeliveryController' => Controller\DeliveryController::class
        ),
    ),



    'router' => array(
        'routes' => array(






            'reporte-pagados' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/reporte-pagados[/:action][/:id][/order_by/:order_by][/:order][/p/:page]'.
                        '[/search[/:q1][/:q2][/:q3][/:q4][/:q5][/:q6][/:q7]]' .
                        '[/empresa/:empresa][/campania/:campania][/oferta/:oferta][/estado/:estado][/desde/:desde][/hasta/:hasta][/codigo/:codigo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[0-9]+',
                        'q3' => '[0-9]+',
                        'q4' => '[a-zA-Z_-]+',
                        'q5' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'q6' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'q7' => '[A-Z0-9]+',
                        'empresa' => '[0-9]+',
                        'campania' => '[0-9]+',
                        'oferta' => '[0-9]+',
                        'estado' => '[a-zA-Z_-]+',
                        'desde' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'hasta' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'codigo' => '[A-Z0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Puntos\Controller\ReportePagados',
                        'action' => 'index',
                    ),
                )
            ),
            'reporte-redimidos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/reporte-redimidos[/:action][/:id][/order_by/:order_by][/:order][/p/:page]'.
                        '[/search[/:q1][/:q2][/:q3][/:q4][/:q5][/:q6][/:q7]]' .
                        '[/empresa/:empresa][/campania/:campania][/oferta/:oferta][/estado/:estado][/desde/:desde][/hasta/:hasta][/codigo/:codigo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*',
                        'id' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'q1' => '[0-9]+',
                        'q2' => '[0-9]+',
                        'q3' => '[0-9]+',
                        'q4' => '[a-zA-Z_-]+',
                        'q5' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'q6' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'q7' => '[A-Z0-9]+',
                        'empresa' => '[0-9]+',
                        'campania' => '[0-9]+',
                        'oferta' => '[0-9]+',
                        'estado' => '[a-zA-Z_-]+',
                        'desde' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'hasta' => '(\d{4}-\d{2}-\d{2})|(0|[1-9][0-9]*)',
                        'codigo' => '[A-Z0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Puntos\Controller\ReporteRedimidos',
                        'action' => 'index',
                    ),
                )
            ),



















            'campanias-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/campanias-puntos[/:action][/:id][/:val][/order_by/:order_by]' .
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
                        'controller' => 'Puntos\Controller\Campanias',
                        'action' => 'index',
                    ),
                )
            ),
            'ofertas-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ofertas-puntos[/:action][/:id][/:val][/order_by/:order_by]' .
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
                        'controller' => 'Puntos\Controller\OfertaPuntos',
                        'action' => 'index',
                    ),
                )
            ),
            'asignaciones-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/asignaciones-puntos[/:action][/:id][/:val][/order_by/:order_by]' .
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
                        'controller' => 'Puntos\Controller\Asignaciones',
                        'action' => 'index',
                    ),
                )
            ),
            'cancelar-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cancelar-puntos[/:action][/:id][/order_by/:order_by]' .
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
                        'controller' => 'Puntos\Controller\Cancelaciones',
                        'action' => 'index',
                    ),
                )
            ),
            'reporte-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/reporte-puntos[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Puntos\Controller\ReportePuntos',
                        'action' => 'index',
                    ),
                )
            ),
            'ofertas-top' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ofertas-top[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Puntos\Controller\OfertasTop',
                        'action' => 'index',
                    ),
                )
            ),
            'ingresos-globales' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ingresos-globales[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)'
                    ),
                    'defaults' => array(
                        'controller' => 'Puntos\Controller\IngresosGlobales',
                        'action' => 'index',
                    ),
                )
            ),
            'proveedor-campanias' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/proveedor-campanias[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)'
                    ),
                    'defaults' => array(
                        'controller' => 'Puntos\Controller\ProveedorCampanias',
                        'action' => 'index',
                    ),
                )
            ),
            'proveedor-ofertas' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/proveedor-ofertas[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)'
                    ),
                    'defaults' => array(
                        'controller' => 'Puntos\Controller\ProveedorOfertas',
                        'action' => 'index',
                    ),
                )
            ),
            'delivery-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/delivery-puntos[/:action]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*(?!\border)'
                    ),
                    'defaults' => array(
                        'controller' => 'Puntos\Controller\DeliveryController',
                        'action' => 'index',
                    ),
                )
            )
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-campanias-puntos' => __DIR__ . '/../view/layout/slidePaginator.phtml',
            'paginator-reporte-pagados' => __DIR__ . '/../view/layout/slidePaginatorPuntos.phtml',
            'paginator-reporte-redimidos' => __DIR__ . '/../view/layout/slidePaginatorPuntosRedimidos.phtml',

            'paginator-ofertas-puntos' => __DIR__ . '/../view/layout/slidePaginatorOferta.phtml',
            'paginator-asignaciones-puntos' => __DIR__ . '/../view/layout/slidePaginatorAsignacion.phtml',
            'paginator-cancelaciones-puntos' => __DIR__ . '/../view/layout/slidePaginatorCancelaciones.phtml',
            'mail-asignaciones-clasico' => __DIR__ . '/../view/mail/clasico.phtml',
            'mail-asignaciones-personalizado' => __DIR__ . '/../view/mail/personalizado.phtml',
            'mail-asignaciones-admin' => __DIR__ . '/../view/mail/asignacion.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
