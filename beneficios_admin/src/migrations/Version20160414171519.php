<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160414171519 extends AbstractMigration
{
    public static $description = "Create BNF_LayoutTiendaPosicion table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_LayoutTiendaPosicion` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF_LayoutTienda_id` INT NOT NULL,
              `BNF_Oferta_id` INT NOT NULL,
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              `Eliminado` TINYINT(1) NULL DEFAULT 0,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF_LayoutTiendaPosicion_1_idx` (`BNF_LayoutTienda_id` ASC),
              INDEX `fk_BNF_LayoutTiendaPosicion_2_idx` (`BNF_Oferta_id` ASC),
              CONSTRAINT `fk_BNF_LayoutTiendaPosicion_1`
                FOREIGN KEY (`BNF_LayoutTienda_id`)
                REFERENCES `BNF_LayoutTienda` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_LayoutTiendaPosicion_2`
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
