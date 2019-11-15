<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150906182309 extends AbstractMigration
{
    public static $description = "Update field Ruc BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Empresa`
          CHANGE COLUMN `Ruc` `Ruc` VARCHAR(11) NOT NULL COMMENT '\nruc de la empresa' ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
