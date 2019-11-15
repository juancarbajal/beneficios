<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716090103 extends AbstractMigration
{
    public static $description = "Create BNF2_Campanias_Empresas table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF2_Campanias_Empresas` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `BNF2_Campania_id` INT NOT NULL,
          `BNF_Empresa_id` INT NOT NULL,
          `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
          `FechaCreacion` DATETIME NULL,
          `FechaActualizacion` DATETIME NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_BNF2_Campanias_Empresas_1_idx` (`BNF2_Campania_id` ASC),
          INDEX `fk_BNF2_Campanias_Empresas_2_idx` (`BNF_Empresa_id` ASC),
          CONSTRAINT `fk_BNF2_Campanias_Empresas_1`
            FOREIGN KEY (`BNF2_Campania_id`)
            REFERENCES `BNF2_Campanias` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF2_Campanias_Empresas_2`
            FOREIGN KEY (`BNF_Empresa_id`)
            REFERENCES `BNF_Empresa` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION);");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
