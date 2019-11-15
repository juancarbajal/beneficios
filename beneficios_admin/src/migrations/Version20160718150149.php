<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150149 extends AbstractMigration
{
    public static $description = "Alter BNF2_Oferta_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Oferta_Puntos` 
            CHANGE COLUMN `Slug` `Slug` VARCHAR(255) NOT NULL ,
            ADD UNIQUE INDEX `Slug_UNIQUE` (`Slug` ASC);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
