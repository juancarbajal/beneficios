<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918153342 extends AbstractMigration
{
    public static $description = "Alter BNF_Cupon table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Cupon`
            DROP FOREIGN KEY `fk_BNF_Cupon_BNF_OfertaEmpresaCliente1`;
            ALTER TABLE `BNF_Cupon`
            CHANGE COLUMN `BNF_OfertaEmpresaCliente_id` `BNF_OfertaEmpresaCliente_id` INT(11) NULL COMMENT '' ,
            CHANGE COLUMN `EstadoCupon` `EstadoCupon` ENUM('Creado', 'Eliminado', 'Generado', 'Redimido')
            NOT NULL COMMENT '' ,
            ADD COLUMN `BNF_Oferta_id` INT NOT NULL COMMENT '' AFTER `EstadoCupon`,
            ADD INDEX `fk_BNF_Cupon_BNF_Oferta1_idx` (`BNF_Oferta_id` ASC)  COMMENT '';
            ALTER TABLE `BNF_Cupon`
            ADD CONSTRAINT `fk_BNF_Cupon_BNF_OfertaEmpresaCliente1`
              FOREIGN KEY (`BNF_OfertaEmpresaCliente_id`)
              REFERENCES `BNF_OfertaEmpresaCliente` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            ADD CONSTRAINT `fk_BNF_Cupon_BNF_Oferta1`
              FOREIGN KEY (`BNF_Oferta_id`)
              REFERENCES `BNF_Oferta` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;
            ALTER TABLE `BNF_Cupon`
            ADD COLUMN `FechaCreacion` DATETIME NULL COMMENT '' AFTER `BNF_Oferta_id`,
            ADD COLUMN `FechaGenerado` DATETIME NULL COMMENT '' AFTER `FechaCreacion`,
            ADD COLUMN `FechaRedimido` DATETIME NULL COMMENT '' AFTER `FechaGenerado`,
            ADD COLUMN `FechaEliminado` DATETIME NULL COMMENT '' AFTER `FechaRedimido`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
