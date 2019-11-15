<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716100106 extends AbstractMigration
{
    public static $description = "Create BNF2_Demanda_Empresas table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Demanda_Empresas` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_Empresa_id` INT NOT NULL,
              `BNF2_Demanda_id` INT NOT NULL,
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              `FechaCreacion` DATETIME NOT NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Demanda_Empresas_1_idx` (`BNF2_Demanda_id` ASC),
              INDEX `fk_BNF2_Demanda_Empresas_2_idx` (`BNF_Empresa_id` ASC),
              CONSTRAINT `fk_BNF2_Demanda_Empresas_1`
                FOREIGN KEY (`BNF2_Demanda_id`)
                REFERENCES `BNF2_Demanda` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Demanda_Empresas_2`
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
