<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161020124010 extends AbstractMigration
{
    public static $description = "Alter BNF_Busqueda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Busqueda` 
            CHANGE COLUMN `TipoOferta` `TipoOferta` TINYINT(1) NULL DEFAULT 0 ,
            ADD COLUMN `Empresa` TINYINT(1) NULL DEFAULT 0 AFTER `Descripcion`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
