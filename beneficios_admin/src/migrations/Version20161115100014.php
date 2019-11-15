<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100014 extends AbstractMigration
{
    public static $description = "create BNF3_Oferta_Premios table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Oferta_Premios` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF_Empresa_id` int(11) NOT NULL,
              `Nombre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `Titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `TituloCorto` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `CondicionesUso` longtext COLLATE utf8_unicode_ci NOT NULL,
              `Direccion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `Telefono` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
              `Correo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `Premium` tinyint(1) NOT NULL DEFAULT '0',
              `TipoPrecio` enum('Split','Unico') COLLATE utf8_unicode_ci NOT NULL,
              `PrecioVentaPublico` int(11) DEFAULT NULL,
              `PrecioBeneficio` int(11) DEFAULT NULL,
              `Distrito` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `FechaVigencia` date DEFAULT NULL,
              `DescargaMaxima` int(11) NOT NULL,
              `Stock` int(11) DEFAULT NULL,
              `Slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `Estado` enum('Publicado','Borrador','Caducado') COLLATE utf8_unicode_ci NOT NULL,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime NOT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `Slug_UNIQUE` (`Slug`),
              KEY `fk_BNF3_Oferta_Premios_2_idx` (`BNF_Empresa_id`),
              CONSTRAINT `fk_BNF3_Oferta_Premios_2` FOREIGN KEY (`BNF_Empresa_id`) 
              REFERENCES `BNF_Empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
