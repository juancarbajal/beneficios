<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926111331 extends AbstractMigration
{
    public static $description = "Add fields[FechaCreacion,FechaActualizacion,Eliminado] BNF_Layout table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Layout`
            ADD COLUMN `FechaCreacion` DATETIME NULL COMMENT '' AFTER `imagen`,
            ADD COLUMN `FechaActualizacion` DATETIME NULL COMMENT '' AFTER `FechaCreacion`,
            ADD COLUMN `Eliminado` ENUM('0', '1') NULL COMMENT '' AFTER `FechaActualizacion`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
