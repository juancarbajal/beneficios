<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100013 extends AbstractMigration
{
    public static $description = "create BNF3_Asignacion_Premios_Estado_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Asignacion_Premios_Estado_Log` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `BNF3_Asignacion_Premios_id` int(11) NOT NULL,
          `BNF3_Segmento_id` int(11) NOT NULL,
          `BNF_Cliente_id` int(11) NOT NULL,
          `CantidadPremios` int(11) NOT NULL,
          `CantidadPremiosUsados` int(11) NOT NULL,
          `CantidadPremiosDisponibles` int(11) NOT NULL,
          `CantidadPremiosEliminados` int(11) NOT NULL,
          `EstadoPremios` enum('Activado','Desactivado','Cancelado') COLLATE utf8_unicode_ci NOT NULL,
          `Operacion` enum('Asignar','Aplicar','Redimir','Desactivar','Reactivar','Cancelar','Sumar','Restar') 
          COLLATE utf8_unicode_ci DEFAULT NULL,
          `Premios` int(11) DEFAULT NULL,
          `Motivo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `BNF_Usuario_id` int(11) DEFAULT NULL,
          `FechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `fk_BNF3_Asignacion_Premios_Log_Estado_1_idx` (`BNF3_Segmento_id`),
          KEY `fk_BNF3_Asignacion_Premios_Log_Estado_2_idx` (`BNF_Cliente_id`),
          KEY `fk_BNF3_Asignacion_Premios_Log_Estado_3_idx` (`BNF3_Asignacion_Premios_id`),
          KEY `fk_BNF3_Asignacion_Premios_Log_Estado_4_idx` (`BNF_Usuario_id`),
          CONSTRAINT `fk_BNF3_Asignacion_Premios_Estado_Log_1` FOREIGN KEY (`BNF3_Segmento_id`) 
          REFERENCES `BNF3_Segmentos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF3_Asignacion_Premios_Estado_Log_2` FOREIGN KEY (`BNF_Cliente_id`) 
          REFERENCES `BNF_Cliente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF3_Asignacion_Premios_Estado_Log_3` FOREIGN KEY (`BNF3_Asignacion_Premios_id`) 
          REFERENCES `BNF3_Asignacion_Premios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF3_Asignacion_Premios_Estado_Log_4` FOREIGN KEY (`BNF_Usuario_id`) 
          REFERENCES `BNF_Usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("DROP TABLE BNF3_Asignacion_Premios_Estado_Log");
    }
}
