<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160125121117 extends AbstractMigration
{
    public static $description = "Create BNF_OfertaFormClienteLead table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_OfertaFormClienteLead` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_Oferta_id` INT NOT NULL,
              `BNF_Cliente_id` INT NOT NULL,
              `BNF_Empresa_id` INT NOT NULL,
              `BNF_Categoria_id` VARCHAR(255) NOT NULL,
              `BNF_Formulario_id` INT NOT NULL,
              `Descripcion` VARCHAR(255) NOT NULL,
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF_OfertaFormClienteLead_1_idx` (`BNF_Oferta_id` ASC),
              INDEX `fk_BNF_OfertaFormClienteLead_2_idx` (`BNF_Cliente_id` ASC),
              INDEX `fk_BNF_OfertaFormClienteLead_3_idx` (`BNF_Empresa_id` ASC),
              INDEX `fk_BNF_OfertaFormClienteLead_4_idx` (`BNF_Formulario_id` ASC),
              CONSTRAINT `fk_BNF_OfertaFormClienteLead_1`
                FOREIGN KEY (`BNF_Oferta_id`)
                REFERENCES `BNF_Oferta` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_OfertaFormClienteLead_2`
                FOREIGN KEY (`BNF_Cliente_id`)
                REFERENCES `BNF_Cliente` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_OfertaFormClienteLead_3`
                FOREIGN KEY (`BNF_Empresa_id`)
                REFERENCES `BNF_Empresa` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_OfertaFormClienteLead_4`
                FOREIGN KEY (`BNF_Formulario_id`)
                REFERENCES `BNF_FormularioLead` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
