<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170213190853 extends AbstractMigration
{
    public static $description = "Alter BNF2_Asignacion_Puntos";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF2_Asignacion_Puntos` 
ADD COLUMN `TipoAsignamiento` ENUM('Normal', 'Referido') NULL DEFAULT 'Normal' AFTER `BNF_Cliente_id`;
");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
