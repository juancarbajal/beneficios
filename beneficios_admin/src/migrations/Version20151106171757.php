<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151106171757 extends AbstractMigration
{
    public static $description = "Seed insert BNF_Formulario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Formulario`(`id`,`Descripcion`,`Posicion`,`Eliminado`)
              VALUES (null,'CorreoContacto',1,'0');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
