<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150930105457 extends AbstractMigration
{
    public static $description = "update field TipoPaquete BNF_PaqueteEmpresaProveedor table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE  `BNF_PaqueteEmpresaProveedor` CHANGE  `TipoPaquete`  `BNF_TipoPaquete_id`
            INT( 11 ) NOT NULL ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
