<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716110117 extends AbstractMigration
{
    public static $description = "Create BNF2_Oferta_Puntos_Campania table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Oferta_Puntos_Campania` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Oferta_Puntos_id` INT NOT NULL,
              `BNF_CampaniaUbigeo_id` INT NOT NULL,
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              `FechaCreacion` DATETIME NOT NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Oferta_Puntos_Campania_1_idx` (`BNF2_Oferta_Puntos_id` ASC),
              INDEX `fk_BNF2_Oferta_Puntos_Campania_2_idx` (`BNF_CampaniaUbigeo_id` ASC),
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Campania_1`
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Campania_2`
                FOREIGN KEY (`BNF_CampaniaUbigeo_id`)
                REFERENCES `BNF_CampaniaUbigeo` (`id`)
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
