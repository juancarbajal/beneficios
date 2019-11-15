<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150145 extends AbstractMigration
{
    public static $description = "Create BNF2_Segmentos_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Segmentos_Log` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Segmentos_id` INT NOT NULL,
              `BNF2_Campania_id` INT NOT NULL,
              `NombreSegmento` VARCHAR(255) NOT NULL,
              `CantidadPuntos` INT NOT NULL,
              `CantidadPersonas` INT NOT NULL,
              `Subtotal` BIGINT NOT NULL,
              `Comentario` TEXT NOT NULL,
              `RazonEliminado` TEXT NULL,
              `FechaCreacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Segmentos_Log_1_idx` (`BNF2_Segmentos_id` ASC),
              INDEX `fk_BNF2_Segmentos_Log_2_idx` (`BNF2_Campania_id` ASC),
              CONSTRAINT `fk_BNF2_Segmentos_Log_1`
                FOREIGN KEY (`BNF2_Segmentos_id`)
                REFERENCES `BNF2_Segmentos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Segmentos_Log_2`
                FOREIGN KEY (`BNF2_Campania_id`)
                REFERENCES `BNF2_Campanias` (`id`)
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
