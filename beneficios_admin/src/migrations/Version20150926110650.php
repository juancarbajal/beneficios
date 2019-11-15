<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926110650 extends AbstractMigration
{
    public static $description = "Add field Eliminado BNF_EmpresaSegmentoCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_EmpresaSegmentoCliente`
            ADD COLUMN `Eliminado` ENUM('0', '1') NULL COMMENT '' AFTER `BNF_Cliente_id`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
