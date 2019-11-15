<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161020124007 extends AbstractMigration
{
    public static $description = "Create BNF_Oferta_Atributos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_Oferta_Atributos` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `BNF_Oferta_id` INT(11) NOT NULL,
                `NombreAtributo` TEXT NOT NULL,
                `Stock` INT(11) NOT NULL,
                `StockInicial` INT(11) NOT NULL,
                `FechaVigencia` DATE NOT NULL,
                `Eliminado` TINYINT(1) NOT NULL DEFAULT '0',
                `FechaCreacion` DATETIME NOT NULL,
                `FechaActualizacion` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `fk_BNF_Oferta_Atributos_1_idx` (`BNF_Oferta_id`),
                CONSTRAINT `fk_BNF_Oferta_Atributos_1` FOREIGN KEY (`BNF_Oferta_id`)
                    REFERENCES `BNF_Oferta` (`id`)
                    ON DELETE NO ACTION ON UPDATE NO ACTION
            );"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
