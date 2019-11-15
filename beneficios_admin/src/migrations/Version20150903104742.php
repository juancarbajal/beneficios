<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150903104742 extends AbstractMigration
{
    public static $description = "Create BNF_PaquetePais table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_PaquetePais` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Paquete_id` INT NOT NULL COMMENT '',
          `BNF_Pais_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_PaquetePais_BNF_Paquete1_idx` (`BNF_Paquete_id` ASC)  COMMENT '',
          INDEX `fk_BNF_PaquetePais_BNF_Pais1_idx` (`BNF_Pais_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_PaquetePais_BNF_Paquete1`
            FOREIGN KEY (`BNF_Paquete_id`)
            REFERENCES `BNF_Paquete` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_PaquetePais_BNF_Pais1`
            FOREIGN KEY (`BNF_Pais_id`)
            REFERENCES `BNF_Pais` (`id`)
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
