<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160414171520 extends AbstractMigration
{
    public static $description = "Create BNF_LayoutCampaniaPosicion table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_LayoutCampaniaPosicion` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_LayoutCampania_id` INT NOT NULL,
              `BNF_Oferta_id` INT NOT NULL,
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              `Eliminado` TINYINT(1) NULL DEFAULT 0,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF_LayoutCampaniaPosicion_1_idx` (`BNF_LayoutCampania_id` ASC),
              INDEX `fk_BNF_LayoutCampaniaPosicion_2_idx` (`BNF_Oferta_id` ASC),
              CONSTRAINT `fk_BNF_LayoutCampaniaPosicion_1`
                FOREIGN KEY (`BNF_LayoutCampania_id`)
                REFERENCES `BNF_LayoutCampania` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_LayoutCampaniaPosicion_2`
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
