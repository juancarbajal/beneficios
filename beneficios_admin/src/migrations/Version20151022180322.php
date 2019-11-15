<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151022180322 extends AbstractMigration
{
    public static $description = "Seed BNF_Banners";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Banners` (`id`,`Nombre`,`Descripcion`,`FechaCreacion`,`FechaActualizacion`,`Eliminado`)
            VALUES (null,'Banner Principal','',NOW(),null,'0');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
