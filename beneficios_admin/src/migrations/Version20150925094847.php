<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150925094847 extends AbstractMigration
{
    public static $description = "Update field RepresentanteNumeroDocumento BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE  `BNF_Empresa`
            CHANGE  `RepresentanteNumeroDocumento`  `RepresentanteNumeroDocumento`
            VARCHAR( 15 ) NULL DEFAULT NULL COMMENT 'documento del representante legal';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
