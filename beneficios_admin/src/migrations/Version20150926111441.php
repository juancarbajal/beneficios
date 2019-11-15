<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926111441 extends AbstractMigration
{
    public static $description = "Add fields[Index,FechaCreacion,FechaActualizacion,Eliminado] BNF_LayoutCampania table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_LayoutCampania`
            ADD COLUMN `Index` INT NULL COMMENT '' AFTER `BNF_Layout_id`,
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
