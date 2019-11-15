<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175024 extends AbstractMigration
{
    public static $description = "Create BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Empresa` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Usuario_id` INT NOT NULL COMMENT 'Asesor, administrador, finanzas',
          `BNF_TipoDocumento_id` INT NOT NULL COMMENT '\ndel asesor, administrador, finanzas',
          `BNF_Ubigeo_id_envio` INT NOT NULL COMMENT '',
          `BNF_Ubigeo_id_legal` INT NOT NULL COMMENT '',
          `NombreComercial` VARCHAR(255) NULL COMMENT '\nnombre comercial de la empresa',
          `RazonSocial` VARCHAR(255) NULL COMMENT '\nrazón social de la empresa',
          `ApellidoPaterno` VARCHAR(255) NULL COMMENT '\napellido de persona natural',
          `ApellidoMaterno` VARCHAR(255) NULL COMMENT '\napellido de persona natural',
          `Nombre` VARCHAR(255) NULL COMMENT '\nnombre de persona natural\n',
          `Ruc` INT NOT NULL COMMENT '\nruc de la empresa',
          `Descripcion` TEXT(255) NULL COMMENT '',
          `RepresentanteLegal` VARCHAR(255) NULL COMMENT '\nrepresentante legar de la empresa',
          `RepresentanteNumeroDocumento` INT NULL COMMENT '\ndocumento del representante legal',
          `DireccionLegal` VARCHAR(255) NULL COMMENT '',
          `DireccionEnvio` VARCHAR(255) NULL COMMENT '',
          `HoraAtencionInicio` VARCHAR(255) NULL COMMENT '\ninicio de horario de atención\n',
          `HoraAtencionFin` VARCHAR(255) NULL COMMENT '\nfin de horario de atencioin',
          `PersonaAtencion` VARCHAR(255) NULL COMMENT '\npersona de contacto de atención',
          `CargoPersonaAtencion` VARCHAR(255) NULL COMMENT '\ncargo de persona contacto de atención\n',
          `Telefono` VARCHAR(45) NULL COMMENT '',
          `Celular` VARCHAR(45) NULL COMMENT '',
          `CorreoPersonaAtencion` VARCHAR(255) NOT NULL COMMENT '',
          `Logo` VARCHAR(255) NOT NULL COMMENT '',
          `IdSap` VARCHAR(45) NULL COMMENT '',
          `FechaCreacion` DATETIME NULL COMMENT '',
          `FechaActualizacion` DATETIME NULL COMMENT '',
          `Eliminado` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_Empresa_BNF_Usuario1_idx` (`BNF_Usuario_id` ASC)  COMMENT '',
          INDEX `fk_BNF_Empresa_BNF_TipoDocumento1_idx` (`BNF_TipoDocumento_id` ASC)  COMMENT '',
          INDEX `fk_BNF_Empresa_BNF_Ubigeo1_idx` (`BNF_Ubigeo_id_envio` ASC)  COMMENT '',
          INDEX `fk_BNF_Empresa_BNF_Ubigeo2_idx` (`BNF_Ubigeo_id_legal` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_Empresa_BNF_Usuario1`
            FOREIGN KEY (`BNF_Usuario_id`)
            REFERENCES `BNF_Usuario` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Empresa_BNF_TipoDocumento1`
            FOREIGN KEY (`BNF_TipoDocumento_id`)
            REFERENCES `BNF_TipoDocumento` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Empresa_BNF_Ubigeo1`
            FOREIGN KEY (`BNF_Ubigeo_id_envio`)
            REFERENCES `BNF_Ubigeo` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Empresa_BNF_Ubigeo2`
            FOREIGN KEY (`BNF_Ubigeo_id_legal`)
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
