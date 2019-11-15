<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170214172439 extends AbstractMigration
{
    public static $description = "Alter BNF_Cliente_Landing table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Cliente_Landing` 
          ADD COLUMN `NombreEspecialista` VARCHAR(80) NULL DEFAULT NULL AFTER `Email`");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
