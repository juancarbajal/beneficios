<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716100107 extends AbstractMigration
{
    public static $description = "Create BNF2_Demanda_Departamentos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Demanda_Departamentos` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_Departamentos_id` INT NOT NULL,
              `BNF2_Demanda_id` INT NOT NULL,
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              `FechaCreacion` DATETIME NOT NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Demanda_Departamentos_1_idx` (`BNF2_Demanda_id` ASC),
              INDEX `fk_BNF2_Demanda_Departamentos_2_idx` (`BNF_Departamentos_id` ASC),
              CONSTRAINT `fk_BNF2_Demanda_Provincias_1`
                FOREIGN KEY (`BNF2_Demanda_id`)
                REFERENCES `BNF2_Demanda` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Demanda_Departamentos_2`
                FOREIGN KEY (`BNF_Departamentos_id`)
                REFERENCES `BNF_Ubigeo` (`id`)
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
