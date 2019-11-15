<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151104202619 extends AbstractMigration
{
    public static $description = "Create BNF_OfertaFormulario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_OfertaFormulario` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
              `BNF_Oferta_id` INT(11) NOT NULL COMMENT '',
              `BNF_Formulario_id` INT NOT NULL COMMENT '',
              `Descripcion` VARCHAR(255) NULL COMMENT '',
              `Activo` ENUM('0', '1') NOT NULL DEFAULT '0' COMMENT '',
              `Eliminado` ENUM('0', '1') NOT NULL DEFAULT '0' COMMENT '',
              PRIMARY KEY (`id`)  COMMENT '',
              INDEX `fk_OfertaFormulario_BNF_Oferta1_idx` (`BNF_Oferta_id` ASC)  COMMENT '',
              INDEX `fk_BNF_OfertaFormulario_BNF_Formulario1_idx` (`BNF_Formulario_id` ASC)  COMMENT '',
              CONSTRAINT `fk_OfertaFormulario_BNF_Oferta1`
                FOREIGN KEY (`BNF_Oferta_id`)
                REFERENCES `BNF_Oferta` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_OfertaFormulario_BNF_Formulario1`
                FOREIGN KEY (`BNF_Formulario_id`)
                REFERENCES `BNF_Formulario` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
