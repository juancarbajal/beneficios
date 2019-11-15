<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716100104 extends AbstractMigration
{
    public static $description = "Create BNF2_Demanda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Demanda` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `ConceptoCampania` VARCHAR(255) NOT NULL,
              `BNF_Empresa_id` INT NOT NULL,
              `FechaDemanda` DATE NOT NULL,
              `PrecioMinimo` DECIMAL(10,2) NULL,
              `PrecioMaximo` DECIMAL(10,2) NULL,
              `Target` VARCHAR(255) NULL,
              `Comentarios` TEXT NULL,
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              `FechaCreacion` DATETIME NOT NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Demanda_1_idx` (`BNF_Empresa_id` ASC),
              CONSTRAINT `fk_BNF2_Demanda_1`
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
