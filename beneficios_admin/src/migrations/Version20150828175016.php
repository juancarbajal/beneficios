<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175016 extends AbstractMigration
{
    public static $description = "Create BNF_TipoPaquete table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_TipoPaquete` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '\npaquetes de tipo: presencia, descarga y lead',
          `NombreTipoPaquete` VARCHAR(255) NULL COMMENT '',
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
