<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161229114538 extends AbstractMigration
{
    public static $description = "Create BNF2_Delivery_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Delivery_Puntos` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Oferta_Puntos_id` INT NOT NULL,
              `Nombre_Campo` VARCHAR(45) NOT NULL,
              `Tipo_Campo` ENUM('0', '1') NOT NULL,
              `Detalle` VARCHAR(255) NULL,
              `Requerido` ENUM('0', '1') NOT NULL,
              `Activo` ENUM('0', '1') NOT NULL,
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Delivery_Puntos_1_idx` (`BNF2_Oferta_Puntos_id` ASC),
              CONSTRAINT `fk_BNF2_Delivery_Puntos_1`
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION);");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
