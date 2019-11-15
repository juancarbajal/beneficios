<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926111546 extends AbstractMigration
{
    public static $description = "Add fields[FechaCreacion,FechaActualizacion,Eliminado] BNF_LayoutCategoria table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_LayoutCategoria`
            ADD COLUMN `FechaCreacion` DATETIME NULL COMMENT '' AFTER `Index`,
            ADD COLUMN `FechaActualizacion` DATETIME NULL COMMENT '' AFTER `FechaCreacion`,
            ADD COLUMN `Eliminado` ENUM('0', '1') NULL COMMENT '' AFTER `FechaActualizacion`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
