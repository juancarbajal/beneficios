<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150903104741 extends AbstractMigration
{
    public static $description = "Create BNF_EmpresaSubgrupoCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_EmpresaSubgrupoCliente` (
          `idBNF_EmpresaSubgrupoCliente` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_EmpresaSubgrupo_id` INT NOT NULL COMMENT '',
          `BNF_Cliente_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`idBNF_EmpresaSubgrupoCliente`)  COMMENT '',
          INDEX `fk_BNF_EmpresaSubgrupoCliente_BNF_EmpresaSubgrupo1_idx` (`BNF_EmpresaSubgrupo_id` ASC)  COMMENT '',
          INDEX `fk_BNF_EmpresaSubgrupoCliente_BNF_Cliente1_idx` (`BNF_Cliente_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_EmpresaSubgrupoCliente_BNF_EmpresaSubgrupo1`
            FOREIGN KEY (`BNF_EmpresaSubgrupo_id`)
            REFERENCES `BNF_EmpresaSubgrupo` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_EmpresaSubgrupoCliente_BNF_Cliente1`
            FOREIGN KEY (`BNF_Cliente_id`)
            REFERENCES `BNF_Cliente` (`id`)
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
