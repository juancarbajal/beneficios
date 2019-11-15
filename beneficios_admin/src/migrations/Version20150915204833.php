<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150915204833 extends AbstractMigration
{
    public static $description = "Add fields [NombrePaquete,TipoPaquete] BNF_PaqueteEmpresaProveedor table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE  `BNF_PaqueteEmpresaProveedor`
                      ADD  `NombrePaquete` VARCHAR( 255 ) NOT NULL AFTER  `BNF_Usuario_id` ,
                      ADD  `TipoPaquete` VARCHAR( 255 ) NOT NULL AFTER  `NombrePaquete` ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
