<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175108 extends AbstractMigration
{
    public static $description = "Create BNF_PaqueteEmpresaProveedor table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_PaqueteEmpresaProveedor` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Empresa_id` INT NOT NULL COMMENT '',
          `BNF_Paquete_id` INT NOT NULL COMMENT '',
          `FechaCompra` DATETIME NOT NULL COMMENT 'fecha de compra del paquete',
          `DiasComprados` INT NULL COMMENT '',
          `CostoPorLead` DECIMAL(10,2) NULL COMMENT
          'Costo unitario del lead, Si el paquete comprado es de tipo Lead, obligatorio',
          `MaximoLeads` INT NULL COMMENT 'obligatorio si el paquete comprado es de tipo lead',
          `Factura` VARCHAR(255) NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_PaqueteEmpresaProveedor_BNF_Empresa1_idx` (`BNF_Empresa_id` ASC)  COMMENT '',
          INDEX `fk_BNF_PaqueteEmpresaProveedor_BNF_Paquete1_idx` (`BNF_Paquete_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_PaqueteEmpresaProveedor_BNF_Empresa1`
            FOREIGN KEY (`BNF_Empresa_id`)
            REFERENCES `BNF_Empresa` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_PaqueteEmpresaProveedor_BNF_Paquete1`
            FOREIGN KEY (`BNF_Paquete_id`)
            REFERENCES `BNF_Paquete` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION) ENGINE = InnoDB;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
