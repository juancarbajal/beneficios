<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150148 extends AbstractMigration
{
    public static $description = "Alter BNF2_Asignacion_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Asignacion_Puntos` 
            ADD COLUMN `CantidadPuntosEliminados` INT NULL DEFAULT '0' AFTER `CantidadPuntosDisponibles`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
