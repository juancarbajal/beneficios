<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175037 extends AbstractMigration
{
    public static $description = "Create BNF_Segmento table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Segmento` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `Nombre` VARCHAR(255) NOT NULL COMMENT '\nnombre del segmento',
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
