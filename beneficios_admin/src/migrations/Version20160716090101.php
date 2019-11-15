<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716090101 extends AbstractMigration
{
    public static $description = "Create BNF2_Campanias table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF2_Campanias` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `NombreCampania` VARCHAR(255) NOT NULL,
          `TipoSegmento` ENUM('Clasico', 'Personalizado') NOT NULL,
          `VigenciaInicio` DATE NOT NULL,
          `VigenciaFin` DATE NOT NULL,
          `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
          `FechaCreacion` DATETIME NULL,
          `FechaActualizacion` DATETIME NULL,
          PRIMARY KEY (`id`));");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
