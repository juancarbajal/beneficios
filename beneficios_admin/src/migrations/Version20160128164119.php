<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160128164119 extends AbstractMigration
{
    public static $description = "Alter CondicionesDeliveryEstado BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Oferta`
            CHANGE COLUMN `CondicionesDeliveryEstado` `CondicionesDeliveryEstado` ENUM('0', '1') NULL DEFAULT '0' ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
