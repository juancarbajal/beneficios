<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175038 extends AbstractMigration
{
    public static $description = "Create BNF_Subgrupo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Subgrupo` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `Nombre` VARCHAR(255) NOT NULL COMMENT '\nnombre del subgrupo',
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
