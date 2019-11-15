<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100005 extends AbstractMigration
{
    public static $description = "create BNF3_Demanda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Demanda` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF_Empresa_id` int(11) NOT NULL,
              `FechaDemanda` date NOT NULL,
              `PrecioMinimo` int(11) DEFAULT NULL,
              `PrecioMaximo` int(11) DEFAULT NULL,
              `Target` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `Comentarios` mediumtext COLLATE utf8_unicode_ci,
              `Actualizaciones` mediumtext COLLATE utf8_unicode_ci,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime NOT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF3_Demanda_1_idx` (`BNF_Empresa_id`),
              CONSTRAINT `fk_BNF3_Demanda_1` FOREIGN KEY (`BNF_Empresa_id`) 
              REFERENCES `BNF_Empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
