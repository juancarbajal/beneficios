<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150147 extends AbstractMigration
{
    public static $description = "Create BNF2_Asignacion_Puntos_Estado_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Asignacion_Puntos_Estado_Log` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `BNF2_Asignacion_Puntos_id` INT(11) NOT NULL,
            `BNF2_Segmento_id` INT(11) NOT NULL,
            `BNF_Cliente_id` INT(11) NOT NULL,
            `CantidadPuntos` INT(11) NOT NULL,
            `CantidadPuntosUsados` INT(11) NOT NULL,
            `CantidadPuntosDisponibles` INT(11) NOT NULL,
            `CantidadPuntosEliminados` INT(11) NOT NULL,
            `EstadoPuntos` ENUM('Activado', 'Desactivado', 'Cancelado') NOT NULL,
            `EstadoPuntosAnterior` ENUM('Activado', 'Desactivado', 'Cancelado') NOT NULL,
            `BNF_Usuario_id` INT(11) NOT NULL,
            `Motivo` VARCHAR(255) NULL,
            `FechaCreacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_BNF2_Asignacion_Puntos_Log_Estado_1_idx` (`BNF2_Segmento_id`),
            KEY `fk_BNF2_Asignacion_Puntos_Log_Estado_2_idx` (`BNF_Cliente_id`),
            KEY `fk_BNF2_Asignacion_Puntos_Log_Estado_3_idx` (`BNF2_Asignacion_Puntos_id`),
            KEY `fk_BNF2_Asignacion_Puntos_Log_Estado_4_idx` (`BNF_Usuario_id`),
            CONSTRAINT `fk_BNF2_Asignacion_Puntos_Estado_Log_1` FOREIGN KEY (`BNF2_Segmento_id`)
                REFERENCES `BNF2_Segmentos` (`id`)
                ON DELETE NO ACTION ON UPDATE NO ACTION,
            CONSTRAINT `fk_BNF2_Asignacion_Puntos_Estado_Log_2` FOREIGN KEY (`BNF_Cliente_id`)
                REFERENCES `BNF_Cliente` (`id`)
                ON DELETE NO ACTION ON UPDATE NO ACTION,
            CONSTRAINT `fk_BNF2_Asignacion_Puntos_Estado_Log_3` FOREIGN KEY (`BNF2_Asignacion_Puntos_id`)
                REFERENCES `BNF2_Asignacion_Puntos` (`id`)
                ON DELETE NO ACTION ON UPDATE NO ACTION,
            CONSTRAINT `fk_BNF2_Asignacion_Puntos_Estado_Log_4` FOREIGN KEY (`BNF_Usuario_id`)
                REFERENCES `BNF_Usuario` (`id`)
                ON DELETE NO ACTION ON UPDATE NO ACTION);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
