<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175116 extends AbstractMigration
{
    public static $description = "Create BNF_CampaniaUbigeo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_CampaniaUbigeo` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Campanias_id` INT NOT NULL COMMENT '',
          `BNF_Ubigeo_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_CampaniaPais_BNF_Campanias1_idx` (`BNF_Campanias_id` ASC)  COMMENT '',
          INDEX `fk_BNF_CampaniaUbigeo_BNF_Ubigeo1_idx` (`BNF_Ubigeo_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_CampaniaPais_BNF_Campanias1`
            FOREIGN KEY (`BNF_Campanias_id`)
            REFERENCES `BNF_Campanias` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_CampaniaUbigeo_BNF_Ubigeo1`
            FOREIGN KEY (`BNF_Ubigeo_id`)
            REFERENCES `BNF_Ubigeo` (`id`)
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
