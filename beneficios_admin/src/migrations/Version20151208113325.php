<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151208113325 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_OfertaCategoriaUbigeo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_OfertaCategoriaUbigeo`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NULL DEFAULT NULL COMMENT '' ;

            UPDATE `BNF_OfertaCategoriaUbigeo`
            SET `BNF_OfertaCategoriaUbigeo`.Eliminado = case `BNF_OfertaCategoriaUbigeo`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
