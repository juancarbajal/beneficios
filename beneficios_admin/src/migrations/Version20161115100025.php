<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100025 extends AbstractMigration
{
    public static $description = "create  table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF_LayoutPremiosPosicion` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF_LayoutPremios_id` int(11) NOT NULL,
              `BNF3_Oferta_Premios_id` int(11) NOT NULL,
              `Index` enum('1','2','3') NOT NULL,
              `FechaCreacion` datetime DEFAULT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              `Eliminado` tinyint(1) DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `fk_BNF_LayoutPremiosPosicion_1_idx` (`BNF_LayoutPremios_id`),
              KEY `fk_BNF_LayoutPremiosPosicion_2_idx` (`BNF3_Oferta_Premios_id`),
              CONSTRAINT `fk_BNF_LayoutPremiosPosicion_1` FOREIGN KEY (`BNF_LayoutPremios_id`) 
              REFERENCES `BNF_LayoutPremios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_LayoutPremiosPosicion_2` FOREIGN KEY (`BNF3_Oferta_Premios_id`) 
              REFERENCES `BNF3_Oferta_Premios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
