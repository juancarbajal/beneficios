<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151016211041 extends AbstractMigration
{
    public static $description = "Alter BNF_Banner table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Banners`
            ADD COLUMN `Nombre` VARCHAR(100) NOT NULL COMMENT '' AFTER `id`,
            ADD COLUMN `FechaPublicacionInicio` DATETIME NULL COMMENT '' AFTER `Posicion`,
            ADD COLUMN `FechaPublicacionFin` DATETIME NULL COMMENT '' AFTER `FechaPublicacionInicio`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
