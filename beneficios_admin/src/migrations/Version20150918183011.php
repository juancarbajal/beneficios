<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918183011 extends AbstractMigration
{
    public static $description = "Update Seed BNF_TipoUsuario";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_TipoUsuario` SET `Nombre` = 'Finanzas' WHERE `id` = 3;
          INSERT INTO `BNF_TipoUsuario` (`Nombre`,`FechaCreacion`,`Eliminado`)
          VALUES ('Demanda',CURRENT_TIMESTAMP,0);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
