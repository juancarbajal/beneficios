<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150904121254 extends AbstractMigration
{
    public static $description = "Create EmpresaClienteCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_EmpresaClienteCliente` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Empresa_id` INT NOT NULL COMMENT '',
          `BNF_Cliente_id` INT NOT NULL COMMENT '',
          `Estado` ENUM('Activo', 'Inactivo') NULL COMMENT '',
          `Eliminado` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_EmpresaClienteCliente_BNF_Empres1_idx` (`BNF_Empresa_id` ASC)  COMMENT '',
          INDEX `fk_EmpresaClienteCliente_BNF_Cliente1_idx` (`BNF_Cliente_id` ASC)  COMMENT '',
          CONSTRAINT `fk_EmpresaClienteCliente_BNF_Empres1`
            FOREIGN KEY (`BNF_Empresa_id`)
            REFERENCES `BNF_Empresa` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_EmpresaClienteCliente_BNF_Cliente1`
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
