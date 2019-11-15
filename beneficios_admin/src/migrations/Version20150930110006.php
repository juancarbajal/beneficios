<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150930110006 extends AbstractMigration
{
    public static $description = "alter BNF_PaqueteEmpresaProveedor table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE  `BNF_PaqueteEmpresaProveedor`
            ADD CONSTRAINT  `fk_BNF_PaqueteEmpresaProveedor_BNF_TipoPaquete1`
            FOREIGN KEY (  `BNF_TipoPaquete_id` )
            REFERENCES  `BNF_TipoPaquete` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
