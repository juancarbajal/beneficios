<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100000 extends AbstractMigration
{
    public static $description = "create BNF3_Campanias table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Campanias` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `NombreCampania` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `TipoSegmento` enum('Clasico','Personalizado') COLLATE utf8_unicode_ci NOT NULL,
              `FechaCampania` date NOT NULL,
              `VigenciaInicio` date NOT NULL,
              `VigenciaFin` date NOT NULL,
              `PresupuestoNegociado` int(11) DEFAULT NULL,
              `ParametroAlerta` int(11) DEFAULT NULL,
              `Comentario` mediumtext COLLATE utf8_unicode_ci,
              `Relacionado` int(11) DEFAULT NULL,
              `EstadoCampania` enum('Borrador','Publicado','Eliminado','Caducado') CHARACTER SET utf8 NOT NULL,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime DEFAULT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
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
