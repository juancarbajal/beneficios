<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160412115656 extends AbstractMigration
{
    public static $description = "Update ColorColumn BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_Empresa` SET `Color` = '#3C8DBC' WHERE `Color` IS NULL;
             UPDATE `BNF_Empresa` SET `Color_menu` = '#0A0D12' WHERE `Color_menu` IS NULL;
             UPDATE `BNF_Empresa` SET `Color_hover` = '#400090' WHERE `Color_hover` IS NULL;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
