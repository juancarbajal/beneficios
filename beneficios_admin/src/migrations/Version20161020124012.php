<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161020124012 extends AbstractMigration
{
    public static $description = "Alter BNF_Oferta_Atributos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Oferta_Atributos` 
            ADD COLUMN `DatoBeneficio` VARCHAR(255) NOT NULL AFTER `NombreAtributo`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
