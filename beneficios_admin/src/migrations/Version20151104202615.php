<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151104202615 extends AbstractMigration
{
    public static $description = "Create BNF_Formulario table";

    public function up(MetadataInterface $schema)
    {
         $this->addSql(
             "CREATE TABLE IF NOT EXISTS `BNF_Formulario` (
                  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
                  `Descripcion` VARCHAR(255) NOT NULL COMMENT '',
                  `Eliminado` ENUM('0', '1') NOT NULL DEFAULT '0' COMMENT '',
                  PRIMARY KEY (`id`)  COMMENT '')
                ENGINE = InnoDB"
         );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
