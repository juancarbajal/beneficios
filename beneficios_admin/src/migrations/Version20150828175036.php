<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175036 extends AbstractMigration
{
    public static $description = "Create BNF_Permiso table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Permiso` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_TipoUsuario_id` INT NOT NULL COMMENT '',
          `BNF_Accion_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_Permiso_BNF_TipoUsuario1_idx` (`BNF_TipoUsuario_id` ASC)  COMMENT '',
          INDEX `fk_BNF_Permiso_BNF_Accion1_idx` (`BNF_Accion_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_Permiso_BNF_TipoUsuario1`
            FOREIGN KEY (`BNF_TipoUsuario_id`)
            REFERENCES `BNF_TipoUsuario` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Permiso_BNF_Accion1`
            FOREIGN KEY (`BNF_Accion_id`)
            REFERENCES `BNF_Accion` (`id`)
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
