<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151208111325 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_CategoriaUbigeo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_CategoriaUbigeo`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NULL DEFAULT NULL COMMENT '' ;

            UPDATE `BNF_CategoriaUbigeo`
            SET `BNF_CategoriaUbigeo`.Eliminado = case `BNF_CategoriaUbigeo`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
