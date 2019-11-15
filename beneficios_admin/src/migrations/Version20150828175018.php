<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175018 extends AbstractMigration
{
    public static $description = "Create BNF_TipoBeneficio table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_TipoBeneficio` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `NombreBeneficio` VARCHAR(255) NOT NULL COMMENT 'Descuento porcentual\nDescuento en efectivo\nNxN\notros',
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
