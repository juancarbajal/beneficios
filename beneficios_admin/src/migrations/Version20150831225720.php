<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150831225720 extends AbstractMigration
{
    public static $description = "Seed BNF_TipoDocumento";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO BNF_TipoDocumento (Nombre, FechaCreacion, Eliminado)
                      VALUES  ('DNI', CURRENT_TIMESTAMP, 0),
                              ('Pasaporte', CURRENT_TIMESTAMP, 0);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
