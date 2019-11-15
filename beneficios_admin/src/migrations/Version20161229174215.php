<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161229174215 extends AbstractMigration
{
    public static $description = "Alter BNF2_Oferta_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Oferta_Puntos` 
            ADD COLUMN `CondicionesDelivery` TEXT NULL AFTER `Estado`,
            ADD COLUMN `CondicionesDeliveryTexto` VARCHAR(255) NULL AFTER `CondicionesDelivery`,
            ADD COLUMN `CondicionesDeliveryEstado` ENUM('0', '1') NULL AFTER `CondicionesDeliveryTexto`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
