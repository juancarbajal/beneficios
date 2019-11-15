<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150831225725 extends AbstractMigration
{
    public static $description = "Seed BNF_TipoUsuario";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_TipoUsuario` ( `Nombre`, `FechaCreacion`, `Eliminado`)
                      VALUES( 'Asesor', CURRENT_TIMESTAMP, 0),
                      ( 'Administrador',  CURRENT_TIMESTAMP, 0),
                      ( 'Proveedor', CURRENT_TIMESTAMP, 0);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
