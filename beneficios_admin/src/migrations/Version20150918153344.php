<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918153344 extends AbstractMigration
{
    public static $description = "Add fields[FechaCreacion,FechaActualizacion] BNF_OfertaEmpresaCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_OfertaEmpresaCliente`
            ADD COLUMN `FechaCreacion` DATETIME NULL COMMENT '' AFTER `NumeroCupones`,
            ADD COLUMN `FechaActualizacion` DATETIME NULL COMMENT '' AFTER `FechaCreacion`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
