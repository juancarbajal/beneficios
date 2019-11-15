<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151208111334 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_Layout table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Layout`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NULL DEFAULT NULL COMMENT '' ;

            UPDATE `BNF_Layout`
            SET `BNF_Layout`.Eliminado = case `BNF_Layout`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
