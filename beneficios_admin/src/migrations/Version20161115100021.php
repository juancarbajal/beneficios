<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100021 extends AbstractMigration
{
    public static $description = "create BNF3_Cupon_Premios table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Cupon_Premios` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF3_Oferta_Empresa_id` int(11) DEFAULT NULL,
              `BNF_Empresa_id` int(11) DEFAULT NULL,
              `BNF_Cliente_id` int(11) DEFAULT NULL,
              `CodigoCupon` varchar(45) DEFAULT NULL,
              `EstadoCupon` enum('Creado','Eliminado','Generado','Redimido','Por Pagar','Pagado','Stand By',
              'Anulado','Finalizado','Caducado') NOT NULL,
              `PremiosUsuario` int(11) DEFAULT NULL,
              `PremiosUtilizados` int(11) DEFAULT NULL,
              `BNF3_Asignacion_Premios_id` int(11) DEFAULT NULL,
              `BNF3_Oferta_Premios_id` int(11) NOT NULL,
              `BNF3_Oferta_Premios_Atributos_id` int(11) DEFAULT NULL,
              `BNF_Categoria_id` varchar(255) DEFAULT NULL,
              `BNF_Rubro_id` int(11) DEFAULT NULL,
              `BNF_ClienteCorreo_id` int(11) DEFAULT NULL,
              `FechaCreacion` datetime DEFAULT NULL,
              `FechaEliminado` datetime DEFAULT NULL,
              `FechaGenerado` datetime DEFAULT NULL,
              `FechaRedimido` datetime DEFAULT NULL,
              `FechaPorPagar` datetime DEFAULT NULL,
              `FechaPagado` datetime DEFAULT NULL,
              `FechaStandBy` datetime DEFAULT NULL,
              `FechaAnulado` datetime DEFAULT NULL,
              `FechaCaducado` datetime DEFAULT NULL,
              `FechaFinalizado` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF3_Cupon_Premios_2_idx` (`BNF_Empresa_id`),
              KEY `fk_BNF3_Cupon_Premios_3_idx` (`BNF_Cliente_id`),
              KEY `fk_BNF3_Cupon_Premios_4_idx` (`BNF3_Oferta_Premios_id`),
              KEY `fk_BNF3_Cupon_Premios_5_idx` (`BNF_ClienteCorreo_id`),
              KEY `fk_BNF3_Cupon_Premios_6_idx` (`BNF3_Oferta_Premios_Atributos_id`),
              KEY `fk_BNF3_Cupon_Premios_1_idx` (`BNF3_Oferta_Empresa_id`),
              KEY `fk_BNF3_Cupon_Premios_7_idx` (`BNF_Rubro_id`),
              KEY `fk_BNF3_Cupon_Premios_8_idx` (`BNF3_Asignacion_Premios_id`),
              CONSTRAINT `fk_BNF3_Cupon_Premios_1` FOREIGN KEY (`BNF3_Oferta_Empresa_id`) 
              REFERENCES `BNF_Empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_2` FOREIGN KEY (`BNF_Empresa_id`) 
              REFERENCES `BNF_Empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_3` FOREIGN KEY (`BNF_Cliente_id`) 
              REFERENCES `BNF_Cliente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_4` FOREIGN KEY (`BNF3_Oferta_Premios_id`) 
              REFERENCES `BNF3_Oferta_Premios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_5` FOREIGN KEY (`BNF_ClienteCorreo_id`) 
              REFERENCES `BNF_ClienteCorreo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_6` FOREIGN KEY (`BNF3_Oferta_Premios_Atributos_id`) 
              REFERENCES `BNF3_Oferta_Premios_Atributos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_7` FOREIGN KEY (`BNF_Rubro_id`) 
              REFERENCES `BNF_Rubro` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_8` FOREIGN KEY (`BNF3_Asignacion_Premios_id`) 
              REFERENCES `BNF3_Asignacion_Premios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
