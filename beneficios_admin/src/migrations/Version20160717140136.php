<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160717140136 extends AbstractMigration
{
    public static $description = "Create BNF2_Demanda_Segmentos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Demanda_Segmentos` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF2_Demanda_id` int(11) NOT NULL,
              `BNF2_Segmento_id` int(11) NOT NULL,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime NOT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF2_Demanda_Segmentos_1_idx` (`BNF2_Demanda_id`),
              KEY `fk_BNF2_Demanda_Segmentos_2_idx` (`BNF2_Segmento_id`),
              CONSTRAINT `fk_BNF2_Demanda_Segmentos_2` 
                FOREIGN KEY (`BNF2_Segmento_id`) 
                REFERENCES `BNF2_Segmentos` (`id`) 
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Demanda_Segmentos_1` 
                FOREIGN KEY (`BNF2_Demanda_id`) 
                REFERENCES `BNF2_Demanda` (`id`) 
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
