<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918153340 extends AbstractMigration
{
    public static $description = "Create BNF_BolsaTotal table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_BolsaTotal` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_TipoPaquete_id` INT NOT NULL COMMENT '',
          `BNF_Empresa_id` INT NOT NULL COMMENT '',
          `BolsaActual` INT NOT NULL DEFAULT 0 COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_BolsaTotal_TipoPaquete_idx` (`BNF_TipoPaquete_id` ASC)  COMMENT '',
          INDEX `fk_BNF_BolsaTotal_Empresa_idx` (`BNF_Empresa_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_BolsaTotal_TipoPaquete`
            FOREIGN KEY (`BNF_TipoPaquete_id`)
            REFERENCES `BNF_TipoPaquete` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_BolsaTotal_Empresa`
            FOREIGN KEY (`BNF_Empresa_id`)
            REFERENCES `BNF_Empresa` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
