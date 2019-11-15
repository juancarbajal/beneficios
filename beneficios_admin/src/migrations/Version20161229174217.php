<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161229174217 extends AbstractMigration
{
    public static $description = "Alter BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Oferta` 
            CHANGE COLUMN `CondicionesDelivery` `CondicionesDelivery` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
            CHANGE COLUMN `CondicionesTebca` `CondicionesTebca` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
