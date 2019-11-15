<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175109 extends AbstractMigration
{
    public static $description = "Create BNF_EmpresaSegmento table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_EmpresaSegmento` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Empresa_id` INT NOT NULL COMMENT '',
          `BNF_Segmento_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_EmpresaSegmento_BNF_Empresa1_idx` (`BNF_Empresa_id` ASC)  COMMENT '',
          INDEX `fk_BNF_EmpresaSegmento_BNF_Segmento1_idx` (`BNF_Segmento_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_EmpresaSegmento_BNF_Empresa1`
            FOREIGN KEY (`BNF_Empresa_id`)
            REFERENCES `BNF_Empresa` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_EmpresaSegmento_BNF_Segmento1`
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
