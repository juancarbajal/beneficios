<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151020101404 extends AbstractMigration
{
    public static $description = "Update field FechaInicioPublicacion BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE  `BNF_Oferta` CHANGE  `FechaInicioPublicacion`  `FechaInicioPublicacion` DATETIME
                      NOT NULL COMMENT  'Fecha en que puede salir publicada la oferta';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
