<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190757 extends AbstractMigration
{
    public static $description = "Alter Eliminado BNF_Usuario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Usuario` CHANGE COLUMN `Eliminado` `Eliminado` INT(11) DEFAULT 0;
              UPDATE `BNF_Usuario` SET `Eliminado` = 0 WHERE `Eliminado` IS NULL;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
