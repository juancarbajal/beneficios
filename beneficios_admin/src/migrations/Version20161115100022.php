<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100022 extends AbstractMigration
{
    public static $description = "create BNF3_Cupon_Premios_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
        CREATE TABLE `BNF3_Cupon_Premios_Log` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF3_Cupon_Premios_id` int(11) NOT NULL,
              `CodigoCupon` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
              `EstadoCupon` enum('Creado','Eliminado','Generado','Redimido','Por Pagar','Pagado','Stand By',
              'Anulado','Finalizado','Caducado') COLLATE utf8_unicode_ci NOT NULL,
              `BNF3_Oferta_Premios_id` int(11) NOT NULL,
              `BNF3_Oferta_Premios_Atributos_id` int(11) DEFAULT NULL,
              `BNF_Cliente_id` int(11) DEFAULT NULL,
              `BNF_Usuario_id` int(11) DEFAULT NULL,
              `Comentario` mediumtext COLLATE utf8_unicode_ci NOT NULL,
              `FechaCreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `fk_BNF3_Cupon_Premios_Log_1_idx` (`BNF3_Cupon_Premios_id`),
              KEY `fk_BNF3_Cupon_Premios_Log_2_idx` (`BNF3_Oferta_Premios_id`),
              KEY `fk_BNF3_Cupon_Premios_Log_3_idx` (`BNF3_Oferta_Premios_Atributos_id`),
              KEY `fk_BNF3_Cupon_Premios_Log_4_idx` (`BNF_Usuario_id`),
              KEY `fk_BNF3_Cupon_Premios_Log_5_idx` (`BNF_Cliente_id`),
              CONSTRAINT `fk_BNF3_Cupon_Premios_Log_1` FOREIGN KEY (`BNF3_Cupon_Premios_id`) 
              REFERENCES `BNF3_Cupon_Premios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_Log_2` FOREIGN KEY (`BNF3_Oferta_Premios_id`) 
              REFERENCES `BNF3_Oferta_Premios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_Log_3` FOREIGN KEY (`BNF3_Oferta_Premios_Atributos_id`) 
              REFERENCES `BNF3_Oferta_Premios_Atributos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_Log_4` FOREIGN KEY (`BNF_Usuario_id`) 
              REFERENCES `BNF_Usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Cupon_Premios_Log_5` FOREIGN KEY (`BNF_Cliente_id`) 
              REFERENCES `BNF_Cliente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
