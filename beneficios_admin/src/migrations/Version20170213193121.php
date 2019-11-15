<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170213193121 extends AbstractMigration
{
    public static $description = "Alter  BNF2_Asignacion_Puntos_Estado_Log";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF2_Asignacion_Puntos_Estado_Log` 
ADD COLUMN `TipoAsignamiento` ENUM('Normal', 'Referido') NOT NULL DEFAULT 'Normal' AFTER `BNF_Cliente_id`;
");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
