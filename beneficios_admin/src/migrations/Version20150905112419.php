<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150905112419 extends AbstractMigration
{
    public static $description = "Drop BNF_EmpresaSubgrupo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_EmpresaSubgrupoCliente`
            DROP FOREIGN KEY `fk_BNF_EmpresaSubgrupoCliente_BNF_EmpresaSubgrupo1`;
            ALTER TABLE `BNF_EmpresaSubgrupoCliente`
            DROP INDEX `fk_BNF_EmpresaSubgrupoCliente_BNF_EmpresaSubgrupo1_idx` ;

            DROP TABLE `BNF_EmpresaSubgrupo`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
