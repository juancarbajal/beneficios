<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100003 extends AbstractMigration
{
    public static $description = "create BNF3_Segmentos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Segmentos` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF3_Campania_id` int(11) NOT NULL,
              `NombreSegmento` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `CantidadPremios` int(11) NOT NULL,
              `CantidadPersonas` int(11) NOT NULL,
              `Subtotal` bigint(20) NOT NULL,
              `Comentario` mediumtext COLLATE utf8_unicode_ci,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime DEFAULT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF3_Segmentos_1_idx` (`BNF3_Campania_id`),
              CONSTRAINT `fk_BNF3_Segmentos_1` FOREIGN KEY (`BNF3_Campania_id`) 
              REFERENCES `BNF3_Campanias` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("DROP TABLE BNF3_Segmentos");
    }
}
