<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170220191546 extends AbstractMigration
{
    public static $description = "ALTER BNF_EmpresaClienteCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_EmpresaClienteCliente` 
              CHANGE COLUMN `Estado` `Estado` ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo' ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
