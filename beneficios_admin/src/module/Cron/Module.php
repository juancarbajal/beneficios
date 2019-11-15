<?php

namespace Cron;

use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConsoleBanner(Console $console)
    {
        return 'Console Module v1.0.0';
    }

    public function getConsoleUsage(Console $console)
    {
        return array(
            "** Crons de Ofertas **",
            "- Oferta Descarga",
            "cron ofertas-descarga-actualizar --finalizadas" => "Comprueba la fecha de fin de publicación de las ofertas para actualizar sus los cupones",
            "cron ofertas-descarga-actualizar --expiradas" => "Comprueba la fecha de fin de vigencia de las ofertas para actualizar sus los cupones",

            "- Oferta Presencia",
            "cron ofertas-presencia-actualizar--finalizadas" => "Comprueba la fecha de fin de publicación de las ofertas para actualizar sus los cupones",
            "cron ofertas-presencia-actualizar--expiradas" => "Comprueba la fecha de fin de vigencia de las ofertas para actualizar sus los cupones",
            "cron ofertas-presencia-cambiar" => "Reduce el stock de la oferta en 1",

            "- Oferta Lead",
            "cron ofertas-lead-actualizar --finalizadas" => "Actualiza ofertas vencidas",

            "** Crons de Datos **",
            "- Búsquedas",
            "cron update-empresas-busqueda" => "Actualiza la descripción de empresas en la tabla de búsqueda",

            "- ETL Cliente",
            "cron etl cliente" => "Generación de data del reporte ETL",

            "- Borrar Duplicados",
            "cron delete cliente" => "Elimina todos los clientes duplicados",
            "cron delete preguntas" => "Elimina todos los datos duplicados en la tabla de preguntas",

            "** Crons de Puntos **",
            "- Cupón Puntos",
            "cron update-cupon-puntos" => "Actualiza los cupones de estado redimidos a pagados",
            "cron caducar-cupon-puntos --finalizadas" => "Finaliza los cupones que tengan ofertas con fecha fin de vigencia vencidas",
            "cron caducar-cupon-puntos --expiradas" => "Caduca cupones de las ofertas que estén finalizadas",

            "- Campañas Puntos",
            "cron caducar - campania - puntos: Caduca las campañas puntos con fecha de fin de vigencia vencidas",

            "** Crons de Premios **",

            "- Cupón Premios",
            "cron update-cupon-premios" => "Actualiza los cupones de estado redimidos a pagados",
            "cron caducar-cupon-premios --finalizadas" => "Finaliza los cupones que tengan ofertas con fecha fin de vigencia vencidas",
            "cron caducar-cupon-premios --expiradas" => "Caduca cupones de las ofertas que estén finalizadas",

            "- Campañas Premios",
            "cron caducar-campania-premios" => "Caduca las campañas puntos con fecha de fin de vigencia vencidas",

            "** Crons de Referidos **",
            "- Cancelar Puntos Referidos",
            "cron caducar-referidos" => "Caduca todas las asignaciones de puntos referidos con vigencia hasta la fecha",
        );
    }
}
