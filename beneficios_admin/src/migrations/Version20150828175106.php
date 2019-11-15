<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175106 extends AbstractMigration
{
    public static $description = "Create BNF_OfertaSegmento table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_OfertaSegmento` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Oferta_id` INT NOT NULL COMMENT '',
          `BNF_Segmento_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_OfertaSegmento_BNF_Oferta1_idx` (`BNF_Oferta_id` ASC)  COMMENT '',
          INDEX `fk_BNF_OfertaSegmento_BNF_Segmento1_idx` (`BNF_Segmento_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_OfertaSegmento_BNF_Oferta1`
            FOREIGN KEY (`BNF_Oferta_id`)
            REFERENCES `BNF_Oferta` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_OfertaSegmento_BNF_Segmento1`
            FOREIGN KEY (`BNF_Segmento_id`)
            REFERENCES `BNF_Segmento` (`id`)
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
