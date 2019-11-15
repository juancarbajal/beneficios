<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926112752 extends AbstractMigration
{
    public static $description = "Add fields[FechaCreacion,FechaActualizacion,Eliminado] BNF_Pais table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Pais`
            ADD COLUMN `FechaCreacion` DATETIME NULL COMMENT '' AFTER `NombrePais`,
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
