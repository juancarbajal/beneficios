<?php

namespace Cupon;

return array(
    'controllers' => array(
        'invokables' => array(
            'Cupon\Controller\Index' => Controller\IndexController::class,
            'Cupon\Controller\Puntos' => Controller\PuntosController::class,
            'Cupon\Controller\Premios' => Controller\PremiosController::class,

            'Cupon\Controller\ReportePagados' => Controller\ReportePagadosController::class,
            'Cupon\Controller\ReporteRedimidos' => Controller\ReporteRedimidosController::class,
        ),
    ),

    'router' => array(
        'routes' => array(

            'reporte-pagados-proveedor' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/reporte-pagados-proveedor[/:action][/:id][/order_by/:order_by][/:order][/p/:page]'.
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
                        'controller' => 'Cupon\Controller\ReportePagados',
                        'action' => 'index',
                    ),
                )
            ),


            'reporte-redimidos-proveedor' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/reporte-redimidos-proveedor[/:action][/:id][/order_by/:order_by][/:order][/p/:page]'.
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
                        'controller' => 'Cupon\Controller\ReporteRedimidos',
                        'action' => 'index',
                    ),
                )
            ),























            'cupon' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cupon[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Cupon\Controller\Index',
                        'action' => 'index',
                    ),
                )
            ),
            'cupon-puntos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cupon-puntos[/:action][/:id][/order_by/:order_by][/:order][/p/:page]'.
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
                        'controller' => 'Cupon\Controller\Puntos',
                        'action' => 'index',
                    ),
                )
            ),




            'cupon-premios' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cupon-premios[/:action][/:id][/order_by/:order_by][/:order][/p/:page]'.
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
                        'controller' => 'Cupon\Controller\Premios',
                        'action' => 'index',
                    ),
                )
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'paginator-cupon-puntos' => __DIR__ . '/../view/layout/slidePaginator.phtml',


            'paginator-reporte-pagados-proveedor' => __DIR__ . '/../view/layout/slidePaginatorPuntos.phtml',
            'paginator-reporte-redimidos-proveedor' => __DIR__ . '/../view/layout/slidePaginatorPuntosRedimidos.phtml',


            'paginator-cupon-premios' => __DIR__ . '/../view/layout/slidePaginatorPremios.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
