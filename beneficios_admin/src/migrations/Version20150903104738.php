<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150903104738 extends AbstractMigration
{
    public static $description = "Create BNF_LayoutCategoria table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_LayoutCategoria` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Layout_id` INT NOT NULL COMMENT '',
          `BNF_Categoria_id` INT NOT NULL COMMENT '',
          `Index` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_LayoutCategoria_BNF_Layout1_idx` (`BNF_Layout_id` ASC)  COMMENT '',
          INDEX `fk_BNF_LayoutCategoria_BNF_Categoria1_idx` (`BNF_Categoria_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_LayoutCategoria_BNF_Layout1`
            FOREIGN KEY (`BNF_Layout_id`)
            REFERENCES `BNF_Layout` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_LayoutCategoria_BNF_Categoria1`
            FOREIGN KEY (`BNF_Categoria_id`)
            REFERENCES `BNF_Categoria` (`id`)
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
