<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175017 extends AbstractMigration
{
    public static $description = "Create BNF_Paquete table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Paquete` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_TipoPaquete_id` INT NOT NULL COMMENT '',
          `BNF_Usuario_id` INT NOT NULL COMMENT '',
          `Nombre` VARCHAR(255) NOT NULL COMMENT 'nombre del paquete',
          `Precio` DECIMAL(10,2) NULL COMMENT 'Precio obligatorio para descarga y presencia',
          `CantidadDescargas` VARCHAR(45) NULL COMMENT '',
          `PrecioUnitarioDescarga` DECIMAL(10,2) NULL COMMENT '',
          `Bonificacion` VARCHAR(45) NULL COMMENT '',
          `PrecioUnitarioBonificacion` DECIMAL(10,2) NULL COMMENT '',
          `CostoPorLead` VARCHAR(45) NULL COMMENT '',
          `NumeroDias` VARCHAR(45) NULL COMMENT '',
          `CostoDia` DECIMAL(10,2) NULL COMMENT 'Obligatorio si es de tipo presencia',
          `Bolsa` VARCHAR(45) NULL COMMENT '',
          `FechaCreacion` VARCHAR(45) NULL COMMENT '',
          `FechaActualizacion` VARCHAR(45) NULL COMMENT '',
          `Eliminado` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_Paquete_BNF_Usuario1_idx` (`BNF_Usuario_id` ASC)  COMMENT '',
          INDEX `fk_BNF_Paquete_BNF_TipoPaquete1_idx` (`BNF_TipoPaquete_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_Paquete_BNF_Usuario1`
            FOREIGN KEY (`BNF_Usuario_id`)
            REFERENCES `BNF_Usuario` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Paquete_BNF_TipoPaquete1`
            FOREIGN KEY (`BNF_TipoPaquete_id`)
            REFERENCES `BNF_TipoPaquete` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION)
        ENGINE = InnoDB;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
