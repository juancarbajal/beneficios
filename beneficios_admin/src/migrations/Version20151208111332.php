<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151208111332 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_Galeria table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Galeria`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NOT NULL COMMENT '' ;

            UPDATE `BNF_Galeria`
            SET `BNF_Galeria`.Eliminado = case `BNF_Galeria`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
