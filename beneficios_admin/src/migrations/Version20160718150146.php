<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150146 extends AbstractMigration
{
    public static $description = "Create BNF2_Asignacion_Puntos_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Asignacion_Puntos_Log` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Asignacion_Puntos_id` INT NOT NULL,
              `BNF2_Segmento_id` INT NOT NULL,
              `BNF_Cliente_id` INT NOT NULL,
              `CantidadPuntos` INT NOT NULL,
              `CantidadPuntosUsados` INT NOT NULL,
              `CantidadPuntosDisponibles` INT NOT NULL,
              `EstadoPuntos` ENUM('Activado', 'Desactivado', 'Cancelado') NOT NULL,
              `Operacion` ENUM('Suma', 'Resta') NOT NULL,
              `Puntos` INT NOT NULL,
              `FechaCreacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Asignacion_Puntos_Log_1_idx` (`BNF2_Segmento_id` ASC),
              INDEX `fk_BNF2_Asignacion_Puntos_Log_2_idx` (`BNF_Cliente_id` ASC),
              INDEX `fk_BNF2_Asignacion_Puntos_Log_3_idx` (`BNF2_Asignacion_Puntos_id` ASC),
              CONSTRAINT `fk_BNF2_Asignacion_Puntos_Log_1`
                FOREIGN KEY (`BNF2_Segmento_id`)
                REFERENCES `BNF2_Segmentos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Asignacion_Puntos_Log_2`
                FOREIGN KEY (`BNF_Cliente_id`)
                REFERENCES `BNF_Cliente` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Asignacion_Puntos_Log_3`
                FOREIGN KEY (`BNF2_Asignacion_Puntos_id`)
                REFERENCES `BNF2_Asignacion_Puntos` (`id`)
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
