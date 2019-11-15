<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160129145928 extends AbstractMigration
{
    public static $description = "add fields [nombres,apellidos] BNF_DM_Met_Cliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_DM_Met_Cliente`
          ADD COLUMN `nombres` VARCHAR(255) NULL AFTER `DiasUltimoLogin`,
          ADD COLUMN `apellidos` VARCHAR(255) NULL AFTER `nombres`,
           ADD COLUMN `distrito_vive` VARCHAR(255) NULL AFTER `apellidos`,
            ADD COLUMN `distrito_trabaja` VARCHAR(255) NULL AFTER `distrito_vive`;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
