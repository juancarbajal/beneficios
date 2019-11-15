<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175023 extends AbstractMigration
{
    public static $description = "Create BNF_Ubigeo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Ubigeo` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `Nombre` VARCHAR(45) NOT NULL COMMENT '',
          `id_padre` INT NULL COMMENT '',
          `BNF_Pais_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_Ubigeo_BNF_Ubigeo1_idx` (`id_padre` ASC)  COMMENT '',
          INDEX `fk_BNF_Ubigeo_BNF_Pais1_idx` (`BNF_Pais_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_Ubigeo_BNF_Ubigeo1`
            FOREIGN KEY (`id_padre`)
            REFERENCES `BNF_Ubigeo` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Ubigeo_BNF_Pais1`
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
