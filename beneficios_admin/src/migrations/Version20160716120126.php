<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716120126 extends AbstractMigration
{
    public static $description = "Create BNF2_Asignacion_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Asignacion_Puntos` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Segmento_id` INT NOT NULL,
              `BNF_Cliente_id` INT NOT NULL,
              `CantidadPuntos` INT NOT NULL,
              `Eliminado` TINYINT(1) NOT NULL,
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Asignacion_Puntos_1_idx` (`BNF2_Segmento_id` ASC),
              INDEX `fk_BNF2_Asignacion_Puntos_2_idx` (`BNF_Cliente_id` ASC),
              CONSTRAINT `fk_BNF2_Asignacion_Puntos_1`
                FOREIGN KEY (`BNF2_Segmento_id`)
                REFERENCES `BNF2_Segmentos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Asignacion_Puntos_2`
                FOREIGN KEY (`BNF_Cliente_id`)
                REFERENCES `BNF_Cliente` (`id`)
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
