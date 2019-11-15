<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175035 extends AbstractMigration
{
    public static $description = "Create BNF_Accion table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Accion` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Grupo_id` INT NOT NULL COMMENT '',
          `Nombre` VARCHAR(255) NULL COMMENT '',
          `Descripciom` VARCHAR(255) NULL COMMENT '',
          `FechaCreacion` DATETIME NULL COMMENT '',
          `FechaActualizacion` DATETIME NULL COMMENT '',
          `Eliminado` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_Accion_BNF_Grupo1_idx` (`BNF_Grupo_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_Accion_BNF_Grupo1`
            FOREIGN KEY (`BNF_Grupo_id`)
            REFERENCES `BNF_Grupo` (`id`)
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
