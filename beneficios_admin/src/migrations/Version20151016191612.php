<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151016191612 extends AbstractMigration
{
    public static $description = "Alter BNF_Banners table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Banners`
              CHANGE COLUMN `Descripcion` `Descripcion` VARCHAR(100) NULL COMMENT '' ,
              ADD COLUMN `Posicion` INT NOT NULL COMMENT '' AFTER `Descripcion`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
