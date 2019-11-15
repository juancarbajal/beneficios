<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160208161032 extends AbstractMigration
{
    public static $description = "Create BNF_DM_Met_Cliente_Preguntas table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF_DM_Met_Cliente_Preguntas` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `BNF_Cliente_id` INT NOT NULL,
          `BNF_DM_Dim_Empresa_id` INT NOT NULL,
          `BNF_DM_Dim_EstadoCivil_id` INT NOT NULL,
          `BNF_DM_Dim_Hijos_id` INT NOT NULL,
          `BNF_DM_Dim_Edad_id` INT NOT NULL,
          `Genero` VARCHAR(45) NULL,
          `nombres` VARCHAR(45) NULL,
          `apellidos` VARCHAR(45) NULL,
          `distrito_vive` VARCHAR(45) NULL,
          `distrito_trabaja` VARCHAR(45) NULL,
          PRIMARY KEY (`id`),
          INDEX `BNF_Cliente_id_index` (`BNF_Cliente_id` ASC),
          INDEX `fk_BNF_DM_Met_Cliente_Preguntas_1_idx` (`BNF_DM_Dim_Empresa_id` ASC),
          INDEX `fk_BNF_DM_Met_Cliente_Preguntas_2_idx` (`BNF_DM_Dim_EstadoCivil_id` ASC),
          INDEX `fk_BNF_DM_Met_Cliente_Preguntas_3_idx` (`BNF_DM_Dim_Edad_id` ASC),
          INDEX `fk_BNF_DM_Met_Cliente_Preguntas_4_idx` (`BNF_DM_Dim_Hijos_id` ASC),
          CONSTRAINT `fk_BNF_DM_Met_Cliente_Preguntas_1`
            FOREIGN KEY (`BNF_DM_Dim_Empresa_id`)
            REFERENCES `BNF_DM_Dim_Empresa` (`BNF_Empresa_Cliente_id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_DM_Met_Cliente_Preguntas_2`
            FOREIGN KEY (`BNF_DM_Dim_EstadoCivil_id`)
            REFERENCES `BNF_DM_Dim_EstadoCivil` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_DM_Met_Cliente_Preguntas_3`
            FOREIGN KEY (`BNF_DM_Dim_Edad_id`)
            REFERENCES `BNF_DM_Dim_Edad` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_DM_Met_Cliente_Preguntas_4`
            FOREIGN KEY (`BNF_DM_Dim_Hijos_id`)
            REFERENCES `BNF_DM_Dim_Hijos` (`id`)
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
