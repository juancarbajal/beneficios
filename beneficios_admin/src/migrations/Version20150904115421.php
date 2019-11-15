<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150904115421 extends AbstractMigration
{
    public static $description = "Alter BNF_PaqueteEmpresaProveedor table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_PaqueteEmpresaProveedor`
            ADD COLUMN `BNF_Usuario_id` INT NOT NULL COMMENT '' AFTER `BNF_Paquete_id`,
            ADD COLUMN `Precio` DECIMAL(10,2) NULL COMMENT '' AFTER `Factura`,
            ADD COLUMN `CantidadDescargas` INT NULL COMMENT '' AFTER `Precio`,
            ADD COLUMN `PrecioUnitarioDescarga` DECIMAL(10,2) NULL COMMENT '' AFTER `CantidadDescargas`,
            ADD COLUMN `Bonificacion` INT NULL COMMENT '' AFTER `PrecioUnitarioDescarga`,
            ADD COLUMN `PrecioUnitarioBonificacion` DECIMAL(10,2) NULL COMMENT '' AFTER `Bonificacion`,
            ADD COLUMN `NumeroDias` INT NULL COMMENT '' AFTER `PrecioUnitarioBonificacion`,
            ADD COLUMN `CostoDia` DECIMAL(10,2) NULL COMMENT '' AFTER `NumeroDias`,
            ADD COLUMN `Bolsa` INT NULL COMMENT '' AFTER `CostoDia`,
            ADD COLUMN `FechaCreacion` DATETIME NULL COMMENT '' AFTER `Bolsa`,
            ADD COLUMN `FechaActualizacion` DATETIME NULL COMMENT '' AFTER `FechaCreacion`,
            ADD COLUMN `Eliminado` VARCHAR(45) NULL COMMENT '' AFTER `FechaActualizacion`,
            ADD INDEX `fk_BNF_PaqueteEmpresaProveedor_BNF_Usuario1_idx` (`BNF_Usuario_id` ASC)  COMMENT '';
            ALTER TABLE `BNF_PaqueteEmpresaProveedor`
            ADD CONSTRAINT `fk_BNF_PaqueteEmpresaProveedor_BNF_Usuario1`
              FOREIGN KEY (`BNF_Usuario_id`)
              REFERENCES `BNF_Usuario` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
