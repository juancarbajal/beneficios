<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161116195229 extends AbstractMigration
{
    public static $description = "Create BNF2_Cupon_Puntos_Asignacion Table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF2_Cupon_Puntos_Asignacion` (
                      `id` INT NOT NULL AUTO_INCREMENT,
                      `BNF2_Cupon_Puntos_id` INT NULL,
                      `BNF2_Asignacion_Puntos_id` INT NULL,
                      `PuntosUtilizados` INT NULL,
                      `FechaCreacion` DATETIME NULL,
                      `FechaActualizacion` DATETIME NULL,
                      PRIMARY KEY (`id`),
                      INDEX `fk_BNF2_Cupon_Puntos_Asignacion_1_idx` (`BNF2_Cupon_Puntos_id` ASC),
                      INDEX `fk_BNF2_Cupon_Puntos_Asignacion_2_idx` (`BNF2_Asignacion_Puntos_id` ASC),
                      CONSTRAINT `fk_BNF2_Cupon_Puntos_Asignacion_1`
                        FOREIGN KEY (`BNF2_Cupon_Puntos_id`)
                        REFERENCES `BNF2_Cupon_Puntos` (`id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                      CONSTRAINT `fk_BNF2_Cupon_Puntos_Asignacion_2`
                        FOREIGN KEY (`BNF2_Asignacion_Puntos_id`)
                        REFERENCES `BNF2_Asignacion_Puntos` (`id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION);
                    ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
