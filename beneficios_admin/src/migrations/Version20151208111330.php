<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151208111330 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_EmpresaSubgrupoCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_EmpresaSubgrupoCliente`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NULL DEFAULT NULL COMMENT '' ;

            UPDATE `BNF_EmpresaSubgrupoCliente`
            SET `BNF_EmpresaSubgrupoCliente`.Eliminado = case `BNF_EmpresaSubgrupoCliente`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
