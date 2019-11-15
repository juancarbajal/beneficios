<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150905112422 extends AbstractMigration
{
    public static $description = "Alter BNF_EmpresaSubgrupoCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_EmpresaSubgrupoCliente`
            CHANGE COLUMN `BNF_Cliente_id` `BNF_Cliente_id` INT(11)
            NOT NULL COMMENT '' AFTER `idBNF_EmpresaSubgrupoCliente`,
            CHANGE COLUMN `BNF_EmpresaSubgrupo_id` `BNF_Subgrupo_id` INT(11) NOT NULL COMMENT '' ,
            ADD INDEX `fk_BNF_EmpresaSubgrupoCliente_BNF_Segmento1_idx` (`BNF_Subgrupo_id` ASC)  COMMENT '';
            ALTER TABLE `BNF_EmpresaSubgrupoCliente`
            ADD CONSTRAINT `fk_BNF_EmpresaSubgrupoCliente_BNF_Segmento1`
              FOREIGN KEY (`BNF_Subgrupo_id`)
              REFERENCES `BNF_Segmento` (`id`)
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
