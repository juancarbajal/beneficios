<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151207205249 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_Banners table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Banners`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NOT NULL COMMENT '' ;

            UPDATE `BNF_Banners`
            SET `BNF_Banners`.Eliminado = case `BNF_Banners`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
