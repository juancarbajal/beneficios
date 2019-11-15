<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100012 extends AbstractMigration
{
    public static $description = "create BNF3_Asignacion_Premios table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Asignacion_Premios` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `BNF3_Segmento_id` int(11) NOT NULL,
          `BNF_Cliente_id` int(11) NOT NULL,
          `CantidadPremios` int(11) NOT NULL,
          `CantidadPremiosUsados` int(11) DEFAULT '0',
          `CantidadPremiosDisponibles` int(11) DEFAULT '0',
          `CantidadPremiosEliminados` int(11) DEFAULT '0',
          `EstadoPremios` enum('Activado','Desactivado','Cancelado') DEFAULT 'Activado',
          `Eliminado` tinyint(1) NOT NULL,
          `FechaCreacion` datetime DEFAULT NULL,
          `FechaActualizacion` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `fk_BNF3_Asignacion_Premios_1_idx` (`BNF3_Segmento_id`),
          KEY `fk_BNF3_Asignacion_Premios_2_idx` (`BNF_Cliente_id`),
          CONSTRAINT `fk_BNF3_Asignacion_Premios_1` FOREIGN KEY (`BNF3_Segmento_id`) 
          REFERENCES `BNF3_Segmentos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF3_Asignacion_Premios_2` FOREIGN KEY (`BNF_Cliente_id`) 
          REFERENCES `BNF_Cliente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("DROP TABLE BNF3_Asignacion_Premios;");
    }
}
