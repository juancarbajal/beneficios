<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161229114539 extends AbstractMigration
{
    public static $description = "Create BNF2_Oferta_Puntos_Delivery table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Oferta_Puntos_Delivery` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Delivery_Puntos_id` INT NOT NULL,
              `BNF2_Oferta_Puntos_id` INT NOT NULL,
              `BNF2_Asignacion_Puntos_id` INT NOT NULL,
              `BNF_Empresa_id` INT NOT NULL,
              `BNF_Cliente_id` INT NOT NULL,
              `Detalle` VARCHAR(255) NULL,              
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Oferta_Puntos_Delivery_1_idx` (`BNF2_Delivery_Puntos_id` ASC),
              INDEX `fk_BNF2_Oferta_Puntos_Delivery_2_idx` (`BNF2_Oferta_Puntos_id` ASC),
              INDEX `fk_BNF2_Oferta_Puntos_Delivery_3_idx` (`BNF2_Asignacion_Puntos_id` ASC),
              INDEX `fk_BNF2_Oferta_Puntos_Delivery_4_idx` (`BNF_Empresa_id` ASC),
              INDEX `fk_BNF2_Oferta_Puntos_Delivery_5_idx` (`BNF_Cliente_id` ASC),
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Delivery_1`
                FOREIGN KEY (`BNF2_Delivery_Puntos_id`)
                REFERENCES `BNF2_Delivery_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Delivery_2`
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Delivery_3`
                FOREIGN KEY (`BNF2_Asignacion_Puntos_id`)
                REFERENCES `BNF2_Asignacion_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Delivery_4`
                FOREIGN KEY (`BNF_Empresa_id`)
                REFERENCES `BNF_Empresa` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Oferta_Puntos_Delivery_5`
                FOREIGN KEY (`BNF_Cliente_id`)
                REFERENCES `BNF_Cliente` (`id`)
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
