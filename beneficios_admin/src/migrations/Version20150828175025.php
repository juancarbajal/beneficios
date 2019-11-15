<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175025 extends AbstractMigration
{
    public static $description = "Create BNF_Cliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Cliente` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '\nUsuario final',
          `BNF_Empresa_id` INT NOT NULL COMMENT '\nId de la empresa Cliente y jala tipo empresa cliente',
          `BNF_TipoDocumento_id` INT NOT NULL COMMENT '',
          `Nombre` VARCHAR(255) NULL COMMENT '',
          `Apellido` VARCHAR(255) NULL COMMENT '',
          `NumeroDocumento` VARCHAR(45) NOT NULL COMMENT '',
          `Genero` VARCHAR(45) NULL COMMENT '',
          `FechaNacimiento` DATETIME NULL COMMENT '',
          `FechaCreacion` DATETIME NULL COMMENT '',
          `FechaActualizacion` DATETIME NULL COMMENT '',
          `Eliminado` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_Cliente_BNF_Empresa1_idx` (`BNF_Empresa_id` ASC)  COMMENT '',
          INDEX `fk_BNF_Cliente_BNF_TipoDocumento1_idx` (`BNF_TipoDocumento_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_Cliente_BNF_Empresa1`
            FOREIGN KEY (`BNF_Empresa_id`)
            REFERENCES `BNF_Empresa` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Cliente_BNF_TipoDocumento1`
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
