<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150828175041 extends AbstractMigration
{
    public static $description = "Create BNF_Campanias table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Campanias` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT
          'Campaña promocional de la oferta: Dia de la madre, Mistura, fiestas patrias, etc.',
          `Nombre` VARCHAR(255) NOT NULL COMMENT '',
          `Descripcion` VARCHAR(255) NULL COMMENT '',
          `FechaCreacion` DATETIME NULL COMMENT '',
          `FechaActualizacion` DATETIME NULL COMMENT '',
          `Eliminado` INT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '')
        ENGINE = InnoDB;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
