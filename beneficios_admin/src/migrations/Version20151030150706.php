<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151030150706 extends AbstractMigration
{
    public static $description = "Alter BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Empresa`
                ADD COLUMN `BNF_Ubigeo_id_empresa` INT(11) NULL COMMENT '' AFTER `BNF_Ubigeo_id_legal`,
                ADD COLUMN `DireccionLegalDetalle` VARCHAR(255) NULL COMMENT '' AFTER `DireccionLegal`,
                ADD COLUMN `DireccionEnvioDetalle` VARCHAR(255) NULL COMMENT '' AFTER `DireccionEnvio`,
                ADD COLUMN `DireccionEmpresa` VARCHAR(255) NULL COMMENT '' AFTER `DireccionEnvioDetalle`,
                ADD COLUMN `DireccionEmpresaDetalle` VARCHAR(255) NULL COMMENT '' AFTER `DireccionEmpresa`,
                ADD COLUMN `SitioWeb` VARCHAR(255) NULL COMMENT '' AFTER `Slug`,
                ADD COLUMN `NombreContacto` VARCHAR(255) NULL COMMENT '' AFTER `SitioWeb`,
                ADD COLUMN `CorreoContacto` VARCHAR(255) NULL COMMENT '' AFTER `NombreContacto`,
                ADD COLUMN `TelefonoContacto` VARCHAR(45) NULL COMMENT '' AFTER `CorreoContacto`,
                ADD COLUMN `HoraAtencionContacto` VARCHAR(45) NULL COMMENT '' AFTER `TelefonoContacto`,
                ADD COLUMN `HoraAtencionInicioContacto` VARCHAR(255) NULL COMMENT '' AFTER `HoraAtencionContacto`,
                ADD COLUMN `HoraAtencionFinContacto` VARCHAR(255) NULL COMMENT '' AFTER `HoraAtencionInicioContacto`,
                ADD INDEX `fk_BNF_Empresa_BNF_Ubigeo3_idx` (`BNF_Ubigeo_id_empresa` ASC)  COMMENT '';
                ALTER TABLE `BNF_Empresa`
                ADD CONSTRAINT `fk_BNF_Empresa_BNF_Ubigeo3`
                  FOREIGN KEY (`BNF_Ubigeo_id_empresa`)
                  REFERENCES `BNF_Ubigeo` (`id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
