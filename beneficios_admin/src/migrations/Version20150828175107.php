<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175107 extends AbstractMigration
{
    public static $description = "Create BNF_OfertaRubro table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_OfertaRubro` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Oferta_id` INT NOT NULL COMMENT '',
          `BNF_Rubro_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_OfertaRubro_BNF_Oferta1_idx` (`BNF_Oferta_id` ASC)  COMMENT '',
          INDEX `fk_BNF_OfertaRubro_BNF_Rubro1_idx` (`BNF_Rubro_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_OfertaRubro_BNF_Oferta1`
            FOREIGN KEY (`BNF_Oferta_id`)
            REFERENCES `BNF_Oferta` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_OfertaRubro_BNF_Rubro1`
            FOREIGN KEY (`BNF_Rubro_id`)
            REFERENCES `BNF_Rubro` (`id`)
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
