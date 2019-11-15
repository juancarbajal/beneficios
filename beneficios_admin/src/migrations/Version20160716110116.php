<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716110116 extends AbstractMigration
{
    public static $description = "Create BNF2_Oferta_Puntos_Categoria table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Oferta_Puntos_Categoria` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Oferta_Puntos_id` INT NOT NULL,
              `BNF_CategoriaUbigeo_id` INT NOT NULL,
              `Eliminado` TINYINT NOT NULL DEFAULT 0,
              `FechaCreacion` DATETIME NOT NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Oferta_Puntos_Categoria_1_idx` (`BNF2_Oferta_Puntos_id` ASC),
              INDEX `fk_BNF2_Oferta_Puntos_Categoria_2_idx` (`BNF_CategoriaUbigeo_id` ASC),
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Categoria_1`
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Categoria_2`
                FOREIGN KEY (`BNF_CategoriaUbigeo_id`)
                REFERENCES `BNF_CategoriaUbigeo` (`id`)
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
