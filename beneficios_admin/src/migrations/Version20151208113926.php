<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151208113926 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_Segmento table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Segmento`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NULL DEFAULT NULL COMMENT '' ;

            UPDATE `BNF_Segmento`
            SET `BNF_Segmento`.Eliminado = case `BNF_Segmento`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
