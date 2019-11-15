<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160412134620 extends AbstractMigration
{
    public static $description = "Seed BNF_TipoUsuario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_TipoUsuario`(`Nombre`,`Descripcion`,`FechaCreacion`,`Eliminado`) 
              VALUES ('Cliente', 'cliente', NOW(), 0)"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
