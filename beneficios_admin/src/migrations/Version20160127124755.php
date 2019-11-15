<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160127124755 extends AbstractMigration
{
    public static $description = "Alter BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Oferta`
            ADD COLUMN `CondicionesDeliveryTexto` VARCHAR(255) NULL DEFAULT 'Condiciones' AFTER `CondicionesDelivery`,
            ADD COLUMN `CondicionesDeliveryEstado` ENUM('0', '1') NULL DEFAULT '1' AFTER `CondicionesDeliveryTexto`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
