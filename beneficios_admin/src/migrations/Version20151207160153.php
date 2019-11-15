<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151207160153 extends AbstractMigration
{
    public static $description = "Add fields BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Empresa`
            ADD COLUMN `Proveedor` BIT(1) NOT NULL DEFAULT 0 COMMENT '' AFTER `HoraAtencionFinContacto`,
            ADD COLUMN `Cliente` BIT(1) NOT NULL DEFAULT 0 COMMENT '' AFTER `Proveedor`,
            ADD INDEX `Proveedor_index` (`Proveedor` ASC)  COMMENT '',
            ADD INDEX `Cliente_index` (`Cliente` ASC)  COMMENT '';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
