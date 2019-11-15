<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175019 extends AbstractMigration
{
    public static $description = "Create BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Oferta` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Paquete_id` INT NOT NULL COMMENT '',
          `BNF_TipoBeneficio_id` INT NOT NULL COMMENT '',
          `Nombre` VARCHAR(255) NULL COMMENT '',
          `Titulo` VARCHAR(255) NOT NULL COMMENT '',
          `TituloCorto` VARCHAR(255) NULL COMMENT '',
          `SubTitulo` VARCHAR(255) NULL COMMENT '',
          `FormatoBeneficio` VARCHAR(255) NULL COMMENT '',
          `DatoBeneficio` VARCHAR(255) NULL COMMENT '\ndato del beneficio',
          `Descripcion` VARCHAR(255) NOT NULL COMMENT '\ndescripcion de la oferta',
          `CondicionesUso` VARCHAR(255) NOT NULL COMMENT '',
          `Direccion` VARCHAR(255) NOT NULL COMMENT '',
          `Telefono` VARCHAR(45) NOT NULL COMMENT '',
          `Premium` INT NULL COMMENT '\nPremium / No Premium\n',
          `Distrito` VARCHAR(255) NULL COMMENT '',
          `FechaInicioVigencia` DATETIME NULL COMMENT '\nFecha de vigencia de la oferta\n',
          `FechaFinVigencia` DATETIME NULL COMMENT '\nFecha fin de vigencia de la oferta, hasta cuando puede ser usado',
          `FechaInicioPublicacion` DATETIME NULL COMMENT '\nFecha en que puede salir publicada la oferta',
          `FechaFinPublicacion` DATETIME NULL COMMENT '\nHasta cuando estara publicada la oferta',
          `Stock` INT NULL COMMENT '\nStock asignado a la oferta Descarga, debe ser menor o igual a la Bolsa\nStock
          asignado a la oferta Presencia, deber ser menor o igual a los días comprados según el paquete\nStock
          asignado a la oferta Lead, numero de leads a enviar\nLa Bolsa del proveedor va disminuyendo según el stock
           que se va asignando.',
          `Correo` VARCHAR(255) NULL COMMENT '',
          `Estado` ENUM('Pendiente','Publicado','Caducado') NOT NULL COMMENT '',
          `DescargaMaximaDia` INT NOT NULL COMMENT '\ndescarga maxima de cupones de la oferta que pueden descargar por
           día',
          `Distribucion` VARCHAR(45) NULL COMMENT '',
          `FechaCreacion` DATETIME NULL COMMENT '',
          `FechaActualizacion` DATETIME NULL COMMENT '',
          `Eliminado` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_Oferta_BNF_Paquete1_idx` (`BNF_Paquete_id` ASC)  COMMENT '',
          INDEX `fk_BNF_Oferta_BNF_TipoBeneficio1_idx` (`BNF_TipoBeneficio_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_Oferta_BNF_Paquete1`
            FOREIGN KEY (`BNF_Paquete_id`)
            REFERENCES `BNF_Paquete` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_Oferta_BNF_TipoBeneficio1`
            FOREIGN KEY (`BNF_TipoBeneficio_id`)
            REFERENCES `BNF_TipoBeneficio` (`id`)
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
