<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160717140134 extends AbstractMigration
{
    public static $description = "Create BNF_LayoutPuntosPosicion table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_LayoutPuntosPosicion` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `BNF_LayoutPuntos_id` INT(11) NOT NULL,
              `BNF2_Oferta_Puntos_id` INT(11) NOT NULL,
              `Index` ENUM('1', '2', '3') NOT NULL,
              `FechaCreacion` DATETIME DEFAULT NULL,
              `FechaActualizacion` DATETIME DEFAULT NULL,
              `Eliminado` TINYINT(1) DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `fk_BNF_LayoutPuntosPosicion_1_idx` (`BNF_LayoutPuntos_id`),
              KEY `fk_BNF_LayoutPuntosPosicion_2_idx` (`BNF2_Oferta_Puntos_id`),
              CONSTRAINT `fk_BNF_LayoutPuntosPosicion_1` FOREIGN KEY (`BNF_LayoutPuntos_id`)
                REFERENCES `BNF_LayoutPuntos` (`id`)
                ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_LayoutPuntosPosicion_2` FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
                ON DELETE NO ACTION ON UPDATE NO ACTION);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
