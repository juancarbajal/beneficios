<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926113113 extends AbstractMigration
{
    public static $description = "Update field Eliminado BNF_PaqueteEmpresaProveedor table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_PaqueteEmpresaProveedor`
            CHANGE COLUMN `Eliminado` `Eliminado` ENUM('0', '1') NULL DEFAULT NULL COMMENT '' ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
