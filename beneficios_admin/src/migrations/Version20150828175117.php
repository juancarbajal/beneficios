<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175117 extends AbstractMigration
{
    public static $description = "Create BNF_CategoriaUbigeo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_CategoriaUbigeo` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Categoria_id` INT NOT NULL COMMENT '',
          `BNF_Ubigeo_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_CategoriaPais_BNF_Categoria1_idx` (`BNF_Categoria_id` ASC)  COMMENT '',
          INDEX `fk_BNF_CategoriaUbigeo_BNF_Ubigeo1_idx` (`BNF_Ubigeo_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_CategoriaPais_BNF_Categoria1`
            FOREIGN KEY (`BNF_Categoria_id`)
            REFERENCES `BNF_Categoria` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_CategoriaUbigeo_BNF_Ubigeo1`
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
