<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718140139 extends AbstractMigration
{
    public static $description = "Alter BNF2_Oferta_Segmentos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Oferta_Puntos_Segmentos` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF2_Oferta_Puntos_id` int(11) NOT NULL,
              `BNF2_Segmento_id` int(11) NOT NULL,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime NOT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF2_Oferta_Segmentos_1_idx` (`BNF2_Oferta_Puntos_id`),
              KEY `fk_BNF2_Oferta_Segmentos_2_idx` (`BNF2_Segmento_id`),
              CONSTRAINT `fk_BNF2_Oferta_Segmentos_2` 
                FOREIGN KEY (`BNF2_Segmento_id`) 
                REFERENCES `BNF2_Segmentos` (`id`) 
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Oferta_Segmentos_1` 
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`) 
                REFERENCES `BNF2_Oferta_Puntos` (`id`) 
                ON DELETE NO ACTION 
                ON UPDATE NO ACTION);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
