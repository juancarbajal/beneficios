<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160412180359 extends AbstractMigration
{
    public static $description = "Create BNF_Tarjetas_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_Tarjetas_Oferta` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_Tarjetas_id` INT NOT NULL,
              `BNF_Oferta_id` INT NOT NULL,
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF_Tarjetas_Oferta_1_idx` (`BNF_Tarjetas_id` ASC),
              INDEX `fk_BNF_Tarjetas_Oferta_2_idx` (`BNF_Oferta_id` ASC),
              CONSTRAINT `fk_BNF_Tarjetas_Oferta_1`
                FOREIGN KEY (`BNF_Tarjetas_id`)
                REFERENCES `BNF_Tarjetas` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_Tarjetas_Oferta_2`
                FOREIGN KEY (`BNF_Oferta_id`)
                REFERENCES `BNF_Oferta` (`id`)
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
