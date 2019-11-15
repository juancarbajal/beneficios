<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716110114 extends AbstractMigration
{
    public static $description = "Create BNF2_Oferta_Puntos_Imagen table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Oferta_Puntos_Imagen` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Oferta_Puntos_id` INT NOT NULL,
              `Nombre` VARCHAR(255) NOT NULL,
              `Principal` ENUM('0', '1') NOT NULL DEFAULT '0',
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              `FechaCreacion` DATETIME NOT NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Oferta_Puntos_Imagen_1_idx` (`BNF2_Oferta_Puntos_id` ASC),
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Imagen_1`
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
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
