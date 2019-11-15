<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918202302 extends AbstractMigration
{
    public static $description = "Update field ClaseEmpresaCliente BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE  `BNF_Empresa` CHANGE  `ClaseEmpresaCliente`  `ClaseEmpresaCliente`
        ENUM(  'Especial',  'Normal' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
