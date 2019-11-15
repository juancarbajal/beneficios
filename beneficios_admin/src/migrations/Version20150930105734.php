<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150930105734 extends AbstractMigration
{
    public static $description = "update field BNF_TipoPaquete_id BNF_PaqueteEmpresaProveedor table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE  `BNF_PaqueteEmpresaProveedor` ADD INDEX (  `BNF_TipoPaquete_id` ) ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
