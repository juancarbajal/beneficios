<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151023110610 extends AbstractMigration
{
    public static $description = "Alter BNF_Galeria table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Galeria`
            ADD COLUMN `Url` VARCHAR(100) NULL COMMENT '' AFTER `Imagen`,
            ADD COLUMN `FechaSubida` DATETIME NULL COMMENT '' AFTER `Url`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
