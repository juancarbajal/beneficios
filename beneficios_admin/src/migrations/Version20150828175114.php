<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175114 extends AbstractMigration
{
    public static $description = "Create BNF_OfertaSubgrupo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_OfertaSubgrupo` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Oferta_id` INT NOT NULL COMMENT '',
          `BNF_Subgrupo_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_OfertaSubgrupo_BNF_Oferta1_idx` (`BNF_Oferta_id` ASC)  COMMENT '',
          INDEX `fk_BNF_OfertaSubgrupo_BNF_Subgrupo1_idx` (`BNF_Subgrupo_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_OfertaSubgrupo_BNF_Oferta1`
            FOREIGN KEY (`BNF_Oferta_id`)
            REFERENCES `BNF_Oferta` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_OfertaSubgrupo_BNF_Subgrupo1`
            FOREIGN KEY (`BNF_Subgrupo_id`)
            REFERENCES `BNF_Subgrupo` (`id`)
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
