<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151022174719 extends AbstractMigration
{
    public static $description = "Alter BNF_Banners table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Banners`
            DROP COLUMN `Posicion`,
            DROP COLUMN `Imagen`,
            CHANGE COLUMN `FechaPublicacionInicio` `FechaCreacion` DATETIME NULL DEFAULT NULL COMMENT '' ,
            CHANGE COLUMN `FechaPublicacionFin` `FechaActualizacion` DATETIME NULL DEFAULT NULL COMMENT '' ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
