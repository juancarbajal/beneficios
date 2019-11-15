<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716100112 extends AbstractMigration
{
    public static $description = "Alter BNF2_Demanda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Demanda` 
            CHANGE COLUMN `PrecioMinimo` `PrecioMinimo` INT NULL DEFAULT NULL ,
            CHANGE COLUMN `PrecioMaximo` `PrecioMaximo` INT NULL DEFAULT NULL ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
