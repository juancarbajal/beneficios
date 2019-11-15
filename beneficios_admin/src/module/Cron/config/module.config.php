<?php

namespace Cron;

return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'download-update' => array(
                    'options' => array(
                        'route' => 'cron ofertas-descarga-actualizar (--expiradas|--finalizadas)',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Descarga',
                            'action' => 'update'
                        )
                    )
                ),
                'presence-update' => array(
                    'options' => array(
                        'route' => 'cron ofertas-presencia-actualizar (--expiradas|--finalizadas)',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Presencia',
                            'action' => 'update'
                        )
                    )
                ),
                'presence-change' => array(
                    'options' => array(
                        'route' => 'cron ofertas-presencia-cambiar',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Presencia',
                            'action' => 'change'
                        )
                    )
                ),
                'lead-update' => array(
                    'options' => array(
                        'route' => 'cron ofertas-lead-actualizar --finalizadas',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Lead',
                            'action' => 'update'
                        )
                    )
                ),
                'etl-client' => array(
                    'options' => array(
                        'route' => 'cron etl cliente',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Etl',
                            'action' => 'generate'
                        )
                    )
                ),
                'delete-client' => array(
                    'options' => array(
                        'route' => 'cron delete cliente',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Cliente',
                            'action' => 'delete'
                        )
                    )
                ),
                'clean-questions' => array(
                    'options' => array(
                        'route' => 'cron delete preguntas',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Cliente',
                            'action' => 'cleanpreguntas'
                        )
                    )
                ),
                'update-cupon' => array(
                    'options' => array(
                        'route' => 'cron update cupon',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Cupon',
                            'action' => 'update'
                        )
                    )
                ),
                'cupon-puntos' => array(
                    'options' => array(
                        'route' => 'cron update-cupon-puntos',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\CuponPuntos',
                            'action' => 'update'
                        )
                    )
                ),
                'caducar-puntos' => array(
                    'options' => array(
                        'route' => 'cron caducar-cupon-puntos (--expiradas|--finalizadas)',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\CuponPuntos',
                            'action' => 'caducar'
                        )
                    )
                ),
                'campania-puntos' => array(
                    'options' => array(
                        'route' => 'cron caducar-campania-puntos',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\CampaniaPuntosController',
                            'action' => 'caducar'
                        )
                    )
                ),
                'update-busqueda' => array(
                    'options' => array(
                        'route' => 'cron update-empresas-busqueda',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Busqueda',
                            'action' => 'update'
                        )
                    )
                ),
                'cupon-premios' => array(
                    'options' => array(
                        'route' => 'cron update-cupon-premios',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\CuponPremios',
                            'action' => 'update'
                        )
                    )
                ),
                'caducar-premios' => array(
                    'options' => array(
                        'route' => 'cron caducar-cupon-premios (--expiradas|--finalizadas)',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\CuponPremios',
                            'action' => 'caducar'
                        )
                    )
                ),
                'campania-premios' => array(
                    'options' => array(
                        'route' => 'cron caducar-campania-premios',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\CampaniaPremiosController',
                            'action' => 'caducar'
                        )
                    )
                ),
                'puntos-referidos' => array(
                    'options' => array(
                        'route' => 'cron caducar-referidos',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\ReferidosController',
                            'action' => 'caducar'
                        )
                    )
                ),
            )
        )
    ),

    'controllers' => array(
        'invokables' => array(
            'Cron\Controller\Descarga' => Controller\DescargaController::class,
            'Cron\Controller\Presencia' => Controller\PresenciaController::class,
            'Cron\Controller\Lead' => Controller\LeadController::class,
            'Cron\Controller\Etl' => Controller\EtlController::class,
            'Cron\Controller\Cliente' => Controller\ClienteController::class,
            'Cron\Controller\Cupon' => Controller\CuponController::class,
            'Cron\Controller\CuponPuntos' => Controller\CuponPuntosController::class,
            'Cron\Controller\CampaniaPuntosController' => Controller\CampaniaPuntosController::class,
            'Cron\Controller\Busqueda' => Controller\BusquedaController::class,
            'Cron\Controller\CuponPremios' => Controller\CuponPremiosController::class,
            'Cron\Controller\CampaniaPremiosController' => Controller\CampaniaPremiosController::class,
            'Cron\Controller\ReferidosController' => Controller\ReferidosController::class,
        ),
    ),
);
