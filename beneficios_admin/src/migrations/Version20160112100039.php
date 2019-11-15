<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160112100039 extends AbstractMigration
{
    public static $description = "Create tables CRM";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
            CREATE TABLE IF NOT EXISTS `BNF_DM_Dim_Edad` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `rango` VARCHAR(45) NULL,
            PRIMARY KEY (`id`));

            INSERT INTO BNF_DM_Dim_Edad VALUES
            (1, 'no-definido'),
            (2, '0-20'),
            (3, '21-30'),
            (4, '31-40'),
            (5, '40+');

            CREATE TABLE IF NOT EXISTS `BNF_DM_Dim_Empresa` (
            --  `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_Empresa_Cliente_id` INT NOT NULL,
              `nombre` VARCHAR(255) NULL,
              PRIMARY KEY (`BNF_Empresa_Cliente_id`));

              CREATE TABLE IF NOT EXISTS `BNF_DM_DIM_Localidad` (
            --  `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_Ubigeo_id` INT NOT NULL,
              `localidad` VARCHAR(45) NULL,
              PRIMARY KEY (`BNF_Ubigeo_id`));

            CREATE TABLE IF NOT EXISTS `BNF_DM_Dim_Hijos` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `hijos` INT NULL,
              PRIMARY KEY (`id`));

            INSERT INTO BNF_DM_Dim_Hijos VALUES
            (1, -1),
            (2, 0),
            (3, 1);

            CREATE TABLE IF NOT EXISTS `BNF_DM_Dim_EstadoCivil` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `estado` ENUM('soltero', 'casado', 'viudo', 'no-definido') NULL DEFAULT 'no-definido',
              PRIMARY KEY (`id`));

            INSERT INTO BNF_DM_Dim_EstadoCivil VALUES
            (1, 'soltero'),
            (2, 'casado'),
            (3, 'viudo'),
            (4, 'no-definido');

            CREATE TABLE IF NOT EXISTS `BNF_DM_Met_Cliente` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_DM_Dim_EstadoCivil_id` INT NOT NULL,
              `BNF_DM_DIM_Localidad_id` INT NOT NULL,
              `BNF_DM_Dim_Empresa_id` INT NOT NULL,
              `BNF_DM_Dim_Hijos_id` INT NOT NULL,
              `BNF_DM_Dim_Edad_id` INT NOT NULL,
              `BNF_Cliente_id` INT NULL,
              `BNF_Cliente_FechaCreacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF_DM_Met_Cliente_BNF_DM_Dim_EstadoCivil1_idx` (`BNF_DM_Dim_EstadoCivil_id` ASC),
              INDEX `fk_BNF_DM_Met_Cliente_BNF_DM_DIM_Localidad1_idx` (`BNF_DM_DIM_Localidad_id` ASC),
              INDEX `fk_BNF_DM_Met_Cliente_BNF_DM_Dim_Empresa1_idx` (`BNF_DM_Dim_Empresa_id` ASC),
              INDEX `fk_BNF_DM_Met_Cliente_BNF_DM_Dim_Hijos1_idx` (`BNF_DM_Dim_Hijos_id` ASC),
              INDEX `fk_BNF_DM_Met_Cliente_BNF_DM_Dim_Edad1_idx` (`BNF_DM_Dim_Edad_id` ASC),
              CONSTRAINT `fk_BNF_DM_Met_Cliente_BNF_DM_Dim_EstadoCivil1`
                FOREIGN KEY (`BNF_DM_Dim_EstadoCivil_id`)
                REFERENCES `BNF_DM_Dim_EstadoCivil` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_DM_Met_Cliente_BNF_DM_DIM_Localidad1`
                FOREIGN KEY (`BNF_DM_DIM_Localidad_id`)
                REFERENCES `BNF_DM_DIM_Localidad` (`BNF_Ubigeo_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_DM_Met_Cliente_BNF_DM_Dim_Empresa1`
                FOREIGN KEY (`BNF_DM_Dim_Empresa_id`)
                REFERENCES `BNF_DM_Dim_Empresa` (`BNF_Empresa_Cliente_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_DM_Met_Cliente_BNF_DM_Dim_Hijos1`
                FOREIGN KEY (`BNF_DM_Dim_Hijos_id`)
                REFERENCES `BNF_DM_Dim_Hijos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_DM_Met_Cliente_BNF_DM_Dim_Edad1`
                FOREIGN KEY (`BNF_DM_Dim_Edad_id`)
                REFERENCES `BNF_DM_Dim_Edad` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION);
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
