<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150158 extends AbstractMigration
{
    public static $description = "Drop BNF2_Asignacion_Puntos_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("DROP TABLE `BNF2_Asignacion_Puntos_Log`;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
