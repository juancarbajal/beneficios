<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160125121114 extends AbstractMigration
{
    public static $description = "Create BNF_FormularioLead table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_FormularioLead` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_Oferta_id` INT NOT NULL,
              `Nombre_Campo` VARCHAR(45) NOT NULL,
              `Tipo_Campo` ENUM('0', '1') NOT NULL,
              `Detalle` VARCHAR(255) NULL DEFAULT NULL,
              `Requerido` ENUM('0', '1') NOT NULL DEFAULT '0',
              `Activo` ENUM('0', '1') NOT NULL DEFAULT '0',
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF_OfertaFormularioLead_1_idx` (`BNF_Oferta_id` ASC),
              CONSTRAINT `fk_BNF_OfertaFormularioLead_1`
                FOREIGN KEY (`BNF_Oferta_id`)
                REFERENCES `BNF_Oferta` (`id`)
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
