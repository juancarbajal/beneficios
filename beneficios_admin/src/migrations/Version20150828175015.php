<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175015 extends AbstractMigration
{
    public static $description = "Create BNF_Usuario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Usuario` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_TipoUsuario_id` INT NOT NULL COMMENT '',
          `BNF_TipoDocumento_id` INT NOT NULL COMMENT '',
          `Nombres` VARCHAR(255) NULL COMMENT '',
          `Apellidos` VARCHAR(255) NULL COMMENT '',
          `NombreUsuario` VARCHAR(255) NULL COMMENT '',
          `Contrasenia` VARCHAR(45) NULL COMMENT '',
          `NumeroDocumento` VARCHAR(45) NOT NULL COMMENT '',
          `Correo` VARCHAR(45) NOT NULL COMMENT '',
          `FechaCreacion` DATETIME NULL COMMENT '',
          `FechaActualizacion` DATETIME NULL COMMENT '',
          `FechaUltimoAcceso` DATETIME NULL COMMENT '',
          `Eliminado` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_Usuario_BNF_TipoUsuario1_idx` (`BNF_TipoUsuario_id` ASC)  COMMENT '',
          INDEX `fk_BNF_Usuario_BNF_TipoDocumento1_idx` (`BNF_TipoDocumento_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_Usuario_BNF_TipoUsuario1`
            FOREIGN KEY (`BNF_TipoUsuario_id`)
            REFERENCES `BNF_TipoUsuario` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Usuario_BNF_TipoDocumento1`
            FOREIGN KEY (`BNF_TipoDocumento_id`)
            REFERENCES `BNF_TipoDocumento` (`id`)
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
