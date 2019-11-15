<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160412115004 extends AbstractMigration
{
    public static $description = "Update Color BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Empresa` 
            CHANGE COLUMN `Color` `Color` VARCHAR(7) NULL DEFAULT '#3C8DBC' ,
            CHANGE COLUMN `Color_menu` `Color_menu` VARCHAR(7) NULL DEFAULT '#0A0D12' ,
            CHANGE COLUMN `Color_hover` `Color_hover` VARCHAR(7) NULL DEFAULT '#400090' ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
