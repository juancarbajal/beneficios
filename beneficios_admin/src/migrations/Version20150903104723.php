<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150903104723 extends AbstractMigration
{
    public static $description = "Create BNF_ClienteCorreo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_ClienteCorreo` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `Correo` VARCHAR(255) NOT NULL COMMENT '',
          `BNF_Cliente_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_ClienteCorreo_BNF_Cliente1_idx` (`BNF_Cliente_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_ClienteCorreo_BNF_Cliente1`
            FOREIGN KEY (`BNF_Cliente_id`)
            REFERENCES `BNF_Cliente` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION)
        ENGINE = InnoDB;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
