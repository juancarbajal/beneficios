<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160124150838 extends AbstractMigration
{
    public static $description = "Seed BNF_Configuraciones";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Configuraciones`(`Campo`, `Atributo`, `FechaCreacion`)
              VALUES ('mensaje_confirmacion_lead','Su información ya fue enviada',NOW())"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
