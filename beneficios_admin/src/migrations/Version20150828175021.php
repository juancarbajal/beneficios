<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175021 extends AbstractMigration
{
    public static $description = "Create BNF_Pais table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Pais` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `NombrePais` VARCHAR(255) NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '')
        ENGINE = InnoDB;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
