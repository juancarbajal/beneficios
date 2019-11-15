<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100006 extends AbstractMigration
{
    public static $description = "create BNF3_Demanda_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Demanda_Log` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF3_Demanda_id` int(11) NOT NULL,
              `BNF_Empresa_id` int(11) NOT NULL,
              `FechaDemanda` date NOT NULL,
              `PrecioMinimo` decimal(10,2) DEFAULT NULL,
              `PrecioMaximo` decimal(10,2) DEFAULT NULL,
              `Target` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `Comentarios` mediumtext COLLATE utf8_unicode_ci,
              `Actualizaciones` mediumtext COLLATE utf8_unicode_ci,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `Rubros` mediumtext COLLATE utf8_unicode_ci NOT NULL,
              `Segmentos` mediumtext COLLATE utf8_unicode_ci NOT NULL,
              `EmpresaProveedor` mediumtext COLLATE utf8_unicode_ci NOT NULL,
              `EmpresasAdicionales` mediumtext COLLATE utf8_unicode_ci NOT NULL,
              `Departamentos` mediumtext COLLATE utf8_unicode_ci NOT NULL,
              `FechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
