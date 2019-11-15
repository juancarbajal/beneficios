<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170112161232 extends AbstractMigration
{
    public static $description = "Alter BNF2_Delivery_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Delivery_Puntos` 
            CHANGE COLUMN `Nombre_Campo` `Nombre_Campo` VARCHAR(50) NOT NULL ,
            ADD COLUMN `Etiqueta_Campo` VARCHAR(50) NULL AFTER `BNF2_Oferta_Puntos_id`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
