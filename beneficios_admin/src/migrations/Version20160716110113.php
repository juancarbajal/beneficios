<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716110113 extends AbstractMigration
{
    public static $description = "Create BNF2_Oferta_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Oferta_Puntos` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Segmento_id` INT NOT NULL,
              `BNF_Empresa_id` INT NOT NULL,
              `Nombre` VARCHAR(255) NOT NULL,
              `Titulo` VARCHAR(255) NULL,
              `TituloCorto` VARCHAR(255) NULL,
              `CondicionesUso` LONGTEXT NOT NULL,
              `Direccion` VARCHAR(255) NOT NULL,
              `Telefono` VARCHAR(50) NULL,
              `Correo` VARCHAR(255) NULL,
              `Premium` TINYINT(1) NOT NULL DEFAULT 0,
              `TipoPrecio` ENUM('Split', 'Unico') NOT NULL,
              `PrecioVentaPublico` INT NULL,
              `PrecioBeneficio` INT NULL,
              `Distrito` VARCHAR(255) NULL,
              `FechaVigencia` DATE NOT NULL,
              `DescargaMaxima` INT NOT NULL,
              `Stock` INT NOT NULL,
              `Slug` INT NOT NULL,
              `Estado` ENUM('Publicada', 'Borrador', 'Caducada') NOT NULL,
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              `FechaCreacion` DATETIME NOT NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Oferta_Puntos_1_idx` (`BNF2_Segmento_id` ASC),
              INDEX `fk_BNF2_Oferta_Puntos_2_idx` (`BNF_Empresa_id` ASC),
              CONSTRAINT `fk_BNF2_Oferta_Puntos_1`
                FOREIGN KEY (`BNF2_Segmento_id`)
                REFERENCES `BNF2_Segmentos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Oferta_Puntos_2`
                FOREIGN KEY (`BNF_Empresa_id`)
                REFERENCES `BNF_Empresa` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
