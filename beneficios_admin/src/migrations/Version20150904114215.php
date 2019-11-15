<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150904114215 extends AbstractMigration
{
    public static $description = "Alter BNF_Paquete Table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Paquete`
        CHANGE COLUMN `CantidadDescargas` `CantidadDescargas` INT NULL DEFAULT NULL COMMENT '' ,
        CHANGE COLUMN `Bonificacion` `Bonificacion` INT NULL DEFAULT NULL COMMENT '' ,
        CHANGE COLUMN `NumeroDias` `NumeroDias` INT NULL DEFAULT NULL COMMENT '' ,
        CHANGE COLUMN `Bolsa` `Bolsa` INT NULL DEFAULT NULL COMMENT '' ,
        CHANGE COLUMN `FechaCreacion` `FechaCreacion` DATETIME NULL DEFAULT NULL COMMENT '' ,
        CHANGE COLUMN `FechaActualizacion` `FechaActualizacion` DATETIME NULL DEFAULT NULL COMMENT '';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
