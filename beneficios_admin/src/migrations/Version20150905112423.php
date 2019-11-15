<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150905112423 extends AbstractMigration
{
    public static $description = "Add field ClaseEmpresaCliente BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Empresa`
          ADD COLUMN `ClaseEmpresaCliente` ENUM('Normal', 'Especial') NULL COMMENT '' AFTER `Eliminado`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
